<?php $session = session(); ?>	
	
	<div class="form-group row">
		<label for="identity" class="col-2 pl-0">Identity</label>
		<input type="text" class="form-control col-2" id="id" aria-describedby="userHelp">
		<small id="userHelp" class="form-text text-muted col-2">This must be your <?php echo $session->current_project[0]['project_name']; ?> user name.</small>
		<label for="password" class="col-2">Password</label>
		<input type="password" class="form-control col-2" id="pw">
		<small id="userHelp" class="form-text text-muted col-2">This must be your <?php echo $session->current_project[0]['project_name']; ?> password.</small>
	</div>
	
	<div class="row mt-4 d-flex justify-content-between">
		
		<a id="return" class="btn btn-primary mr-0" href="<?php echo(base_url("home/close/")); ?>">
			<span>Close application</span>
		</a>
		
		<a id="return" class="btn btn-primary mr-0" href="<?php echo(base_url("home/index/")); ?>">
			<span>Select project</span>
		</a>
		
		<button
			class="btn btn-primary mr-0" id="submit">Sign in
		</button>
			
	</div>

	
	<div>
		<form action="<?=(base_url('identity/signin_step2')); ?>" method="POST" name="signin" >
			<input name="identity" id="identity" type="hidden" />
			<input name="password" id="password" type="hidden" />
			<input name="actual_x" id="actual_x" type="hidden" />
			<input name="actual_y" id="actual_y" type="hidden" />
		</form>
	</div>
	
	<br><br>
	
	<div class="row">
		<?php
		switch ($session->current_project[0]['project_index'])
			{
				case 1: ?>
					<label for="create_freebmd_identity" class="col-8 pl-0">You don't have a <?=$session->current_project[0]['project_name']?> Identity?</label>
					<a id="create_freebmd_identity" class="btn btn-outline-primary btn-sm col-4 d-flex" href="https://www.freebmd.org.uk/Signup.html">
						<span>Start the <?=$session->current_project[0]['project_name']?> project registration process</span>
					</a>
					<?php
					break;
				case 2: ?>
					<label for="create_freereg_identity" class="col-8 pl-0">You don't have a <?=$session->current_project[0]['project_name']?> Identity?</label>
					<a id="create_freereg_identity" class="btn btn-outline-primary btn-sm col-4 d-flex" href="https://www.freereg.org.uk/cms/opportunities-to-volunteer-with-freereg.html">
						<span>Start the <?=$session->current_project[0]['project_name']?> project registration process</span>
					</a>
					<?php
					break;
				case 3: ?>
					<?php
					break;
			} ?>
	</div>
	
	<br><br>
	
	<?php 
		if ( $session->show_message == 'show' )
			{ 
				foreach ( $session->current_message as $message )
					{
						$lines = explode('\n', $message['message']);
						foreach ( $lines as $line )
						{
							?>
								<div class="row">
									<p 	
										class="col-12 pl-0"
										style="	overflow-wrap: break-word;
												font-weight: bold;
												line-height: 0.1;
												color: <?php echo $message['colour']; ?>;">
										<?php echo $line; ?>	
									</p>
								</div>
						<?php
						}
						?>
						<hr />
					<?php
					} ?>
			<?php
			} ?>

<script>	
// handle signon actions
	$('#submit').on("click", function()
			{
				// get screen size
				var actual_x = window.innerWidth;
				var actual_y = window.innerHeight;
				// load variables to form
				$('#identity').val(document.getElementById("id").value);
				$('#password').val(document.getElementById("pw").value);
				$('#actual_x').val(actual_x);
				$('#actual_y').val(actual_y);
				// and submit the form
				$('form[name="signin"]').submit();
			});
</script>			
				


