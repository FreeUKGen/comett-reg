	<?php $session = session(); ?>
	
	<form action="<?php echo(base_url('document_sources/correct_document_source_step2')) ?>" method="post">
	
		<div class="form-group row">
			<label for="identity">Current Document Source</label>
			<input type="text" class="form-control" id="current_document_source" name="current_document_source" aria-describedby="userHelp" value="<?php echo($session->document_source_to_corrected['document_source']) ?>" readonly tabindex=	"-1">
		</div>
	  
		<div class="form-group row">
				<label for="corrected_firstname">Corrected document_source</label>
				<input type="text" class="form-control" id="corrected_document_source" name="corrected_document_source" value="<?php echo($session->corrected_document_source) ?>" autofocus>
		</div>		
		
		<div class="row mt-2 d-flex justify-content-between">
			
				<a id="return" class="btn btn-primary mr-0" href="<?php echo(base_url('document_sources/manage_document_sources/0')); ?>">
					<?php echo $session->current_project[0]['back_button_text']?>
				</a>

				<button type="submit" class="btn btn-primary mr-0">
					<span>Submit</span>	
				</button>
			
		</div>
		
	</form>


