<?php namespace App\Controllers;

	function database_backup()
	{
		// initialise
		$session = session();
		// delete old file
		if ( file_exists(getcwd().'/Users/'.$session->current_project[0]['project_name'].'/'.$session->identity_userid.'/Backups/freecomett.sql') )
			{ 
				unlink(getcwd().'/Users/'.$session->current_project[0]['project_name'].'/'.$session->identity_userid.'/Backups/freecomett.sql');
			}
		// backup the database
		exec("mysqldump  --user='freecomett' --password='freecomett' --databases freecomett > ".getcwd().'/Users/'.$session->current_project[0]['project_name'].'/'.$session->identity_userid.'/Backups/freecomett.sql');

		// check file exists
		if ( ! file_exists(getcwd().'/Users/'.$session->current_project[0]['project_name'].'/'.$session->identity_userid.'/Backups/freecomett.sql') )
			{
				$session->set('message_2', 'The webBMD backup failed. Send email to Send email to '.$session->linbmd2_email);
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('housekeeping/index/2') );
			}
			
		// does it contain data?
		if ( filesize(getcwd().'/Users/'.$session->current_project[0]['project_name'].'/'.$session->identity_userid.'/Backups/freecomett.sql') == 0 )
			{
				$session->set('message_2', 'The FreeComETT backup failed. Send email to '.$session->linbmd2_email.' File size 0');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('housekeeping/index/2') );
			}
			
		// set flag
		$session->set('database_backup_performed', 1);
		// all good - bye bye
	}
	
	
