<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\NFeModel;
use App\Models\NFCeModel;

use NFePHP\DA\NFe\Danfe;
use NFePHP\DA\NFe\Danfce;

class ImprimeDanfe extends Controller
{
    private $nfe_model;
    private $nfce_model;

    function __construct()
    {
        require_once APPPATH."ThirdParty/sped-da/vendor/autoload.php";

        $this->nfe_model = new NFeModel();
        $this->nfce_model = new NFCeModel();
    }

    public function index($id_nfe, $tipo)
    {
        // $logo = 'data://text/plain;base64,'. base64_encode(file_get_contents(__DIR__ . '/../images/logo.jpg'));

        try {
            if($tipo == 1)
            {
                $xml = $this->nfe_model->where('id_nfe', $id_nfe)->first()['xml'];
                $danfe = new Danfe($xml);
            }
            else
            {
                $xml = $this->nfce_model->where('id_nfce', $id_nfe)->first()['xml'];
                $danfe = new Danfce($xml);
            }

            $danfe->debugMode(false);
            $danfe->creditsIntegratorFooter('Nx Sistemas - http://nxsistemas.com.br');
            // $danfe->monta($logo);
            $pdf = $danfe->render();
            //o pdf porde ser exibido como view no browser
            //salvo em arquivo
            //ou setado para download forçado no browser 
            //ou ainda gravado na base de dados
            header('Content-Type: application/pdf');
            echo $pdf;
            
            $pdf->stream();
        } catch (InvalidArgumentException $e) {
            echo "Ocorreu um erro durante o processamento :"; //$e->getMessage();
        }   
    }
}
