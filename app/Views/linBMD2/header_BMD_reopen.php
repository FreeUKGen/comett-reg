	<?php $session = session(); ?>
	
	<div class="row">
		<h2 class="col-8 pl-0"><?php echo 'Reopen a FreeComETT transcription.';?></h2>		
	</div>
	
	<br>
	
	<div class="row">
		<p class="col-8 pl-0"><?php echo 'There are two possible results to opening a transcription; '?></p>		
	</div>
	<div class="row">
		<p class="col-8 pl-0"><?php echo 'If you have not uploaded the transcription detail to your project, the transcription will be reopened in FreeComETT and you will be able to continue transcribing.'?></p>		
	</div>
	<div class="row">
		<p class="col-8 pl-0"><?php echo 'If you have uploaded the transcription detail to your project, the transcription will be reopened in FreeComETT and the FreeComETT detail data will be updated to match the data retrieved from your project. You can then continue transcribing.'?></p>		
	</div>
	<div class="row">
		<p class="col-8 pl-0"><?php echo 'If you are using Verify = line-by-line any data updated/added to FreeComETT will be marked as unverified.'?></p>		
	</div>	
	
	<form action="<?php echo(base_url('transcription/reopen_BMD_step2')) ?>" method="post">
		<div class="form-group row">
			<label for="BMD_file" class="col-3 pl-0">Transcription to reopen</label>
			<input type="text" class="form-control col-2" id="BMD_file" name="BMD_file" aria-describedby="userHelp" value="<?php echo($session->BMD_file) ?>">
			<small id="userHelp" class="form-text text-muted col-6">eg, 1988BL0319</small>
		</div>		
		<div class="form-group row">
			<label for="BMD_reopen_confirm" class="col-3 pl-0">Reopen this transcription?</label>
			<select name="BMD_reopen_confirm" id="BMD_reopen_confirm" class="box col-1">
				<?php foreach ($session->yesno as $key => $value): ?>
					 <option value="<?php echo esc($key)?>"<?php if ( $key == $session->BMD_reopen_confirm ) {echo esc(' selected');} ?>><?php echo esc($value)?></option>
				<?php endforeach; ?>
			</select>
			<small id="userHelp" class="form-text text-muted col-6">Yes, will reopen the transcription so that you can make changes and re-upload if required.</small>
		</div>
			
		<div class="row d-flex justify-content-between mt-4">
			<a id="return" class="btn btn-primary mr-0" href="<?=(base_url('transcribe/transcribe_step1/0')); ?>">
			<?php echo $session->current_project[0]['back_button_text']?>
			</a>
			
			<button type="submit" class="btn btn-primary mr-0 d-flex">
				<span>Reopen Transcription</span>
			</button>
		</div>

	</form>

