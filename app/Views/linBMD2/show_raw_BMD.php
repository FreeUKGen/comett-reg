	<?php $session = session(); ?>
	
	<?php
	// read content
	foreach ( $session->csv_file as $csv_file )
		{
			$content = "
				<code>
					<pre>".htmlspecialchars($csv_file['csv_string'])."</pre>
				</code>";
			// display
			?>
			<div class="row table-responsive w-auto" style="height:500px">
				<?php
					echo $content;
				?>
			</div>
		<?php
		} ?>
		
	<div class="row mt-4 d-flex justify-content-between">	
		<a id="return" class="btn btn-primary mr-0" href="<?php echo(base_url('transcribe/transcribe_step1/0')); ?>">
			<?php echo $session->current_project['back_button_text']?>
		</a>
	</div