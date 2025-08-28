<?php namespace App\Controllers;

use App\Models\Firstname_Model;
use App\Models\Def_Ranges_Model;
use App\Models\Transcription_Cycle_Model;

class Firstname extends BaseController
{
	public function manage_firstnames($start_message)
	{		
		// initialise method
		$session = session();
		$firstname_model = new Firstname_Model();
		$def_ranges_model = new Def_Ranges_Model();
		// set messages
		switch ($start_message) 
			{
				case 0:
					$session->set('message_1', 'Manage Firstnames - first 100 names shown. Use search to find the name you are looking for.');
					$session->set('message_class_1', 'alert alert-primary');
					$session->set('message_2', '');
					$session->set('message_class_2', '');
					$session->set('search', '');
					// get all firstnames in firstname name sequence - limit to 100
					$session->firstnames = $firstname_model	->orderby('Firstname') ->findAll(100);
					if (  ! $session->firstnames )
						{
							$session->set('message_2',  'No firstnames found. Have you synced your names database with master?');
							$session->set('message_class_2', 'alert alert-danger');
							return redirect()->to( base_url('firstname/manage_firstnames/2') );
						}
					
					break;
				case 1:
					break;
				case 2:
					$session->set('message_1', 'Manage Firstnames - first 100 names shown. Use search to find the name you are looking for.');
					$session->set('message_class_1', 'alert alert-primary');
					break;
			}
		
		// show firstnames
		echo view('templates/header');
		echo view('linBMD2/manage_firstnames');
		echo view('templates/footer');
	}
	
	public function next_action()
	{
		// initialise method
		$session = session();
		$firstname_model = new Firstname_Model();
		$transcription_cycle_model = new Transcription_Cycle_Model();
		// get inputs
		$session->set('firstname', $this->request->getPost('Firstname'));
		$session->set('BMD_cycle_code', $this->request->getPost('BMD_next_action'));
		// get cycle text
		$session->set('BMD_cycle_text', $transcription_cycle_model	
			->where('project_index', $session->current_project[0]['project_index'])
			->where('BMD_cycle_code', $session->BMD_cycle_code)
			->where('BMD_cycle_type', 'FIRNA')
			->find());
		
		// get firstname from DB
		$firstname_record = $firstname_model->where('Firstname',  $session->firstname)->find();
		if ( ! $firstname_record )
			{
				$session->set('message_2', 'Invalid firstname, please select again.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('firstname/manage_firstnames/2') );
			}
		
		// perform action selected
		switch ($session->BMD_cycle_code) 
			{
				case 'NONFN': // nothing was selected
					$session->set('message_2', 'Please select an action to perform from the dropdown.');
					$session->set('message_class_2', 'alert alert-danger');
					return redirect()->to( base_url('firstname/manage_firstnames/2') );
					break;
				case 'FIRCH': // Correct firstname
					return redirect()->to( base_url('firstname/correct_firstname_step1/0') );	
					break;
				case 'FIRDL': // Delete firstname
					$firstname_model->delete($session->firstname);
					$session->firstnames = $firstname_model	->orderby('Firstname') ->findAll(100);
					$session->set('message_2', 'Firstname, '.$session->firstname.', was deleted.');
					$session->set('message_class_2', 'alert alert-success');
					return redirect()->to( base_url('firstname/manage_firstnames/2') );
					break;	
			}
		// no action found
		$session->set('message_2', 'No action performed. Selected action not recognised.');
		$session->set('message_class_2', 'alert alert-warning');
		return redirect()->to( base_url('firstname/manage_firstnames/2') );			
	}
	
	public function search()
	{
		// initialise method
		$session = session();
		$firstname_model = new Firstname_Model();
		
		// get input
		$session->set('search', $this->request->getPost('search'));
		
		// test not empty
		if ( empty($session->search) )
		{
			$session->set('message_2',  'No search entered. Please enter a search to find firstnames.');
			$session->set('message_class_2', 'alert alert-danger');
			return redirect()->to( base_url('firstname/manage_firstnames/2') );
		}
		
		// get results
		$session->firstnames = $firstname_model	->like('Firstname', $session->search, 'after')
																		->findAll();
		// anthing found?
		if (  ! $session->firstnames )
		{
			$session->set('message_2',  'No firstnames starting with '.$session->search.' were found. Try again.');
			$session->set('message_class_2', 'alert alert-danger');
			return redirect()->to( base_url('firstname/manage_firstnames/2') );
		}
		
		// show results
		$session->set('message_2', 'Firstnames starting with the search, '.$session->search);
		$session->set('message_class_2', 'alert alert-warning');
		$session->set('search', '');
		return redirect()->to( base_url('firstname/manage_firstnames/2') );				
	}
	
	public function correct_firstname_step1($start_message)
	{
		// initialise method
		$session = session();
		$firstname_model = new Firstname_Model();
		
		// set messages
		switch ($start_message) 
			{
				case 0:
					$session->set('message_1', 'Correct Firstname.');
					$session->set('message_class_1', 'alert alert-primary');
					$session->set('message_2', '');
					$session->set('message_class_2', '');
					$session->set('search', '');
					// get firstname from DB
					$session->firstname_to_corrected = $firstname_model->find($session->firstname);
					$session->corrected_firstname = $session->firstname_to_corrected['Firstname'];
					break;
				case 1:
					break;
				case 2:
					$session->set('message_1', 'Correct Firstname.');
					$session->set('message_class_1', 'alert alert-primary');
					break;
			}
		
		// show firstnames
		echo view('templates/header');
		echo view('linBMD2/correct_firstname');
		echo view('templates/footer');		
	}
	
	public function correct_firstname_step2()
	{
		// initialise method
		$session = session();
		$firstname_model = new Firstname_Model();
		
		// get input
		$session->set('corrected_firstname', $this->request->getPost('corrected_firstname'));
		
		// is corrected firstname in the DB
		$session->set('corrected_firstname', strtoupper($session->corrected_firstname));
		$firstname_in_DB = $firstname_model	->find($session->corrected_firstname);
		if ( $firstname_in_DB )
		{
			$session->set('message_2', 'The corrected name is already in the Database');
			$session->set('message_class_2', 'alert alert-warning');
			return redirect()->to( base_url('firstname/correct_firstname_step1/2') );	
		}
		
		// update record
		$firstname_model->delete($session->firstname_to_corrected['Firstname']);
		$data =	[
						'Firstname' => $session->corrected_firstname,
						'Firstname_popularity' =>$session->firstname_to_corrected['Firstname_popularity'],
					];		
		$firstname_model->insert($data);
		
		// reload names
		$session->firstnames = $firstname_model	->orderby('Firstname')
																		->findAll(100);
		
		// go round again
		$session->set('message_2', 'The firstname has been corrected.');
		$session->set('message_class_2', 'alert alert-success');
		return redirect()->to( base_url('firstname/manage_firstnames/2') );	
	}
}
