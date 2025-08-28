<?php

use App\Models\Districts_Model;
use App\Models\Allocation_Model;
use App\Models\Syndicate_Model;
use App\Models\User_Parameters_Model;
use App\Models\Parameter_Model;
use App\Models\Identity_Model;
use App\Models\Transcription_Cycle_Model;
use App\Models\Projects_Model;
use App\Models\Project_Types_Model;
use App\Models\Reporting_Model;
use App\Models\Transcription_Model;
use CodeIgniter\I18n\Time;

function load_report_data($detail_line, $action)
	{
		// inialise
		$session = session();
		$projects_model = new Projects_Model();
		$identity_model = new Identity_Model();
		$transcription_model = new Transcription_Model();
		$syndicate_model = new Syndicate_Model();
		$allocation_model = new Allocation_Model();
		$reporting_model = new Reporting_Model();
		
		// set unknowns
		$project_name = 'Unknown';
		$transcriber_userid = 'Unknown';
		$transcription_name = 'Unknown';
		$syndicate_name = 'Unknown';
		$allocation_name = 'Unknown';
		$transcription_action = 'Unknown';
		
		// get project name
		$report_data = $projects_model
			->where('project_index', $detail_line['project_index'])
			->find();
		if ( $report_data )
			{
				$project_name = $report_data[0]['project_name'];
			}
		 
		// get transcriber userid
		$report_data = $identity_model
			->where('BMD_identity_index', $detail_line['BMD_identity_index'])
			->find();
		if ( $report_data )
			{
				$transcriber_userid = $report_data[0]['BMD_user'];
			}
				
		// get transcription record
		$transcription_data = $transcription_model 
			->where('BMD_header_index', $detail_line['BMD_header_index'])
			->find();
		if ( $transcription_data )
			{
				// set transcription name
				$transcription_name = $transcription_data[0]['BMD_file_name'];
			
				// get syndicate id
				$report_data = $syndicate_model
					->where('BMD_syndicate_index', $transcription_data[0]['BMD_syndicate_index'])
					->find();
				if ( $report_data )
					{
						$syndicate_name = $report_data[0]['BMD_syndicate_name'];
					}
			
				// get allocation id
				$report_data = $allocation_model
					->where('BMD_allocation_index', $transcription_data[0]['BMD_allocation_index'])
					->find();
				if ( $report_data )
					{
						$allocation_name = $report_data[0]['BMD_allocation_name'];
					}
				
				// get last action
				$transcription_action = $transcription_data[0]['BMD_last_action'];
			}
	
		// get date fields
		$time = Time::parse($detail_line['Change_date'], 'Europe/London', 'en_UK');
					
		// load totals by year / dayofyear
		$report_record = $reporting_model
			->where('report_project', $project_name)
			->where('report_syndicate', $syndicate_name)
			->where('report_transcriber', $transcriber_userid)
			->where('report_allocation', $allocation_name)
			->where('report_transcription', $transcription_name)
			->where('report_last_action', $transcription_action)
			->where('report_year', $time->year)
			->where('report_yday', $time->dayOfYear)
			->find();
				
		if ( $report_record )
			{
				// set quantity
				if ( $action == 'add' )
					{
						$qty = $report_record[0]['report_quantity'] + 1;
					}
				else
					{
						$qty = $report_record[0]['report_quantity'] - 1;
					}
				// update	
				$reporting_model
					->set(['report_quantity' => $qty])
					->update($report_record[0]['report_index']);		
			}
		else
			{		
				// record not found - add it
				// set quantity
				if ( $action == 'add' )
					{
						$qty = 1;
					}
				else
					{
						$qty = -1;
					}
				// insert
				$reporting_model
					->set(['report_project' => $project_name])
					->set(['report_syndicate' => $syndicate_name])
					->set(['report_transcriber' => $transcriber_userid])
					->set(['report_allocation' => $allocation_name])
					->set(['report_transcription' => $transcription_name])
					->set(['report_last_action' => $transcription_action])
					->set(['report_year' => $time->year])
					->set(['report_quarter' => $time->quarter])
					->set(['report_mon' => $time->month])
					->set(['report_yweek' => $time->weekOfYear])
					->set(['report_mweek' => $time->weekOfMonth])
					->set(['report_yday' => $time->dayOfYear])
					->set(['report_mday' => $time->day])
					->set(['report_wday' => $time->dayOfWeek])
					->set(['report_quantity' => $qty])
					->insert();
			}	
	}
