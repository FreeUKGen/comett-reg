<?php namespace App\Controllers;

use App\Models\Occupation_Model;
use App\Models\Def_Ranges_Model;
use App\Models\Transcription_Cycle_Model;

class Occupation extends BaseController
{
	public function manage_occupations($start_message)
	{		
		// initialise method
		$session = session();
		$occupation_model = new Occupation_Model();
		$def_ranges_model = new Def_Ranges_Model();
		
		// only for FreeREG
		if ( $session->current_project[0]['project_index'] != 2 )
			{
				$session->set('message_2',  'Manage Occupations is for FreeREG only.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('database/database_step1/1') );
			}
			
		// set messages
		switch ($start_message) 
			{
				case 0:
					$session->set('message_1', 'Manage Occupations - first 100 occupations shown. Use search to find the occupation you are looking for.');
					$session->set('message_class_1', 'alert alert-primary');
					$session->set('message_2', '');
					$session->set('message_class_2', '');
					$session->set('search', '');
					// get all occupations in occupation name sequence - limit to 100
					$session->occupations = $occupation_model
						->orderby('Occupation') 
						->findAll(100);
						
					if (  ! $session->occupations )
						{
							$session->set('message_2',  'No occupations found.');
							$session->set('message_class_2', 'alert alert-danger');
							return redirect()->to( base_url('occupation/manage_occupations/2') );
						}
					
					break;
				case 1:
					break;
				case 2:
					$session->set('message_1', 'Manage Occupations - first 100 occupations shown. Use search to find the occupation you are looking for.');
					$session->set('message_class_1', 'alert alert-primary');
					break;
			}
		
		// show occupations
		echo view('templates/header');
		echo view('linBMD2/manage_occupations');
		echo view('templates/footer');
	}
	
	public function next_action()
	{
		// initialise method
		$session = session();
		$occupation_model = new Occupation_Model();
		$transcription_cycle_model = new Transcription_Cycle_Model();
		// get inputs
		$session->set('occupation', $this->request->getPost('Occupation'));
		$session->set('BMD_cycle_code', $this->request->getPost('BMD_next_action'));
		// get cycle text
		$session->set('BMD_cycle_text', $transcription_cycle_model
			->where('project_index', $session->current_project[0]['project_index'])
			->where('BMD_cycle_code', $session->BMD_cycle_code)
			->where('BMD_cycle_type', 'OCCNA')
			->find());
		
		// get occupation from DB
		$occupation_record = $occupation_model
			->where('Occupation',  $session->occupation)
			->find();
		if ( ! $occupation_record )
			{
				$session->set('message_2', 'Invalid occupation, please select again.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('occupation/manage_occupations/2') );
			}
		
		// perform action selected
		switch ($session->BMD_cycle_code) 
			{
				case 'NONOC': // nothing was selected
					$session->set('message_2', 'Please select an action to perform from the dropdown.');
					$session->set('message_class_2', 'alert alert-danger');
					return redirect()->to( base_url('occupation/manage_occupations/2') );
					break;
				case 'OCCCH': // Correct occupation
					return redirect()->to( base_url('occupation/correct_occupation_step1/0') );	
					break;
				case 'OCCDL': // Delete occupation
					$occupation_model->delete($session->occupation);
					$session->occupations = $occupation_model	
						->orderby('Occupation') 
						->findAll(100);
					$session->set('message_2', 'Occupation, '.$session->occupation.', was deleted.');
					$session->set('message_class_2', 'alert alert-success');
					return redirect()->to( base_url('occupation/manage_occupations/2') );
					break;	
			}
		// no action found
		$session->set('message_2', 'No action performed. Selected action not recognised.');
		$session->set('message_class_2', 'alert alert-warning');
		return redirect()->to( base_url('occupation/manage_occupation/2') );			
	}
	
	public function search()
	{
		// initialise method
		$session = session();
		$occupation_model = new Occupation_Model();
		
		// get input
		$session->set('search', $this->request->getPost('search'));
		
		// test not empty
		if ( empty($session->search) )
		{
			$session->set('message_2',  'No search entered. Please enter a search to find occupations.');
			$session->set('message_class_2', 'alert alert-danger');
			return redirect()->to( base_url('occupation/manage_occupations/2') );
		}
		
		// get results
		$session->occupations = $occupation_model	
			->like('Occupation', $session->search, 'after')
			->findAll();
		// anthing found?
		if (  ! $session->occupations )
		{
			$session->set('message_2',  'No occupations starting with '.$session->search.' were found. Try again.');
			$session->set('message_class_2', 'alert alert-danger');
			return redirect()->to( base_url('occupation/manage_occupations/2') );
		}
		
		// show results
		$session->set('message_2', 'Occupations starting with the search, '.$session->search);
		$session->set('message_class_2', 'alert alert-warning');
		$session->set('search', '');
		return redirect()->to( base_url('occupation/manage_occupations/2') );				
	}
	
	public function correct_occupation_step1($start_message)
	{
		// initialise method
		$session = session();
		$occupation_model = new Occupation_Model();
		
		// set messages
		switch ($start_message) 
			{
				case 0:
					$session->set('message_1', 'Correct Occupation.');
					$session->set('message_class_1', 'alert alert-primary');
					$session->set('message_2', '');
					$session->set('message_class_2', '');
					$session->set('search', '');
					// get occupation from DB
					$session->occupation_to_corrected = $occupation_model
					->find($session->occupation);
					$session->corrected_occupation = $session->occupation_to_corrected['Occupation'];
					break;
				case 1:
					break;
				case 2:
					$session->set('message_1', 'Correct Occupation.');
					$session->set('message_class_1', 'alert alert-primary');
					break;
			}
		
		// show occupations
		echo view('templates/header');
		echo view('linBMD2/correct_occupation');
		echo view('templates/footer');		
	}
	
	public function correct_occupation_step2()
	{
		// initialise method
		$session = session();
		$occupation_model = new Occupation_Model();
		
		// get input
		$session->set('corrected_occupation', $this->request->getPost('corrected_occupation'));
		
		// is corrected occupation in the DB
		$session->set('corrected_occupation', $session->corrected_occupation);
		$occupation_in_DB = $occupation_model	
		->find($session->corrected_occupation);
		if ( $occupation_in_DB )
		{
			$session->set('message_2', 'The corrected occupation is already in the Database');
			$session->set('message_class_2', 'alert alert-warning');
			return redirect()->to( base_url('occupation/correct_occupation_step1/2') );	
		}
		
		// update record
		$occupation_model
			->delete($session->occupation_to_corrected['Occupation']);
		$data =	[
						'Occupation' => $session->corrected_occupation,
						'Occupation_popularity' =>$session->occupation_to_corrected['Occupation_popularity'],
					];		
		$occupation_model->insert($data);
		
		// reload occupations
		$session->occupations = $occupation_model	
			->orderby('Occupation')
			->findAll(100);
		
		// go round again
		$session->set('message_2', 'The occupation has been corrected.');
		$session->set('message_class_2', 'alert alert-success');
		return redirect()->to( base_url('occupation/manage_occupations/2') );	
	}
	
	public function add_occupation()
	{
		// initialise method
		$session = session();
		$occupation_model = new Occupation_Model();
		
		// get input
		$session->set('add_occupation', ucfirst($this->request->getPost('add_occupation')));
		
		// blank?
		if ( $session->add_occupation == '' )
			{
				$session->set('message_2', 'The entry cannot be blank');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('occupation/manage_occupations/2') );
			}
			
		// is add occupation in the DB
		$occupation_in_DB = $occupation_model	
		->find($session->add_occupation);
		if ( $occupation_in_DB )
		{
			$session->set('message_2', 'The occupation is already in the Database');
			$session->set('message_class_2', 'alert alert-warning');
			return redirect()->to( base_url('occupation/manage_occupations/2') );	
		}
		
		// add record
		$data =	[
						'Occupation' => $session->add_occupation,
						'Occupation_popularity' => 0,
					];		
		$occupation_model->insert($data);
		
		// reload occupations
		$session->occupations = $occupation_model	
			->orderby('Occupation')
			->findAll(100);
		
		// go round again
		$session->add_occupation = '';
		$session->set('message_2', 'The occupation has been added.');
		$session->set('message_class_2', 'alert alert-success');
		return redirect()->to( base_url('occupation/manage_occupations/2') );	
	}
}
