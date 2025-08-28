	<?php $session = session(); ?>
	
	<form action="<?php echo(base_url('identity/create_identity_step2')) ?>" method="post">
		<div class="form-group row">
			<label for="identity" class="col-2 pl-0">Identity</label>
			<input type="text" class="form-control col-2" id="identity" name="identity" aria-describedby="userHelp" value="<?php echo($session->identity) ?>">
			<small id="userHelp" class="form-text text-muted col-2">This must be your <?php echo $session->current_project[0]['project_name']; ?> user name.</small>
			<label for="password" class="col-2 pl-0">Password</label>
			<input type="password" class="form-control col-2" id="password" name="password" value="<?php echo($session->password) ?>">
			<small id="userHelp" class="form-text text-muted col-2">This must be your <?php echo $session->current_project[0]['project_name']; ?> password.</small>
		</div>
	
		<div class="form-group row">
			<label for="realname" class="col-2 pl-0">Your real name (eg John Doe)</label>
			<input type="text" class="form-control col-2" id="realname" name="realname" value="<?php echo($session->realname) ?>">
			<small id="userHelp" class="form-text text-muted col-2">This is required in order to submit transcriptions to <?php echo $session->current_project[0]['project_name']; ?>.</small>
			<label for="email" class="col-2 pl-0">Your email (eg john.doe@xyz.com)</label>
			<input type="text" class="form-control col-2" id="email" name="email" value="<?php echo($session->email) ?>">
			<small id="userHelp" class="form-text text-muted col-2">This is required in order to submit transcriptions to <?php echo $session->current_project[0]['project_name']; ?>.</small>
		</div>
		
			
		
		<div class="row d-flex justify-content-between mt-4">
			
			<a id="return" class="btn btn-primary mr-0" href="<?php echo(base_url("home/close/")); ?>">
				<span>Close application</span>
			</a>
			
			<a id="return" class="btn btn-primary mr-0" href="<?php echo(base_url("home/index/")); ?>">
				<span>Select project</span>
			</a>
			
			
			<button type="submit" class="btn btn-primary mr-0">
				<span>Create Identity</span>	
			</button>
		</div>
	
		<br><br>
		<div class="row">
			<label for="create_freebmd_identity" class="col-8 pl-0">You don't have a <?php echo $session->current_project[0]['project_name']; ?> Identity?</label>
			<a id="create_freebmd_identity" class="btn btn-outline-primary btn-sm col-4" href="https://www.freebmd.org.uk/Signup.html">
				<span>Start the <?php echo $session->current_project[0]['project_name']; ?> registration process</span>
			</a>
		</div>
	
	</form>


