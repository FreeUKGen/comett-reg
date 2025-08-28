<?php $session = session(); ?>
		
	<div class="row">
		<p class="bg-danger col-12 pl-0 text-center" style="font-size:2vw;">This is VERY sensitive stuff. Get this wrong and you will break FreeComETT. Back out NOW!</p>
	</div>
	
	<form action="<?php echo(base_url('projects/manage_projects_step3')) ?>" method="post">
		<div class="row d-flex justify-content-between mt-4">
			<a id="return" class="btn btn-primary mr-0" href="<?=(base_url('projects/manage_projects_step1/0')); ?>">
			<?php echo $session->current_project[0]['back_button_text']?>
			</a>
			
			<button type="submit" class="btn btn-primary mr-0">
				<span>Change Project Values</span>	
			</button>
		</div>
		
		<br>
		
		<div class="row">
			<p class="col-2 pl-0 text-left" style="font-size:1vw; font-weight:bold;">Value</p>
			<p class="col-4 pl-0 text-left" style="font-size:1vw; font-weight:bold;">Current Value</p>
			<p class="col-4 pl-0 text-left" style="font-size:1vw; font-weight:bold;">New Value</p>
			<p class="col-2 pl-0 text-left" style="font-size:1vw; font-weight:bold;">Defaults</p>
		</div>

		<div class="form-group row">
			<p class="col-2 pl-0 text-left" style="font-size:1vw;">Environment</p>
			<p class="col-4 pl-0 text-left" style="font-size:1vw;"><?php echo($session->project_values[0]['environment']) ?></p>
			<input type="text" class="form-control col-4 text-left" style="font-size:1vw;" id="environment" name="environment" aria-describedby="userHelp" value="<?php echo($session->new_environment) ?>">
			<p class="col-2 pl-0 text-left" style="font-size:0.8vw;">TEST, LIVE</p>
		</div>
		
		<div class="form-group row">
			<p class="col-2 pl-0 text-left" style="font-size:1vw;">Status</p>
			<p class="col-4 pl-0 text-left" style="font-size:1vw;"><?php echo($session->project_values[0]['project_status']) ?></p>
			<input type="text" class="form-control col-4 text-left" style="font-size:1vw;" id="project_status" name="project_status" aria-describedby="userHelp" value="<?php echo($session->new_project_status) ?>">
			<p class="col-2 pl-0 text-left" style="font-size:0.8vw;">Open, Closed</p>
		</div>
		
		<div class="form-group row">
			<p class="col-2 pl-0 text-left" style="font-size:1vw;">Project Name</p>
			<p class="col-4 pl-0 text-left" style="font-size:1vw;"><?php echo($session->project_values[0]['project_name']) ?></p>
			<input type="text" class="form-control col-4 text-left" style="font-size:1vw;" id="project_name" name="project_name" aria-describedby="userHelp" value="<?php echo($session->new_project_name) ?>">
			<p class="col-2 pl-0 text-left" style="font-size:0.8vw;">Text</p>
		</div>
		
		<div class="form-group row">
			<p class="col-2 pl-0 text-left" style="font-size:1vw;">Icon - Name</p>
			<p class="col-4 pl-0 text-left" style="font-size:1vw;"><?php echo($session->project_values[0]['project_iconname']) ?></p>
			<input type="text" class="form-control col-4 text-left" style="font-size:1vw;" id="project_iconname" name="project_iconname" aria-describedby="userHelp" value="<?php echo($session->new_project_iconname) ?>">
			<p class="col-2 pl-0 text-left" style="font-size:0.8vw;">A valid file name
			</p>
		</div>
		
		<div class="form-group row">
			<p class="col-2 pl-0 text-left" style="font-size:1vw;">Icon - Path to Icon</p>
			<p class="col-4 pl-0 text-left" style="font-size:1vw;"><?php echo($session->project_values[0]['project_pathtoicon']) ?></p>
			<input type="text" class="form-control col-4 text-left" style="font-size:1vw;" id="project_pathtoicon" name="project_pathtoicon" aria-describedby="userHelp" value="<?php echo($session->new_project_pathtoicon) ?>">
			<p class="col-2 pl-0 text-left" style="font-size:0.8vw;">Icons, relative to FreeComETT base path.
			</p>
		</div>
		
		<div class="form-group row">
			<p class="col-2 pl-0 text-left" style="font-size:1vw;">Upload URL for LIVE servers</p>
			<p class="col-4 pl-0 text-left" style="font-size:1vw;"><?php echo($session->project_values[0]['project_autouploadurllive']) ?></p>
			<input type="text" class="form-control col-4 text-left" style="font-size:1vw;" id="project_autouploadurllive" name="project_autouploadurllive" aria-describedby="userHelp" value="<?php echo($session->new_project_autouploadurllive) ?>">
			<p class="col-2 pl-0 text-left" style="font-size:0.8vw;">A valid URL.
			</p>
		</div>
		
		<div class="form-group row">
			<p class="col-2 pl-0 text-left" style="font-size:1vw;">Upload URL for TEST servers</p>
			<p class="col-4 pl-0 text-left" style="font-size:1vw;"><?php echo($session->project_values[0]['project_autouploadurltest']) ?></p>
			<input type="text" class="form-control col-4 text-left" style="font-size:1vw;" id="project_autouploadurltest" name="project_autouploadurltest" aria-describedby="userHelp" value="<?php echo($session->new_project_autouploadurltest) ?>">
			<p class="col-2 pl-0 text-left" style="font-size:0.8vw;">A valid URL.
			</p>
		</div>
		
		<div class="form-group row">
			<p class="col-2 pl-0 text-left" style="font-size:1vw;">Back Button Text</p>
			<p class="col-4 pl-0 text-left" style="font-size:1vw;"><?php echo($session->project_values[0]['back_button_text']) ?></p>
			<input type="text" class="form-control col-4 text-left" style="font-size:1vw;" id="back_button_text" name="back_button_text" aria-describedby="userHelp" value="<?php echo($session->new_back_button_text) ?>">
			<p class="col-2 pl-0 text-left" style="font-size:0.8vw;">Text for the Back button.
			</p>
		</div>
		
		<div class="form-group row">
			<p class="col-2 pl-0 text-left" style="font-size:1vw;">Submit Button Text</p>
			<p class="col-4 pl-0 text-left" style="font-size:1vw;"><?php echo($session->project_values[0]['submit_button_text']) ?></p>
			<input type="text" class="form-control col-4 text-left" style="font-size:1vw;" id="submit_button_text" name="submit_button_text" aria-describedby="userHelp" value="<?php echo($session->new_submit_button_text) ?>">
			<p class="col-2 pl-0 text-left" style="font-size:0.8vw;">Text for the Submit button. Currently not implemented.
			</p>
		</div>
		
		<div class="form-group row">
			<p class="col-2 pl-0 text-left" style="font-size:1vw;">Description</p>
			<p class="col-4 pl-0 text-left" style="font-size:1vw;"><?php echo($session->project_values[0]['project_desc']) ?></p>
			<input type="text" class="form-control col-4 text-left" style="font-size:1vw;" id="project_desc" name="project_desc" aria-describedby="userHelp" value="<?php echo($session->new_project_desc) ?>">
			<p class="col-2 pl-0 text-left" style="font-size:0.8vw;">Description.
			</p>
		</div>
		
		<div class="row d-flex justify-content-between mt-4">
			<a id="return" class="btn btn-primary mr-0 flex-column align-items-center" href="<?=(base_url('projects/manage_projects_step1/0')); ?>">
			<?php echo $session->current_project[0]['back_button_text']?>
			</a>
			
			<button type="submit" class="btn btn-primary mr-0">
				<span>Change Project Values</span>	
			</button>
		</div>
	
	</form>
