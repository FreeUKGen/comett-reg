<?php namespace App\Controllers;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require APPPATH.'ThirdParty/PHPMailer/src/Exception.php';
require APPPATH.'ThirdParty/PHPMailer/src/PHPMailer.php';
require APPPATH.'ThirdParty/PHPMailer/src/SMTP.php';

// a helper to send emails
class Email extends BaseController
{
	public function send_email($email_type)
	{		
		// initialise
		$session = session();
		$result = '';
		$result_dump = '';

		// set standard email fields for all type of emails
		$mail = new PHPMailer();
		$mail->SMTPDebug = 2;
		$mail->Debugoutput = 'html';
		$mail->isHTML(true);
		$mail->setFrom('freecomettdev@freeukgenealogy.org.uk');
		
		// set up message specific parameters
		switch ($email_type) 
			{
				case 'allocation':
					$leader = explode(' ', $session->current_syndicate[0]['BMD_syndicate_leader']);
					$mail->addAddress($session->current_syndicate[0]['BMD_syndicate_email']);
					$mail->addReplyTo($session->identity_emailid);
					$mail->addCC($session->identity_emailid);
					$mail->Subject = 'Message from FreeComETT - '.$session->current_project[0]['allocation_text'].' '.$session->current_allocation[0]['BMD_allocation_name'].' completed';
					$mail->Body = 	'<html>Hello '
												.$leader[0]
												.','
												.'<br><br>I completed the '.$session->current_project[0]['allocation_text'].' '
												.'<b>'
												.$session->current_allocation[0]['BMD_allocation_name']
												.'</b>'
												.' on '
												.$session->current_allocation[0]['BMD_end_date']
												.'.'
												.'<br><br>Please provide me with another '.$session->current_project[0]['allocation_text']
												.'<br><br>Thank you.'
												.'<br><br>Best wishes,'
												.'<br><br>'
												.$session->realname
												.'<br><br>'
												.$session->identity_userid;
					$mail->AltBody = 'Allocation '.$session->current_allocation[0]['BMD_allocation_name'].'completed';
					// set return message
					$session->set('message_2', 'An email was sent to the syndicate owner informing him/her that this '.$session->current_project[0]['allocation_text'].' is complete and asking for another one.');
					break;
					
				case 'BMD_file':
					// create a temp file
					$fhup = fopen(sys_get_temp_dir().$session->current_transcription[0]['BMD_file_name'].'.BMD', "w");
					fwrite($fhup, $session->BMD_data);
					$tmpf = stream_get_meta_data($fhup)['uri'];
					fclose($fhup);
					// get coordinator's firstname
					$leader = explode(' ', $session->current_syndicate[0]['BMD_syndicate_leader']);
					$mail->addAddress($session->current_syndicate[0]['BMD_syndicate_email']);
					$mail->addReplyTo($session->identity_emailid);
					$mail->addCC($session->identity_emailid);
					$mail->Subject = 'Message from FreeComETT - BMD File transcription '.$session->current_transcription[0]['BMD_file_name'].' completed';
					$mail->Body = 	'<html>'
												.'Hello '
												.$leader[0]
												.','
												.'<br><br>Please find attached the completed transcription file: '
												.'<b>'
												.$session->current_transcription[0]['BMD_file_name']
												.'</b>'
												.' of the scanned image file:  '
												.$session->current_image_file_name
												.'.'
												.'<br><br>Please provide me with another scan.'
												.'<br><br>Thank you.'
												.'<br><br>Best regards,'
												.'<br><br>'
												.$session->realname
												.'<br><br>'
												.$session->identity_userid
												.'</html>';
					$mail->AltBody = 'Transcription '.$session->current_transcription[0]['BMD_file_name'].' completed';
					$mail->AddAttachment($tmpf);
					// set return message
					$session->set('message_2', 'An email was sent to the syndicate coordinator informing him/her that the transcription, '.$session->current_transcription[0]['BMD_file_name'].', is complete and asking for another scan to transcribe.');
					break;
					
				case 'transcribe':
					$mail->addAddress($session->current_syndicate[0]['BMD_syndicate_email']);
					$mail->addCC($session->identity_emailid);
					$mail->addReplyTo($session->identity_emailid);
					$mail->Subject = $session->subject1.' - '.$session->subject2;
					$mail->Body = $session->body;
					$mail->AltBody = strip_tags($session->body);
					// add attachment if one was selected
					if ( $session->myfile != '' )
						{
							$mail->addAttachment($session->cfile);
						}
					// set return message
					$session->set('message_2', 'Your email was sent');
					break;
					
				case 'district':
					$mail->addAddress('freebmd-districts@freeukgenealogy.org.uk');
					$mail->addReplyTo($session->identity_emailid);
					$mail->addCC($session->identity_emailid);
					$mail->Subject = 'Message from FreeComETT - a district has been added to Districts Master.';
					$mail->Body = 	'<html>'
												.'Hello'
												.','
												.'<br><br>I have added a new district to the Districts Master table in FreeComETT,'
												.'<br><br>'
												.'<b>'
												.'District => '
												.$session->district
												.'<br>'
												.'Transcriber = '
												.$session->identity_userid
												.'<br>'
												.'File => '
												.$session->current_transcription[0]['BMD_file_name']
												.'</b>'
												.'<br><br>Please verify this district addition.'
												.'<br><br>Thank you.'
												.'<br><br>'
												.$session->realname
												.'<br>'
												.$session->identity_userid
												.'</html>';
					$mail->AltBody = 'District '.$session->district.' added to Districts Master';
					// set return message
					$session->set('message_2', '');
					break;
					
				case 'new_user':
					// set syndicate to address
					$mail->addAddress($session->current_syndicate[0]['BMD_syndicate_email']);
					// set up other fields
					$mail->addReplyTo($session->identity_emailid);
					$mail->addCC($session->identity_emailid);
					$mail->Subject = 'Message from FreeComETT - Transcriber => '.$session->identity_userid.' has signed on to FreeComETT for the first time.';
					$mail->Body = 	'<html>Hello'
									.','
									.'<br><br>Just to let you know, I have started using FreeComETT to transcribe scans.'
									.'<br><br>Best wishes,'
									.'<br><br>'
									.$session->realname
									.'<br>'
									.$session->identity_userid
									.'<br><br>'
									.$session->current_syndicate[0]['BMD_syndicate_leader']
									.'<br>'
									.$session->current_syndicate[0]['BMD_syndicate_name'];
					$mail->AltBody = 'FreeComETT New User '.$session->identity_userid;
					break;
			}
		
		if ( ! $mail->send() )
			{
				// if file was selected
				if ( $session->myfile != '' )
					{
						// delete it if it exists
						if ( file_exists($session->cfile) )
							{
								unlink($session->cfile);
							}
					}
				
				$result = 'Internal error ending email, contact '.$session->linbmd2_email;
dd($mail->ErrorInfo);
				$result_dump =  $mail->ErrorInfo;
				$session->set('message_2', $result.' '.$result_dump.' => '. 'for message type = '.$email_type);
				$session->set('message_class_2', 'alert alert-warning');
				// show view
				echo view('templates/header');
				echo view('linBMD2/error');
				echo view('templates/footer');
			}
		else
			{	   
				// if file was selected
				if ( $session->myfile != '' )
					{
						// delete it if it exists
						if ( file_exists($session->cfile) )
							{
								unlink($session->cfile);
							}
					}
					
				// set message class
				$session->set('message_class_2', 'alert alert-success');
				// go back to designated route
				switch ($email_type) 
					{
						case 'allocation':
							return redirect()->to( base_url('allocation/manage_allocations/2') );
							break;
						case 'identity':
							return redirect()->to( base_url('identity/signin_step1/1') );
							break;
						case 'transcribe':
							switch ($session->BMD_cycle_code) 
								{
									case 'INPRO':
										return redirect()->to(base_url($session->controller.'/transcribe_'.$session->controller.'_step1/0'));
										break;
									case 'VERIT':
										return redirect()->to(base_url('transcribe/verify_step1/2'));
										break;
								}
							break;
						case 'BMD_file':
							unlink($tmpf);
							return redirect()->to(base_url('transcribe/transcribe_step1/2'));
							break;
						case 'district':
							$session->show_view_type = 'transcribe';
							$session->district_added = 1;
							return redirect()->to(base_url($session->controller.'/transcribe_'.$session->controller.'_step2'));
							break;
						case 'new_user':
							return redirect()->to( base_url('help/help_show/0') );
							break;
					}
			}		
	}
}
