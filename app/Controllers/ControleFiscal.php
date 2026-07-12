<?php

namespace App\Controllers;

use App\Libraries\SefazFiscalService;
use App\Models\NFCeModel;
use App\Models\NFeModel;
use CodeIgniter\Controller;
use ZipArchive;

class ControleFiscal extends Controller
{
    private NFeModel $nfe_model;
    private NFCeModel $nfce_model;
    private SefazFiscalService $sefaz;

    /**
     * Inicializa as dependencias usadas pela gestao fiscal.
     */
    public function __construct()
    {
        $this->nfe_model = new NFeModel();
        $this->nfce_model = new NFCeModel();
        $this->sefaz = new SefazFiscalService();
    }

    /**
     * Exibe a gestao fiscal consolidada.
     */
    public function index()
    {
        return $this->documentos(null);
    }

    /**
     * Exibe somente NFe.
     */
    public function nfe()
    {
        return $this->documentos(SefazFiscalService::MODELO_NFE);
    }

    /**
     * Exibe somente NFCe.
     */
    public function nfce()
    {
        return $this->documentos(SefazFiscalService::MODELO_NFCE);
    }

    /**
     * Consulta o status do servico autorizador da SEFAZ.
     */
    public function statusServico($modelo)
    {
        try {
            $resultado = $this->sefaz->statusServico((string) $modelo);
            session()->setFlashdata('alert', $resultado['cstat'] === '107' ? 'success_fiscal' : 'warning_fiscal');
            session()->setFlashdata('fiscal_message', $this->mensagemRetorno($resultado, 'Status do servico consultado.'));
        } catch (\Throwable $exception) {
            session()->setFlashdata('alert', 'error_fiscal');
            session()->setFlashdata('fiscal_message', $exception->getMessage());
        }

        return redirect()->back();
    }

    /**
     * Consulta a situacao atual do documento na SEFAZ.
     */
    public function consultar($modelo, $idDocumento)
    {
        try {
            $resultado = $this->sefaz->consultarDocumento((string) $modelo, (int) $idDocumento);
            session()->setFlashdata('alert', $resultado['sucesso'] ? 'success_fiscal' : 'warning_fiscal');
            session()->setFlashdata('fiscal_message', $this->mensagemRetorno($resultado, 'Documento sincronizado com a SEFAZ.'));
        } catch (\Throwable $exception) {
            session()->setFlashdata('alert', 'error_fiscal');
            session()->setFlashdata('fiscal_message', $exception->getMessage());
        }

        return redirect()->back();
    }

    /**
     * Cancela NFe/NFCe autorizada.
     */
    public function cancelar()
    {
        $modelo = (string) $this->request->getPost('modelo');
        $idDocumento = (int) $this->request->getPost('id_documento');
        $justificativa = (string) $this->request->getPost('justificativa');

        try {
            $resultado = $this->sefaz->cancelarDocumento($modelo, $idDocumento, $justificativa);
            session()->setFlashdata('alert', $resultado['sucesso'] ? 'success_fiscal' : 'error_fiscal');
            session()->setFlashdata('fiscal_message', $this->mensagemRetorno($resultado, $resultado['sucesso'] ? 'Cancelamento homologado.' : 'Cancelamento rejeitado.'));
        } catch (\Throwable $exception) {
            session()->setFlashdata('alert', 'error_fiscal');
            session()->setFlashdata('fiscal_message', $exception->getMessage());
        }

        return redirect()->back();
    }

    /**
     * Exibe os detalhes do erro registrado na NFe.
     */
    public function showErroNFe($id_nfe)
    {
        $data = $this->dadosErro('Erro da NFe', '/controleFiscal/nfe');
        $data['nfe'] = $this->nfe_model->where('id_nfe', $id_nfe)->first();

        echo view('templates/header');
        echo view('controle_fiscal/show_erro_nfe', $data);
        echo view('templates/footer');
    }

    /**
     * Mantem compatibilidade com a rota antiga da NFCe.
     */
    public function showErro($id_nfce)
    {
        return $this->showErroNFCe($id_nfce);
    }

    /**
     * Exibe os detalhes do erro registrado na NFCe.
     */
    public function showErroNFCe($id_nfce)
    {
        $data = $this->dadosErro('Erro da NFCe', '/controleFiscal/nfce');
        $data['nfce'] = $this->nfce_model->where('id_nfce', $id_nfce)->first();

        echo view('templates/header');
        echo view('controle_fiscal/show_erro_nfce', $data);
        echo view('templates/footer');
    }

