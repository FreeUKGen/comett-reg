	<?php $session = session(); ?>
	
	<div class="step1">
		<form action="<?php echo(base_url('help/help_change_step2')) ?>" method="post">			
			<div class="form-group row d-flex align-items-center">
				<label for="help_category" class="col-2">Help/Howto category =></label>
				<select name="help_category" id="help_category" class="box col-2">
					<?php foreach ($session->help_categories as $key => $category): ?>
						 <option value="<?php echo esc($key)?>"<?php if ( $key == $session->help_category ) {echo esc(' selected');} ?>><?php echo esc($category)?></option>
					<?php endforeach; ?>
				</select>
				<small id="userHelp" class="form-text text-muted col-2">Pick a category from the list.</small>
			</div>
			
			<div class="form-group row d-flex align-items-center">
				<label for="help_title" class="col-2">Help/Howto title =></label>
				<input type="text" class="form-control col-5" id="help_title" name="help_title" aria-describedby="userHelp" value="<?php echo esc($session->help_title); ?>">
				<small id="userHelp" class="form-text text-muted col-2">The title of the help/howto shown to the user. Limit 60 characters.</small>
			</div>
			
			<div class="form-group row d-flex align-items-center">
				<label for="help_url" class="col-2">Help/Howto URL =></label>
				<input type="text" class="form-control col-6" id="help_url" name="help_url" aria-describedby="userHelp" value="<?php echo esc($session->help_url); ?>">
				<small id="userHelp" class="form-text text-muted col-3">The URL used to retrieve the document to show to the user. Limit 100 characters. This must be a fully qualified URL, eg https://www.freebmd.org.uk/vol_faq.html. Always test it in Manage Help!</small>
			</div>
			
			<div class="form-group row d-flex align-items-center">
				<label for="help_permanent" class="col-2">Permanent entry =></label>
				<input type="text" class="form-control col-1" id="help_permanent" name="help_permanent" aria-describedby="userHelp" value="<?php echo esc($session->help_permanent); ?>">
				<small id="userHelp" class="form-text text-muted col-3">Is this entry Permanent = YES? Else = NO.</small>
			</div>
		
			<div class="row mt-4 d-flex justify-content-between">	
				<a id="return" class="btn btn-primary mr-0" href="<?php echo(base_url('help/help_manage/0')); ?>">
					<?php echo $session->current_project[0]['back_button_text']?>
				</a>
				<button type="submit" class="create_message btn btn-primary mr-0 d-flex">
					<span>Change Help/Howto</span>	
				</button>
			</div>
	
	</form>
