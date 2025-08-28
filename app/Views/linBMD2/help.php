	<?php $session = session(); ?>
		
	<div class="row d-flex justify-content-end mt-4">
		<iframe src="<?php echo $session->current_help[0]['help_url']; ?>" target="_blank" width="100%" height="100%">
	</div>
