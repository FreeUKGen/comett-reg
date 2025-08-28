<?php namespace App\Controllers;

use App\Models\Districts_Model;
use App\Models\Parameter_Model;
use App\Models\Volumes_Model;
use App\Models\Firstname_Model;
use App\Models\Surname_Model;
use App\Models\Detail_Data_Model;
use App\Models\Header_Table_Details_Model;
use App\Models\Allocation_Model;
use App\Models\Table_Details_Model;
use App\Models\Def_Fields_Model;
use App\Models\Def_Image_Model;
use App\Models\Def_Ranges_Model;
use App\Models\Identity_Model;
use App\Models\Submitters_Model;
use App\Models\Detail_Comments_Model;
use App\Models\Transcription_Model;
use App\Models\Transcription_Detail_Def_Model;
use App\Models\Identity_Last_Indexes_Model;
use App\Models\Roles_Model;
use App\Models\Syndicate_Model;
use App\Models\Reporting_Model;
use App\Models\Projects_Model;
use CodeIgniter\HTTP\Response;


class Report extends BaseController
{
	function __construct() 
	{
        helper('common');
        helper('backup');
        helper('remote');
        helper('report');
    }
	
	public function report_step1($start_message)
	{
		// initialise
		$session = session();
		
		switch ($start_message) 
			{
				case 0:
					// message defaults
					$session->set('message_1', 'Choose the Report action you wish to perform.' );
					$session->set('message_class_1', 'alert alert-primary');
					$session->set('message_2', '');
					$session->set('message_class_2', '');
					break;
				case 1:
					break;
				case 2:
					$session->set('message_1', 'Choose the Report action you wish to perform.' );
					$session->set('message_class_1', 'alert alert-primary');
					break;
				default:
			}
			
		// create dimensions
		$report_axes[0] = ['title' => 'None', 'field' => 'none'];
		$report_axes[1] = ['title' => 'Year', 'field' => 'report_year'];
		$report_axes[2] = ['title' => 'Month', 'field' => 'report_mon'];
		$report_axes[3] = ['title' => 'Day', 'field' => 'report_mday'];
		$report_axes[4] = ['title' => 'Project', 'field' => 'report_project'];
		$report_axes[5] = ['title' => 'Syndicate', 'field' => 'report_syndicate'];
		$report_axes[6] = ['title' => 'Transcriber', 'field' => 'report_transcriber'];
		$report_axes[7] = ['title' => 'Allocation', 'field' => 'report_allocation'];
		$report_axes[8] = ['title' => 'Transcription', 'field' => 'report_transcription'];
		$report_axes[9] = ['title' => 'Last Action', 'field' => 'report_last_action'];
		$session->report_axes = $report_axes;
	
		// default dimensions
		$session->report_index = ['report_year', 'report_project', 'report_syndicate', 'none', 'none', 'none', 'none', 'none', 'none'];
		
		// default filters
		$session->filter_index = ['none', 'none', 'none', 'none', 'none', 'none', 'none', 'none', 'none'];

		// default dates
		$session->from_date = "01/01/0000";
		$session->to_date = "31/12/9999";
		
		// show views
		echo view('templates/header');
		echo view('linBMD2/report_menu');
		echo view('templates/footer');
	}
	
	public function create_report_data($start_message)
	{
		// initialise
		$session = session();
		$detail_data_model = new Detail_Data_Model();
		$reporting_model = new Reporting_Model();
		$projects_model = new Projects_Model();
		$identity_model = new Identity_Model();
		$transcription_model = new Transcription_Model();
		$syndicate_model = new Syndicate_Model();
		$allocation_model = new Allocation_Model();
		$parameter_model = new Parameter_Model();
		ini_set("memory_limit","1024M");
		ini_set("max_execution_time", 0);
		
		// get entered password
		$entered_password = $this->request->getPost('report_rebuild_password');
		
		// get defined password
		$parameter = $parameter_model->where('Parameter_key', 'reportRebuildPassword')->findAll();
	
		// test password
		if ( $entered_password != $parameter[0]['Parameter_value'] )
			{
				$session->set('message_2', 'Rebuild password is not correct!');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('database/database_step1/1') );
			}
		
		// this method will build / rebuild the report table from scratch
		// first empty the report table
		$reporting_model
			->truncate();
			
		// get all detail records
		$all_detail = $detail_data_model
			->orderby('Change_date', 'ASC')
			->findAll();

		// read all detail data
		foreach ( $all_detail as $detail_line )
			{
				// setup report data
				load_report_data($detail_line, 'add');
			}
			
