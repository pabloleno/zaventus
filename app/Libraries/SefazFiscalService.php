<?php

namespace App\Libraries;

use App\Models\ConfigNFCeModel;
use App\Models\ConfigNFeNFCeModel;
use App\Models\DocumentoFiscalEventoModel;
use App\Models\NFCeModel;
use App\Models\NFeModel;
use NFePHP\Common\Certificate;
use NFePHP\NFe\Complements;
use NFePHP\NFe\Common\Standardize;
use NFePHP\NFe\Tools;

class SefazFiscalService
{
    public const MODELO_NFE = '55';
    public const MODELO_NFCE = '65';

    private static bool $autoloadCarregado = false;
    private NFeModel $nfes;
    private NFCeModel $nfces;
    private ConfigNFeNFCeModel $configNfe;
    private ConfigNFCeModel $configNfce;
    private DocumentoFiscalEventoModel $eventos;

    /**
     * Inicializa modelos e carrega a biblioteca fiscal de terceiros quando necessario.
     */
    public function __construct()
    {
        $this->carregarDependencias();
        $this->nfes = new NFeModel();
        $this->nfces = new NFCeModel();
        $this->configNfe = new ConfigNFeNFCeModel();
        $this->configNfce = new ConfigNFCeModel();
        $this->eventos = new DocumentoFiscalEventoModel();
    }

    /**
     * Consulta o status do WebService autorizador da SEFAZ.
     */
    public function statusServico(string $modelo): array
    {
        $modelo = $this->normalizarModelo($modelo);
        $dados = $this->configuracao($modelo);
        $response = $this->tools($modelo, $dados)->sefazStatus((string) $dados['UF'], (int) $dados['tpAmb']);
        $std = $this->standardize($response);

        $resultado = [
            'modelo' => $modelo,
            'cstat' => (string) ($std->cStat ?? ''),
            'xmotivo' => (string) ($std->xMotivo ?? ''),
            'ambiente' => (int) ($std->tpAmb ?? $dados['tpAmb']),
            'retorno' => $response,
        ];

        $this->registrarEvento($modelo, 0, 'status_servico', [
            'cstat' => $resultado['cstat'],
            'xmotivo' => $resultado['xmotivo'],
            'xml_retorno' => $response,
            'status' => $resultado['cstat'] === '107' ? 'Disponivel' : 'Atencao',
        ]);

        return $resultado;
    }

    /**
     * Consulta a situacao atual da chave na SEFAZ e sincroniza o documento local.
     */
    public function consultarDocumento(string $modelo, int $idDocumento): array
    {
        $modelo = $this->normalizarModelo($modelo);
        $documento = $this->documento($modelo, $idDocumento);
        $metadadosChave = self::metadadosDaChave((string) $documento['chave']);
        $dados = $this->configuracao($modelo);
        $response = $this->tools($modelo, $dados)->sefazConsultaChave((string) $documento['chave'], (int) $dados['tpAmb']);
        $std = $this->standardize($response);
        $protocolo = $std->protNFe->infProt ?? null;
        $cStat = (string) ($protocolo->cStat ?? $std->cStat ?? '');
        $xMotivo = (string) ($protocolo->xMotivo ?? $std->xMotivo ?? '');
        $nProt = (string) ($protocolo->nProt ?? $documento['nprot'] ?? $this->extrairTag((string) ($documento['protocolo'] ?? ''), 'nProt'));
        $dataAutorizacao = $this->dataFiscal((string) ($protocolo->dhRecbto ?? ''));
        $status = $this->statusPorCstat($cStat, (string) ($documento['status'] ?? ''));

        $this->atualizarDocumento($modelo, $idDocumento, [
            'status' => $status,
            'ambiente' => (int) ($std->tpAmb ?? $dados['tpAmb']),
            'serie' => trim((string) ($documento['serie'] ?? '')) !== '' ? $documento['serie'] : ($metadadosChave['serie'] ?? null),
            'numero' => trim((string) ($documento['numero'] ?? '')) !== '' ? $documento['numero'] : ($metadadosChave['numero'] ?? null),
            'nprot' => $nProt ?: null,
            'cstat' => $cStat ?: null,
            'xmotivo' => $xMotivo ?: null,
            'data_autorizacao' => $dataAutorizacao,
            'ultimo_retorno_sefaz' => $response,
            'ultima_consulta_em' => date('Y-m-d H:i:s'),
        ]);

        $this->registrarEvento($modelo, $idDocumento, 'consulta_chave', [
            'chave' => (string) $documento['chave'],
            'protocolo' => $nProt ?: null,
            'cstat' => $cStat ?: null,
            'xmotivo' => $xMotivo ?: null,
            'xml_retorno' => $response,
            'status' => in_array($cStat, ['100', '101', '150', '151', '155'], true) ? 'Sincronizado' : 'Atencao',
        ]);

        return [
            'sucesso' => in_array($cStat, ['100', '101', '150', '151', '155'], true),
            'status' => $status,
            'cstat' => $cStat,
            'xmotivo' => $xMotivo,
            'nprot' => $nProt,
            'retorno' => $response,
        ];
    }

