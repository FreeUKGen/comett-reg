	<?php $session = session(); ?>
	
	<br><br><br><br><br><br>

	<form action="<?php echo(base_url('transcribe/close_header_step2')) ?>" method="post">
			
		<div class="row">
			<label for="file_name" class="col-2 pl-0">BMD file name =></label>
			<span class="col-2 pl-0" id="file_name" name="file_name"><?php echo esc($session->current_transcription[0]['BMD_file_name']); ?></span>
			<label for="scan_name" class="col-2 pl-0">BMD scan name =></label>
			<span class="col-2 pl-0" id="scan_name" name="scan_name"><?php echo esc($session->current_transcription[0]['BMD_scan_name']); ?></span>
		</div>
		
		<div class="row">
			<label for="upload_date" class="col-2 pl-0">Upload date =></label>
			<span class="col-2 pl-0" id="upload_date" name="upload_date"><?php echo esc($session->current_transcription[0]['BMD_submit_date']); ?></span>
			<label for="upload_status" class="col-2 pl-0">Upload status =></label>
			<span class="col-2 pl-0" id="upload_status" name="upload_status"><?php echo esc($session->current_transcription[0]['BMD_submit_status']); ?></span>
		</div>
	
		<div class="row">
			<label for="upload_message" class="col-2 pl-0">Upload messages =></label>
			<span class="col-10 pl-0" id="upload_message" name="upload_message"><?php echo esc($session->current_transcription[0]['BMD_submit_message']); ?></span>
		</div>
		
		<div class="row mt-4">
			<label for="close_header" class="col-2 pl-0">Confirm close header?</label>
				<select name="close_header" id="close_header" class="box col-2">
					<?php foreach ($session->yesno as $key => $value): ?>
						 <option value="<?php echo esc($key)?>"<?php if ( $key == $session->close_header ) {echo esc(' selected');} ?>><?php echo esc($value)?></option>
					<?php endforeach; ?>
				</select>
		</div>
		
		<div class="row mt-4 d-flex justify-content-between">
				
			<a id="return" class="btn btn-primary mr-0" href="<?php echo(base_url('transcribe/transcribe_step1/0')); ?>">
				<?php echo $session->current_project[0]['back_button_text']?>
			</a>
			
			<button type="submit" class="btn btn-primary mr-0">
				<span>Submit</span>	
			</button>
		</div>
	</form>
	
