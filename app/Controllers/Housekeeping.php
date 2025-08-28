<?php namespace App\Controllers;

use App\Models\Districts_Model;
use App\Models\Parameter_Model;
use App\Models\Volumes_Model;
use App\Models\Firstname_Model;
use App\Models\Surname_Model;
use App\Models\Detail_Data_Model;
use App\Models\Header_Model;
use App\Models\Header_Table_Details_Model;
use App\Models\Allocation_Model;
use App\Models\Table_Details_Model;

class Housekeeping extends BaseController
{
	function __construct() 
	{
        helper('common');
        helper('backup');
        helper('remote');
    }
	
	public function index($start_message)
	{
		// From the CI 4 manual,
		// When a page is loaded, the session class will check to see if a valid session cookie is sent by the user’s browser. If a session's cookie does not exist (or if it doesn’t match one stored on the server or has expired) a new session will be created and saved.
		$session = session();
		
		// So if the login time out doesn't exist, it must mean that the session had expired.
		if ( ! isset($session->login_time_stamp) )
			{
				$session->set('session_expired', 1);
				return redirect()->to( base_url('/') );
			}
		
		// intialise		
		switch ($start_message) 
			{
				case 0:
					// message defaults
					$session->set('message_1', 'Choose the Housekeeping action you wish to perform.' );
					$session->set('message_class_1', 'alert alert-primary');
					$session->set('message_2', '');
					$session->set('message_class_2', '');
					break;
				case 1:
					break;
				case 2:
					$session->set('message_1', 'Choose Housekeeping action you want to perform.' );
					$session->set('message_class_1', 'alert alert-primary');
					break;
				default:
			}
		
		// show views
		echo view('templates/header');
		echo view('linBMD2/housekeeping_menu');
		echo view('templates/footer');
	}
	
	public function firstnames()
	{
		// initialise
		$session = session();
		$firstname_model = new Firstname_Model();
		// get firstnames
		$session->set('names', $firstname_model->select('Firstname AS name')
																			->select('Firstname_popularity AS popularity')
																			->orderby('popularity', 'DESC')
																			->findAll());
		// show views
		$session->set('message_1', 'First names listed in descending order by popularity');
		$session->set('message_class_1', 'alert alert-primary');
		echo view('templates/header');
		echo view('linBMD2/show_names');
		echo view('templates/footer');
	}
	
	public function surnames()
	{
		// initialise
		$session = session();
		$surname_model = new Surname_Model();
		// get surnames
		$session->set('names', $surname_model->select('Surname AS name')
																			->select('Surname_popularity AS popularity')
																			->orderby('popularity', 'DESC')
																			->findAll());
		// show views
		$session->set('message_1', 'Family names listed in descending order by popularity');
		$session->set('message_class_1', 'alert alert-primary');
		echo view('templates/header');
		echo view('linBMD2/show_names');
		echo view('templates/footer');
	}
	
	public function admin_user_step1($start_message)
	{		
		// initialise method
		$session = session();
		
		// this function is work in progress
		$session->set('message_2', 'Admin user - This function is work-in-progress and is not available at this time');
		$session->set('message_class_2', 'alert alert-warning');
		//return redirect()->to( base_url('housekeeping/index/2') );

		// set values
		switch ($start_message) 
			{
				case 0:
					// initialise values
					$session->set('admin-user', '');
					// message defaults
					$session->set('message_1', 'Give webBMD admin rights to a webBMD user.');
					$session->set('message_class_1', 'alert alert-primary');
					$session->set('message_2', '');
					$session->set('message_class_2', '');
					break;
				case 1:
					break;
				case 2:
					$session->set('message_1', 'Give webBMD admin rights to a webBMD user.');
					$session->set('message_class_1', 'alert alert-primary');
					break;
				default:
			}	
	
		echo view('templates/header');
		echo view('linBMD2/admin_user');
		echo view('templates/footer');
	}
	
	public function merge_names()
	{		
		// initialise method
		$session = session();
		$detail_data_model = new Detail_Data_Model();
		
		// get all details
		$detail_data = $detail_data_model	
			->findAll();
			
		// read data
		foreach ($detail_data as $detail_line) 
			{
				// merge second and third name to first name
				$detail_line['BMD_firstname'] = $detail_line['BMD_firstname'].' '.$detail_line['BMD_secondname'].' '.$detail_line['BMD_thirdname'];
				
				// update record
				$data =	[
							'BMD_firstname' => $detail_line['BMD_firstname'],
							'BMD_secondname' => '',
							'BMD_thirdname' => '',
						];
				$detail_data_model->update($detail_line['BMD_index'], $data);
			}
			
		// all done
		$session->set('message_2', 'Second and third names have been merged to first name.');
		$session->set('message_class_2', 'alert alert-success');
		return redirect()->to( base_url('housekeeping/index/2') );	
	}
}
