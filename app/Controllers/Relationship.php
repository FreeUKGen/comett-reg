<?php namespace App\Controllers;

use App\Models\Relationship_Model;
use App\Models\Def_Ranges_Model;
use App\Models\Transcription_Cycle_Model;

class Relationship extends BaseController
{
	public function manage_relationships($start_message)
	{		
		// initialise method
		$session = session();
		$relationship_model = new Relationship_Model();
		$def_ranges_model = new Def_Ranges_Model();
		
		// only for FreeREG
		if ( $session->current_project[0]['project_index'] != 2 )
			{
				$session->set('message_2',  'Manage Relationships is for FreeREG only.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('database/database_step1/1') );
			}
		
		// set messages
		switch ($start_message) 
			{
				case 0:
					$session->set('message_1', 'Manage Relationships - first 100 relationships shown. Use search to find the relationship you are looking for.');
					$session->set('message_class_1', 'alert alert-primary');
					$session->set('message_2', '');
					$session->set('message_class_2', '');
					$session->set('search', '');
					// get all relationships in relationship name sequence - limit to 100
					$session->relationships = $relationship_model
						->orderby('Relationship') 
						->findAll(100);
						
					if (  ! $session->relationships )
						{
							$session->set('message_2',  'No relationships found.');
							$session->set('message_class_2', 'alert alert-danger');
							return redirect()->to( base_url('relationship/manage_relationships/2') );
						}
					break;
				case 1:
					break;
				case 2:
					$session->set('message_1', 'Manage Relationships - first 100 relationships shown. Use search to find the relationship you are looking for.');
					$session->set('message_class_1', 'alert alert-primary');
					break;
			}
		
		// show relationships
		echo view('templates/header');
		echo view('linBMD2/manage_relationships');
		echo view('templates/footer');
	}
	
	public function next_action()
	{
		// initialise method
		$session = session();
		$relationship_model = new Relationship_Model();
		$transcription_cycle_model = new Transcription_Cycle_Model();
		// get inputs
		$session->set('relationship', $this->request->getPost('Relationship'));
		$session->set('BMD_cycle_code', $this->request->getPost('BMD_next_action'));
		// get cycle text
		$session->set('BMD_cycle_text', $transcription_cycle_model
			->where('project_index', $session->current_project[0]['project_index'])
			->where('BMD_cycle_code', $session->BMD_cycle_code)
			->where('BMD_cycle_type', 'RELNA')
			->find());
		
		// get relationship from DB
		$relationship_record = $relationship_model
			->where('Relationship',  $session->relationship)
			->find();
		if ( ! $relationship_record )
			{
				$session->set('message_2', 'Invalid relationship, please select again.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('relationship/manage_relationships/2') );
			}
		
		// perform action selected
		switch ($session->BMD_cycle_code) 
			{
				case 'NOREL': // nothing was selected
					$session->set('message_2', 'Please select an action to perform from the dropdown.');
					$session->set('message_class_2', 'alert alert-danger');
					return redirect()->to( base_url('relationship/manage_relationships/2') );
					break;
				case 'RELCH': // Correct relationship
					return redirect()->to( base_url('relationship/correct_relationship_step1/0') );	
					break;
				case 'RELDL': // Delete relationship
					$relationship_model->delete($session->relationship);
					$session->relationships = $relationship_model	
						->orderby('Relationship') 
						->findAll(100);
					$session->set('message_2', 'Relationship, '.$session->relationship.', was deleted.');
					$session->set('message_class_2', 'alert alert-success');
					return redirect()->to( base_url('relationship/manage_relationships/2') );
					break;	
			}
		// no action found
		$session->set('message_2', 'No action performed. Selected action not recognised.');
		$session->set('message_class_2', 'alert alert-warning');
		return redirect()->to( base_url('relationship/manage_relationships/2') );			
	}
	
	public function search()
	{
		// initialise method
		$session = session();
		$relationship_model = new Relationship_Model();
		
		// get input
		$session->set('search', $this->request->getPost('search'));
		
		// test not empty
		if ( empty($session->search) )
		{
			$session->set('message_2',  'No search entered. Please enter a search to find relationships.');
			$session->set('message_class_2', 'alert alert-danger');
			return redirect()->to( base_url('relationship/manage_relationships/2') );
		}
		
		// get results
		$session->relationships = $relationship_model	
			->like('Relationship', $session->search, 'after')
			->findAll();
		// anthing found?
		if (  ! $session->relationships )
		{
			$session->set('message_2',  'No relationships starting with '.$session->search.' were found. Try again.');
			$session->set('message_class_2', 'alert alert-danger');
			return redirect()->to( base_url('relationship/manage_relationships/2') );
		}
		
		// show results
		$session->set('message_2', 'Relationships starting with the search, '.$session->search);
		$session->set('message_class_2', 'alert alert-warning');
		$session->set('search', '');
		return redirect()->to( base_url('relationship/manage_relationships/2') );				
	}
	
	public function correct_relationship_step1($start_message)
	{
		// initialise method
		$session = session();
		$relationship_model = new Relationship_Model();
		
		// set messages
		switch ($start_message) 
			{
				case 0:
					$session->set('message_1', 'Correct Relationship.');
					$session->set('message_class_1', 'alert alert-primary');
					$session->set('message_2', '');
					$session->set('message_class_2', '');
					$session->set('search', '');
					// get relationship from DB
					$session->relationship_to_corrected = $relationship_model
					->find($session->relationship);
					$session->corrected_relationship = $session->relationship_to_corrected['Relationship'];
					break;
				case 1:
					break;
				case 2:
					$session->set('message_1', 'Correct Relationship.');
					$session->set('message_class_1', 'alert alert-primary');
					break;
			}
		
		// show relationships
		echo view('templates/header');
		echo view('linBMD2/correct_relationship');
		echo view('templates/footer');		
	}
	
	public function correct_relationship_step2()
	{
		// initialise method
		$session = session();
		$relationship_model = new Relationship_Model();
		
		// get input
		$session->set('corrected_relationship', $this->request->getPost('corrected_relationship'));
		
		// is corrected relationship in the DB
		$session->set('corrected_relationship', $session->corrected_relationship);
		$relationship_in_DB = $relationship_model	
		->find($session->corrected_relationship);
		if ( $relationship_in_DB )
		{
			$session->set('message_2', 'The corrected relationship is already in the Database');
			$session->set('message_class_2', 'alert alert-warning');
			return redirect()->to( base_url('relationship/correct_relationship_step1/2') );	
		}
		
		// update record
		$relationship_model
			->delete($session->relationship_to_corrected['Relationship']);
		$data =	[
						'Relationship' => $session->corrected_relationship,
						'Relationship_popularity' =>$session->relationship_to_corrected['Relationship_popularity'],
					];		
		$relationship_model->insert($data);
		
		// reload relationships
		$session->relationships = $relationship_model	
			->orderby('Relationship')
			->findAll(100);
		
		// go round again
		$session->set('message_2', 'The relationship has been corrected.');
		$session->set('message_class_2', 'alert alert-success');
		return redirect()->to( base_url('relationship/manage_relationships/2') );	
	}
	
	public function add_relationship()
	{
		// initialise method
		$session = session();
		$relationship_model = new Relationship_Model();
		
		// get input
		$session->set('add_relationship', ucfirst($this->request->getPost('add_relationship')));
		
		// blank?
		if ( $session->add_relationship == '' )
			{
				$session->set('message_2', 'The entry cannot be blank');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('relationship/manage_relationships/2') );
			}
		
		// is add relationship in the DB
		$relationship_in_DB = $relationship_model	
		->find($session->add_relationship);
		if ( $relationship_in_DB )
		{
			$session->set('message_2', 'The relationship is already in the Database');
			$session->set('message_class_2', 'alert alert-warning');
			return redirect()->to( base_url('relationship/manage_relationships/2') );	
		}
		
		// add record
		$data =	[
						'Relationship' => $session->add_relationship,
						'Relationship_popularity' => 0,
					];		
		$relationship_model->insert($data);
		
		// reload relationships
		$session->relationships = $relationship_model	
			->orderby('Relationship')
			->findAll(100);
		
		// go round again
		$session->add_relationship = '';
		$session->set('message_2', 'The relationship has been added.');
		$session->set('message_class_2', 'alert alert-success');
		return redirect()->to( base_url('relationship/manage_relationships/2') );	
	}
}