    /**
     * Cancela uma NFe/NFCe autorizada e registra o XML do evento.
     */
    public function cancelarDocumento(string $modelo, int $idDocumento, string $justificativa): array
    {
        $modelo = $this->normalizarModelo($modelo);
        $justificativa = trim($justificativa);

        if (mb_strlen($justificativa) < 15 || mb_strlen($justificativa) > 255) {
            throw new \InvalidArgumentException('Informe uma justificativa de cancelamento entre 15 e 255 caracteres.');
        }

        $documento = $this->documento($modelo, $idDocumento);
        $dados = $this->configuracao($modelo);
        $nProt = $this->protocoloAutorizacao($documento);

        if ($nProt === '') {
            $consulta = $this->consultarDocumento($modelo, $idDocumento);
            $documento = $this->documento($modelo, $idDocumento);
            $nProt = (string) ($consulta['nprot'] ?: $this->protocoloAutorizacao($documento));
        }

        if ($nProt === '') {
            throw new \RuntimeException('Nao foi encontrado protocolo de autorizacao para cancelar o documento.');
        }

        $tools = $this->tools($modelo, $dados);
        $response = $tools->sefazCancela((string) $documento['chave'], $justificativa, $nProt);
        $std = $this->standardize($response);
        $evento = $std->retEvento->infEvento ?? null;
        $cStat = (string) ($evento->cStat ?? $std->cStat ?? '');
        $xMotivo = (string) ($evento->xMotivo ?? $std->xMotivo ?? '');
        $protocoloEvento = (string) ($evento->nProt ?? '');
        $sucesso = in_array($cStat, ['101', '135', '155'], true);
        $xmlCancelamento = null;

        if ($sucesso) {
            $xmlCancelamento = Complements::toAuthorize($tools->lastRequest, $response);
            $this->atualizarDocumento($modelo, $idDocumento, [
                'status' => 'Cancelada',
                'cstat' => $cStat,
                'xmotivo' => $xMotivo,
                'data_cancelamento' => date('Y-m-d H:i:s'),
                'xml_cancelamento' => $xmlCancelamento,
                'xml_protocolado_cancelamento' => $xmlCancelamento,
                'ultimo_retorno_sefaz' => $response,
                'ultima_consulta_em' => date('Y-m-d H:i:s'),
            ]);
        } else {
            $this->atualizarDocumento($modelo, $idDocumento, [
                'cstat' => $cStat ?: null,
                'xmotivo' => $xMotivo ?: null,
                'ultimo_retorno_sefaz' => $response,
                'ultima_consulta_em' => date('Y-m-d H:i:s'),
            ]);
        }

        $this->registrarEvento($modelo, $idDocumento, 'cancelamento', [
            'chave' => (string) $documento['chave'],
            'protocolo' => $protocoloEvento ?: $nProt,
            'cstat' => $cStat ?: null,
            'xmotivo' => $xMotivo ?: null,
            'justificativa' => $justificativa,
            'xml_envio' => $tools->lastRequest ?? null,
            'xml_retorno' => $xmlCancelamento ?: $response,
            'status' => $sucesso ? 'Autorizado' : 'Rejeitado',
        ]);

        return [
            'sucesso' => $sucesso,
            'cstat' => $cStat,
            'xmotivo' => $xMotivo,
            'protocolo' => $protocoloEvento,
            'retorno' => $response,
        ];
    }

