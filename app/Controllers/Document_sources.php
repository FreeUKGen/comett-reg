<?php namespace App\Controllers;

use App\Models\Document_Sources_Model;
use App\Models\Def_Ranges_Model;
use App\Models\Transcription_Cycle_Model;

class Document_sources extends BaseController
{
	public function manage_document_sources($start_message)
	{		
		// initialise method
		$session = session();
		$document_sources_model = new Document_Sources_Model();
		$def_ranges_model = new Def_Ranges_Model();
		
		// only for FreeREG
		if ( $session->current_project[0]['project_index'] != 2 )
			{
				$session->set('message_2',  'Manage Document Sources is for FreeREG only.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('database/database_step1/1') );
			}
			
		// set messages
		switch ($start_message) 
			{
				case 0:
					$session->set('message_1', 'Manage Document Sources - first 100 document sources shown. Use search to find the document source you are looking for.');
					$session->set('message_class_1', 'alert alert-primary');
					$session->set('message_2', '');
					$session->set('message_class_2', '');
					$session->set('search', '');
					// get all document sources in document source name sequence - limit to 100
					$session->document_sources = $document_sources_model
						->orderby('document_source') 
						->findAll(100);
						
					if (  ! $session->document_sources )
						{
							$session->set('message_2',  'No document sources found.');
							$session->set('message_class_2', 'alert alert-danger');
							return redirect()->to( base_url('document_sources/manage_document_sources/2') );
						}
					
					break;
				case 1:
					break;
				case 2:
					$session->set('message_1', 'Manage Document Sources - first 100 document sources shown. Use search to find the document source you are looking for.');
					$session->set('message_class_1', 'alert alert-primary');
					break;
			}
		
		// show occupations
		echo view('templates/header');
		echo view('linBMD2/manage_document_sources');
		echo view('templates/footer');
	}
	
	public function next_action()
	{
		// initialise method
		$session = session();
		$document_sources_model = new Document_Sources_Model();
		$transcription_cycle_model = new Transcription_Cycle_Model();
		// get inputs
		$session->set('document_source', $this->request->getPost('document_source'));
		$session->set('BMD_cycle_code', $this->request->getPost('BMD_next_action'));
		// get cycle text
		$session->set('BMD_cycle_text', $transcription_cycle_model
			->where('project_index', $session->current_project[0]['project_index'])
			->where('BMD_cycle_code', $session->BMD_cycle_code)
			->where('BMD_cycle_type', 'DOCNA')
			->find());
		
		// get document_source from DB
		$document_source_record = $document_sources_model
			->where('document_source',  $session->document_source)
			->find();
		if ( ! $document_source_record )
			{
				$session->set('message_2', 'Invalid document source, please select again.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('document_sources/manage_document_sources/2') );
			}
		
		// perform action selected
		switch ($session->BMD_cycle_code) 
			{
				case 'NONOC': // nothing was selected
					$session->set('message_2', 'Please select an action to perform from the dropdown.');
					$session->set('message_class_2', 'alert alert-danger');
					return redirect()->to( base_url('document_sources/manage_document_sources/2') );
					break;
				case 'DOCCH': // Correct occupation
					return redirect()->to( base_url('document_sources/correct_document_source_step1/0') );	
					break;
				case 'DOCDL': // Delete occupation
					$document_sources_model->delete($session->document_source);
					$session->document_sources = $document_sources_model	
						->orderby('document_source') 
						->findAll(100);
					$session->set('message_2', 'Document source, '.$session->document_source.', was deleted.');
					$session->set('message_class_2', 'alert alert-success');
					return redirect()->to( base_url('document_sources/manage_document_sources/2') );
					break;	
			}
		// no action found
		$session->set('message_2', 'No action performed. Selected action not recognised.');
		$session->set('message_class_2', 'alert alert-warning');
		return redirect()->to( base_url('document_sources/manage_document_sources/2') );			
	}
	
	public function search()
	{
		// initialise method
		$session = session();
		$document_sources_model = new Document_Sources_Model();
		
		// get input
		$session->set('search', $this->request->getPost('search'));
		
		// test not empty
		if ( empty($session->search) )
		{
			$session->set('message_2',  'No search entered. Please enter a search to find document sources.');
			$session->set('message_class_2', 'alert alert-danger');
			return redirect()->to( base_url('document_sources/manage_document_sources/2') );
		}
		
		// get results
		$session->document_sources = $document_sources_model	
			->like('document_source', $session->search, 'after')
			->findAll();
		// anthing found?
		if (  ! $session->document_sources )
		{
			$session->set('message_2',  'No document sources starting with '.$session->search.' were found. Try again.');
			$session->set('message_class_2', 'alert alert-danger');
			return redirect()->to( base_url('document_sources/manage_document_sources/2') );
		}
		
		// show results
		$session->set('message_2', 'Document sources starting with the search, '.$session->search);
		$session->set('message_class_2', 'alert alert-warning');
		$session->set('search', '');
		return redirect()->to( base_url('document_sources/manage_document_sources/2') );				
	}
	
	public function correct_document_source_step1($start_message)
	{
		// initialise method
		$session = session();
		$document_sources_model = new Document_Sources_Model();
		
		// set messages
		switch ($start_message) 
			{
				case 0:
					$session->set('message_1', 'Correct document source.');
					$session->set('message_class_1', 'alert alert-primary');
					$session->set('message_2', '');
					$session->set('message_class_2', '');
					$session->set('search', '');
					// get occupation from DB
					$session->document_source_to_corrected = $document_sources_model
					->find($session->document_source);
					$session->corrected_document_source = $session->document_source_to_corrected['document_source'];
					break;
				case 1:
					break;
				case 2:
					$session->set('message_1', 'Correct document source.');
					$session->set('message_class_1', 'alert alert-primary');
					break;
			}
		
		// show document source
		echo view('templates/header');
		echo view('linBMD2/correct_document_source');
		echo view('templates/footer');		
	}
	
	public function correct_document_source_step2()
	{
		// initialise method
		$session = session();
		$document_sources_model = new Document_Sources_Model();
		
		// get input
		$session->set('corrected_document_source', $this->request->getPost('corrected_document_source'));
		
		// is corrected document source in the DB
		$session->set('corrected_document_source', $session->corrected_document_source);
		$document_source_in_DB = $document_sources_model	
		->find($session->corrected_document_source);
		if ( $document_source_in_DB )
		{
			$session->set('message_2', 'The corrected document source is already in the Database');
			$session->set('message_class_2', 'alert alert-warning');
			return redirect()->to( base_url('document_sources/manage_document_sources/2') );	
		}
		
		// update record
		$document_sources_model
			->delete($session->document_source_to_corrected['document_source']);
		$data =	[
						'document_source' => $session->corrected_document_source,
						'document_source_popularity' =>$session->document_source_to_corrected['document_source_popularity'],
					];		
		$document_sources_model->insert($data);
		// reload document sources
		$session->document_sources = $document_sources_model	
			->orderby('document_source')
			->findAll(100);
		
		// go round again
		$session->set('message_2', 'The document source has been corrected.');
		$session->set('message_class_2', 'alert alert-success');
		return redirect()->to( base_url('document_sources/manage_document_sources/2') );	
	}
	
	public function add_document_source()
	{
		// initialise method
		$session = session();
		$document_sources_model = new Document_Sources_Model();
		
		// get input
		$session->set('add_document_source', ucfirst($this->request->getPost('add_document_source')));
		
		// blank?
		if ( $session->add_document_source == '' )
			{
				$session->set('message_2', 'The entry cannot be blank');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('document_sources/manage_document_sources/2') );
			}
			
		// is add occupation in the DB
		$document_source_in_DB = $document_sources_model	
		->find($session->add_document_source);
		if ( $document_source_in_DB )
		{
			$session->set('message_2', 'The document source is already in the Database');
			$session->set('message_class_2', 'alert alert-warning');
			return redirect()->to( base_url('document_sources/manage_document_sources/2') );	
		}
		
		// add record
		$data =	[
						'document_source' => $session->add_document_source,
						'document_source_popularity' => 0,
					];		
		$document_sources_model->insert($data);
		
		// reload document sources
		$session->document_sources = $document_sources_model	
			->orderby('document_source')
			->findAll(100);
		
		// go round again
		$session->add_document_source = '';
		$session->set('message_2', 'The document source has been added.');
		$session->set('message_class_2', 'alert alert-success');
		return redirect()->to( base_url('document_sources/manage_document_sources/2') );	
	}
}
