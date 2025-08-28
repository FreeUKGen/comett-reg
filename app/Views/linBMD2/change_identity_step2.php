	<?php $session = session(); ?>
		
	<div class="row">
		<h2 class="col-8 pl-0"><?php echo 'Change '.$session->current_project[0]['project_name'].' details for your identity : '.$session->identity_userid.', '.$session->realname ?></h2>		
	</div>
	
	<br>
	
	<div class="row">
		<p class="col-8 pl-0"><?php echo 'Changing your identity is done directly through your project '.$session->current_project[0]['project_name'].'.';?></p>		
	</div>
	
	<div class="row">
		<p class="col-8 pl-0">Click on the Change Identity button to signin to your project. This will open a new tab in your browser.</p>		
	</div>
	
	<div class="row">
		<p class="col-8 pl-0">Make any changes you need, ie email, Name, Given Name.</p>		
	</div>
	
	<div class="row">
		<p class="col-8 pl-0">Then close the tab.</p>		
	</div>

	<div class="row">
		<p class="col-8 pl-0">The changes you made will be applied when FreeComETT starts next time. Press Restart FreeComETT to apply the changes now.</p>		
	</div>
	
	<div class="row d-flex justify-content-between mt-4">	
		<a id="return" class="btn btn-primary mr-0" href="<?php echo(base_url('transcribe/transcribe_step1/0')); ?>">
		<?php echo $session->current_project[0]['back_button_text']?>
		</a>
		
		<a class="btn btn-primary mr-0" target="_blank" href="https://www.freebmd.org.uk/cgi/bmd-user-admin.pl">
		<span><?php echo $session->current_project[0]['project_name']?> => Change Identity</span>
		</a>
		
		<a id="return" class="btn btn-primary mr-0" href="<?php echo(base_url("home/signout/")); ?>">
			<span>Restart FreeComETT</span></a>
	</div>
	
	<br><br>
	
	<?php
	if ( $session->current_project[0]['project_index'] != 2 )
		{ ?>
			<div class="row">
				<h2 class="col-12 pl-0"><?php echo 'There are certain parameters which are unique to FreeComETT and which can be managed here for your identity : '.$session->identity_userid.', '.$session->realname ?></h2>		
			</div>
			
			<div class="row">
				<p class="col-8 pl-0">The changes you make in this section will be applied immediately after validation of your entries.</p>		
			</div>
			
			<div class="row">
				<p class="col-8 pl-0"><?php echo 'Your current Verify Mode => '.$session->verify_mode_text; ?></p>		
			</div>
			
			<form action="<?php echo(base_url('/identity/change_details_step3')) ?>" method="post">
			
				<!-- show verify options -->
				<div class="form-group row d-flex align-items-center">
					<label for="verify_onthefly" class="col-2">Verify line-by-line during transcription =></label>
					<input type="radio" class="form-control col-1" id="verify_onthefly" name="verify_mode" aria-describedby="userHelp" value="<?php echo 'onthefly' ?>">
					<small id="userHelp" class="form-text text-muted col-2">You can choose to verify a transcribed line immediately after having transcribed it in the Transcribe module by clicking this button </small>
					<p class="col-1"></p>
					<label for="verify_after" class="col-2">Verify after Transcription completed =></label>
					<input type="radio" class="form-control col-1" id="verify_after" name="verify_mode" aria-describedby="userHelp" value="<?php echo 'after' ?>">
					<small id="userHelp" class="form-text text-muted col-2">You can choose to verify a transcription after it is completed using the Verify module by clicking this button. </small>
				</div>

			<div class="row d-flex justify-content-between mt-4">	
				<a id="return" class="btn btn-primary mr-0" href="<?php echo(base_url('transcribe/transcribe_step1/0')); ?>">
				<?php echo $session->current_project[0]['back_button_text']?>
				</a>
				
				<button type="submit" class="btn btn-primary mr-0">
							<span>Submit</span>	
				</button>
			</div>
		<?php
		} ?>
	
	</form>
	
	<br>