    /**
     * Retorna o XML de autorizacao ou cancelamento para download.
     */
    public function xmlDocumento(string $modelo, int $idDocumento, string $tipo = 'autorizacao'): array
    {
        $modelo = $this->normalizarModelo($modelo);
        $documento = $this->documento($modelo, $idDocumento);
        $campo = $tipo === 'cancelamento' ? 'xml_cancelamento' : 'xml';
        $xml = (string) ($documento[$campo] ?? '');

        if ($tipo === 'cancelamento' && $xml === '') {
            $xml = (string) ($documento['xml_protocolado_cancelamento'] ?? '');
        }

        if ($xml === '') {
            throw new \RuntimeException('XML nao encontrado para este documento.');
        }

        $sufixo = $tipo === 'cancelamento' ? '-cancelamento' : '';

        return [
            'nome' => ((string) $documento['chave']) . $sufixo . '.xml',
            'conteudo' => $xml,
        ];
    }

    /**
     * Classifica documentos fiscais para exibicao visual.
     */
    public static function classeStatus(string $status): string
    {
        return match ($status) {
            'Emitida' => 'success',
            'Cancelada' => 'secondary',
            'Denegada' => 'dark',
            'Nao localizada', 'Rejeitada' => 'danger',
            default => 'warning',
        };
    }

    /**
     * Retorna o nome legivel do modelo fiscal.
     */
    public static function nomeModelo(string $modelo): string
    {
        return $modelo === self::MODELO_NFCE ? 'NFCe' : 'NFe';
    }

    /**
     * Extrai serie e numero da chave de acesso fiscal quando eles nao foram gravados separadamente.
     */
    public static function metadadosDaChave(string $chave): array
    {
        $chave = (string) preg_replace('/\D/', '', $chave);

        if (strlen($chave) !== 44) {
            return [];
        }

        return [
            'serie' => ltrim(substr($chave, 22, 3), '0') ?: '0',
            'numero' => ltrim(substr($chave, 25, 9), '0') ?: '0',
        ];
    }

    /**
     * Carrega o autoload local do sped-nfe apenas quando ainda nao estiver disponivel.
     */
    private function carregarDependencias(): void
    {
        if (self::$autoloadCarregado || class_exists(Tools::class)) {
            self::$autoloadCarregado = true;
            return;
        }

        ThirdPartyComposerLoader::loadWithoutPsrLog(APPPATH . 'ThirdParty/sped-nfe/vendor/autoload.php');
        self::$autoloadCarregado = true;
    }

    /**
     * Cria a instancia do cliente SEFAZ para o modelo informado.
     */
    private function tools(string $modelo, array $dados): Tools
    {
        $certificado = $modelo === self::MODELO_NFCE ? 'certificado_nfce.pfx' : 'certificado_nfe.pfx';
        $arquivo = WRITEPATH . 'uploads/' . $certificado;

        if (! is_file($arquivo)) {
            throw new \RuntimeException('Certificado digital nao encontrado em Configs > ' . self::nomeModelo($modelo) . '.');
        }

        $config = [
            'atualizacao' => date('Y-m-d H:i:s'),
            'tpAmb' => (int) ($dados['tpAmb'] ?? 2),
            'razaosocial' => (string) ($dados['xNome'] ?? ''),
            'cnpj' => (string) ($dados['CNPJ'] ?? ''),
            'ie' => (string) ($dados['IE'] ?? ''),
            'siglaUF' => (string) ($dados['UF'] ?? ''),
            'schemes' => 'PL_009_V4',
            'versao' => '4.00',
            'tokenIBPT' => 'AAAAAAA',
            'CSC' => (string) ($dados['CSC'] ?? ''),
            'CSCid' => (string) ($dados['CSCid'] ?? ''),
        ];

        $tools = new Tools(json_encode($config), Certificate::readPfx(file_get_contents($arquivo), (string) ($dados['senha'] ?? '')));
        $tools->model($modelo);

        return $tools;
    }

    /**
     * Retorna as configuracoes fiscais cadastradas para o modelo.
     */
    private function configuracao(string $modelo): array
    {
        $dados = $modelo === self::MODELO_NFCE
            ? $this->configNfce->where('id_config', 1)->first()
            : $this->configNfe->where('id_config', 1)->first();

        if (empty($dados)) {
            throw new \RuntimeException('Configuracao fiscal nao encontrada para ' . self::nomeModelo($modelo) . '.');
        }

        return $dados;
    }

    /**
     * Busca o documento fiscal local pelo modelo.
     */
    private function documento(string $modelo, int $idDocumento): array
    {
        $documento = $this->modelDocumento($modelo)->find($idDocumento);

        if (empty($documento)) {
            throw new \RuntimeException('Documento fiscal nao encontrado.');
        }

        if (empty($documento['chave'])) {
            throw new \RuntimeException('Documento fiscal sem chave de acesso.');
        }

        return $documento;
    }

