	<?php $session = session(); ?>
	
	<form action="<?php echo(base_url('transcription/create_bmd_step2')) ?>" method="post">
		
		<div class="form-group row d-flex align-items-center">
			<label for="allocation" class="col-2">Select an Allocation =></label>
			<select name="allocation" id="allocation" class="box col-7">
				<?php
				foreach ( $session->allocations as $allocation )
					{ ?>
						<option 
							value="<?php echo esc($allocation['BMD_allocation_index']);?>"
							<?php 
								if ( $allocation['BMD_allocation_index'] == $session->last_allocation ) 
									{
										echo esc('selected');
									}
							?>
							>
							<?php 
								echo esc($allocation['BMD_allocation_name']);
							?>
						</option>
					<?php
					}
					?>
			</select>
		</div>
	
		<div class="form-group row d-flex align-items-center">
			<label for="scan_page" class="col-2">Scan page number =></label>
			<input type="text" class="form-control col-1" id="scan_page" name="scan_page" aria-describedby="userHelp" value="<?php echo esc($session->scan_page); ?>">
			<small id="userHelp" class="form-text text-muted col-1">Must be in your current allocation page range.</small>
			<label for="scan_page_suffix" class="col-1">Scan page suffix</label>
			<input type="text" class="form-control col-1" id="scan_page_suffix" name="scan_page_suffix" aria-describedby="userHelp" value="<?php echo esc($session->scan_page_suffix); ?>">
			<small id="userHelp" class="form-text text-muted col-1">Occasionally scan page numbers have a suffix, eg a, b. Leave blank otherwise.</small>
		</div>
		
		<div class="form-group row d-flex align-items-center">
			<label for="comment_text" class="col-2">Comment for this transcription =></label>
			<input type="text" class="form-control col-6" id="comment_text" name="comment_text" aria-describedby="userHelp" value="<?php echo esc($session->comment_text); ?>">
			<small id="userHelp" class="form-text text-muted col-1">You can enter a comment for this transcription here if you want; otherwise just leave blank.</small>
		</div>
		
		<div class="form-group row d-flex align-items-center">
				<label for="autocreate" class="col-2">Auto create scan name? =></label>
				<select name="autocreate" id="autocreate" class="box col-1">
					<?php foreach ($session->yesno as $key => $value): ?>
						 <option value="<?php echo esc($key)?>"<?php if ( $key == $session->autocreate ) {echo esc(' selected');} ?>><?php echo esc($value)?></option>
					<?php endforeach; ?>
				</select>
				<small id="userHelp" class="form-text text-muted col-1">Select YES if you are unsure and the scan name will be created from the current allocation parameters.</small>
				<label for="scan_name" class="col-1">Scan Name</label>
				<input type="text" class="form-control col-4" id="scan_name" name="scan_name" aria-describedby="userHelp" value="<?php echo esc($session->scan_name); ?>">
		</div>
		
		<br>
		
		<div class="row mt-4 d-flex justify-content-between">	
				<a id="return" class="btn btn-primary mr-0" href="<?php echo(base_url('transcribe/transcribe_step1/0')); ?>">
					<?php echo $session->current_project[0]['back_button_text']?>
				</a>
				<a class="btn btn-primary mr-0" href="<?=(base_url('allocation/manage_allocations/0')) ?>"><?php echo 'Manage your '.$session->current_project[0]['project_name'].' '.$session->current_project[0]['allocation_text'].'s'?></a>
				<a class="btn btn-primary mr-0" href="<?=(base_url('transcription/download_transcription_step1/0')) ?>">Download a transcription from <?php echo $session->current_project[0]['project_name'];?> that you did not originally transcribe with FreeComETT.</a>
				<button type="submit" class="create_allocation btn btn-primary mr-0 d-flex">
					<span>Create Transcription</span>
					<span class="spinner-border"  role="status">
						<span class="sr-only">Loading...</span>
					</span>		
				</button>
			</div>

	</form>

	<script type="text/javascript">
		$('input[name=<?php echo $session->field_name; ?>]').focus();
	</script>