    /**
     * Prepara o download de XML de autorizacao ou cancelamento.
     */
    public function baixaXML($modeloOuId = null, $idDocumento = null, $tipo = 'autorizacao')
    {
        $modelo = $idDocumento === null ? SefazFiscalService::MODELO_NFE : (string) $modeloOuId;
        $id = $idDocumento === null ? (int) $modeloOuId : (int) $idDocumento;

        try {
            $xml = $this->sefaz->xmlDocumento($modelo, $id, (string) $tipo);

            return $this->response->download($xml['nome'], $xml['conteudo']);
        } catch (\Throwable $exception) {
            session()->setFlashdata('alert', 'error_fiscal');
            session()->setFlashdata('fiscal_message', $exception->getMessage());

            return redirect()->back();
        }
    }

    /**
     * Prepara o download de XMLs de NFe autorizadas do periodo.
     */
    public function baixaXMLS($data_inicio, $data_final)
    {
        $nfes = $this->nfe_model
            ->where('status', 'Emitida')
            ->where('data >=', $data_inicio)
            ->where('data <=', $data_final)
            ->findAll();

        $nomeDoArquivo = 'xmls_do_periodo_' . date('d-m-Y', strtotime($data_inicio)) . '_ate_' . date('d-m-Y', strtotime($data_final));
        $pasta = WRITEPATH . 'put_xmls/';

        if (! is_dir($pasta)) {
            mkdir($pasta, 0775, true);
        }

        $zip = new ZipArchive();
        $zip->open($pasta . $nomeDoArquivo . '.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);

        foreach ($nfes as $nfe) {
            $arquivo = $pasta . "{$nfe['chave']}.xml";
            file_put_contents($arquivo, $nfe['xml']);
            $zip->addFile($arquivo, "{$nfe['chave']}.xml");
        }

        $zip->close();

        foreach (glob($pasta . '*.xml') ?: [] as $arquivo) {
            @unlink($arquivo);
        }

        return $this->response->download($pasta . $nomeDoArquivo . '.zip', null);
    }

    /**
     * Monta os dados e exibe a listagem fiscal.
     */
    private function documentos(?string $modeloFiltro)
    {
        $filtros = $this->filtros($modeloFiltro);
        $documentos = [];

        if ($filtros['modelo'] === '' || $filtros['modelo'] === SefazFiscalService::MODELO_NFE) {
            $documentos = array_merge($documentos, $this->documentosPorModelo(SefazFiscalService::MODELO_NFE, $filtros));
        }

        if ($filtros['modelo'] === '' || $filtros['modelo'] === SefazFiscalService::MODELO_NFCE) {
            $documentos = array_merge($documentos, $this->documentosPorModelo(SefazFiscalService::MODELO_NFCE, $filtros));
        }

        usort($documentos, static function (array $a, array $b): int {
            return strcmp($b['ordenacao'], $a['ordenacao']);
        });

        $data = [
            'links' => [
                'menu' => '11.m',
                'item' => '11.0',
                'subItem' => '11.8',
            ],
            'titulo' => [
                'modulo' => 'Gestao Fiscal',
                'icone' => 'fas fa-file-invoice',
            ],
            'caminhos' => [
                ['titulo' => 'Inicio', 'rota' => '/inicio', 'active' => false],
                ['titulo' => 'Configs', 'rota' => '/configs/sistema', 'active' => false],
                ['titulo' => 'Gestao Fiscal', 'rota' => '', 'active' => true],
            ],
            'documentos' => $documentos,
            'filtros' => $filtros,
            'resumo' => $this->resumo($documentos),
        ];

        echo view('templates/header');
        echo view('controle_fiscal/documentos', $data);
        echo view('templates/footer');
    }

    /**
     * Consulta documentos de uma tabela fiscal e normaliza para a view.
     */
    private function documentosPorModelo(string $modelo, array $filtros): array
    {
        $model = $modelo === SefazFiscalService::MODELO_NFCE ? $this->nfce_model : $this->nfe_model;
        $idCampo = $modelo === SefazFiscalService::MODELO_NFCE ? 'id_nfce' : 'id_nfe';
        $query = $model->orderBy($idCampo, 'DESC');

        if ($filtros['data_inicio'] !== '') {
            $query->where('data >=', $filtros['data_inicio']);
        }

        if ($filtros['data_final'] !== '') {
            $query->where('data <=', $filtros['data_final']);
        }

        if ($filtros['status'] !== '') {
            $query->where('status', $filtros['status']);
        }

        if ($filtros['chave'] !== '') {
            $query->like('chave', $filtros['chave']);
        }

        return array_map(static function (array $documento) use ($modelo, $idCampo): array {
            $status = (string) ($documento['status'] ?? 'Pendente');
            $dataHora = trim((string) ($documento['data'] ?? '') . ' ' . (string) ($documento['hora'] ?? ''));
            $metadadosChave = SefazFiscalService::metadadosDaChave((string) ($documento['chave'] ?? ''));

            if (trim((string) ($documento['serie'] ?? '')) === '' && isset($metadadosChave['serie'])) {
                $documento['serie'] = $metadadosChave['serie'];
            }

            if (trim((string) ($documento['numero'] ?? '')) === '' && isset($metadadosChave['numero'])) {
                $documento['numero'] = $metadadosChave['numero'];
            }

            return $documento + [
                'modelo' => $modelo,
                'modelo_nome' => SefazFiscalService::nomeModelo($modelo),
                'id_documento' => (int) $documento[$idCampo],
                'status_classe' => SefazFiscalService::classeStatus($status),
                'ambiente_rotulo' => (int) ($documento['ambiente'] ?? 0) === 1 ? 'Producao' : 'Homologacao',
                'ordenacao' => $dataHora !== '' ? $dataHora : (string) ($documento['created_at'] ?? ''),
            ];
        }, $query->findAll());
    }

    /**
     * Le os filtros da requisicao.
     */
    private function filtros(?string $modeloFiltro): array
    {
        $dados = $this->request->getGet();
        $modelo = $modeloFiltro ?? trim((string) ($dados['modelo'] ?? ''));

        if (! in_array($modelo, [SefazFiscalService::MODELO_NFE, SefazFiscalService::MODELO_NFCE], true)) {
            $modelo = '';
        }

        return [
            'modelo' => $modelo,
            'status' => trim((string) ($dados['status'] ?? '')),
            'chave' => trim((string) ($dados['chave'] ?? '')),
            'data_inicio' => trim((string) ($dados['data_inicio'] ?? '')),
            'data_final' => trim((string) ($dados['data_final'] ?? '')),
        ];
    }

    /**
     * Resume os documentos exibidos.
     */
    private function resumo(array $documentos): array
    {
        $resumo = [
            'total' => count($documentos),
            'emitidas' => 0,
            'canceladas' => 0,
            'pendentes' => 0,
            'problemas' => 0,
        ];

        foreach ($documentos as $documento) {
            $status = (string) ($documento['status'] ?? '');

            if ($status === 'Emitida') {
                $resumo['emitidas']++;
            } elseif ($status === 'Cancelada') {
                $resumo['canceladas']++;
            } elseif (in_array($status, ['Nao Emitida', 'Pendente'], true)) {
                $resumo['pendentes']++;
            } else {
                $resumo['problemas']++;
            }
        }

        return $resumo;
    }

    /**
     * Dados comuns das telas de erro.
     */
    private function dadosErro(string $titulo, string $voltar): array
    {
        return [
            'links' => [
                'menu' => '11.m',
                'item' => '11.0',
                'subItem' => '11.8',
            ],
            'titulo' => [
                'modulo' => $titulo,
                'icone' => 'fa fa-database',
            ],
            'caminhos' => [
                ['titulo' => 'Inicio', 'rota' => '/inicio', 'active' => false],
                ['titulo' => 'Gestao Fiscal', 'rota' => $voltar, 'active' => false],
                ['titulo' => 'Erro', 'rota' => '', 'active' => true],
            ],
        ];
    }

    /**
     * Monta uma mensagem curta para retorno de operacoes fiscais.
     */
    private function mensagemRetorno(array $resultado, string $padrao): string
    {
        $cStat = trim((string) ($resultado['cstat'] ?? ''));
        $xMotivo = trim((string) ($resultado['xmotivo'] ?? ''));

        if ($cStat === '' && $xMotivo === '') {
            return $padrao;
        }

        return trim($padrao . ' ' . ($cStat !== '' ? '[' . $cStat . '] ' : '') . $xMotivo);
    }
}
