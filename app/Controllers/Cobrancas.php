<?php

namespace App\Controllers;

use App\Libraries\CobrancaRecorrente;
use App\Libraries\Moeda;
use App\Models\ClienteModel;
use App\Models\CobrancaModel;
use App\Models\CobrancaOcorrenciaModel;
use CodeIgniter\Controller;
use DateTimeImmutable;

class Cobrancas extends Controller
{
    private array $links;
    private CobrancaModel $cobrancas;
    private CobrancaOcorrenciaModel $ocorrencias;
    private ClienteModel $clientes;
    private CobrancaRecorrente $recorrencia;

    /**
     * Inicializa os componentes do modulo independente de cobrancas.
     */
    public function __construct()
    {
        $this->links = ['menu' => '5.m', 'item' => '5.0', 'subItem' => '5.8'];
        $this->cobrancas = new CobrancaModel();
        $this->ocorrencias = new CobrancaOcorrenciaModel();
        $this->clientes = new ClienteModel();
        $this->recorrencia = new CobrancaRecorrente();
    }

    /**
     * Exibe os cadastros e a agenda de cobrancas pendentes.
     */
    public function index()
    {
        $cobrancas = $this->cobrancas
            ->select('cobrancas.*, clientes.nome, clientes.razao_social')
            ->join('clientes', 'clientes.id_cliente = cobrancas.id_cliente', 'left')
            ->orderBy('cobrancas.id_cobranca', 'DESC')
            ->findAll();
        $pendencias = $this->ocorrencias
            ->select('cobranca_ocorrencias.*, cobrancas.titulo, cobrancas.quantidade_parcelas, cobrancas.juros_atraso, cobrancas.juros_percentual, clientes.nome, clientes.razao_social, clientes.whatsapp, clientes.celular')
            ->join('cobrancas', 'cobrancas.id_cobranca = cobranca_ocorrencias.id_cobranca AND cobrancas.deleted_at IS NULL')
            ->join('clientes', 'clientes.id_cliente = cobrancas.id_cliente', 'left')
            ->where('cobranca_ocorrencias.status', 'Pendente')
            ->where('cobrancas.status', 'Ativa')
            ->orderBy('cobranca_ocorrencias.vencimento', 'ASC')
            ->findAll();
        $proximas = [];

        foreach ($pendencias as $pendencia) {
            $idCobranca = (int) $pendencia['id_cobranca'];
            $proximas[$idCobranca] ??= $pendencia;
        }

        $data = $this->dadosBase('Cobrancas', 'fas fa-bell');
        $data['cobrancas'] = $cobrancas;
        $data['pendencias'] = $pendencias;
        $data['proximas'] = $proximas;
        $data['resumos_parcelas'] = $this->recorrencia->resumosParcelas(array_map(static fn (array $cobranca): int => (int) $cobranca['id_cobranca'], $cobrancas));

        echo view('templates/header');
        echo view('cobrancas/index', $data);
        echo view('templates/footer');
    }

    /**
     * Exibe o formulario para cadastrar uma cobranca.
     */
    public function create()
    {
        $data = $this->dadosBase('Nova Cobranca', 'fas fa-plus-circle');
        $data['clientes'] = $this->clientes->orderBy('nome', 'ASC')->findAll();
        $data['recorrencias'] = CobrancaRecorrente::opcoesRecorrencia();

        echo view('templates/header');
        echo view('cobrancas/form', $data);
        echo view('templates/footer');
    }

    /**
     * Exibe o formulario para editar uma cobranca existente.
     */
    public function edit($idCobranca)
    {
        $cobranca = $this->cobrancas->find($idCobranca);

        if (empty($cobranca)) {
            return redirect()->to('/cobrancas');
        }

        $data = $this->dadosBase('Editar Cobranca', 'fas fa-edit');
        $data['cobranca'] = $cobranca;
        $data['clientes'] = $this->clientes->orderBy('nome', 'ASC')->findAll();
        $data['recorrencias'] = CobrancaRecorrente::opcoesRecorrencia();

        echo view('templates/header');
        echo view('cobrancas/form', $data);
        echo view('templates/footer');
    }

