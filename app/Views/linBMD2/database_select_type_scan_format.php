	<?php $session = session(); ?>
			
	<div class="def">
		<form action="<?php echo(base_url('database/def_step2')) ?>" method="post">
			
		<div class="form-group row">
				<label for="scan_format_from" class="col-3 pl-0">Select a Scan Format as base for new records FROM :</label>
					<select name="scan_format_from" id="scan_format_from" class="box col-2">
						<?php foreach ($session->scan_formats as $key => $value): ?>
						 <option value="<?php echo esc($key)?>"<?php if ( $key == $session->scan_format ) {echo esc(' selected');} ?>><?php echo esc($value)?></option>
						<?php endforeach; ?>
					</select>
		</div>
		
		<div class="form-group row">
				<label for="scan_format_to" class="col-3 pl-0">Select a Scan Format to create entries for TO :</label>
					<select name="scan_format_to" id="scan_format_to" class="box col-2">
						<?php foreach ($session->scan_formats as $key => $value): ?>
						 <option value="<?php echo esc($key)?>"<?php if ( $key == $session->scan_format ) {echo esc(' selected');} ?>><?php echo esc($value)?></option>
						<?php endforeach; ?>
					</select>
		</div>
		
		
	</div>
			
	<div class="row mt-4 d-flex justify-content-between">	
		<a id="return" class="btn btn-primary mr-0" href="<?php echo(base_url('housekeeping/index/0')); ?>">
			<?php echo $session->current_project[0]['back_button_text']?>
		</a>
		
		<button type="submit" class="create_allocation btn btn-primary mr-0 d-flex">
			<span>Apply</span>
			<span class="spinner-border"  role="status">
				<span class="sr-only">Loading...</span>
			</span>		
		</button>
	</div>

	
	</form>

