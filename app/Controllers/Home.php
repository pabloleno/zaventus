<?php namespace App\Controllers;

class Home extends BaseController
{
	/**
	 * Carrega os dados e exibe a tela principal deste modulo.
	 */
	public function index()
	{
		return redirect()->to('/login');
	}

	//--------------------------------------------------------------------

}
