	<?php $session = session(); ?>
	
	<form action="<?php echo(base_url('licence/correct_licence_step2')) ?>" method="post">
	
		<div class="form-group row">
			<label for="identity">Current Licence</label>
			<input type="text" class="form-control" id="current_licence" name="current_licence" aria-describedby="userHelp" value="<?php echo($session->licence_to_corrected['Licence']) ?>" readonly tabindex=	"-1">
		</div>
	  
		<div class="form-group row">
				<label for="corrected_licence">Corrected Licence</label>
				<input type="text" class="form-control" id="corrected_licence" name="corrected_licence" value="<?php echo($session->corrected_licence) ?>" autofocus>
		</div>		
		
		<div class="row mt-2 d-flex justify-content-between">
			
				<a id="return" class="btn btn-primary mr-0" href="<?php echo(base_url('licence/manage_licences/0')); ?>">
					<?php echo $session->current_project[0]['back_button_text']?>
				</a>

				<button type="submit" class="btn btn-primary mr-0">
					<span>Submit</span>	
				</button>
			
		</div>
		
	</form>


