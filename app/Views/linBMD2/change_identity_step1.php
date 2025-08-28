	<?php $session = session(); ?>
		
	<form action="<?php echo(base_url('identity/change_details_step2')) ?>" method="post">
		<div class="form-group row">
			<label for="identity" class="col-2 pl-0">Identity</label>
			<input type="text" class="form-control col-2" id="identity" name="identity" aria-describedby="userHelp" value="<?php echo($session->identity) ?>">
			<small id="userHelp" class="form-text text-muted col-2"><?php echo 'This must be your '.$session->current_project[0]['project_name'].' user name.' ?></small>
		</div>
	
	<div class="row d-flex justify-content-between mt-4">
			<a id="return" class="btn btn-primary mr-0" href="<?php echo(base_url("home/close/")); ?>">
				<span>Close application</span>
			</a>
			
			<a id="return" class="btn btn-primary mr-0" href="<?php echo(base_url("home/index/")); ?>">
				<span>Select project</span>
			</a>
			
			<a id="return" class="btn btn-primary mr-0" href="<?php echo(base_url("identity/signin_step1/0/")); ?>">
				<span>Sign in</span>
			</a>
			
			<button type="submit" class="btn btn-primary mr-0">
				<span>Continue</span>	
			</button>
		</div>
	
	</form>


