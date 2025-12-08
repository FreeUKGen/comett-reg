	<?php $session = session(); ?>
	
	<div class="row text-center table-responsive w-auto" style="max-height: 450px;">
		<table class="table table-hover table-striped table-borderless" style="border-collapse: separate; border-spacing: 0;">
			<thead class="sticky-top bg-white">
				<tr>
					<th>Licence</th>
					<th>Popularity</th>
					<th>What do you want to do?</th>
					<th>
						<form action="<?= (base_url('licence/add_licence')) ?>" method="post">
							<div>
								<input type="text" class="form-control" id="add_licence" name="add_licence" placeholder="Add licence..." value="<?= ($session->add_licence) ?>">
								<button type="submit" class="btn btn-primary mr-0">
									<span>Add Licence</span>	
								</button>
							</div>
						</form>
					
					</th>
					<th>
						<form action="<?= (base_url('licence/search')) ?>" method="post">
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
				<?php foreach ($session->licences as $licence): ?>
						<tr>
							<td><?= esc($licence['Licence'])?></th>
							<td><?= esc($licence['Licence_popularity'])?></td>
							<td>
								<label for="next_action" class="sr-only">Next action</label>
									<select class="box" name="next_action" id="next_action">
										<?php foreach ($session->transcription_cycles as $key => $transcription_cycle): ?>
											<?php if ( $transcription_cycle['BMD_cycle_type'] == 'LICNA' ): ?>
												 <option value="<?= esc($transcription_cycle['BMD_cycle_code'])?>">
													<?= esc($transcription_cycle['BMD_cycle_name'])?>
												</option>
											<?php endif; ?>
										<?php endforeach; ?>
									</select>
							</td>
							<td>
								<button  
									data-id="<?= esc($licence['Licence']); ?>" 
									class="go_licence_button btn btn-success btn-sm">Go
								</button>
							</td>
						</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	
	<div>
		<form action="<?= (base_url('licence/next_action')); ?>" method="POST" name="form_next_action" >
			<input name="Licence" id="Licence" type="hidden" />
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
// handle licence actions
	$('.go_licence_button').on("click", function()
			{
				// define the variables
				var id=$(this).data('id');
				var BMD_next_action=$(this).parents('tr').find('select[name="next_action"]').val();
				// load variables to form
				$('#Licence').val(id);
				$('#BMD_next_action').val(BMD_next_action);
				// and submit the form
				$('form[name="form_next_action"]').submit();
			});
</script>	