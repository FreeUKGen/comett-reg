	<?php $session = session(); ?>
	
	<form action="<?php echo(base_url('occupation/correct_occupation_step2')) ?>" method="post">
	
		<div class="form-group row">
			<label for="identity">Current Occupation</label>
			<input type="text" class="form-control" id="current_occupation" name="current_occupation" aria-describedby="userHelp" value="<?php echo($session->occupation_to_corrected['Occupation']) ?>" readonly tabindex=	"-1">
		</div>
	  
		<div class="form-group row">
				<label for="corrected_firstname">Corrected Occupation</label>
				<input type="text" class="form-control" id="corrected_occupation" name="corrected_occupation" value="<?php echo($session->corrected_occupation) ?>" autofocus>
		</div>		
		
		<div class="row mt-2 d-flex justify-content-between">
			
				<a id="return" class="btn btn-primary mr-0" href="<?php echo(base_url('occupation/manage_occupations/0')); ?>">
					<?php echo $session->current_project['back_button_text']?>
				</a>

				<button type="submit" class="btn btn-primary mr-0">
					<span>Submit</span>	
				</button>
			
		</div>
		
	</form>