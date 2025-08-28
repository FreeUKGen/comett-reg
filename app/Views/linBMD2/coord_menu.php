<?php $session = session(); ?>
	
	<div class="row">
					<p class="bg-danger col-12 pl-0 text-center" style="font-size:1vw;">YOU ARE A FreeComETT COORDINATOR. HERE ARE TASKS YOU CAN PERFORM. BE CAREFUL!</p>
				</div>
	
	<div class="row">
		<label for="admin-user" class="col-8 pl-0">Give or remove FreeComETT rights to an existing FreeComETT user.</label>
		<a id="admin-user" class="btn btn-outline-primary btn-sm col-4" href="<?php echo(base_url('identity/admin_user_step1/0')) ?>">
			<span>Manage User Rights</span>
		</a>
	</div>
	
	<div class="row">
		<label for="manage_messages" class="col-8 pl-0">Manage messages that will be shown to Transcribers on signin </label>
		<a id="manage_messages" class="btn btn-outline-primary btn-sm col-4" href="<?php echo(base_url('messaging/manage_messages/0')) ?>">
			<span>Manage Messages</span>
		</a>
	</div>
	
	<div class="row">
		<label for="manage syndicates" class="col-8 pl-0">Manage your Syndicate(s) for FreeComETT</label>
		<a class="btn btn-outline-primary btn-sm col-4" href="<?=(base_url('syndicate/manage_syndicates/0')) ?>">
			<span>Manage your <?php echo $session->current_project[0]['project_name'] ?> Syndicates for FreeComETT</span>
		</a>
	</div>
			
	<br>
	
	<div class="row mt-4 d-flex justify-content-between">	
		<a id="return" class="btn btn-primary mr-0" href="<?php echo(base_url('housekeeping/index/0')); ?>">
			<?php echo $session->current_project[0]['back_button_text']?>
		</a>
	</div>
