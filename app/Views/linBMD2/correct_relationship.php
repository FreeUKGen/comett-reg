	<?php $session = session(); ?>
	
	<form action="<?php echo(base_url('relationship/correct_relationship_step2')) ?>" method="post">
	
		<div class="form-group row">
			<label for="identity">Current Relationship</label>
			<input type="text" class="form-control" id="current_relationship" name="current_relationship" aria-describedby="userHelp" value="<?php echo($session->relationship_to_corrected['Relationship']) ?>" readonly tabindex=	"-1">
		</div>
	  
		<div class="form-group row">
				<label for="corrected_relationship">Corrected Relationship</label>
				<input type="text" class="form-control" id="corrected_relationship" name="corrected_relationship" value="<?php echo($session->corrected_relationship) ?>" autofocus>
		</div>		
		
		<div class="row mt-2 d-flex justify-content-between">
			
				<a id="return" class="btn btn-primary mr-0" href="<?php echo(base_url('relationship/manage_relationships/0')); ?>">
					<?php echo $session->current_project[0]['back_button_text']?>
				</a>

				<button type="submit" class="btn btn-primary mr-0">
					<span>Submit</span>	
				</button>
			
		</div>
		
	</form>