    /**
     * Valida e salva uma cobranca sem gerar movimentacao financeira.
     */
    public function store()
    {
        $dados = $this->prepararDados($this->request->getPost());
        $erros = $this->validarDados($dados);

        if (! empty($erros)) {
            session()->setFlashdata('errors', $erros);

            return redirect()->back()->withInput();
        }

        $db = db_connect();
        $db->transStart();
        $editando = ! empty($dados['id_cobranca']);
        $this->cobrancas->save($dados);
        $idCobranca = $editando ? (int) $dados['id_cobranca'] : (int) $this->cobrancas->getInsertID();
        $cobranca = $this->cobrancas->find($idCobranca);
        $this->recorrencia->sincronizar($cobranca);
        $db->transComplete();

        session()->setFlashdata('alert', $editando ? 'success_edit' : 'success_create');

        return redirect()->to('/cobrancas');
    }

    /**
     * Marca uma ocorrencia como realizada apenas dentro da agenda de alertas.
     */
    public function concluir($idOcorrencia)
    {
        $ocorrencia = $this->ocorrencias->find($idOcorrencia);

        if (! empty($ocorrencia)) {
            $this->ocorrencias->save([
                'id_ocorrencia' => $idOcorrencia,
                'status' => 'Realizada',
                'concluida_em' => date('Y-m-d H:i:s'),
            ]);

            if ($this->ocorrencias->where('id_cobranca', $ocorrencia['id_cobranca'])->where('status', 'Pendente')->countAllResults() === 0) {
                $this->cobrancas->save(['id_cobranca' => $ocorrencia['id_cobranca'], 'status' => 'Concluida']);
            }
        }

        session()->setFlashdata('alert', 'success_complete');

        return redirect()->to('/cobrancas');
    }

    /**
     * Entrega ao topo do sistema os alertas que permanecem ate serem concluidos.
     */
    public function alertasNavbar()
    {
        $alertas = array_map(static function (array $alerta): array {
            return [
                'id_ocorrencia' => (int) $alerta['id_ocorrencia'],
                'titulo' => (string) $alerta['titulo'],
                'cliente' => (string) $alerta['cliente'],
                'numero_parcela' => (int) $alerta['numero_parcela'],
                'total_parcelas' => (int) ($alerta['total_parcelas'] ?? $alerta['numero_parcela']),
                'parcelas_realizadas' => (int) ($alerta['parcelas_realizadas'] ?? 0),
                'parcelas_restantes' => (int) ($alerta['parcelas_restantes'] ?? 1),
                'rotulo_parcela' => (string) ($alerta['rotulo_parcela'] ?? $alerta['numero_parcela']),
                'vencimento' => (string) $alerta['vencimento'],
                'valor_com_juros' => (float) $alerta['valor_com_juros'],
                'status_alerta' => (string) $alerta['status_alerta'],
            ];
        }, $this->recorrencia->alertasExigiveis());

        return $this->response->setJSON([
            'total' => count($alertas),
            'alertas' => $alertas,
        ]);
    }

    /**
     * Estende uma cobranca recorrente acrescentando uma nova parcela.
     */
    public function estender($idCobranca)
    {
        $sucesso = $this->recorrencia->estender((int) $idCobranca);
        session()->setFlashdata('alert', $sucesso ? 'success_extend' : 'error_extend');

        return redirect()->to('/cobrancas');
    }

    /**
     * Exclui a cobranca e seus lembretes sem afetar outros modulos.
     */
    public function delete($idCobranca)
    {
        $this->ocorrencias->where('id_cobranca', $idCobranca)->delete();
        $this->cobrancas->delete($idCobranca);
        session()->setFlashdata('alert', 'success_delete');

        return redirect()->to('/cobrancas');
    }

