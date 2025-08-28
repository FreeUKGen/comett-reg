	<?php $session = session(); ?>
	
	<form action="<?php echo(base_url('condition/correct_condition_step2')) ?>" method="post">
	
		<div class="form-group row">
			<label for="identity"><b>Current Condition</b></label>
			<input type="text" class="form-control" id="current_condition" aria-describedby="userHelp" value="<?php echo($session->condition_to_corrected['Condition']) ?>" readonly tabindex=	"-1">
			<label for="current_condition_sex"><b>Current Applies to m=male/f=female/b=both</b></label>
			<input type="text" class="form-control" id="current_condition_sex" aria-describedby="userHelp" value="<?php echo($session->condition_to_corrected['condition_sex']) ?>" readonly tabindex=	"-1">
		</div>
		
		<br><br>
		
		<div class="form-group row">
			<label for="corrected_condition"><b>Corrected Condition</b></label>
			<input type="text" class="form-control" id="corrected_condition" name="corrected_condition" value="<?php echo($session->corrected_condition) ?>" autofocus>
			<label for="corrected_condition_sex"><b>Corrected Applies to m=male/f=female/b=both</b></label>
			<input type="text" class="form-control" id="corrected_condition_sex" name="corrected_condition_sex" value="<?php echo($session->corrected_condition_sex) ?>">
		</div>		
		
		<div class="row mt-2 d-flex justify-content-between">
			
				<a id="return" class="btn btn-primary mr-0" href="<?php echo(base_url('condition/manage_conditions/0')); ?>">
					<?php echo $session->current_project[0]['back_button_text']?>
				</a>

				<button type="submit" class="btn btn-primary mr-0">
					<span>Submit</span>	
				</button>
			
		</div>
		
	</form>


