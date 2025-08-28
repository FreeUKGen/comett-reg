<?php namespace App\Controllers;

use App\Models\Licence_Model;
use App\Models\Def_Ranges_Model;
use App\Models\Transcription_Cycle_Model;

class Licence extends BaseController
{
	public function manage_licences($start_message)
	{		
		// initialise method
		$session = session();
		$licence_model = new Licence_Model();
		$def_ranges_model = new Def_Ranges_Model();
		
		// only for FreeREG
		if ( $session->current_project[0]['project_index'] != 2 )
			{
				$session->set('message_2',  'Manage Licences is for FreeREG only.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('database/database_step1/1') );
			}
		
		// set messages
		switch ($start_message) 
			{
				case 0:
					$session->set('message_1', 'Manage Licences - first 100 licences shown. Use search to find the licence you are looking for.');
					$session->set('message_class_1', 'alert alert-primary');
					$session->set('message_2', '');
					$session->set('message_class_2', '');
					$session->set('search', '');
					// get all licences in licence name sequence - limit to 100
					$session->licences = $licence_model
						->orderby('Licence') 
						->findAll(100);
						
					if (  ! $session->licences )
						{
							$session->set('message_2',  'No licences found.');
							$session->set('message_class_2', 'alert alert-danger');
							return redirect()->to( base_url('licence/manage_licences/2') );
						}
					break;
				case 1:
					break;
				case 2:
					$session->set('message_1', 'Manage Licences - first 100 licences shown. Use search to find the licence you are looking for.');
					$session->set('message_class_1', 'alert alert-primary');
					break;
			}
		
		// show licences
		echo view('templates/header');
		echo view('linBMD2/manage_licences');
		echo view('templates/footer');
	}
	
	public function next_action()
	{
		// initialise method
		$session = session();
		$licence_model = new Licence_Model();
		$transcription_cycle_model = new Transcription_Cycle_Model();
		// get inputs
		$session->set('licence', $this->request->getPost('Licence'));
		$session->set('BMD_cycle_code', $this->request->getPost('BMD_next_action'));
		// get cycle text
		$session->set('BMD_cycle_text', $transcription_cycle_model	
			->where('project_index', $session->current_project[0]['project_index'])
			->where('BMD_cycle_code', $session->BMD_cycle_code)
			->where('BMD_cycle_type', 'LICNA')
			->find());
		
		// get licence from DB
		$licence_record = $licence_model
			->where('Licence',  $session->licence)
			->find();
		if ( ! $licence_record )
			{
				$session->set('message_2', 'Invalid licence, please select again.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('licence/manage_licences/2') );
			}
		
		// perform action selected
		switch ($session->BMD_cycle_code) 
			{
				case 'NOLIC': // nothing was selected
					$session->set('message_2', 'Please select an action to perform from the dropdown.');
					$session->set('message_class_2', 'alert alert-danger');
					return redirect()->to( base_url('licence/manage_licences/2') );
					break;
				case 'LICCH': // Correct licence
					return redirect()->to( base_url('licence/correct_licence_step1/0') );	
					break;
				case 'LICDL': // Delete licence
					$licence_model->delete($session->licence);
					$session->licences = $licence_model	
						->orderby('Licence') 
						->findAll(100);
					$session->set('message_2', 'Licence, '.$session->licence.', was deleted.');
					$session->set('message_class_2', 'alert alert-success');
					return redirect()->to( base_url('licence/manage_licences/2') );
					break;	
			}
		// no action found
		$session->set('message_2', 'No action performed. Selected action not recognised.');
		$session->set('message_class_2', 'alert alert-warning');
		return redirect()->to( base_url('licence/manage_licences/2') );			
	}
	
	public function search()
	{
		// initialise method
		$session = session();
		$licence_model = new Licence_Model();
		
		// get input
		$session->set('search', $this->request->getPost('search'));
		
		// test not empty
		if ( empty($session->search) )
		{
			$session->set('message_2',  'No search entered. Please enter a search to find licences.');
			$session->set('message_class_2', 'alert alert-danger');
			return redirect()->to( base_url('licence/manage_licences/2') );
		}
		
		// get results
		$session->licences = $licence_model	
			->like('Licence', $session->search, 'after')
			->findAll();
		// anthing found?
		if (  ! $session->licences )
		{
			$session->set('message_2',  'No licences starting with '.$session->search.' were found. Try again.');
			$session->set('message_class_2', 'alert alert-danger');
			return redirect()->to( base_url('licence/manage_licences/2') );
		}
		
		// show results
		$session->set('message_2', 'Licences starting with the search, '.$session->search);
		$session->set('message_class_2', 'alert alert-warning');
		$session->set('search', '');
		return redirect()->to( base_url('licence/manage_licences/2') );				
	}
	
	public function correct_licence_step1($start_message)
	{
		// initialise method
		$session = session();
		$licence_model = new Licence_Model();
		
		// set messages
		switch ($start_message) 
			{
				case 0:
					$session->set('message_1', 'Correct Licence.');
					$session->set('message_class_1', 'alert alert-primary');
					$session->set('message_2', '');
					$session->set('message_class_2', '');
					$session->set('search', '');
					// get licence from DB
					$session->licence_to_corrected = $licence_model
					->find($session->licence);
					$session->corrected_licence = $session->licence_to_corrected['Licence'];
					break;
				case 1:
					break;
				case 2:
					$session->set('message_1', 'Correct Licence.');
					$session->set('message_class_1', 'alert alert-primary');
					break;
			}
		
		// show licences
		echo view('templates/header');
		echo view('linBMD2/correct_licence');
		echo view('templates/footer');		
	}
	
	public function correct_licence_step2()
	{
		// initialise method
		$session = session();
		$licence_model = new Licence_Model();
		
		// get input
		$session->set('corrected_licence', $this->request->getPost('corrected_licence'));
		
		// is corrected licence in the DB
		$session->set('corrected_licence', $session->corrected_licence);
		$licence_in_DB = $licence_model	
		->find($session->corrected_licence);
		if ( $licence_in_DB )
		{
			$session->set('message_2', 'The corrected licence is already in the Database');
			$session->set('message_class_2', 'alert alert-warning');
			return redirect()->to( base_url('licence/correct_licence_step1/2') );	
		}
		
		// update record
		$licence_model
			->delete($session->licence_to_corrected['Licence']);
		$data =	[
						'Licence' => $session->corrected_licence,
						'Licence_popularity' =>$session->licence_to_corrected['Licence_popularity'],
					];		
		$licence_model->insert($data);
		
		// reload licences
		$session->licences = $licence_model	
			->orderby('Licence')
			->findAll(100);
		
		// go round again
		$session->set('message_2', 'The licence has been corrected.');
		$session->set('message_class_2', 'alert alert-success');
		return redirect()->to( base_url('licence/manage_licences/2') );	
	}
	
	public function add_licence()
	{
		// initialise method
		$session = session();
		$licence_model = new Licence_Model();
		
		// get input
		$session->set('add_licence', ucfirst($this->request->getPost('add_licence')));
		
		// blank?
		if ( $session->add_licence == '' )
			{
				$session->set('message_2', 'The entry cannot be blank');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('licence/manage_licences/2') );
			}
		
		// is add licence in the DB
		$licence_in_DB = $licence_model	
		->find($session->add_licence);
		if ( $licence_in_DB )
		{
			$session->set('message_2', 'The licence is already in the Database');
			$session->set('message_class_2', 'alert alert-warning');
			return redirect()->to( base_url('licence/manage_licences/2') );	
		}
		
		// add record
		$data =	[
						'Licence' => $session->add_licence,
						'Licence_popularity' => 0,
					];		
		$licence_model->insert($data);
		
		// reload licences
		$session->licences = $licence_model	
			->orderby('Licence')
			->findAll(100);
		
		// go round again
		$session->add_licence = '';
		$session->set('message_2', 'The licence has been added.');
		$session->set('message_class_2', 'alert alert-success');
		return redirect()->to( base_url('licence/manage_licences/2') );	
	}
}