    /**
     * Normaliza valores e opcoes recebidas pelo formulario.
     */
    private function prepararDados(array $dados): array
    {
        $recorrencias = array_keys(CobrancaRecorrente::opcoesRecorrencia());
        $dados['titulo'] = trim((string) ($dados['titulo'] ?? ''));
        $dados['descricao'] = trim((string) ($dados['descricao'] ?? ''));
        $dados['observacoes'] = trim((string) ($dados['observacoes'] ?? ''));
        $dados['valor_total'] = Moeda::normalizar($dados['valor_total'] ?? 0);
        $dados['quantidade_parcelas'] = min(999, max(1, (int) ($dados['quantidade_parcelas'] ?? 1)));
        $dados['recorrencia'] = in_array($dados['recorrencia'] ?? '', $recorrencias, true)
            ? $dados['recorrencia']
            : CobrancaRecorrente::UNICA;
        $dados['intervalo_personalizado_dias'] = $dados['recorrencia'] === CobrancaRecorrente::PERSONALIZADA
            ? min(36500, max(1, (int) ($dados['intervalo_personalizado_dias'] ?? 1)))
            : null;
        $dados['quantidade_parcelas'] = $dados['recorrencia'] === CobrancaRecorrente::UNICA
            ? 1
            : $dados['quantidade_parcelas'];
        $dados['juros_atraso'] = (string) ($dados['juros_atraso'] ?? '0') === '1' ? 1 : 0;
        $dados['juros_percentual'] = $dados['juros_atraso'] ? min(9999.9999, max(0, (float) str_replace(',', '.', (string) ($dados['juros_percentual'] ?? 0)))) : 0;
        $dados['lembrete_1_hora'] = (string) ($dados['lembrete_1_hora'] ?? '0') === '1' ? 1 : 0;
        $dados['lembrete_1_dia'] = (string) ($dados['lembrete_1_dia'] ?? '0') === '1' ? 1 : 0;
        $dados['lembrete_1_semana'] = (string) ($dados['lembrete_1_semana'] ?? '0') === '1' ? 1 : 0;
        $dados['status'] = in_array($dados['status'] ?? 'Ativa', ['Ativa', 'Pausada'], true) ? $dados['status'] : 'Ativa';

        return $dados;
    }

    /**
     * Valida os campos essenciais para montar a agenda.
     */
    private function validarDados(array $dados): array
    {
        $erros = [];
        $dataInformada = (string) ($dados['data_inicio'] ?? '');
        $data = DateTimeImmutable::createFromFormat('!Y-m-d', $dataInformada);
        $hora = substr((string) ($dados['hora_cobranca'] ?? ''), 0, 5);
        $horaValida = preg_match('/^(\d{2}):(\d{2})$/', $hora, $partesHora) === 1
            && (int) $partesHora[1] <= 23
            && (int) $partesHora[2] <= 59;

        if (empty($dados['id_cliente']) || empty($this->clientes->find($dados['id_cliente']))) {
            $erros[] = 'Selecione um cliente valido.';
        }
        if ($dados['titulo'] === '') {
            $erros[] = 'Informe o titulo da cobranca.';
        } elseif (mb_strlen($dados['titulo']) > 120) {
            $erros[] = 'O titulo da cobranca deve ter no maximo 120 caracteres.';
        }
        if ((float) $dados['valor_total'] <= 0) {
            $erros[] = 'Informe um valor total maior que zero.';
        }
        if (! $data || $data->format('Y-m-d') !== $dataInformada || $dataInformada < '1900-01-01' || $dataInformada > '2100-12-31') {
            $erros[] = 'Informe uma data inicial valida.';
        }
        if (! $horaValida) {
            $erros[] = 'Informe um horario valido para a cobranca.';
        }
        if (! empty($dados['id_cobranca'])) {
            if (empty($this->cobrancas->find($dados['id_cobranca']))) {
                $erros[] = 'A cobranca informada nao existe.';
            }

            $ultimaRealizada = $this->ocorrencias
                ->selectMax('numero_parcela')
                ->where('id_cobranca', $dados['id_cobranca'])
                ->where('status', 'Realizada')
                ->first();

            if ((int) ($ultimaRealizada['numero_parcela'] ?? 0) > (int) $dados['quantidade_parcelas']) {
                $erros[] = 'A quantidade de parcelas nao pode excluir uma parcela ja realizada.';
            }
        }

        return $erros;
    }

    /**
     * Monta titulos e navegacao compartilhados pelas telas.
     */
    private function dadosBase(string $modulo, string $icone): array
    {
        return [
            'links' => $this->links,
            'titulo' => ['modulo' => $modulo, 'icone' => $icone],
            'caminhos' => [
                ['titulo' => 'Inicio', 'rota' => '/inicio', 'active' => false],
                ['titulo' => 'Cobrancas', 'rota' => '/cobrancas', 'active' => $modulo === 'Cobrancas'],
                ['titulo' => 'Dados', 'rota' => '', 'active' => $modulo !== 'Cobrancas'],
            ],
        ];
    }
}
