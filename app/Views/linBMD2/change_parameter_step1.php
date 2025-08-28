<?php $session = session(); ?>
		
	<div class="row">
		<p class="bg-danger col-12 pl-0 text-center" style="font-size:2vw;">This is VERY sensitive stuff. Get this wrong and you will break FreeComETT.</p>
	</div>
	
	<div class="row">
		<p class="col-3 pl-0 text-left" style="font-size:1vw;">Change value for parameter key => </p>
		<p class="col-4 pl-0 text-left" style="font-size:1vw;"><?php echo $session->parameter_key ?></p>
	</div>
	
	<div class="row">
		<p class="col-3 pl-0 text-left" style="font-size:1vw;">Current parameter value => </p>
		<p class="col-4 pl-0 text-left" style="font-size:1vw;"><?php echo $session->current_parameter_value ?></p>
	</div>
	
	<div class="row">
		<p class="col-3 pl-0 text-left" style="font-size:1vw;">Allowed parameter values => </p>
		<p class="col-4 pl-0 text-left" style="font-size:1vw;"><?php echo $session->allowed_parameter_values ?></p>
	</div>
	
	<form action="<?php echo(base_url('parameter/manage_parameters_step3')) ?>" method="post">
		<div class="form-group row">
			<label for="new_parameter_value" class="col-3 pl-0 text-left" style="font-size:1vw;">New parameter value => </label>
			<input type="text" class="form-control col-4 text-left" style="font-size:1vw;" id="new_parameter_value" name="new_parameter_value" aria-describedby="userHelp" value="<?php echo($session->new_parameter_value) ?>">
		</div>
		
		<div class="row d-flex justify-content-between mt-4">
			<a id="return" class="btn btn-primary mr-0" href="<?=(base_url('parameter/manage_parameters_step1/0')); ?>">
			<?php echo $session->current_project[0]['back_button_text']?>
			</a>
				
			<button type="submit" class="btn btn-primary mr-0">
				<span>Change Parameter</span>	
			</button>
		</div>
	
	</form>
