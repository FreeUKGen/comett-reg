	<?php $session = session(); ?>
	
	<div>
		<div class="row small text-muted">
			<p class="col-2 pl-0">Height in pixels</p>
			<p class="col-10">Allows you to increase/decrease the height of the window for the image.</p>
		</div>
	</div>
	
	<div>
		<div class="row small text-muted">
			<p class="col-2 pl-0">Scroll step</p>
			<p class="col-10">Allows you to set the number of pixels that the image will be scrolled when revealing next line.</p>
		</div>
	</div>
	
	<div>
		<div class="row small text-muted">
			<p class="col-2 pl-0">Rotate</p>
			<p class="col-10">Allows you to rotate the image +ve for rotate right; -ve for rotate left.</p>
		</div>
	</div>
	
	<div>
		<b>
			<div class="row">
				<p class="col-2 pl-0"></p>
				<p class="col-2 text-center">Height (pixels)</p>
				<p class="col-2 text-center">Scroll Step (pixels)</p>
				<p class="col-2 text-center">Rotate (degrees)</p>
			</div>
		</b>
	</div>
	
	<div>
			<div class="row">
				<p class="col-2 pl-0 font-weight-bold">Default</p>
				<p class="col-2 text-center">35</p>
				<p class="col-2 text-center">12.65</p>
				<p class="col-2 text-center">0</p>
			</div>
	</div>
	
	<div>
			<div class="row">
				<p class="col-2 pl-0 font-weight-bold">Current</p>
				<p class="col-2 text-center"><?php echo($session->image_y);?></p>
				<p class="col-2 text-center"><?php echo($session->scroll_step);?></p>
				<p class="col-2 text-center"><?php echo($session->rotation);?></p>
			</div>
	</div>

	<div>
		<form action="<?php echo(base_url('transcribe/image_parameters_step2/'.$session->current_transcription[0]['BMD_header_index'])); ?>" method="post">
			<div class="form-group row">
				<label for="image_zoom" class="col-2 pl-0 font-weight-bold">New</label>
				<input type="text" class="form-control col-2 text-center" id="image_height" name="image_height" autofocus value="<?php echo esc($session->image_y);?>">
				<input type="text" class="form-control col-2 text-center" id="image_scroll_step" name="image_scroll_step" autofocus value="<?php echo esc($session->scroll_step);?>">
				<input type="text" class="form-control col-2 text-center" id="image_rotate" name="image_rotate" autofocus value="<?php echo esc($session->rotation);?>">
			</div>
		
		<div>
	</div>
		
	<div class="row mt-4 d-flex justify-content-between">	
		<a id="return" class="btn btn-primary mr-0" href="<?php echo(base_url($session->controller.'/transcribe_'.$session->controller.'_step1/0')); ?>">
			<?php echo $session->current_project[0]['back_button_text']?>
		</a>
		<button type="submit" class="btn btn-primary mr-0 d-flex">
			<span>Apply</span>	
		</button>
	</div>
		
			
		</form>
	</div>
	
