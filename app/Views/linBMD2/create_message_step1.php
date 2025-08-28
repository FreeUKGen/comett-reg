	<?php $session = session(); ?>
	
	<div class="step1">
		<form action="<?php echo(base_url('messaging/create_message_step2')) ?>" method="post">
			<div class="form-group row d-flex align-items-center">
				<label for="from_date" class="col-2">Message should be shown from this date =></label>
				<input type="text" class="form-control col-2" id="from_date" name="from_date" aria-describedby="userHelp" value="<?php echo esc($session->from_date); ?>">
				<small id="userHelp" class="form-text text-muted col-2">Enter a date in yyyy-mm-dd format.</small>
			</div>
			
			<div class="form-group row d-flex align-items-center">
				<label for="to_date" class="col-2">Message should be shown until this date =></label>
				<input type="text" class="form-control col-2" id="to_date" name="to_date" aria-describedby="userHelp" value="<?php echo esc($session->to_date); ?>">
				<small id="userHelp" class="form-text text-muted col-2">Enter a date in yyyy-mm-dd format. Must be greater than from date!</small>
			</div>
			
			<div class="form-group row d-flex align-items-center">
				<label for="colour" class="col-2">Message should be shown in this colour =></label>
				<select name="colour" id="colour" class="box col-2">
					<?php foreach ($session->colours as $key => $colour): ?>
						 <option value="<?php echo esc($key)?>"<?php if ( $key == $session->colour ) {echo esc(' selected');} ?>><?php echo esc($colour)?></option>
					<?php endforeach; ?>
				</select>
				<small id="userHelp" class="form-text text-muted col-2">Pick a colour from the dropdown list.</small>
			</div>
			
			<div class="form-group row d-flex align-items-center">
				<label for="message" class="col-2">Message text =></label>
				<textarea id="message" name="message" rows="4" cols="50" aria-describedby="userHelp" value="<?php echo esc($session->message); ?>"> </textarea>
				<small id="userHelp" class="form-text text-muted col-2">Type your message. No more than 200 characters. Be concise! New line = \n</small>
			</div>
		
			<div class="row mt-4 d-flex justify-content-between">	
				<a id="return" class="btn btn-primary mr-0" href="<?php echo(base_url('messaging/manage_messages/0')); ?>">
					<?php echo $session->current_project[0]['back_button_text']?>
				</a>
				<button type="submit" class="create_message btn btn-primary mr-0">
					<span>Create Message</span>	
				</button>
			</div>
	
	</form>
