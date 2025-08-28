<?php $session = session(); ?>
	
	<form action="<?php echo(base_url('district/add_district_step2')) ?>" method="post">
	  
		<div class="form-group row">
				<label class="col-1" for="district">District => </label>
				<input type="text" class="form-control col-2" id="district" name="district" value="<?php echo($session->district) ?>">
				<small id="userHelp" class="form-text text-muted col-4">Enter the of the District that you want to create.</small>
		</div>
		
		<div class="row mt-2 d-flex justify-content-between">
			
				<a id="return" class="btn btn-primary mr-0" href="<?php echo(base_url('district/manage_districts/0')); ?>">
					<?php echo $session->current_project[0]['back_button_text']?>
				</a>

				<button type="submit" class="btn btn-primary mr-0">
					<span>Add District</span>	
				</button>
			
		</div>
		
	</form>
