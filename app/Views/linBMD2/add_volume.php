<?php $session = session(); ?>
	
	<form action="<?php echo(base_url('district/add_volume_step2')) ?>" method="post">
	
		<div class="form-group row">
			<label class="col-1" for="type">For type => </label>
			<select name="type" id="type" class="box col-2">
					<?php foreach ($session->project_types as $project_type): ?>
						 <option value="<?php echo esc($project_type['type_code'])?>"<?php if ( $project_type['type_code'] == $session->type ) {echo esc(' selected');} ?>><?php echo esc($project_type['type_name_lower'])?></option>
					<?php endforeach; ?>
			</select>
			<small id="userHelp" class="form-text text-muted col-4">Select from list</small>
		</div>
	  
		<div class="form-group row">
				<label class="col-1" for="volume_from">From period => </label>
				<input type="text" class="form-control col-2" id="volume_from" name="volume_from" value="<?php echo($session->volume_from) ?>">
				<small id="userHelp" class="form-text text-muted col-4">yyyyqq, eg 187003 = year 1870, quarter 03</small>
		</div>
		
		<div class="form-group row">
				<label class="col-1" for="volume_to">To period => </label>
				<input type="text" class="form-control col-2" id="volume_to" name="volume_to" value="<?php echo($session->volume_to) ?>">
				<small id="userHelp" class="form-text text-muted col-4">yyyyqq, eg 199004. Use 999999 to indicate until end of time</small>
		</div>	
		
		<div class="form-group row">
				<label class="col-1" for="volume">Volume => </label>
				<input type="text" class="form-control col-2" id="volume" name="volume" value="<?php echo($session->volume) ?>">
		</div>		
		
		<div class="row mt-2 d-flex justify-content-between">
			
				<a id="return" class="btn btn-primary mr-0" href="<?php echo(base_url('district/manage_volumes/0')); ?>">
					<?php echo $session->current_project[0]['back_button_text']?>
				</a>

				<button type="submit" class="btn btn-primary mr-0">
					<span>Add Volume</span>	
				</button>
			
		</div>
		
	</form>
	
	<table class="table-sm table-hover table-striped table-borderless" style="border-collapse: separate; border-spacing: 0;">
			<thead class="sticky-top bg-white">
				<tr>
					<th>Type</th>
					<th>From</th>
					<th>To</th>
					<th>Volume</th>
				</tr>
			</thead>

			<tbody>
				<?php foreach ( $session->volumes as $volume ) 
					{ 
						?>
						<tr>
							<td><?= esc($volume['BMD_type'])?></td>
							<td><?= esc($volume['volume_from'])?></td>
							<td><?= esc($volume['volume_to'])?></td>
							<td><?= esc($volume['volume'])?></td>
						</tr>
					<?php
					} ?>
			</tbody>
		</table>
