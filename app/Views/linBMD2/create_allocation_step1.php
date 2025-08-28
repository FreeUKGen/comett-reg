	<?php $session = session(); ?>
	
	<div class="step1">
		<form action="<?php echo(base_url('allocation/create_allocation_step2')) ?>" method="post">
			<div class="form-group row d-flex align-items-center">
				<p 
					class="bg-warning col-12 pl-0 text-center font-weight-bold" 
					style="font-size:1.5vw;">
					<?php
					echo 'Create Allocation in '.$session->syndicate_name ?>	
				</p>
			</div>
			
			<div class="form-group row d-flex align-items-center">
				<label for="year" class="col-2">Allocation year =></label>
				<input type="text" class="form-control col-1" id="year" name="year" aria-describedby="userHelp" value="<?php echo esc($session->year); ?>">
				<small id="userHelp" class="form-text text-muted col-1">eg. 1988</small>
				<label for="quarter" class="col-1">Allocation quarter</label>
				<select name="quarter" id="quarter" class="box col-1">
					<?php foreach ($session->quarters_short_long as $key => $quarter): ?>
						 <option value="<?php echo esc($key)?>"<?php if ( $key == $session->quarter ) {echo esc(' selected');} ?>><?php echo esc($quarter)?></option>
					<?php endforeach; ?>
				</select>
				<small id="userHelp" class="form-text text-muted col-1">If year based transcription, select December</small>
				
				<label for="type" class="col-1">Allocation type</label>
				<select name="type" id="type" class="box col-1">
					<?php foreach ($session->project_types as $project_type): ?>
						 <option value="<?php echo esc($project_type['type_code'])?>"<?php if ( $project_type['type_code'] == $session->type ) {echo esc(' selected');} ?>><?php echo esc($project_type['type_name_lower'])?></option>
					<?php endforeach; ?>
				</select>
				<small id="userHelp" class="form-text text-muted col-1">Select from list</small>
			</div>
			
			<div class="form-group row d-flex align-items-center">
				<label for="letter" class="col-2">Allocation letter =></label>
				<input type="text" class="form-control col-1" id="letter" name="letter" aria-describedby="userHelp" value="<?php echo esc($session->letter); ?>">
				<small id="userHelp" class="form-text text-muted col-1">Enter a letter or letter range</small>
				
				<label for="start_page" class="col-1">Page range</label>
				<input type="text" class="form-control col-1" id="start_page" name="start_page" aria-describedby="userHelp" value="<?php echo esc($session->start_page); ?>">
				<input type="text" class="form-control col-1" id="end_page" name="end_page" aria-describedby="userHelp" value="<?php echo esc($session->end_page); ?>">
				<small id="userHelp" class="form-text text-muted col-1">Enter Page range from - to</small>
			</div>
			
			<div class="form-group row d-flex align-items-center">
				<label for="scan_format" class="col-2">Scan Format? =></label>
				<select name="scan_format" id="scan_format" class="box col-1">
					<?php foreach ($session->scan_formats as $key => $value): ?>
						 <option value="<?php echo esc($key)?>"<?php if ( $key == $session->scan_format ) {echo esc(' selected');} ?>><?php echo esc($value)?></option>
					<?php endforeach; ?>
				</select>
				<small id="userHelp" class="form-text text-muted col-2">Select the format of the scan from the drop down list. This is to try to match the transcription data entry format to the scan format as closely as possible in order to make your life simpler!</small>
			</div>
			
			<div class="form-group row d-flex align-items-center">
				<label for="autocreate" class="col-2">Auto create name? =></label>
				<select name="autocreate" id="autocreate" class="box col-1">
					<?php foreach ($session->yesno as $key => $value): ?>
						 <option value="<?php echo esc($key)?>"<?php if ( $key == $session->autocreate ) {echo esc(' selected');} ?>><?php echo esc($value)?></option>
					<?php endforeach; ?>
				</select>
				<small id="userHelp" class="form-text text-muted col-1">Select YES if you are unsure and the allocation name will be created in standard format.</small>
				<label for="name" class="col-1">Allocation Name</label>
				<input type="text" class="form-control col-4" id="name" name="name" aria-describedby="userHelp" value="<?php echo esc($session->name); ?>">
				<small id="userHelp" class="form-text text-muted col-1">Select NO to enter the Allocation name yourself. Only do this if you are sure about what you are doing.</small>
			</div>
		
			<div class="row mt-4 d-flex justify-content-between">	
				<a id="return" class="btn btn-primary mr-0" href="<?php echo(base_url('allocation/manage_allocations/0')); ?>">
					<?php echo $session->current_project[0]['back_button_text']?>
				</a>
				<button type="submit" class="create_allocation btn btn-primary mr-0 d-flex">
					<span>Create Allocation</span>
					<span class="spinner-border"  role="status">
						<span class="sr-only">Loading...</span>
					</span>		
				</button>
			</div>
	
	</form>

	<script type="text/javascript">
		$('input[name=<?php echo $session->field_name; ?>]').focus();
	</script>
	
	<script type="text/javascript">
		$( document ).ready(function() 
		{	
			let $create_allocation = $('.create_allocation');
			$create_allocation.on("click",function()
				{
					let $spinner = $('.spinner-border');
					$spinner.addClass("active");
				});
		});
	</script>
