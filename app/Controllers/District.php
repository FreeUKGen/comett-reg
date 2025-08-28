<?php namespace App\Controllers;

use App\Models\Districts_Model;
use App\Models\Transcription_Cycle_Model;
use App\Models\Volumes_Model;

class District extends BaseController
{	
	public function manage_districts($start_message)
	{		
		// initialise method
		$session = session();
		$districts_model = new Districts_Model();
		$transcription_cycle_model = new Transcription_Cycle_Model;
		
		// set messages
		switch ($start_message) 
			{
				case 0:
					$session->set('message_1', 'Manage Districts - first 100 districts shown. Use search to find the district you are looking for.');
					$session->set('message_class_1', 'alert alert-primary');
					$session->set('message_2', '');
					$session->set('message_class_2', '');
					$session->set('search', '');
					// get cycle codes for districts management
					$session->districts_cycle =	$transcription_cycle_model
						->where('project_index', $session->current_project[0]['project_index'])
						->where('BMD_cycle_type', 'DISTR')
						->orderby('BMD_cycle_sort')
						->findAll();					
					break;
				case 1:
					break;
				case 2:
					$session->set('message_1', 'Manage Districts - first 100 districts shown. Use search to find the district you are looking for.');
					$session->set('message_class_1', 'alert alert-primary');
					break;
			}
			
		// get all districts in district name sequence - limit to 100
		$session->districts =	$districts_model
								->orderby('District_name')
								->findAll(100);
		if (  ! $session->districts )
			{
				$session->set('message_2',  'No districts found. Please report to '.$session->linbmd2_email);
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('district/manage_districts/2') );
			}
			

