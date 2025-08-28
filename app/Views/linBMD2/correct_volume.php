<?php $session = session(); ?>
	
	<form action="<?php echo(base_url('district/correct_volume_step2')) ?>" method="post">
	
		<div class="form-group row">
			<label for="identity">Current volume this range</label>
			<input type="text" class="form-control" id="current_volume" name="current_volume" aria-describedby="userHelp" value="<?php echo($session->volume_to_corrected) ?>" readonly tabindex=	"-1">
		</div>
	  
		<div class="form-group row">
				<label for="corrected_volume">Corrected Volume</label>
				<input type="text" class="form-control" id="corrected_volume" name="corrected_volume" value="<?php echo($session->corrected_volume) ?>" autofocus>
		</div>		
		
		<div class="row mt-2 d-flex justify-content-between">
			
				<a id="return" class="btn btn-primary mr-0" href="<?php echo(base_url('district/manage_volumes/0')); ?>">
					<?php echo $session->current_project[0]['back_button_text']?>
				</a>

				<button type="submit" class="btn btn-primary mr-0">
					<span>Submit</span>	
				</button>
			
		</div>
		
	</form>
