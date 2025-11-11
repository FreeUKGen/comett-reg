	<?php $session = session(); ?>
	
	<div class="row text-center table-responsive w-auto" style="max-height: 450px;">
		<table class="table table-hover table-striped table-borderless" style="border-collapse: separate; border-spacing: 0;">
			<thead class="sticky-top bg-white">
				<tr>
					<th>Condition</th>
					<th>Applies to m=male/f=female/b=both</th>
					<th>Popularity</th>
					<th>What do you want to do?</th>
					<th>
						<form action="<?= (base_url('condition/add_condition')) ?>" method="post">
							<div>
								<input type="text" class="form-control" id="add_condition" name="add_condition" placeholder="Add condition..." value="<?= ($session->add_condition) ?>">
								<input type="text" class="form-control" id="add_condition_sex" name="add_condition_sex" placeholder="Add Applies to m/f/b..." value="<?= ($session->add_condition_sex) ?>">
								<button type="submit" class="btn btn-primary mr-0">
									<span>Add Condition</span>	
								</button>
							</div>
						</form>
					
					</th>
					<th>
						<form action="<?= (base_url('condition/search')) ?>" method="post">
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
				<?php foreach ($session->conditions as $condition): ?>
						<tr>
							<td><?= esc($condition['Condition'])?></th>
							<td><?= esc($condition['condition_sex'])?></th>
							<td><?= esc($condition['Condition_popularity'])?></td>
							<td>
								<label for="next_action" class="sr-only">Next action</label>
									<select class="box" name="next_action" id="next_action">
										<?php foreach ($session->transcription_cycles as $key => $transcription_cycle): ?>
											<?php if ( $transcription_cycle['BMD_cycle_type'] == 'CONNA' ): ?>
												 <option value="<?= esc($transcription_cycle['BMD_cycle_code'])?>">
													<?= esc($transcription_cycle['BMD_cycle_name'])?>
												</option>
											<?php endif; ?>
										<?php endforeach; ?>
									</select>
							</td>
							<td>
								<button  
									data-id="<?= esc($condition['Condition']); ?>" 
									class="go_condition_button btn btn-success btn-sm">Go
								</button>
							</td>
						</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	
	<div>
		<form action="<?= (base_url('condition/next_action')); ?>" method="POST" name="form_next_action" >
			<input name="Condition" id="Condition" type="hidden" />
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
// handle condition actions
	$('.go_condition_button').on("click", function()
			{
				// define the variables
				var id=$(this).data('id');
				var BMD_next_action=$(this).parents('tr').find('select[name="next_action"]').val();
				// load variables to form
				$('#Condition').val(id);
				$('#BMD_next_action').val(BMD_next_action);
				// and submit the form
				$('form[name="form_next_action"]').submit();
			});
</script>	