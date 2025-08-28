	<?php $session = session(); ?>
	
	<form action="<?php echo(base_url('person_status/correct_person_status_step2')) ?>" method="post">
	
		<div class="form-group row">
			<label for="identity">Current Person_status</label>
			<input type="text" class="form-control" id="current_person_status" name="current_person_status" aria-describedby="userHelp" value="<?php echo($session->person_status_to_corrected['Person_status']) ?>" readonly tabindex=	"-1">
		</div>
	  
		<div class="form-group row">
				<label for="corrected_person_status">Corrected Person_status</label>
				<input type="text" class="form-control" id="corrected_person_status" name="corrected_person_status" value="<?php echo($session->corrected_person_status) ?>" autofocus>
		</div>		
		
		<div class="row mt-2 d-flex justify-content-between">
			
				<a id="return" class="btn btn-primary mr-0" href="<?php echo(base_url('person_status/manage_person_statuss/0')); ?>">
					<?php echo $session->current_project[0]['back_button_text']?>
				</a>

				<button type="submit" class="btn btn-primary mr-0">
					<span>Submit</span>	
				</button>
			
		</div>
		
	</form>


