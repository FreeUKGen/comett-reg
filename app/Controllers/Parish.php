<?php namespace App\Controllers;

use App\Models\Parish_Model;
use App\Models\Def_Ranges_Model;
use App\Models\Transcription_Cycle_Model;

class Parish extends BaseController
{
	public function manage_parishes($start_message)
	{		
		// initialise method
		$session = session();
		$parish_model = new Parish_Model();
		$def_ranges_model = new Def_Ranges_Model();
		
		// only for FreeREG
		if ( $session->current_project['project_index'] != 2 )
			{
				$session->set('message_2',  'Manage Parishes is for FreeREG only.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('database/database_step1/1') );
			}
		
		// set messages
		switch ($start_message) 
			{
				case 0:
					$session->set('message_1', 'Manage Parishes - first 100 parishes shown. Use search to find the parish you are looking for.');
					$session->set('message_class_1', 'alert alert-primary');
					$session->set('message_2', '');
					$session->set('message_class_2', '');
					$session->set('search', '');
					// get all parishes in parish name sequence - limit to 100
					$session->parishes = $parish_model
						->orderby('Parish') 
						->findAll(100);
						
					if (  ! $session->parishes )
						{
							$session->set('message_2',  'No parishes found.');
							$session->set('message_class_2', 'alert alert-danger');
							return redirect()->to( base_url('parish/manage_parishes/2') );
						}
					break;
				case 1:
					break;
				case 2:
					$session->set('message_1', 'Manage Parishes - first 100 parishes shown. Use search to find the parish you are looking for.');
					$session->set('message_class_1', 'alert alert-primary');
					break;
			}
		
		// show parishes
		echo view('templates/header');
		echo view('linBMD2/manage_parishes');
		echo view('templates/footer');
	}
	
	public function next_action()
	{
		// initialise method
		$session = session();
		$parish_model = new Parish_Model();
		$transcription_cycle_model = new Transcription_Cycle_Model();
		// get inputs
		$session->set('parish', $this->request->getPost('Parish'));
		$session->set('BMD_cycle_code', $this->request->getPost('BMD_next_action'));
		// get cycle text
		$session->set('BMD_cycle_text', $transcription_cycle_model
			->where('project_index', $session->current_project['project_index'])
			->where('BMD_cycle_code', $session->BMD_cycle_code)
			->where('BMD_cycle_type', 'PARNA')
			->find());
		
		// get parish from DB
		$parish_record = $parish_model
			->where('Parish',  $session->parish)
			->find();
		if ( ! $parish_record )
			{
				$session->set('message_2', 'Invalid parish, please select again.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('parish/manage_parishes/2') );
			}
		
		// perform action selected
		switch ($session->BMD_cycle_code) 
			{
				case 'NONPA': // nothing was selected
					$session->set('message_2', 'Please select an action to perform from the dropdown.');
					$session->set('message_class_2', 'alert alert-danger');
					return redirect()->to( base_url('parish/manage_parishes/2') );
					break;
				case 'PARCH': // Correct parish
					return redirect()->to( base_url('parish/correct_parish_step1/0') );	
					break;
				case 'PARDL': // Delete parish
					$parish_model->delete($session->parish);
					$session->parishes = $parish_model	
						->orderby('Parish') 
						->findAll(100);
					$session->set('message_2', 'Parish, '.$session->parish.', was deleted.');
					$session->set('message_class_2', 'alert alert-success');
					return redirect()->to( base_url('parish/manage_parishes/2') );
					break;	
			}
		// no action found
		$session->set('message_2', 'No action performed. Selected action not recognised.');
		$session->set('message_class_2', 'alert alert-warning');
		return redirect()->to( base_url('parish/manage_parish/2') );			
	}
	
	public function search()
	{
		// initialise method
		$session = session();
		$parish_model = new Parish_Model();
		
		// get input
		$session->set('search', $this->request->getPost('search'));
		
		// test not empty
		if ( empty($session->search) )
		{
			$session->set('message_2',  'No search entered. Please enter a search to find parishes.');
			$session->set('message_class_2', 'alert alert-danger');
			return redirect()->to( base_url('parish/manage_parishes/2') );
		}
		
		// get results
		$session->parishes = $parish_model	
			->like('Parish', $session->search, 'after')
			->findAll();
		// anthing found?
		if (  ! $session->parishes )
		{
			$session->set('message_2',  'No parishes starting with '.$session->search.' were found. Try again.');
			$session->set('message_class_2', 'alert alert-danger');
			return redirect()->to( base_url('parish/manage_parishes/2') );
		}
		
		// show results
		$session->set('message_2', 'Parishes starting with the search, '.$session->search);
		$session->set('message_class_2', 'alert alert-warning');
		$session->set('search', '');
		return redirect()->to( base_url('parish/manage_parishes/2') );				
	}
	
	public function correct_parish_step1($start_message)
	{
		// initialise method
		$session = session();
		$parish_model = new Parish_Model();
		
		// set messages
		switch ($start_message) 
			{
				case 0:
					$session->set('message_1', 'Correct Parish.');
					$session->set('message_class_1', 'alert alert-primary');
					$session->set('message_2', '');
					$session->set('message_class_2', '');
					$session->set('search', '');
					// get parish from DB
					$session->parish_to_corrected = $parish_model
					->find($session->parish);
					$session->corrected_parish = $session->parish_to_corrected['Parish'];
					break;
				case 1:
					break;
				case 2:
					$session->set('message_1', 'Correct Parish.');
					$session->set('message_class_1', 'alert alert-primary');
					break;
			}
		
		// show parishes
		echo view('templates/header');
		echo view('linBMD2/correct_parish');
		echo view('templates/footer');		
	}
	
	public function correct_parish_step2()
	{
		// initialise method
		$session = session();
		$parish_model = new Parish_Model();
		
		// get input
		$session->set('corrected_parish', $this->request->getPost('corrected_parish'));
		
		// is corrected parish in the DB
		$session->set('corrected_parish', $session->corrected_parish);
		$parish_in_DB = $parish_model	
		->find($session->corrected_parish);
		if ( $parish_in_DB )
		{
			$session->set('message_2', 'The corrected parish is already in the Database');
			$session->set('message_class_2', 'alert alert-warning');
			return redirect()->to( base_url('parish/correct_parish_step1/2') );	
		}
		
		// update record
		$parish_model
			->delete($session->parish_to_corrected['Parish']);
		$data =	[
						'Parish' => $session->corrected_parish,
						'Parish_popularity' =>$session->parish_to_corrected['Parish_popularity'],
					];		
		$parish_model->insert($data);
		
		// reload parishes
		$session->parishes = $parish_model	
			->orderby('Parish')
			->findAll(100);
		
		// go round again
		$session->set('message_2', 'The parish has been corrected.');
		$session->set('message_class_2', 'alert alert-success');
		return redirect()->to( base_url('parish/manage_parishes/2') );	
	}
	
	public function add_parish()
	{
		// initialise method
		$session = session();
		$parish_model = new Parish_Model();
		
		// get input
		$session->set('add_parish', ucfirst($this->request->getPost('add_parish')));
		
		// blank?
		if ( $session->add_parish == '' )
			{
				$session->set('message_2', 'The entry cannot be blank');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('parish/manage_parishes/2') );
			}
		
		// is add parish in the DB
		$parish_in_DB = $parish_model	
		->find($session->add_parish);
		if ( $parish_in_DB )
		{
			$session->set('message_2', 'The parish is already in the Database');
			$session->set('message_class_2', 'alert alert-warning');
			return redirect()->to( base_url('parish/manage_parishes/2') );	
		}
		
		// add record
		$data =	[
						'Parish' => $session->add_parish,
						'Parish_popularity' => 0,
					];		
		$parish_model->insert($data);
		
		// reload parishes
		$session->parishes = $parish_model	
			->orderby('Parish')
			->findAll(100);
		
		// go round again
		$session->add_parish = '';
		$session->set('message_2', 'The parish has been added.');
		$session->set('message_class_2', 'alert alert-success');
		return redirect()->to( base_url('parish/manage_parishes/2') );	
	}
}