    /**
     * Atualiza somente campos existentes na tabela atual.
     */
    private function atualizarDocumento(string $modelo, int $idDocumento, array $dados): void
    {
        $model = $this->modelDocumento($modelo);
        $tabela = $modelo === self::MODELO_NFCE ? 'nfces' : 'nfes';
        $dados = $this->filtrarCamposExistentes($tabela, $dados);

        if (empty($dados)) {
            return;
        }

        $model->save([$this->chavePrimaria($modelo) => $idDocumento] + $dados);
    }

    /**
     * Registra uma ocorrencia fiscal no historico quando a tabela existir.
     */
    private function registrarEvento(string $modelo, int $idDocumento, string $tipo, array $dados): void
    {
        if (! db_connect()->tableExists('documentos_fiscais_eventos')) {
            return;
        }

        $this->eventos->insert([
            'modelo' => $modelo,
            'id_documento' => $idDocumento,
            'tipo_evento' => $tipo,
        ] + $dados);
    }

    /**
     * Retorna a model correta para NFe/NFCe.
     */
    private function modelDocumento(string $modelo)
    {
        return $modelo === self::MODELO_NFCE ? $this->nfces : $this->nfes;
    }

    /**
     * Retorna a chave primaria da tabela fiscal.
     */
    private function chavePrimaria(string $modelo): string
    {
        return $modelo === self::MODELO_NFCE ? 'id_nfce' : 'id_nfe';
    }

    /**
     * Normaliza apelidos aceitos pelo sistema.
     */
    private function normalizarModelo(string $modelo): string
    {
        $modelo = strtolower(trim($modelo));

        if (in_array($modelo, ['65', 'nfce', 'nfc-e'], true)) {
            return self::MODELO_NFCE;
        }

        return self::MODELO_NFE;
    }

    /**
     * Converte retorno XML em objeto padronizado.
     */
    private function standardize(string $xml): \stdClass
    {
        $standardize = new Standardize();

        return $standardize->toStd($xml);
    }

    /**
     * Traduz cStat fiscal para o status simples usado pela tela.
     */
    private function statusPorCstat(string $cStat, string $statusAtual): string
    {
        return match ($cStat) {
            '100', '150' => 'Emitida',
            '101', '135', '151', '155' => 'Cancelada',
            '110', '301', '302' => 'Denegada',
            '217' => 'Nao localizada',
            '' => $statusAtual !== '' ? $statusAtual : 'Pendente',
            default => 'Rejeitada',
        };
    }

    /**
     * Localiza o protocolo de autorizacao salvo no XML ou nos metadados.
     */
    private function protocoloAutorizacao(array $documento): string
    {
        foreach (['nprot', 'protocolo', 'xml'] as $campo) {
            $valor = trim((string) ($documento[$campo] ?? ''));

            if ($valor === '') {
                continue;
            }

            if ($campo === 'nprot' && preg_match('/^\d+$/', $valor)) {
                return $valor;
            }

            $tag = $this->extrairTag($valor, 'nProt');

            if ($tag !== '') {
                return $tag;
            }
        }

        return '';
    }

    /**
     * Extrai uma tag simples de um XML sem depender de namespace.
     */
    private function extrairTag(string $xml, string $tag): string
    {
        if ($xml === '') {
            return '';
        }

        return preg_match('/<' . preg_quote($tag, '/') . '>(.*?)<\/' . preg_quote($tag, '/') . '>/s', $xml, $match) === 1
            ? trim($match[1])
            : '';
    }

    /**
     * Converte data fiscal ISO para DATETIME local.
     */
    private function dataFiscal(string $data): ?string
    {
        if ($data === '') {
            return null;
        }

        try {
            return (new \DateTimeImmutable($data))->format('Y-m-d H:i:s');
        } catch (\Throwable $exception) {
            return null;
        }
    }

    /**
     * Evita erro quando a migration ainda nao foi aplicada.
     */
    private function filtrarCamposExistentes(string $tabela, array $dados): array
    {
        $db = db_connect();

        return array_filter(
            $dados,
            fn ($_valor, string $campo): bool => $db->fieldExists($campo, $tabela),
            ARRAY_FILTER_USE_BOTH
        );
    }
}
