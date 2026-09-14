<?php

namespace App\Controllers;

use App\Libraries\ImagemCadastro;
use App\Libraries\OrcamentoCalculo;
use App\Models\ServicoMaoDeObraModel;
use CodeIgniter\Controller;
use CodeIgniter\Exceptions\PageNotFoundException;
use InvalidArgumentException;
use RuntimeException;
use Throwable;

class ServicosMaoDeObra extends Controller
{
    private $links;
    private $servico_mao_de_obra_model;

    /**
     * Inicializa as dependencias usadas por este componente.
     */
    function __construct()
    {
        $this->links = [
            'menu' => '3.m',
            'item' => '3.0',
            'subItem' => '3.6'
        ];

        $this->servico_mao_de_obra_model = new ServicoMaoDeObraModel();
    }

    /**
     * Carrega os dados e exibe a tela principal deste modulo.
     */
    public function index()
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Catálogo de serviços',
            'icone'  => 'fa fa-users'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Serviços/Mão de Obra", 'rota'   => "", 'active' => true]
        ];

        $data['servicos'] = $this->servico_mao_de_obra_model->findAll();

        echo view('templates/header');
        echo view('servicos_mao_de_obra/index', $data);
        echo view('templates/footer');
    }

    /**
     * Prepara os dados e exibe o formulario de cadastro.
     */
    public function create()
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Novo Serviço/Mão de Obra',
            'icone'  => 'fa fa-user-plus'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Serviços/Mão de Obra", 'rota'   => "/servicosMaoDeObra", 'active' => false],
            ['titulo' => "Novo", 'rota'   => "", 'active' => true]
        ];

        echo view('templates/header');
        echo view('servicos_mao_de_obra/form', $data);
        echo view('templates/footer');
    }

    /**
     * Carrega o registro solicitado e exibe o formulario de edicao.
     */
    public function edit($id_servico)
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Editar Serviço/Mão de Obra',
            'icone'  => 'fa fa-edit'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Serviços/Mão de Obra", 'rota'   => "/servicosMaoDeObra", 'active' => false],
            ['titulo' => "Editar", 'rota'   => "", 'active' => true]
        ];

        $data['servico'] = $this->servico_mao_de_obra_model->where('id_servico', $id_servico)->first();

        if (empty($data['servico'])) {
            throw PageNotFoundException::forPageNotFound('Serviço não encontrado.');
        }

        echo view('templates/header');
        echo view('servicos_mao_de_obra/form', $data);
        echo view('templates/footer');
    }

    /**
     * Valida e persiste os dados enviados pelo formulario.
     */
    public function store()
    {
        $entrada = $this->request->getPost();
        $imagemNova = null;
        $anterior = null;

        try {
            $id = $entrada['id_servico'] ?? '';
            if ($id !== '') {
                if (! is_scalar($id) || preg_match('/^[1-9]\d*$/D', (string) $id) !== 1) {
                    throw new InvalidArgumentException('O serviço informado é inválido.');
                }

                $anterior = $this->servico_mao_de_obra_model->find($id);
                if (empty($anterior)) {
                    throw new InvalidArgumentException('O serviço não foi encontrado.');
                }
            }

            $dados = $this->dadosValidados($entrada);
            if ($anterior !== null) {
                $dados['id_servico'] = $anterior['id_servico'];
            }

            $imagemNova = ImagemCadastro::salvar($this->request->getFile('imagem'), 'servicos');
            $dados['imagem'] = $imagemNova ?? (($entrada['remover_imagem'] ?? '0') === '1' ? null : ($anterior['imagem'] ?? null));

            if (! $this->servico_mao_de_obra_model->save($dados)) {
                throw new RuntimeException('Não foi possível salvar o serviço.');
            }
        } catch (InvalidArgumentException $exception) {
            ImagemCadastro::remover($imagemNova);
            return redirect()->back()->withInput()->with('erros_catalogo', [$exception->getMessage()]);
        } catch (Throwable $exception) {
            ImagemCadastro::remover($imagemNova);
            log_message('error', 'Falha ao salvar serviço do catálogo: ' . $exception->getMessage());
            return redirect()->back()->withInput()->with('erros_catalogo', ['Não foi possível salvar o serviço. Tente novamente.']);
        }

        if (($anterior['imagem'] ?? null) !== $dados['imagem']) {
            ImagemCadastro::remover($anterior['imagem'] ?? null);
        }

        return redirect()->to('/servicosMaoDeObra')->with('alert', $anterior === null ? 'success_create' : 'success_edit');
    }

    /**
     * Inativa o serviço preservando os atendimentos existentes.
     */
    public function delete($id_servico)
    {
        if (empty($this->servico_mao_de_obra_model->find($id_servico))) {
            throw PageNotFoundException::forPageNotFound('Serviço não encontrado.');
        }

        if (! $this->servico_mao_de_obra_model->update($id_servico, ['ativo' => 0])) {
            return redirect()->to('/servicosMaoDeObra')->with('erros_catalogo', ['Não foi possível inativar o serviço.']);
        }

        return redirect()->to('/servicosMaoDeObra')->with('alert', 'success_inactivate');
    }

    private function dadosValidados(array $entrada): array
    {
        $dados = [];
        foreach (['nome' => [128, true], 'descricao' => [1024, false], 'observacoes' => [2048, false], 'unidade' => [16, true]] as $campo => [$limite, $obrigatorio]) {
            $valor = $entrada[$campo] ?? '';
            if (! is_string($valor)) {
                throw new InvalidArgumentException('Informe um texto válido para ' . $campo . '.');
            }

            $valor = trim($valor);
            if (($obrigatorio && $valor === '') || mb_strlen($valor) > $limite) {
                throw new InvalidArgumentException(ucfirst($campo) . ($valor === '' ? ' é obrigatório.' : ' deve ter no máximo ' . $limite . ' caracteres.'));
            }
            $dados[$campo] = $valor;
        }

        $opcoes = [
            'tipo_preco' => ['fixo', 'unidade', 'metro_linear', 'metro_quadrado', 'quantidade'],
            'unidade_dimensao' => ['m', 'cm', 'mm'],
            'tipo_execucao' => ['interna', 'externa', 'mista'],
            'arte_padrao' => ['nao_necessita', 'cliente', 'grafica'],
            'necessita_instalacao' => ['0', '1'],
            'ativo' => ['0', '1'],
            'possui_medidas' => ['0', '1'],
            'remover_imagem' => ['0', '1'],
        ];

        foreach ($opcoes as $campo => $permitidos) {
            $valor = $entrada[$campo] ?? (in_array($campo, ['necessita_instalacao', 'possui_medidas', 'remover_imagem'], true) ? '0' : null);
            if (! in_array($valor, $permitidos, true)) {
                throw new InvalidArgumentException('Selecione uma opção válida para ' . str_replace('_', ' ', $campo) . '.');
            }
            if (! in_array($campo, ['possui_medidas', 'remover_imagem'], true)) {
                $dados[$campo] = $valor;
            }
        }

        try {
            $dados['valor'] = OrcamentoCalculo::decimal($entrada['valor'] ?? '', 2);
        } catch (InvalidArgumentException $exception) {
            throw new InvalidArgumentException('Preço: ' . $exception->getMessage());
        }

        foreach (['largura_padrao', 'altura_padrao'] as $campo) {
            $valor = $entrada[$campo] ?? '';
            $dados[$campo] = null;
            if (($entrada['possui_medidas'] ?? '0') === '1' && $valor !== '') {
                try {
                    $dados[$campo] = OrcamentoCalculo::decimal($valor);
                } catch (InvalidArgumentException $exception) {
                    throw new InvalidArgumentException('Medidas padrão: ' . $exception->getMessage());
                }
                if (bccomp($dados[$campo], '0', 4) <= 0 || bccomp($dados[$campo], '99999999.9999', 4) > 0) {
                    throw new InvalidArgumentException('As medidas padrão devem ser maiores que zero e de até 99999999,9999.');
                }
            }
        }

        return $dados;
    }
}
