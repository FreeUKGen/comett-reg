	<?php $session = session(); ?>
	
	<div class="row text-center table-responsive w-auto" style="max-height: 450px;">
		<table class="table table-hover table-striped table-borderless" style="border-collapse: separate; border-spacing: 0;">
			<thead class="sticky-top bg-white">
				<tr>
					<th>Parish</th>
					<th>Popularity</th>
					<th>What do you want to do?</th>
					<th>
						<form action="<?= (base_url('parish/add_parish')) ?>" method="post">
							<div>
								<input type="text" class="form-control" id="add_parish" name="add_parish" placeholder="Add parish..." value="<?= ($session->add_parish) ?>">
								<button type="submit" class="btn btn-primary mr-0">
									<span>Add Parish</span>	
								</button>
							</div>
						</form>
					
					</th>
					<th>
						<form action="<?= (base_url('parish/search')) ?>" method="post">
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
				<?php foreach ($session->parishes as $parish): ?>
						<tr>
							<td><?= esc($parish['Parish'])?></th>
							<td><?= esc($parish['Parish_popularity'])?></td>
							<td>
								<label for="next_action" class="sr-only">Next action</label>
									<select class="box" name="next_action" id="next_action">
										<?php foreach ($session->transcription_cycles as $key => $transcription_cycle): ?>
											<?php if ( $transcription_cycle['BMD_cycle_type'] == 'PARNA' ): ?>
												 <option value="<?= esc($transcription_cycle['BMD_cycle_code'])?>">
													<?= esc($transcription_cycle['BMD_cycle_name'])?>
												</option>
											<?php endif; ?>
										<?php endforeach; ?>
									</select>
							</td>
							<td>
								<button  
									data-id="<?= esc($parish['Parish']); ?>" 
									class="go_parish_button btn btn-success btn-sm">Go
								</button>
							</td>
						</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	
	<div>
		<form action="<?= (base_url('parish/next_action')); ?>" method="POST" name="form_next_action" >
			<input name="Parish" id="Parish" type="hidden" />
			<input name="BMD_next_action" id="BMD_next_action" type="hidden" />
		</form>
	</div>

<br>
	
	<div class="row mt-4 d-flex justify-content-between">	
		<a id="return" class="btn btn-primary mr-0" href="<?= (base_url('housekeeping/index/0')); ?>">
			<?php echo $session->current_project[0]['back_button_text']?>
		</a>
	</div>

<script>	
// handle parish actions
	$('.go_parish_button').on("click", function()
			{
				// define the variables
				var id=$(this).data('id');
				var BMD_next_action=$(this).parents('tr').find('select[name="next_action"]').val();
				// load variables to form
				$('#Parish').val(id);
				$('#BMD_next_action').val(BMD_next_action);
				// and submit the form
				$('form[name="form_next_action"]').submit();
			});
</script>	
