	<?php $session = session(); ?>
	
	<form action="<?php echo(base_url('surname/correct_surname_step2')) ?>" method="post">
	
		<div class="form-group row">
			<label for="identity">Current Surname</label>
			<input type="text" class="form-control" id="current_surname" name="current_surname" aria-describedby="userHelp" value="<?php echo($session->surname_to_corrected['Surname']) ?>" readonly tabindex=	"-1">
		</div>
	  
		<div class="form-group row">
				<label for="corrected_surname">Corrected Surname</label>
				<input type="text" class="form-control" id="corrected_surname" name="corrected_surname" value="<?php echo($session->corrected_surname) ?>" autofocus>
		</div>		
		
		<div class="row mt-2 d-flex justify-content-between">
			
				<a id="return" class="btn btn-primary mr-0" href="<?php echo(base_url('surname/manage_surnames/0')); ?>">
					<?php echo $session->current_project[0]['back_button_text']?>
				</a>

				<button type="submit" class="btn btn-primary mr-0">
					<span>Submit</span>	
				</button>
			
		</div>
		
	</form>


