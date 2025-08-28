	<?php $session = session(); ?>
	
	<form action="<?php echo(base_url('identity/retrieve_password_step2')) ?>" method="post">
	
		<div class="form-group row">
			<label for="identity">Identity</label>
			<input type="text" class="form-control" id="identity" name="identity" aria-describedby="userHelp" value="<?php echo($session->identity) ?>">
			<small id="userHelp" class="form-text text-muted"><?php echo 'This must be your '.$session->current_project[0]['project_name'].' identity.' ?></small>
		</div>
	  
		<div class="form-group row">
				<label for="email">Your email</label>
				<input type="email" class="form-control" id="email" name="email" value="<?php echo($session->email) ?>">
				<small id="userHelp" class="form-text text-muted"><?php echo 'This must be the email address attached to your '.$session->current_project[0]['project_name'].' identity.' ?></small>
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
				<span>Retrieve Password</span>	
			</button>
		</div>
		

	</form>


