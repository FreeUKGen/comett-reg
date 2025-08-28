	<?php $session = session(); ?>
			
	<br><br><br><br>
	<form action="<?php echo(base_url('transcription/download_transcription_step2')) ?>" method="post">
		
		<div class="form-group row">
			<label for="BMD_file" class="col-2 pl-0">Transcription to DOWNLOAD</label>
			<input type="text" class="form-control col-2" id="BMD_file" name="BMD_file" aria-describedby="userHelp" value="<?php echo($session->BMD_file) ?>">
			<small id="userHelp" class="form-text text-muted col-2">eg, 1988BL0319. The transcription must be one that you did not originally create with FreeComETT and must be in your transcriptions uploaded to <?php echo $session->current_project[0]['project_name'];?>. Do not enter the file extension eg .BMD</small>
			
			<label for="scan_page_suffix" class="col-2 pl-0">Scan Page Suffix</label>
			<input type="text" class="form-control col-2" id="scan_page_suffix" name="scan_page_suffix" aria-describedby="userHelp" value="<?php echo($session->scan_page_suffix) ?>">
			<small id="userHelp" class="form-text text-muted col-2">eg. a, b, c etc</small>
		</div>
		
		<div class="form-group row d-flex align-items-center">
			<label for="allocation" class="col-2 pl-0">Select an Allocation</label>
			<select name="allocation" id="allocation" class="box col-4">
				<option value=<?php echo $session->allocation; ?>><?php echo $session->allocation_name; ?></option>
				<?php foreach ($session->allocations as $allocation): ?>
					<option value="<?php echo esc($allocation['BMD_allocation_index'])?>"><?php echo esc($allocation['BMD_allocation_name'])?></option>
				<?php endforeach; ?>
			</select>
			<small id="userHelp" class="form-text text-muted col-3">All transcriptions must be attached to an Allocation. Either select an existing Allocation for the transcription to be downloaded or create a new one by selecting the Manage your Allocations button below. Then come back here.</small>
		</div>
		
		<div class="form-group row">
			<label for="BMD_download_confirm" class="col-2 pl-0">DOWNLOAD this transcription?</label>
			<select name="BMD_download_confirm" id="BMD_download_confirm" class="box col-1">
				<?php foreach ($session->yesno as $key => $value): ?>
					 <option value="<?php echo esc($key)?>"<?php if ( $key == $session->BMD_download_confirm ) {echo esc(' selected');} ?>><?php echo esc($value)?></option>
				<?php endforeach; ?>
			</select>
			<small id="userHelp" class="form-text text-muted col-3">Yes, will DOWNLOAD the transcription so that you can make changes and re-upload if required.</small>
		</div>
			
		<div class="row d-flex justify-content-between mt-4">
			<a id="return" class="btn btn-primary mr-0" href="<?=(base_url('transcribe/transcribe_step1/0')); ?>">
			<?php echo $session->current_project[0]['back_button_text']?>
			</a>
			
			<a class="btn btn-primary mr-0 d-flex" href="<?=(base_url('allocation/manage_allocations/0')) ?>">Manage your <?php echo $session->current_project[0]['project_name'] ?> Allocations</a>
			
			<button type="submit" class="btn btn-primary mr-0 d-flex">
				<span>DOWNLOAD Transcription</span>
			</button>
		</div>

	</form>

