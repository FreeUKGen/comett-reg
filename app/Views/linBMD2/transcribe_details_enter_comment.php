<?php $session = session(); ?>

	<!-- show transcription comment text  -->
	<?php
	if ( $session->current_project[0]['project_index'] != 2 )
		{ ?>
			<div class="form-group row d-flex align-items-center">
				<label for="comment_text" class="col-2">Comment for this transcription =></label>
				<input type="text" class="form-control col-6" id="comment_text" name="comment_text" aria-describedby="userHelp" value="<?php echo esc($session->comment_text); ?>">
				<small id="userHelp" class="form-text text-muted col-4">You can enter / change a comment at any time for this transcription here if you want. If you want to remove it, just make it blank. The comment will be updated each time you enter a detail line.</small>
			</div>
		<?php
		} ?>