		// go back to menu
		$session->set('message_2', 'Report data has been built / rebuilt.' );
		$session->set('message_class_2', 'alert alert-success');
		return redirect()->to( base_url('database/database_step1/1') );
	}

	public function show_report_data()
	{
		// initialise
		$session = session();
		$reporting_model = new Reporting_Model();
		
		// $session->report_index contains the report fields to analyse
		// $session->filter_index contains the filters to apply
		// build the sql query from the indexes.
		$sql = '';
		$where = array();
		foreach ( $session->report_index as $key => $index )
			{
				// build sql
				if ( $index != 'none' )
					{
						$sql = $sql.$index.', ';
						
						// create filter
						if ( $session->filter_index[$key] != 'none' 
							AND $session->filter_index[$key] != 'Select...'
							AND $session->filter_index[$key] != 'None' )
							{
								$where[$index] = $session->filter_index[$key];
							}
					}
			}
		
		// set from date
		$from_date = explode('/', $session->from_date);
		$where_from_date = array();
		$where_from_date['report_mday >='] = $from_date[0];
		$where_from_date['report_mon >='] = $from_date[1];
		$where_from_date['report_year >='] = $from_date[2];
		
		// set to date
		$to_date = explode('/', $session->to_date);
		$where_to_date = array();
		$where_to_date['report_mday <='] = $to_date[0];
		$where_to_date['report_mon <='] = $to_date[1];
		$where_to_date['report_year <='] = $to_date[2];

		// clean up sql
		$sql = trim($sql);
		$sql = trim($sql, ',');

		// get reporting data
		$session->reporting_data = $reporting_model
			->select($sql)
			->where($where)
			->where($where_from_date)
			->where($where_to_date)
			->selectSum('report_quantity')
			->orderby($sql)
			->groupBy($sql)
			->findAll();
			
		// create filter list for dimensions
		$filters = array();
		$total_qty = 0;
		foreach ( $session->reporting_data as $report_line )
			{
				// accumulate qty
				$total_qty = $total_qty + $report_line['report_quantity'];
				
				// for each line, read the dimensions array
				foreach ( $session->report_index as $key => $index )
					{						
						// don't create filter array if index = none
						if ( $index != 'none' )
							{
								// create filter array if it doesn't exist for this key
								if ( ! isset($filters[$key]) )
									{
										$filters[$key] = array();
										$filters[$key][] = 'Select...';
										$filters[$key][] = 'none';
									}
								
								// test for element in array, add to array if not found
								if ( ! in_array($report_line[$index], $filters[$key]) )
									{
										$filters[$key][] = $report_line[$index];
									}
							}
						else
							{
								// set array for none values
								if ( ! isset($filters[$key]) )
									{
										$filters[$key][] = 'None';
									}
							}
					}
			}

		// sort the filter arrays
		foreach ( $session->report_index as $key => $index )
			{
				natcasesort($filters[$key]);
			}
			
		// set session
		$session->filters = $filters;
		$session->selected_filter = 'Select...';
		$session->total_qty = $total_qty;
		
		// show report	
		$session->set('message_1', 'FreeComETT Reporting.' );
		$session->set('message_class_1', 'alert alert-primary');
		$session->set('message_2', 'Please refine your report by choosing the report dimensions and filters that you wish to analyse.');
		$session->set('message_class_2', 'alert alert-info');
			
		// show views
		echo view('templates/header');
		echo view('linBMD2/report_results');
		echo view('linBMD2/searchTableNew');
		echo view('linBMD2/sortTableNew');
		echo view('templates/footer');
	}
	
	public function report_axes()
	{
		// initialise
		$session = session();
		
		// get inputs
		$report_index[0] = $this->request->getPost('level_0');
		$report_index[1] = $this->request->getPost('level_1');
		$report_index[2] = $this->request->getPost('level_2');
		$report_index[3] = $this->request->getPost('level_3');
		$report_index[4] = $this->request->getPost('level_4');
		$report_index[5] = $this->request->getPost('level_5');
		$report_index[6] = $this->request->getPost('level_6');
		$report_index[7] = $this->request->getPost('level_7');
		$report_index[8] = $this->request->getPost('level_8');
		$session->report_index = $report_index;
		
		$filter_index[0] = $this->request->getPost('filter_0');
		$filter_index[1] = $this->request->getPost('filter_1');
		$filter_index[2] = $this->request->getPost('filter_2');
		$filter_index[3] = $this->request->getPost('filter_3');
		$filter_index[4] = $this->request->getPost('filter_4');
		$filter_index[5] = $this->request->getPost('filter_5');
		$filter_index[6] = $this->request->getPost('filter_6');
		$filter_index[7] = $this->request->getPost('filter_7');
		$filter_index[8] = $this->request->getPost('filter_8');
		$session->filter_index = $filter_index;
		
		$session->from_date = $this->request->getPost('from_date');
		$session->to_date = $this->request->getPost('to_date');
				
		// redirect
		return redirect()->to( base_url('report/show_report_data') );
	}								
	
	public function report_create_csv()
	{
		// initialise
		$session = session();
		
		// build csv file name
		$csv_file = getcwd().'/Users/'.$session->current_project[0]['project_name'].'/'.$session->identity_userid.'/CSV_Files/freecomett_report.csv';
		
		// open csv file for writing
		$f = fopen($csv_file, 'w');

		if ( $f === false ) 
			{
				$session->set('message_2', 'Cannot open CSV file for report data. Error in Reports/report_create_csv. Send email to '.$session->linbmd2_email.' to report this error.');
				$session->set('message_class_2', 'alert alert-warning');
				return redirect()->to( base_url('report/report_step1/1') );
			}

		// write each row at a time to a file
		foreach ( $session->reporting_data as $row ) 
			{
				fputcsv($f, $row);
			}

		// close the file
		fclose($f);
		
		// download the file
		return $this->response->download($csv_file, null);		
	}		
}
			
	
