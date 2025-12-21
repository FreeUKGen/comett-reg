	<?php $session = session(); ?>
	
	<form action="<?php echo(base_url('parish/correct_parish_step2')) ?>" method="post">
	
		<div class="form-group row">
			<label for="identity">Current Parish</label>
			<input type="text" class="form-control" id="current_parish" name="current_parish" aria-describedby="userHelp" value="<?php echo($session->parish_to_corrected['Parish']) ?>" readonly tabindex=	"-1">
		</div>
	  
		<div class="form-group row">
				<label for="corrected_parish">Corrected Parish</label>
				<input type="text" class="form-control" id="corrected_parish" name="corrected_parish" value="<?php echo($session->corrected_parish) ?>" autofocus>
		</div>		
		
		<div class="row mt-2 d-flex justify-content-between">
			
				<a id="return" class="btn btn-primary mr-0" href="<?php echo(base_url('parish/manage_parishes/0')); ?>">
					<?php echo $session->current_project['back_button_text']?>
				</a>

				<button type="submit" class="btn btn-primary mr-0">
					<span>Submit</span>	
				</button>
			
		</div>
		
	</form>