<?php $session = session(); ?>
	
	<div class="row text-center table-responsive w-auto" style="max-height: 450px;">
		<table class="table table-hover table-striped table-borderless" style="border-collapse: separate; border-spacing: 0;">
			<thead class="sticky-top bg-white">
				<tr class="text-primary">
					<th>Type</th>
					<th>From</th>
					<th>To</th>
					<th>Volume</th>
					<th>
						<input class="box" id="search" type="text" placeholder="Search..." >
					</th>
					<th></th>
				</tr>
			</thead>

			<tbody id="user_table">
				<?php foreach ( $session->volumes as $volume ) 
					{ 
						?>
						<tr>
							<td><?= esc($volume['BMD_type'])?></td>
							<td><?= esc($volume['volume_from'])?></td>
							<td><?= esc($volume['volume_to'])?></td>
							<td><?= esc($volume['volume'])?></td>
							<td>
								<label for="next_action" class="sr-only">Next action</label>
									<select class="box" name="next_action" id="next_action">
										<?php foreach ($session->cycle as $key => $cycle): ?>
												<option value="<?= esc($cycle['BMD_cycle_code'])?>">
													<?= esc($cycle['BMD_cycle_name'])?>
												</option>
										<?php endforeach; ?>
									</select>
							</td>
							<td>
								<button  
									data-id="<?= esc($volume['volume_index']); ?>" 
									class="go_volume_button btn btn-success">Go
								</button>
							</td>
						</tr>
					<?php
					} ?>
			</tbody>
		</table>
	</div>
	
	<div>
		<form action="<?=(base_url('district/next_action_volume')); ?>" method="POST" name="form_next_action" >
			<input name="volume_index" id="volume_index" type="hidden" />
			<input name="BMD_next_action" id="BMD_next_action" type="hidden" />
		</form>
	</div>
	
	<div class="row mt-4 d-flex justify-content-between">
		<a id="return" class="btn btn-primary mr-0" href="<?=(base_url('district/manage_districts/0')); ?>">
			<?php echo $session->current_project[0]['back_button_text']?>
		</a>
		
		<a id="add_volume" class="btn btn-primary mr-0" href="<?php echo(base_url('district/add_volume_step1/0')) ?>">
			<span>Add Volume</span>
		</a>
	</div>
	
<script>	
// handle volume actions
	$('.go_volume_button').on("click", function()
			{
				// define the variables
				var id=$(this).data('id');
				var BMD_next_action=$(this).parents('tr').find('select[name="next_action"]').val();
				// load variables to form
				$('#volume_index').val(id);
				$('#BMD_next_action').val(BMD_next_action);
				// and submit the form
				$('form[name="form_next_action"]').submit();
			});
</script>	
