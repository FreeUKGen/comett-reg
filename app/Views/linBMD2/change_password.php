	<?php $session = session(); ?>
		
	<form action="<?php echo(base_url('identity/change_password_step2')) ?>" method="post">
		<div class="form-group row">
			<label for="identity" class="col-1 pl-0">Identity :</label>
			<input type="text" class="form-control col-2" id="identity" name="identity" aria-describedby="userHelp" value="<?php echo($session->identity) ?>">
			<small id="userHelp" class="form-text text-muted col-2"><?php echo 'This must be your '.$session->current_project[0]['project_name'].' user name.' ?></small>
		</div>
		
		<div class="form-group row">
			<label for="realname" class="col-1">New real name :</label>
			<input type="text" class="form-control col-2" id="realname" name="realname" value="<?php echo($session->realname) ?>">
			<small id="userHelp" class="form-text text-muted col-2"><?php echo 'Enter your NEW realname.' ?></small>
		</div>
		
		<div class="form-group row">
			<label for="email" class="col-1">Email :</label>
			<input type="text" class="form-control col-2" id="email" name="email" value="<?php echo($session->email) ?>">
			<small id="userHelp" class="form-text text-muted col-2"><?php echo 'Enter your NEW email.' ?></small>
		</div>
		
		<div class="form-group row">
			<label for="records" class="col-1">Number of records :</label>
			<input type="number" class="form-control col-2" id="records" name="records" value="<?php echo($session->records) ?>">
			<small id="userHelp" class="form-text text-muted col-2"><?php echo 'Enter the number of records you have transcribed.' ?></small>
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
				<span>Change Details</span>	
			</button>
		</div>
	
	</form>


