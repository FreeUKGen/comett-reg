<?php namespace App\Controllers;

use App\Models\Person_Status_Model;
use App\Models\Def_Ranges_Model;
use App\Models\Transcription_Cycle_Model;

class Person_status extends BaseController
{
	public function manage_person_statuses($start_message)
	{		
		// initialise method
		$session = session();
		$person_status_model = new Person_Status_Model();
		$def_ranges_model = new Def_Ranges_Model();
		
		// only for FreeREG
		if ( $session->current_project[0]['project_index'] != 2 )
			{
				$session->set('message_2',  'Manage Person status is for FreeREG only.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('database/database_step1/1') );
			}
			
		// set messages
		switch ($start_message) 
			{
				case 0:
					$session->set('message_1', 'Manage Person_status - first 100 person_statuses shown. Use search to find the person_status you are looking for.');
					$session->set('message_class_1', 'alert alert-primary');
					$session->set('message_2', '');
					$session->set('message_class_2', '');
					$session->set('search', '');
					// get all person_statuses in person_status name sequence - limit to 100
					$session->person_statuses = $person_status_model
						->orderby('Person_status') 
						->findAll(100);
						
					if (  ! $session->person_statuses )
						{
							$session->set('message_2',  'No person_statuses found.');
							$session->set('message_class_2', 'alert alert-danger');
							return redirect()->to( base_url('person_status/manage_person_statuses/2') );
						}
					
					break;
				case 1:
					break;
				case 2:
					$session->set('message_1', 'Manage Person_status - first 100 person_statuses shown. Use search to find the person_status you are looking for.');
					$session->set('message_class_1', 'alert alert-primary');
					break;
			}
		
		// show person_statuses
		echo view('templates/header');
		echo view('linBMD2/manage_person_statuses');
		echo view('templates/footer');
	}
	
	public function next_action()
	{
		// initialise method
		$session = session();
		$person_status_model = new Person_Status_Model();
		$transcription_cycle_model = new Transcription_Cycle_Model();
		// get inputs
		$session->set('person_status', $this->request->getPost('Person_status'));
		$session->set('BMD_cycle_code', $this->request->getPost('BMD_next_action'));
		// get cycle text
		$session->set('BMD_cycle_text', $transcription_cycle_model
			->where('project_index', $session->current_project[0]['project_index'])
			->where('BMD_cycle_code', $session->BMD_cycle_code)
			->where('BMD_cycle_type', 'PSTNA')
			->find());
		
		// get person_status from DB
		$person_status_record = $person_status_model
			->where('Person_status',  $session->person_status)
			->find();
		if ( ! $person_status_record )
			{
				$session->set('message_2', 'Invalid person_status, please select again.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('person_status/manage_person_statuses/2') );
			}
		
		// perform action selected
		switch ($session->BMD_cycle_code) 
			{
				case 'NONPS': // nothing was selected
					$session->set('message_2', 'Please select an action to perform from the dropdown.');
					$session->set('message_class_2', 'alert alert-danger');
					return redirect()->to( base_url('person_status/manage_person_statuses/2') );
					break;
				case 'PSTCH': // Correct person_status
					return redirect()->to( base_url('person_status/correct_person_status_step1/0') );	
					break;
				case 'PSTDL': // Delete person_status
					$person_status_model->delete($session->person_status);
					$session->person_statuses = $person_status_model	
						->orderby('Person_status') 
						->findAll(100);
					$session->set('message_2', 'Person_status, '.$session->person_status.', was deleted.');
					$session->set('message_class_2', 'alert alert-success');
					return redirect()->to( base_url('person_status/manage_person_statuses/2') );
					break;	
			}
		// no action found
		$session->set('message_2', 'No action performed. Selected action not recognised.');
		$session->set('message_class_2', 'alert alert-warning');
		return redirect()->to( base_url('person_status/manage_person_status/2') );			
	}
	
	public function search()
	{
		// initialise method
		$session = session();
		$person_status_model = new Person_Status_Model();
		
		// get input
		$session->set('search', $this->request->getPost('search'));
		
		// test not empty
		if ( empty($session->search) )
		{
			$session->set('message_2',  'No search entered. Please enter a search to find person_statuses.');
			$session->set('message_class_2', 'alert alert-danger');
			return redirect()->to( base_url('person_status/manage_person_statuses/2') );
		}
		
		// get results
		$session->person_statuses = $person_status_model	
			->like('Person_status', $session->search, 'after')
			->findAll();
		// anthing found?
		if (  ! $session->person_statuses )
		{
			$session->set('message_2',  'No person statuses starting with '.$session->search.' were found. Try again.');
			$session->set('message_class_2', 'alert alert-danger');
			return redirect()->to( base_url('person_status/manage_person_statuses/2') );
		}
		
		// show results
		$session->set('message_2', 'Person status starting with the search, '.$session->search);
		$session->set('message_class_2', 'alert alert-warning');
		$session->set('search', '');
		return redirect()->to( base_url('person_status/manage_person_statuses/2') );				
	}
	
	public function correct_person_status_step1($start_message)
	{
		// initialise method
		$session = session();
		$person_status_model = new Person_Status_Model();
		
		// set messages
		switch ($start_message) 
			{
				case 0:
					$session->set('message_1', 'Correct Person status.');
					$session->set('message_class_1', 'alert alert-primary');
					$session->set('message_2', '');
					$session->set('message_class_2', '');
					$session->set('search', '');
					// get person_status from DB
					$session->person_status_to_corrected = $person_status_model
					->find($session->person_status);
					$session->corrected_person_status = $session->person_status_to_corrected['Person_status'];
					break;
				case 1:
					break;
				case 2:
					$session->set('message_1', 'Correct Person status.');
					$session->set('message_class_1', 'alert alert-primary');
					break;
			}
		
		// show person_statuses
		echo view('templates/header');
		echo view('linBMD2/correct_person_status');
		echo view('templates/footer');		
	}
	
	public function correct_person_status_step2()
	{
		// initialise method
		$session = session();
		$person_status_model = new Person_Status_Model();
		
		// get input
		$session->set('corrected_person_status', $this->request->getPost('corrected_person_status'));
		
		// is corrected person_status in the DB
		$session->set('corrected_person_status', $session->corrected_person_status);
		$person_status_in_DB = $person_status_model	
		->find($session->corrected_person_status);
		if ( $person_status_in_DB )
		{
			$session->set('message_2', 'The corrected person status is already in the Database');
			$session->set('message_class_2', 'alert alert-warning');
			return redirect()->to( base_url('person_status/correct_person_status_step1/2') );	
		}
		
		// update record
		$person_status_model
			->delete($session->person_status_to_corrected['Person_status']);
		$data =	[
						'Person_status' => $session->corrected_person_status,
						'Person_status_popularity' =>$session->person_status_to_corrected['Person_status_popularity'],
					];		
		$person_status_model->insert($data);
		
		// reload person_statuses
		$session->person_statuses = $person_status_model	
			->orderby('Person_status')
			->findAll(100);
		
		// go round again
		$session->set('message_2', 'The person status has been corrected.');
		$session->set('message_class_2', 'alert alert-success');
		return redirect()->to( base_url('person_status/manage_person_statuses/2') );	
	}
	
	public function add_person_status()
	{
		// initialise method
		$session = session();
		$person_status_model = new Person_Status_Model();
		
		// get input
		$session->set('add_person_status', ucfirst($this->request->getPost('add_person_status')));
		
		// blank?
		if ( $session->add_person_status == '' )
			{
				$session->set('message_2', 'The entry cannot be blank');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('person_status/manage_person_statuses/2') );
			}
			
		// is add person_status in the DB
		$person_status_in_DB = $person_status_model	
			->find($session->add_person_status);
		if ( $person_status_in_DB )
		{
			$session->set('message_2', 'The person status is already in the Database');
			$session->set('message_class_2', 'alert alert-warning');
			return redirect()->to( base_url('person_status/manage_person_statuses/2') );	
		}
		
		// add record
		$data =	[
						'Person_status' => $session->add_person_status,
						'Person_status_popularity' => 0,
					];		
		$person_status_model->insert($data);
		
		// reload person_statuses
		$session->person_statuses = $person_status_model	
			->orderby('Person_status')
			->findAll(100);
		
		// go round again
		$session->add_person_status = '';
		$session->set('message_2', 'The person status has been added.');
		$session->set('message_class_2', 'alert alert-success');
		return redirect()->to( base_url('person_status/manage_person_statuses/2') );	
	}
}
