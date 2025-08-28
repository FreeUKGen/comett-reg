	<?php $session = session(); ?>
		
	<form action="<?php echo(base_url('identity/change_password_step2')) ?>" method="post">
		<div class="form-group row">
			<label for="identity" class="col-2 pl-0">Identity</label>
			<input type="text" class="form-control col-2" id="identity" name="identity" aria-describedby="userHelp" value="<?php echo($session->identity) ?>">
			<small id="userHelp" class="form-text text-muted col-2">This must be your FreeBMD user name.</small>
			<label for="newpassword" class="col-2">New password</label>
			<input type="password" class="form-control col-2" id="newpassword" name="newpassword" value="<?php echo($session->newpassword) ?>">
			<small id="userHelp" class="form-text text-muted col-2">This must be your NEW FreeBMD password.</small>
		</div>
	
	<div class="row d-flex justify-content-end mt-4">
			<button type="submit" class="btn btn-primary mr-0 d-flex">
				<span>Change Password</span>	
			</button>
		</div>
	
	</form>