		// show districts
		echo view('templates/header');
		echo view('linBMD2/manage_districts');
		echo view('templates/footer');
	}
	
	public function next_action()
	{
		// initialise method
		$session = session();
		$districts_model = new Districts_Model();
		$transcription_cycle_model = new Transcription_Cycle_Model();
		
		// get inputs
		$session->set('district_name', $this->request->getPost('District_name'));
		$session->set('BMD_cycle_code', $this->request->getPost('BMD_next_action'));
		// get cycle text
		$session->set('BMD_cycle_text', $transcription_cycle_model	
			->where('project_index', $session->current_project[0]['project_index'])
			->where('BMD_cycle_code', $session->BMD_cycle_code)
			->where('BMD_cycle_type', 'DISTR')
			->find());
		
		
		// get district from DB
		$district_record = $districts_model->where('District_name',  $session->district_name)->find();
		if ( ! $district_record )
			{
				$session->set('message_2', 'Invalid district, please select again.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('surname/manage_districts/2') );
			}
		
		// perform action selected
		switch ($session->BMD_cycle_code) 
			{
				case 'NODRI': // nothing was selected
					$session->set('message_2', 'Please select an action to perform from the dropdown.');
					$session->set('message_class_2', 'alert alert-danger');
					return redirect()->to( base_url('district/manage_districts/2') );
					break;
				case 'CHGDI': // Correct district
					$session->district_index = $district_record[0]['district_index'];
					return redirect()->to( base_url('district/correct_district_step1/0') );	
					break;
				case 'DEADI': // De-activate district
					$data =	[
								'active' => 'NO',
							];	
					$districts_model->update($district_record[0]['district_index'], $data);
					$session->set('message_2', 'District, '.$session->district_name.', was de-activated.');
					$session->set('message_class_2', 'alert alert-success');
					return redirect()->to( base_url('district/manage_districts/2') );
					break;
				case 'READI': // Re-activate district
					$data =	[
								'active' => 'YES',
							];	
					$districts_model->update($district_record[0]['district_index'], $data);
					$session->set('message_2', 'District, '.$session->district_name.', was re-activated.');
					$session->set('message_class_2', 'alert alert-success');
					return redirect()->to( base_url('district/manage_districts/2') );
					break;
				case 'VOLDI': // Manage volumes
					$session->district_index = $district_record[0]['district_index'];
					$session->district_name = $district_record[0]['District_name'];
					return redirect()->to( base_url('district/manage_volumes/0') );
					break;
				case 'ADSDI': // Add synonym to district
					$session->district_index = $district_record[0]['district_index'];
					$session->district_name = $district_record[0]['District_name'];
					return redirect()->to( base_url('district/add_synonym_step1/0') );
					break;
				case 'CONDI': // Covert to synonym for a district
					$session->synonym_index = $district_record[0]['district_index'];
					$session->synonym_name = $district_record[0]['District_name'];
					return redirect()->to( base_url('district/convert_to_synonym_step1/0') );
					break;
			}
		// no action found
		$session->set('message_2', 'No action performed. Selected action not recognised.');
		$session->set('message_class_2', 'alert alert-warning');
		return redirect()->to( base_url('surname/manage_surnames/2') );			
	}
	
	public function search()
	{
		// initialise method
		$session = session();
		$districts_model = new Districts_Model();
		
		// get input
		$session->set('search', $this->request->getPost('search'));
		
		// test not empty
		if ( empty($session->search) )
		{
			$session->set('message_2',  'No search entered. Please enter a search to find districts.');
			$session->set('message_class_2', 'alert alert-danger');
			return redirect()->to( base_url('district/manage_districts/2') );
		}
		
		// get results
		$session->districts =	$districts_model	
								->like('District_name', $session->search, 'after')
								->findAll();
		// anthing found?
		if (  ! $session->districts )
		{
			$session->set('message_2',  'No districts starting with '.$session->search.' were found. Try again.');
			$session->set('message_class_2', 'alert alert-danger');
			return redirect()->to( base_url('district/manage_districts/2') );
		}
		
		// show results
		$session->set('message_2', 'Districts starting with the search, '.$session->search);
		$session->set('message_class_2', 'alert alert-warning');
		$session->set('search', '');
		
		// show districts
		echo view('templates/header');
		echo view('linBMD2/manage_districts');
		echo view('templates/footer');				
	}
	
	public function correct_district_step1($start_message)
	{
		// initialise method
		$session = session();
		$districts_model = new Districts_Model();
		
		// set messages
		switch ($start_message) 
			{
				case 0:
					$session->set('message_1', 'Correct District.');
					$session->set('message_class_1', 'alert alert-primary');
					$session->set('message_2', '');
					$session->set('message_class_2', '');
					$session->set('search', '');
					// get district from DB
					$session->district_to_corrected = $session->district_name;
					$session->corrected_district = $session->district_name;
					break;
				case 1:
					break;
				case 2:
					$session->set('message_1', 'Correct District.');
					$session->set('message_class_1', 'alert alert-primary');
					break;
			}
		
		// show surnames
		echo view('templates/header');
		echo view('linBMD2/correct_district');
		echo view('templates/footer');		
	}
	
	public function correct_district_step2()
	{
		// initialise method
		$session = session();
		$district_model = new Districts_Model();
		
		// get input
		$session->set('corrected_district', $this->request->getPost('corrected_district'));
		
		// is corrected district in the DB
		$session->set('corrected_district', strtoupper($session->corrected_district));
		$district_in_DB = $district_model	->find($session->corrected_district);
		if ( $district_in_DB )
		{
			$session->set('message_2', 'The corrected district is already in the Database');
			$session->set('message_class_2', 'alert alert-danger');
			return redirect()->to( base_url('district/correct_district_step1/2') );	
		}
		
		// update record
		$data =	[
					'District_name' => $session->corrected_district,
				];	
		$district_model->update($session->district_index, $data);
		
		// reload districts
		$session->districts =	$district_model
								->orderby('District_name')
								->findAll(100);
		
		// go round again
		$session->set('message_2', 'The district has been corrected.');
		$session->set('message_class_2', 'alert alert-success');
		return redirect()->to( base_url('district/manage_districts/2') );	
	}
	
	public function manage_volumes($start_message)
	{		
		// initialise method
		$session = session();
		$districts_model = new Districts_Model();
		$volumes_model = new Volumes_Model();
		$transcription_cycle_model = new Transcription_Cycle_Model;
		
		// set messages
		switch ($start_message) 
			{
				case 0:
					$session->set('message_1', 'Manage Volumes - Volumes are shown for the district you are managing => '.$session->district_name);
					$session->set('message_class_1', 'alert alert-primary');
					$session->set('message_2', '');
					$session->set('message_class_2', '');
					$session->set('search', '');
					// get cycle codes for volumes management
					$session->cycle =	$transcription_cycle_model
						->where('project_index', $session->current_project[0]['project_index'])
						->where('BMD_cycle_type', 'VOLUM')
						->orderby('BMD_cycle_sort')
						->findAll();					
					break;
				case 1:
					break;
				case 2:
					$session->set('message_1', 'Manage Volumes - Volumes are shown for the district you are managing => '.$session->district_name);
					$session->set('message_class_1', 'alert alert-primary');
					break;
			}
			
		// get all volumes in type/from/to sequence
		$session->volumes =	$volumes_model
							->where('district_index', $session->district_index)
							->orderby('BMD_type')
							->orderby('volume_from')
							->findAll();
							
		if (  ! $session->volumes )
			{
				$session->set('message_2',  'No volumes found. Please report to '.$session->linbmd2_email);
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('district/manage_districts/2') );
			}			

		// show volumes
		echo view('templates/header');
		echo view('linBMD2/manage_volumes');
		echo view('linBMD2/sortTableNew');
		echo view('linBMD2/searchTableNew');
		echo view('templates/footer');
	}
	
	public function correct_volume_step1($start_message)
	{
		// initialise method
		$session = session();
		$volumes_model = new Volumes_Model();
		
		// set messages
		switch ($start_message) 
			{
				case 0:
					$session->set('message_1', 'Correct Volume for District => '.$session->district_name.', Type => '.$session->volume_record[0]['BMD_type'].', Range => '.$session->volume_record[0]['volume_from'].' - '.$session->volume_record[0]['volume_to']);
					$session->set('message_class_1', 'alert alert-primary');
					$session->set('message_2', '');
					$session->set('message_class_2', '');
					$session->set('search', '');
					// get volume from DB
					$session->volume_to_corrected = $session->volume_record[0]['volume'];
					$session->corrected_volume = $session->volume_record[0]['volume'];
					break;
				case 1:
					break;
				case 2:
					$session->set('message_1', 'Correct Volume for range => '.$session->volume_record[0]['volume_from'].' - '.$session->volume_record[0]['volume_from']);
					$session->set('message_class_1', 'alert alert-primary');
					break;
			}
		
		// show surnames
		echo view('templates/header');
		echo view('linBMD2/correct_volume');
		echo view('templates/footer');		
	}
	
	public function correct_volume_step2()
	{
		// initialise method
		$session = session();
		$volumes_model = new Volumes_Model();
		$districts_model = new Districts_Model();
		
		// get input
		$session->set('corrected_volume', $this->request->getPost('corrected_volume'));
		
		// update record
		$data =	[
					'volume' => $session->corrected_volume,
				];	
		$volumes_model->update($session->volume_record[0]['volume_index'], $data);
		
		// reload volumes
		$session->volumes =	$volumes_model
							->where('district_index', $session->volume_record[0]['district_index'])
							->orderby('BMD_type')
							->orderby('volume_from')
							->findAll();
		
		// go round again
		$session->set('message_2', 'The Volume has been corrected.');
		$session->set('message_class_2', 'alert alert-success');
		return redirect()->to( base_url('district/manage_volumes/2') );	
	}
	
	public function next_action_volume()
	{
		// initialise method
		$session = session();
		$districts_model = new Districts_Model();
		$volumes_model = new Volumes_Model();
		$transcription_cycle_model = new Transcription_Cycle_Model();
		
		// get inputs
		$session->set('volume_index', $this->request->getPost('volume_index'));
		$session->set('BMD_cycle_code', $this->request->getPost('BMD_next_action'));		
		
		// get volume from DB
		$session->volume_record = $volumes_model->where('volume_index',  $session->volume_index)->find();
		if ( ! $session->volume_record )
			{
				$session->set('message_2', 'Invalid volume, please select again.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('district/manage_volumes/2') );
			}
		
		// perform action selected
		switch ($session->BMD_cycle_code) 
			{
				case 'NOVOL': // nothing was selected
					$session->set('message_2', 'Please select an action to perform from the dropdown.');
					$session->set('message_class_2', 'alert alert-danger');
					return redirect()->to( base_url('district/manage_volumes/2') );
					break;
				case 'CHVOL': // Correct district
					return redirect()->to( base_url('district/correct_volume_step1/0') );	
					break;
			}
		// no action found
		$session->set('message_2', 'No action performed. Selected action not recognised.');
		$session->set('message_class_2', 'alert alert-warning');
		return redirect()->to( base_url('district/manage_volumes/2') );			
	}
	
	public function add_volume_step1($start_message)
	{
		// initialise method
		$session = session();
		$volumes_model = new Volumes_Model();
		
		// set messages
		switch ($start_message) 
			{
				case 0:
					$session->set('message_1', 'Add a new volume period for => '.$session->district_name);
					$session->set('message_class_1', 'alert alert-primary');
					$session->set('message_2', '');
					$session->set('message_class_2', '');
					
					$session->set('type', '');
					$session->set('volume_from', '');
					$session->set('volume_to', '');
					$session->set('volume', '');
					break;
				case 1:
					break;
				case 2:
					$session->set('message_1', 'Add a new volume period for => '.$session->district_name);
					$session->set('message_class_1', 'alert alert-primary');
					break;
			}
		
		// show surnames
		echo view('templates/header');
		echo view('linBMD2/add_volume');
		echo view('templates/footer');	
	}
	
	public function add_volume_step2()
	{
		// initialise method
		$session = session();
		$volumes_model = new Volumes_Model();
		$districts_model = new Districts_Model();
		
		// get input
		$session->set('type', $this->request->getPost('type'));
		$session->set('volume_from', $this->request->getPost('volume_from'));
		$session->set('volume_to', $this->request->getPost('volume_to'));
		$session->set('volume', $this->request->getPost('volume'));
		
		// do tests
		
		// type selected?
		if ( $session->type == 'S' )
			{
				$session->set('message_2', 'Please select a type from the drop down list.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('district/add_volume_step1/2') );
			}
			
		// blanks - all fields are mandatory
		if ( $session->volume_from == '' OR $session->volume_to == '' OR $session->volume == '' )
			{
				$session->set('message_2', 'All fields must be entered.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('district/add_volume_step1/2') );
			}	
		
		// volumes numeric
		if ( ! is_numeric($session->volume_from) OR ! is_numeric($session->volume_to) )
			{
				$session->set('message_2', 'From and To periods must be numeric.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('district/add_volume_step1/2') );
			}
		
		// volume from
		$quarter = substr($session->volume_from, -2);
		$year = substr($session->volume_from, 0, 4);
		
		if ( $quarter < 01 OR $quarter > 04 )
			{
				$session->set('message_2', 'Period from quarter not in quarter range.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('district/add_volume_step1/2') );
			}
		
		if ( $year < 1837 )
			{
				$session->set('message_2', 'Period from year cannot be less than 1837.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('district/add_volume_step1/2') );
			}
		
		// volume to
		$quarter = substr($session->volume_to, -2);
		$year = substr($session->volume_to, 0, 4);
		
		if ( $quarter != 99 )
			{
				if ( $quarter < 01 OR $quarter > 04 )
					{
						$session->set('message_2', 'Period to quarter not in month range.');
						$session->set('message_class_2', 'alert alert-danger');
						return redirect()->to( base_url('district/add_volume_step1/2') );
					}
			}
		
		if ( $year < 1837 )
			{
				$session->set('message_2', 'Period to year cannot be less than 1837.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('district/add_volume_step1/2') );
			}
			
		// does a period already exist for this district and type
		$vol_exists	=	$volumes_model
						->where('district_index', $session->district_index)
						->where('BMD_type', $session->type)
						->where('volume_from', $session->volume_from)
						->where('volume_to', $session->volume_to)
						->find();
		if ( $vol_exists )
			{
				$session->set('message_2', 'A volume record already exists for this District, type and From - To Period with volume = '.$vol_exists[0]['volume']);
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('district/add_volume_step1/2') );
			}
		
		// do volume periods exist which includes the entered period
		$vol_exists	=	$volumes_model
						->where('district_index', $session->district_index)
						->where('BMD_type', $session->type)
						->where('volume_from <=', $session->volume_from)
						->where('volume_to >=', $session->volume_to)
						->find();
		if ( $vol_exists )
			{
				$session->set('message_2', 'A volume record already exists whch includes this District, type and From - To Period.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('district/add_volume_step1/2') );
			}
			
		// do volume periods exist which overlap the entered period
		$vol_exists	=	$volumes_model
						->where('district_index', $session->district_index)
						->where('BMD_type', $session->type)
						->where('volume_from <=', $session->volume_from)
						->where('volume_to >=', $session->volume_from)
						->find();
		if ( $vol_exists )
			{
				$session->set('message_2', 'A volume record already exists whch partially includes this District, type and From Period.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('district/add_volume_step1/2') );
			}
			
		// do volume periods exist which overlap the entered period
		$vol_exists	=	$volumes_model
						->where('district_index', $session->district_index)
						->where('BMD_type', $session->type)
						->where('volume_from >=', $session->volume_to)
						->where('volume_to <=', $session->volume_to)
						->find();
		if ( $vol_exists )
			{
				$session->set('message_2', 'A volume record already exists whch partially includes this District, type and To period.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('district/add_volume_step1/2') );
			}
			
		// all OK
		
		// insert record to DB.
		$data =	[
					'district_index' => $session->district_index,
					'volume_from' => $session->volume_from,
					'volume_to' => $session->volume_to,
					'volume' => $session->volume,
					'BMD_type' => $session->type
				];	
		$volumes_model->insert($data);
		
		// go round again
		$session->set('message_2', 'Your new volume record has been added.');
		$session->set('message_class_2', 'alert alert-success');
		return redirect()->to( base_url('district/manage_volumes/2') );	
	}
	
	public function add_synonym_step1($start_message)
	{
		// initialise method
		$session = session();
		$volumes_model = new Volumes_Model();
		$districts_model = new Districts_Model();
		
		// set messages
		switch ($start_message) 
			{
				case 0:
					$session->set('message_1', 'Add a new synonym for => '.$session->district_name);
					$session->set('message_class_1', 'alert alert-primary');
					$session->set('message_2', '');
					$session->set('message_class_2', '');
					
					$session->set('synonym', '');
					break;
				case 1:
					break;
				case 2:
					$session->set('message_1', 'Add a new synonym for => '.$session->district_name);
					$session->set('message_class_1', 'alert alert-primary');
					break;
			}
		
		// show surnames
		echo view('templates/header');
		echo view('linBMD2/add_synonym');
		echo view('templates/footer');	
	}
	
	public function add_synonym_step2()
	{
		// initialise method
		$session = session();
		$volumes_model = new Volumes_Model();
		$districts_model = new Districts_Model();
		
		// get input
		$session->set('synonym', $this->request->getPost('synonym'));
		
		// do tests
		
		// does it exist already
		$dis_exists =	$districts_model
						->where('District_name', $session->synonym)
						->find();
		if ( $dis_exists )
			{
				$session->set('message_2', 'This synonym already exists in the Districts database => '.$dis_exists[0]['District_name']);
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('district/add_synonym_step1/2') );
			}
		
		// OK tests passed
		
		// insert district record to districts master
		$data =	[
					'District_name' => strtoupper($session->synonym),
					'Added_by_user' => $session->identity_userid,
					'active' => 'YES',
				];	
		$id = $districts_model->insert($data);
		
		// get volumes from based on district
		$vol_exists =	$volumes_model
						->where('district_index', $session->district_index)
						->findAll();
						
		// insert volumes
		foreach ( $vol_exists as $vol )
			{
				$data =	[
							'district_index' => $id,
							'volume_from' => $vol['volume_from'],
							'volume_to' => $vol['volume_to'],
							'volume' => $vol['volume'],
							'BMD_type' => $vol['BMD_type'],
						];	
				$volumes_model->insert($data);
			}
		
		// go round again
		$session->set('message_2', 'Your new synonym has been created.');
		$session->set('message_class_2', 'alert alert-success');
		return redirect()->to( base_url('district/manage_districts/0') );	
	}
	
	public function add_district_step1($start_message)
	{
		// initialise method
		$session = session();
		$districts_model = new Districts_Model();
		
		// set messages
		switch ($start_message) 
			{
				case 0:
					$session->set('message_1', 'Add a new District.');
					$session->set('message_class_1', 'alert alert-primary');
					$session->set('message_2', '');
					$session->set('message_class_2', '');
					
					$session->set('district', '');
					break;
				case 1:
					break;
				case 2:
					$session->set('message_1', 'Add a new District.');
					$session->set('message_class_1', 'alert alert-primary');
					break;
			}
		
		// show district
		echo view('templates/header');
		echo view('linBMD2/add_district');
		echo view('templates/footer');	
	}
	
	public function add_district_step2()
	{
		// initialise method
		$session = session();
		$districts_model = new Districts_Model();
		
		// get input
		$session->set('district', $this->request->getPost('district'));
		
		// do tests
		
		// does it exist already
		$dis_exists =	$districts_model
						->where('District_name', $session->district)
						->find();
		if ( $dis_exists )
			{
				$session->set('message_2', 'This district already exists in the Districts database => '.$dis_exists[0]['District_name']);
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('district/add_district_step1/2') );
			}
		
		// OK tests passed
		
		// insert district record to districts master
		$data =	[
					'District_name' => strtoupper($session->district),
					'Added_by_user' => $session->identity_userid,
					'active' => 'YES',
				];	
		$id = $districts_model->insert($data);
		
		// go round again
		$session->set('message_2', 'Your new District has been created. Now use Manage Volumes to add volumes for this district.');
		$session->set('message_class_2', 'alert alert-success');
		return redirect()->to( base_url('district/manage_districts/2') );	
	}
	
	public function dis_vol_problems()
	{
		// initialise method
		$session = session();
		$volumes_model = new Volumes_Model();
		$districts_model = new Districts_Model();
		$transcription_cycle_model = new Transcription_Cycle_Model;
		$messages = array();
		$counts =	[
						'novol' => 0,
						'lte3' => 0,
						'lte3' => 0,
						'notb' => 0,
						'notm' => 0,
						'notd' => 0,
						'nodis' => 0,
					];
		$session->districts_cycle =	$transcription_cycle_model
			->where('project_index', $session->current_project[0]['project_index'])
			->where('BMD_cycle_type', 'DISTR')
			->orderby('BMD_cycle_sort')
			->findAll();
		
		// check districts have volumes
		
		// Get districts
		$districts = $districts_model
			->findAll();
						
		// read districts and find volumes
		foreach ( $districts as $district )
			{
				$volumes =	$volumes_model
							->where('district_index', $district['district_index'])
							->findAll();
		
				// no volumes for the district?
				if ( ! $volumes )
					{
						$messages[] =	[
											$district['District_name'],
											$district['Added_by_user'],
											$district['active'],
											'No volumes found for this district',
											'novol',
										];
						++$counts['novol'];
					}
				else
					{
						// volumes are found
					
						// not enough volumes for the district?
						if ( count($volumes) <= 2 )
							{
								$messages[] =	[
													$district['District_name'],
													$district['Added_by_user'],
													$district['active'],
													'Less than or equal to 2 volumes found for this district',
													'lte3',
												];
								++$counts['lte3'];
							}
					
						// no volumes for births?
						$no_type = 0;
						foreach ( $volumes as $volume )
							{
								if ( $volume['BMD_type'] == 'B' )
									{
										$no_type = 1;
										break;
									}
							}
						if ( $no_type == 0)
							{
								$messages[] =	[
													$district['District_name'],
													$district['Added_by_user'],
													$district['active'],
													'No Birth volumes found for this district',
													'notb',
												];
								++$counts['notb'];
							}
							
						// no volumes for marriages?
						$no_type = 0;
						foreach ( $volumes as $volume )
							{
								if ( $volume['BMD_type'] == 'M' )
									{
										$no_type = 1;
										break;
									}
							}
						if ( $no_type == 0)
							{
								$messages[] =	[
													$district['District_name'],
													$district['Added_by_user'],
													$district['active'],
													'No Marriage volumes found for this district',
													'notm',
												];
								++$counts['notm'];
							}
					
						// no volumes for deaths?
						$no_type = 0;
						foreach ( $volumes as $volume )
							{
								if ( $volume['BMD_type'] == 'D' )
									{
										$no_type = 1;
										break;
									}
							}
						if ( $no_type == 0)
							{
								$messages[] =	[
													$district['District_name'],
													$district['Added_by_user'],
													$district['active'],
													'No Death volumes found for this district',
													'notd',
												];
								++$counts['notd'];
							}
					}		
			}
			
		// read volumes and find missing districts
		$volumes =	$volumes_model
					->findAll();
					
		foreach ( $volumes as $volume )
			{
				// get district
				$districts =	$districts_model
								->where('district_index', $volume['district_index'])
								->find();
				// any found?
				if ( ! $districts )
					{
						$messages[] =	[
											$volume['volume_index'],
											$volume['volume_from'],
											$volume['BMD_type'],
											'No District found for this volume',
											'nodis',
										];
						++$counts['nodis'];
					}
			}
							
		// show results
		$session->incon = count($messages);
		$session->messages_store = $messages;
		$session->messages = $messages;
		$session->messages_count = $counts;
		$session->set('message_1', 'Districts / Volume Inconsistencies');
		$session->set('message_class_1', 'alert alert-primary');
		$session->set('message_2', 'There appear to be '.$session->incon.' inconsistencies between Districts and Volumes.');
		$session->set('message_class_2', 'alert alert-danger');

		// show inconsistencies
		echo view('templates/header');
		echo view('linBMD2/show_inconsistencies');
		echo view('templates/footer');
	}
	
	public function show_incons_type($incon_type)
	{
		// initialise method
		$session = session();
		
		// select records requested
		switch ($incon_type) 
			{
				case 'all':
					$session->messages = $session->messages_store;
					break;
				case 'novol':
					foreach ( $session->messages_store as $message )
						{
							if ( $message[4] == 'novol' )
								{
									$messages[] = $message;
								}
						}
					$session->messages = $messages;
					$session->set('message_2', 'There are '.count($session->messages).' Districts with no Volumes.');
					$session->set('message_class_2', 'alert alert-primary');
					break;
				case 'lte3':
					foreach ( $session->messages_store as $message )
						{
							if ( $message[4] == 'lte3' )
								{
									$messages[] = $message;
								}
						}
					$session->messages = $messages;
					$session->set('message_2', 'There are '.count($session->messages).' Districts with few Volumes.');
					$session->set('message_class_2', 'alert alert-primary');
					break;
				case 'notb':
					foreach ( $session->messages_store as $message )
						{
							if ( $message[4] == 'notb' )
								{
									$messages[] = $message;
								}
						}
					$session->messages = $messages;
					$session->set('message_2', 'There are '.count($session->messages).' Districts no Births Volumes.');
					$session->set('message_class_2', 'alert alert-primary');
					break;
				case 'notm':
					foreach ( $session->messages_store as $message )
						{
							if ( $message[4] == 'notm' )
								{
									$messages[] = $message;
								}
						}
					$session->messages = $messages;
					$session->set('message_2', 'There are '.count($session->messages).' Districts no Marriages Volumes.');
					$session->set('message_class_2', 'alert alert-primary');
					break;
				case 'notd':
					foreach ( $session->messages_store as $message )
						{
							if ( $message[4] == 'notd' )
								{
									$messages[] = $message;
								}
						}
					$session->messages = $messages;
					$session->set('message_2', 'There are '.count($session->messages).' Districts no Deaths Volumes.');
					$session->set('message_class_2', 'alert alert-primary');
					break;
				case 'nodis':
					foreach ( $session->messages_store as $message )
						{
							if ( $message[4] == 'nodis' )
								{
									$messages[] = $message;
								}
						}
					$session->messages = $messages;
					$session->set('message_2', 'There are '.count($session->messages).' Volumes with no District.');
					$session->set('message_class_2', 'alert alert-primary');
					break;
			}			
							
		// show results
		$session->set('message_1', 'Districts / Volume Inconsistencies');
		$session->set('message_class_1', 'alert alert-primary');

		// show inconsistencies
		echo view('templates/header');
		echo view('linBMD2/show_inconsistencies');
		echo view('templates/footer');
	}
	
	public function convert_to_synonym_step1($start_message)
	{
		// initialise method
		$session = session();
		$volumes_model = new Volumes_Model();
		$districts_model = new Districts_Model();
		
		// set messages
		switch ($start_message) 
			{
				case 0:
					$session->set('message_1', 'Convert to synonym => '.$session->synonym_name);
					$session->set('message_class_1', 'alert alert-primary');
					$session->set('message_2', '');
					$session->set('message_class_2', '');
					
					$session->set('district', '');
					break;
				case 1:
					break;
				case 2:
					$session->set('message_1', 'Convert to synonym => '.$session->synonym_name);
					$session->set('message_class_1', 'alert alert-primary');
					break;
			}
		
		// show surnames
		echo view('templates/header');
		echo view('linBMD2/convert_to_synonym');
		echo view('templates/footer');	
	}
	
	public function convert_to_synonym_step2()
	{
		// initialise method
		$session = session();
		$volumes_model = new Volumes_Model();
		$districts_model = new Districts_Model();
		
		// does the based on synonym already have volumes?
		$dis_vols =	$volumes_model
					->where('district_index', $session->synonym_index)
					->findAll();
		// found?
		if ( $dis_vols )
			{
				$session->set('message_2', 'You are trying to convert '.$session->synonym_name.' to a synonym but it already has volumes.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('district/convert_to_synonym_step1/2') );
			}
			
		// get input
		$session->set('district', $this->request->getPost('district'));

		// does the entered district exist
		$districts =	$districts_model
						->where('District_name', $session->district)
						->find();
		// found?
		if ( ! $districts )
			{
				$session->set('message_2', 'The district you entered does not exist. Check your entry.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('district/convert_to_synonym_step1/2') );
			}
			
		// do volumes exist for the entered district
		$volumes =	$volumes_model
					->where('district_index', $districts[0]['district_index'])
					->findAll();
		// found ?
		if ( ! $volumes )
			{
				$session->set('message_2', 'No volumes found for the district you entered. Check your entry.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('district/convert_to_synonym_step1/2') );
			}
			
		// OK tests passed
						
		// insert volumes
		foreach ( $volumes as $vol )
			{
				$data =	[
							'district_index' => $session->synonym_index,
							'volume_from' => $vol['volume_from'],
							'volume_to' => $vol['volume_to'],
							'volume' => $vol['volume'],
							'BMD_type' => $vol['BMD_type'],
						];	
				$volumes_model->insert($data);
			}
		
		// go round again
		$session->set('message_2', 'Your new synonym has been created => '.$session->synonym_name);
		$session->set('message_class_2', 'alert alert-success');
		return redirect()->to( base_url('district/manage_districts/2') );	
	}
}
