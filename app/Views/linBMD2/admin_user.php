	<?php $session = session(); ?>
	
	<form action="<?php echo(base_url('identity/admin_user_step2')) ?>" method="post">
		<div class="form-group row">
			<label for="identity" class="col-1">Identity</label>
			<input type="text" class="form-control col-2" id="identity" name="identity" aria-describedby="userHelp" value="<?php echo($session->identity) ?>">
			<small id="userHelp" class="form-text text-muted col-6">This must be an existing FreeComETT user.</small>
		</div>
		
		<div class="form-group row">
			<label for="role" class="col-1">Available rights</label>
				<select name="role" id="role" class="box col-2">
					<?php foreach ($session->available_roles as $role): ?>
						 <option value="<?php echo esc($role['role_index'])?>"<?php if ( $role['role_index'] == $session->role ) {echo esc(' selected');} ?>><?php echo esc($role['role_name'])?></option>
					<?php endforeach; ?>
				</select>
			<small id="userHelp" class="form-text text-muted col-6">Assign the role you wish to give this user by selecting it from the drop down.</small>
		</div>
		
		
		<div class="alert row mt-2 d-flex justify-content-between">
			
				<a id="return" class="btn btn-primary mr-0" href="<?php echo(base_url('database/coord_step1/0')); ?>">
					<?php echo $session->current_project[0]['back_button_text']?>
				</a>

				<button type="submit" class="btn btn-primary mr-0">
					<span>Submit</span>	
				</button>
			
		</div>
	
	</form>


