<?php namespace App\Controllers;

use App\Models\Header_Model;
use App\Models\Syndicate_Model;
use App\Models\Allocation_Model;
use App\Models\Detail_Data_Model;
use App\Models\Surname_Model;
use App\Models\Firstname_Model;
use App\Models\Districts_Model;
use App\Models\Volumes_Model;

class Marriages extends BaseController
{
	function __construct() 
	{
        helper('common');
        helper('update_names');
        helper('backup');
        helper('transcribe');
        helper('email');
        helper('report');
    }
	
	public function transcribe_marriages_step1($start_message)
	{			
		// initialise step1 = start message, controller, controller title - method is in transcribe_helper
		transcribe_initialise_step1($start_message, 'marriages', 'marriages');
		// show views - method is in transcribe helper
		set_data_group_and_show(1);
	}
	
	public function transcribe_marriages_step2()
	{
		// initialise method
		$session = session();
		
		// what data am I getting and validating?
		switch ($session->show_view_type) 
			{
				// standard data entry
				case 'transcribe':
					transcribe_get_transcribe_inputs('marriages');
					transcribe_validate_transcribe_inputs('marriages');
					break;
				// confirm district
				case 'confirm_district':
					transcribe_get_confirm_district_inputs('marriages');
					transcribe_validate_confirm_district_inputs('marriages');
					if ( $session->message_error == 'error' )
					{
						return redirect()->to( base_url('marriages/transcribe_marriages_step1/1') );
					}
					return redirect()->to( base_url('email/send_email/district') );
					transcribe_validate_transcribe_inputs('marriages');
					break;
				// confirm page
				case 'confirm_page':
					transcribe_get_confirm_page_inputs('marriages');
					transcribe_validate_confirm_page_inputs('marriages');
					if ( $session->message_error == 'error' )
					{
						return redirect()->to( base_url('marriages/transcribe_marriages_step1/1') );
					}
					transcribe_validate_transcribe_inputs('marriages');
					break;
				// confirm volume
				case 'confirm_volume':
					transcribe_get_confirm_volume_inputs('marriages');
					transcribe_validate_confirm_volume_inputs('marriages');
					if ( $session->message_error == 'error' )
					{
						return redirect()->to( base_url('marriages/transcribe_marriages_step1/1') );
					}
					transcribe_validate_transcribe_inputs('marriages');
					break;
				// confirm registration
				case 'confirm_registration':
					transcribe_get_confirm_registration_inputs('marriages');
					transcribe_validate_confirm_registration_inputs('marriages');
					if ( $session->message_error == 'error' )
					{
						return redirect()->to( base_url('marriages/transcribe_marriages_step1/1') );
					}
					transcribe_validate_transcribe_inputs('marriages');
					break;
				// confirm firstname
				case 'confirm_firstnames':
					transcribe_get_confirm_firstname_inputs('marriages');
					transcribe_validate_confirm_firstname_inputs('marriages');
					if ( $session->message_error == 'error' )
					{
						return redirect()->to( base_url('marriages/transcribe_marriages_step1/1') );
					}
					transcribe_validate_transcribe_inputs('marriages');
					break;
				// confirm surname
				case 'confirm_surname':
					transcribe_get_confirm_surname_inputs('marriages');
					transcribe_validate_confirm_surname_inputs('marriages');
					if ( $session->message_error == 'error' )
					{
						return redirect()->to( base_url('marriages/transcribe_marriages_step1/1') );
					}
					transcribe_validate_transcribe_inputs('marriages');
					break;
				// confirm same as last line
				case 'confirm_same':
					transcribe_get_confirm_same('marriages');
					transcribe_validate_confirm_same('marriages');
					if ( $session->message_error == 'error' )
					{
						return redirect()->to( base_url('marriages/transcribe_marriages_step1/1') );
					}
					transcribe_validate_transcribe_inputs('marriages');
					break;
			}
					
		// is there an error?
		if ( $session->message_error == 'error' )
			{
				return redirect()->to( base_url('marriages/transcribe_marriages_step1/1') );
			}
			
		// if verify onthefly active, verify
		switch ($session->current_project[0]['project_index']) 
			{
				case 1: // FreeBMD verify transcription file
					if ( $session->current_identity[0]['verify_mode'] == 'onthefly' AND $session->verify_onthefly == 0 )
						{
							return redirect()->to( base_url('transcribe/verify_onthefly') );
						}
					break;
				case 2: // FreeREG verify transcription file
					break;
				case 3: // FreeCEN verify transcription file
					break;
			}
			
		// all good - write / update data
		transcribe_update('marriages');
		
		// go round again
		switch ($session->BMD_cycle_code) 
			{
				case 'VERIT': // verify transcription file
					return redirect()->to( base_url('transcribe/verify_step1/'.$session->current_transcription[0]['BMD_header_index']) );
					break;
				default:
					return redirect()->to( base_url('marriages/transcribe_marriages_step1/0') );
					break;
			}
	}
	
	public function select_line($line_index)
	{
		// select the line and load session fields
		select_trans_line($line_index);
		// go back to editor					
		return redirect()->to( base_url('marriages/transcribe_marriages_step1/1') );
	}	
	
	public function delete_line_step1($line_index)
	{
		delete_line_confirm($line_index);
	}
	
	public function delete_line_step2()
	{
		delete_line_delete();
		// return
		return redirect()->to( base_url('marriages/transcribe_marriages_step1/0') );
	}
	
	public function comment_step2()
	{
		// initialse
		$session = session();
		// add/edit comments
		comment_update();
		if ( $session->message_2 != '' )
			{
				return redirect()->to( base_url('marriages/select_comment/'.$session->line_index) );
			}
		else
			{
				return redirect()->to( base_url('marriages/transcribe_marriages_step1/2') );
			}
	}
	
	public function select_comment($line_index)
	{
		// initialise
		$session = session();
		$session->set('controller', 'marriages');
		// process
		comment_select($line_index);
		// show comment page															
		echo view('templates/header');
		echo view('linBMD2/transcribe_comments_enter');
		echo view('linBMD2/transcribe_comments_show');
		echo view('templates/footer');
	}
	
	public function remove_comment($comment_line_index)
	{
		$session = session();
		$session->set('controller', 'marriages');
		comment_remove($comment_line_index);
		return redirect()->to( base_url('marriages/select_comment/'.$session->transcribe_detail[0]['BMD_index']) );
	}
}
