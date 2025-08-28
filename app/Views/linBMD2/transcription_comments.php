	<?php $session = session(); ?>
	
	<div class="step1">
		<form action="<?php echo(base_url('transcription/comments_step2')) ?>" method="post">
			<!-- show transcription comment text  -->
		<div class="form-group row d-flex align-items-center">
			<label for="comment_text" class="col-2">Comments for this transcription =></label>
			<textarea rows="3" class="form-control col-6" id="comment_text" name="comment_text" aria-describedby="userHelp"><?=esc($session->comment_text)?></textarea>
			<small id="userHelp" class="form-text text-muted col-4">You can enter / change a Document Comment at any time for this transcription here if you want. If you want to remove it, just make it blank.</small>
			
			<label for="source_text" class="col-2">Source for this transcription =></label>
			<select class="form-control col-6" id="source_text" name="source_text" aria-describedby="userHelp">
				<option value="SL">Select:</option>
				<?php 
				foreach ( $session->document_sources as $key => $document_source ) 
					{?>	
									
						<option value="<?= esc($document_source['document_source'])?>" <?php if ( $document_source['document_source'] == $session->source_text ) {echo esc(' selected');} ?>>
							<?= esc($document_source['document_source'])?>
						</option>
					<?php
					}?>

			</select>
			
			<small id="userHelp" class="form-text text-muted col-4">You can enter / change a Document Source at any time for this transcription here if you want. If you want to remove it, just make it blank.</small>
		</div>
		
			<div class="row mt-4 d-flex justify-content-between">	
				<a id="return" class="btn btn-primary mr-0" href="<?php echo(base_url('transcribe/transcribe_step1/0')); ?>">
					<?php echo $session->current_project[0]['back_button_text']?>
				</a>
				<button type="submit" class="create_message btn btn-primary mr-0 d-flex">
					<span>Add / Change / Remove Document Comments/Document Source</span>	
				</button>
			</div>
	
	</form>
