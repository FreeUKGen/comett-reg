<?php $session = session(); ?>
	
	<form action="<?php echo(base_url('district/add_synonym_step2')) ?>" method="post">
	  
		<div class="form-group row">
				<label class="col-1" for="synonym">Synonym => </label>
				<input type="text" class="form-control col-2" id="synonym" name="synonym" value="<?php echo($session->synonym) ?>">
				<small id="userHelp" class="form-text text-muted col-4">Enter the synonym that you want to create based on the district you selected.</small>
		</div>
		
		<div class="row mt-2 d-flex justify-content-between">
			
				<a id="return" class="btn btn-primary mr-0" href="<?php echo(base_url('district/manage_volumes/0')); ?>">
					<?php echo $session->current_project[0]['back_button_text']?>
				</a>

				<button type="submit" class="btn btn-primary mr-0">
					<span>Add Synonym</span>	
				</button>
			
		</div>
		
	</form>
