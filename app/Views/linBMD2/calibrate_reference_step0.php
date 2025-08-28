<?php $session = session(); ?>
	
	<div class="step1">
		<form action="<?php echo(base_url('transcribe/calibrate_reference_step1/0')) ?>" method="post">
			
			<div class="form-group row">
				<label for="reference_synd" class="col-2 pl-0">Calibrating for :</label>
				<select class="col-3 pl-0 rounded box" style="height:40px; border:1px solid black; font-size:18px; color:black; background-color:Isabelline;" name="reference_synd" id="reference_synd">
					<?php foreach ($session->syndicates as $value): ?>
						 <option value="<?php echo esc($value['BMD_syndicate_index'])?>"<?php if ( $value['BMD_syndicate_index'] == $session->saved_syndicate_index ) { echo ' selected';} ?>><?php echo esc($value['BMD_syndicate_name'])?></option>
					<?php endforeach; ?>
				</select>
				<small id="userHelp" class="form-text text-muted col-3">Select the syndicate you are calibrating for.</small>
			</div>
			
			<div class="form-group row">
				<label for="reference_scan" class="col-2 pl-0">Transcription type : </label>
				<select name="reference_type" id="reference_type" class="col-3 pl-0 rounded box" style="height:40px; border:1px solid black; font-size:18px; color:black; background-color:Isabelline;">
					<?php foreach ($session->project_types as $project_type): ?>
						 <option value="<?php echo esc($project_type['type_code'])?>"<?php if ( $project_type['type_code'] == $session->reference_type ) {echo esc(' selected');} ?>><?php echo esc($project_type['type_name_lower'])?></option>
					<?php endforeach; ?>
				</select>
				<small id="userHelp" class="form-text text-muted col-5">Select from list.</small>
			</div>
		
		<div class="row mt-4 d-flex justify-content-between">
			<a id="return" class="btn btn-primary mr-0" href="<?=(base_url('syndicate/manage_syndicates/0')); ?>">
				<?php echo $session->current_project[0]['back_button_text']?>
			</a>
			
			<button type="submit" class="btn btn-primary mr-0 d-flex">
				<span>Continue</span>	
			</button>
		</div>
			
		</form>
	</div>
