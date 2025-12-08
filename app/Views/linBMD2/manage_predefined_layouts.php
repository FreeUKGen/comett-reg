	<?php $session = session(); ?>
	
	
		<div>
			<form action="<?= (base_url('predefined_layouts/add_predefined_layout_step1')) ?>" method="post">
			<span class="row mt-2">
				<span>
					<input type="text" class="form-control" id="add_predefined_layout" name="add_predefined_layout" placeholder="Add_predefined_layout..." value="<?= ($session->add_predefined_layout) ?>">
				</span>
				<span>
					<select
						id="add_event_type"
						name="add_event_type"
						style="font-size:1vw !important;">
							<option
								value="na">
								For Event type...
							</option>
							<?php
							foreach ( $session->event_types as $event_type )
								{ 
									?>
									<option
										value="<?=$event_type['type_name_lower']?>"
										<?php if ( $session->add_event_type == $event_type['type_name_lower'] ) { echo ' selected'; } ?>>
										<?=$event_type['type_name_lower']?>
									</option>
								<?php
								} ?>
					</select>
				</span>
				<span>
					<button type="submit" class="btn btn-primary mr-0">
						<span>Create Predefined Layout</span>	
					</button>
				</span>
			</span>
		</form>
	</div>
	
	<div>
	<form action="<?= (base_url('predefined_layouts/search')) ?>" method="post">
	<span class="row mt-2">
		<span>
			<input type="text" class="form-control" id="search" name="search" placeholder="Search..." value="<?= ($session->search) ?>">
		</span>
		<span>
			<button type="submit" class="btn btn-primary mr-0">
				<span>Search</span>	
			</button>
		</span>
	</span>
	</form>
	</div>
						
	
	<div class="row text-center table-responsive w-auto" style="max-height: 450px;">
		<table class="table table-hover table-striped table-borderless" style="border-collapse: separate; border-spacing: 0;">
			<thead class="sticky-top bg-white">
				<tr>
					<th>Event Type</th>
					<th>Predefined Layout</th>
					<th>What do you want to do?</th>
				</tr>
			</thead>

			<tbody>
				<?php foreach ($session->predefined_layouts as $layout): ?>
						<tr>
							<td><?= esc($layout['event_type'])?></th>
							<td><?= esc($layout['layout_name'])?></td>
							<td>
								<label for="next_action" class="sr-only">Next action</label>
									<select class="box" name="next_action" id="next_action">
										<?php foreach ($session->transcription_cycles as $key => $transcription_cycle): ?>
											<?php if ( $transcription_cycle['BMD_cycle_type'] == 'PRELA' ): ?>
												 <option value="<?= esc($transcription_cycle['BMD_cycle_code'])?>">
													<?= esc($transcription_cycle['BMD_cycle_name'])?>
												</option>
											<?php endif; ?>
										<?php endforeach; ?>
									</select>
							</td>
							<td>
								<button  
									data-id="<?= esc($layout['layout_index']); ?>" 
									class="go_document_source_button btn btn-success btn-sm">Go
								</button>
							</td>
						</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	
	<div>
		<form action="<?= (base_url('predefined_layouts/next_action')); ?>" method="POST" name="form_next_action" >
			<input name="layout_index" id="layout_index" type="hidden" />
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
				$('#layout_index').val(id);
				$('#BMD_next_action').val(BMD_next_action);
				// and submit the form
				$('form[name="form_next_action"]').submit();
			});
</script>	