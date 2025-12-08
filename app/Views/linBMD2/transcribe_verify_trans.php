	<?php $session = session(); ?>
	
	<?php
		if ( ! isset($session->feh_show) )
			{
				
				$session->set('feh_show', 1);
			}
	?>
	<br><br><br><br><br><br>
	<div class="row mt-4 d-flex justify-content-between">	
			<a class="btn btn-primary btn-sm" href="<?php echo(base_url('transcribe/transcribe_step1/0')); ?>">Return</a>
	</div>
	
