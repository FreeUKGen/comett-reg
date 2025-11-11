	<?php $session = session(); ?>
	
	<div class="row text-center table-responsive w-auto" style="max-height: 450px;">
		<table class="table table-hover table-striped table-borderless" style="border-collapse: separate; border-spacing: 0;">
			<thead class="sticky-top bg-white">
				<tr>
					<th>Document Source</th>
					<th>Popularity</th>
					<th>What do you want to do?</th>
					<th>
						<form action="<?= (base_url('document_sources/add_document_source')) ?>" method="post">
							<div>
								<input type="text" class="form-control" id="add_document_source" name="add_document_source" placeholder="Add document_source..." value="<?= ($session->add_document_source) ?>">
								<button type="submit" class="btn btn-primary mr-0">
									<span>Add Document Source</span>	
								</button>
							</div>
						</form>
					
					</th>
					<th>
						<form action="<?= (base_url('document_sources/search')) ?>" method="post">
							<div>
								<input type="text" class="form-control" id="search" name="search" placeholder="Search..." value="<?= ($session->search) ?>">
								<button type="submit" class="btn btn-primary mr-0">
									<span>Search</span>	
								</button>
							</div>
						</form>
					</th>
				</tr>
			</thead>

			<tbody>
				<?php foreach ($session->document_sources as $document_source): ?>
						<tr>
							<td><?= esc($document_source['document_source'])?></th>
							<td><?= esc($document_source['document_source_popularity'])?></td>
							<td>
								<label for="next_action" class="sr-only">Next action</label>
									<select class="box" name="next_action" id="next_action">
										<?php foreach ($session->transcription_cycles as $key => $transcription_cycle): ?>
											<?php if ( $transcription_cycle['BMD_cycle_type'] == 'DOCNA' ): ?>
												 <option value="<?= esc($transcription_cycle['BMD_cycle_code'])?>">
													<?= esc($transcription_cycle['BMD_cycle_name'])?>
												</option>
											<?php endif; ?>
										<?php endforeach; ?>
									</select>
							</td>
							<td>
								<button  
									data-id="<?= esc($document_source['document_source']); ?>" 
									class="go_document_source_button btn btn-success btn-sm">Go
								</button>
							</td>
						</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	
	<div>
		<form action="<?= (base_url('document_sources/next_action')); ?>" method="POST" name="form_next_action" >
			<input name="document_source" id="document_source" type="hidden" />
			<input name="BMD_next_action" id="BMD_next_action" type="hidden" />
		</form>
	</div>

<br>
	
	<div class="row mt-4 d-flex justify-content-between">	
		<a id="return" class="btn btn-primary mr-0" href="<?= (base_url('housekeeping/index/0')); ?>">
			<?php echo $session->current_project['back_button_text']?>
		</a>
	</div>

<script>	
// handle document_source actions
	$('.go_document_source_button').on("click", function()
			{
				// define the variables
				var id=$(this).data('id');
				var BMD_next_action=$(this).parents('tr').find('select[name="next_action"]').val();
				// load variables to form
				$('#document_source').val(id);
				$('#BMD_next_action').val(BMD_next_action);
				// and submit the form
				$('form[name="form_next_action"]').submit();
			});
</script>	