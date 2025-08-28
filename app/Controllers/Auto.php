<?php namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\M_user;
use App\Models\Firstname_Model;

class Auto extends Controller
{
    public function index()
    {    
        $session = session();
        return view('linBMD2/auto');
    }
    
	public function search()
	{
		$session = session();
		$model = new Firstname_Model();
		$request = \Config\Services::request();
		$id = $request->getPostGet('term');
		$user = $model->like('Firstname', $id)->findAll();
		$w = array();
		foreach($user as $rt):
			$w[] = [
				"label" => $rt['Firstname'],
				"email" => $rt['Firstname'],
			];
			
		endforeach; 
		echo json_encode($w);

		
	}		
}
