<?php

namespace App\Controllers;

use App\Models\NFCeModel;
use App\Models\NFeModel;
use CodeIgniter\Controller;
use ZipArchive;

class ControleFiscal extends Controller
{
    private $nfce_model;
    private $nfe_model;

    function __construct()
    {
        $this->nfce_model = new NFCeModel();
        $this->nfe_model = new NFeModel();
    }

    public function nfce()
    {
        $data['links'] = [
            'menu' => '5.m',
            'item' => '5.0',
            'subItem' => '5.13'
        ];

        $data['titulo'] = [
            'modulo' => 'Controle Fiscal',
            'icone'  => 'fa fa-database'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "NFCEs", 'rota'   => "", 'active' => true]
        ];

        $data['nfces'] = $this->nfce_model->findAll();

        echo view('templates/header');
        echo view('controle_fiscal/xmls', $data);
        echo view('templates/footer');
    }

    public function nfe()
    {
        $data['links'] = [
            'menu' => '5.m',
            'item' => '5.0',
            'subItem' => '5.12'
        ];

        $data['titulo'] = [
            'modulo' => 'Controle Fiscal',
            'icone'  => 'fa fa-database'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "NFEs", 'rota'   => "", 'active' => true]
        ];

        // ---------------------------------------- FILTRAR ----------------------------------------- //
        $dados = $this->request->getvar();

        $session = session();

        if(!empty($dados))
        {
            $data_inicio = $dados['data_inicio'];
            $data_final = $dados['data_final'];

            
            if($dados['data_inicio'] != "" && $dados['data_final'] != "") // Filtra pela data inicial e data final
            {
                $nfes = $this->nfe_model->where('data >=', $data_inicio)->where('data <=', $data_final)->find();
                
                $data['data_inicio'] = $data_inicio;
                $data['data_final'] = $data_final;
            }
            else // Caso desfaça todos os filtros sem clicar no botão REMOVER FILTROS mostra os 5 últimas contas cadastrados
            {
                $nfes = $this->nfe_model->orderBy('id_nfe', 'DESC')->limit(5)->find();
                $data['ultimos_cinco'] = TRUE;
            }

            $session->setFlashdata('alert', 'success_filter');
        }
        else
        {
            $nfes = $this->nfe_model->orderBy('id_nfe', 'DESC')->limit(5)->find();
            $data['ultimos_cinco'] = TRUE;
        }
        // ------------------------------------------------------------------------------------------ //

        $data['nfes'] = $nfes;

        echo view('templates/header');
        echo view('controle_fiscal/nfe', $data);
        echo view('templates/footer');
    }

    public function showErroNFe($id_nfe)
    {
        $data['links'] = [
            'menu' => '7.m',
            'item' => '7.0',
            'subItem' => '7.2'
        ];

        $data['titulo'] = [
            'modulo' => 'Erro da NFe',
            'icone'  => 'fa fa-database'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "NFEs", 'rota' => "/controleFiscal/nfe", 'active' => false],
            ['titulo' => "Erro", 'rota'   => "", 'active' => true]
        ];

        $data['nfe'] = $this->nfe_model->where('id_nfe', $id_nfe)->first();

        echo view('templates/header');
        echo view('controle_fiscal/show_erro_nfe', $data);
        echo view('templates/footer');
    }

    public function showErroNFCe($id_nfce)
    {
        $data['links'] = [
            'menu' => '7.m',
            'item' => '7.0',
            'subItem' => '7.2'
        ];

        $data['titulo'] = [
            'modulo' => 'Erro da NFCe',
            'icone'  => 'fa fa-database'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "NFEs", 'rota' => "/controleFiscal/nfce", 'active' => false],
            ['titulo' => "Erro", 'rota'   => "", 'active' => true]
        ];

        $data['nfce'] = $this->nfce_model->where('id_nfce', $id_nfce)->first();

        echo view('templates/header');
        echo view('controle_fiscal/show_erro_nfce', $data);
        echo view('templates/footer');
    }

    public function baixaXML($id_nfe)
    {
        $nfe = $this->nfe_model->where('id_nfe', $id_nfe)->first();

        $name = "{$nfe['chave']}.xml";
        $data = $nfe['xml'];

        return $this->response->download($name, $data);
    }

    public function baixaXMLS($data_inicio, $data_final)
    {
        $nfes = $this->nfe_model->where('status', "Emitida")->where('data >=', $data_inicio)->where('data <=', $data_final)->findAll();

        // ------------------------------------- GERA O ARQUIVO ZIP COM O XML ----------------------------------------------- //
        $nome_do_arquivo_download = "xmls_do_periodo_".date('d-m-Y', strtotime($data_inicio))."_ate_".date('d-m-Y', strtotime($data_final));
        
        // Inicia a instância da classe ZipArchive
        $zip = new ZipArchive;

        // Cria um novo arquivo .zip chamado minhas_fotos.zip
        $zip->open(WRITEPATH . "/put_xmls/" . $nome_do_arquivo_download . ".zip", ZipArchive::CREATE);

        // Adiciona os arquivos à pasta
        foreach($nfes as $nfe)
        {
            //Gera o arquivo
            file_put_contents(WRITEPATH . "/put_xmls/" . "{$nfe['chave']}.xml", $nfe['xml']);

            $zip->addFile(
                // Caminho do arquivo original
                WRITEPATH . "/put_xmls/" . "{$nfe['chave']}.xml",
                // Novo nome do arquivo
                "{$nfe['chave']}.xml"
            );
        }

        // Fecha a pasta e salva o arquivo
        $zip->close();
        // ---------------------------------------------------------------------------------------------------------------------- //


        // ------------------------------------- APAGA TODOS OS ARQUIVOS TEMPORARIO DA PASTA ------------------------------------ //
        $pasta = WRITEPATH . "/put_xmls/";

        if(is_dir($pasta))
        {
            $diretorio = dir($pasta);

            while($arquivo = $diretorio->read())
            {
                if(($arquivo != '.') && ($arquivo != '..'))
                {
                    if($arquivo != $nome_do_arquivo_download.".zip") // Não deixa apagar o arquivo zip que foi criado
                    {
                        unlink($pasta . $arquivo);
                        echo 'Arquivo ' . $arquivo . ' foi apagado com sucesso. <br />';
                    }
                }
            }

            $diretorio->close();
        }
        else
        {
            echo 'A pasta não existe.';
        }
        // ----------------------------------------------------------------------------------------------------------------------- //


        // ------------------------------------------------ BAIXA O ARQUIVO ZIP --------------------------------------------------//
        return $this->response->download(WRITEPATH . "/put_xmls/" . $nome_do_arquivo_download . ".zip", NULL);
        // ----------------------------------------------------------------------------------------------------------------------- //
    }
}
