	<?php $session = session(); ?>
	
	<br><br><br><br><br><br>

			
	<div class="row">
		<label for="file_name" class="col-2 pl-0">BMD file name =></label>
		<span class="col-2 pl-0" id="file_name" name="file_name"><?php echo esc($session->current_transcription[0]['BMD_file_name']); ?></span>
		<label for="scan_name" class="col-2 pl-0">BMD scan name =></label>
		<span class="col-2 pl-0" id="scan_name" name="scan_name"><?php echo esc($session->current_transcription[0]['BMD_scan_name']); ?></span>
	</div>
	
	<div class="row">
		<label for="upload_date" class="col-2 pl-0">Upload date =></label>
		<span class="col-2 pl-0" id="upload_date" name="upload_date"><?php echo esc($session->current_transcription[0]['BMD_submit_date']); ?></span>
	</div>

	<div class="row">
		<label for="upload_status" class="col-2 pl-0" style="font-size:2em">Upload status =></label>
		<span class="col-2 pl-0" style="font-size:2em; background-color:powderblue;" id="upload_status" name="upload_status"><?php echo esc($session->current_transcription[0]['BMD_submit_status']); ?></span>	
	</div>
	
	<div class="row">
		<label for="upload_message" class="col-2 pl-0" style="font-size:2em">Upload messages =></label>
		<p class="col-10 pl-0" style="font-size:2em; background-color:powderblue;" id="upload_message" name="upload_message"><?php echo esc($session->current_transcription[0]['BMD_submit_message']); ?></p>
	</div>
		
		
	<div class="row mt-4 d-flex justify-content-between">
			
		<a id="return" class="btn btn-primary mr-0" href="<?php echo(base_url('transcribe/transcribe_step1/0')); ?>">
			<?php echo $session->current_project[0]['back_button_text']?>
		</a>
				
	</div>
	
