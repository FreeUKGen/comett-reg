<?php $session = session(); ?>
	
	<form action="<?php echo(base_url('district/correct_district_step2')) ?>" method="post">
	
		<div class="form-group row">
			<label for="identity">Current District</label>
			<input type="text" class="form-control" id="current_district" name="current_district" aria-describedby="userHelp" value="<?php echo($session->district_to_corrected) ?>" readonly tabindex=	"-1">
		</div>
	  
		<div class="form-group row">
				<label for="corrected_district">Corrected District</label>
				<input type="text" class="form-control" id="corrected_district" name="corrected_district" value="<?php echo($session->corrected_district) ?>" autofocus>
		</div>		
		
		<div class="row mt-2 d-flex justify-content-between">
			
				<a id="return" class="btn btn-primary mr-0" href="<?php echo(base_url('district/manage_districts/0')); ?>">
					<?php echo $session->current_project[0]['back_button_text']?>
				</a>

				<button type="submit" class="btn btn-primary mr-0">
					<span>Submit</span>	
				</button>
			
		</div>
		
	</form>
