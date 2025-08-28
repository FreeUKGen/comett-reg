	<?php $session = session(); ?>
	
	<div class="row text-center table-responsive w-auto" style="max-height: 450px;">
		<table class="table table-hover table-striped table-borderless" style="border-collapse: separate; border-spacing: 0;">
			<thead class="sticky-top bg-white">
				<tr>
					<th>Surname</th>
					<th>Popularity</th>
					<th>What do you want to do?</th>
					<th></th>
					<th>
						<form action="<?=(base_url('surname/search')) ?>" method="post">
							<div>
								<input type="text" class="form-control" id="search" name="search" placeholder="Enter some text to search for..." value="<?=($session->search) ?>">
								<button type="submit" class="btn btn-primary mr-0">
									<span>Search</span>	
								</button>
							</div>
						</form>
					</th>
				</tr>
			</thead>

			<tbody>
				<?php foreach ($session->surnames as $surname): ?>
						<tr>
							<td><?= esc($surname['Surname'])?></td>
							<td><?= esc($surname['Surname_popularity'])?></td>
							<td>
								<label for="next_action" class="sr-only">Next action</label>
									<select class="box" name="next_action" id="next_action">
										<?php foreach ($session->transcription_cycles as $key => $transcription_cycle): ?>
											<?php if ( $transcription_cycle['BMD_cycle_type'] == 'SURNA' ): ?>
												 <option value="<?= esc($transcription_cycle['BMD_cycle_code'])?>">
													<?= esc($transcription_cycle['BMD_cycle_name'])?>
												</option>
											<?php endif; ?>
										<?php endforeach; ?>
									</select>
							</td>
							<td>
								<button  
									data-id="<?= esc($surname['Surname']); ?>" 
									class="go_surname_button btn btn-success btn-sm">Go
								</button>
							</td>
						</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	
	<div>
		<form action="<?=(base_url('surname/next_action')); ?>" method="POST" name="form_next_action" >
			<input name="Surname" id="Surname" type="hidden" />
			<input name="BMD_next_action" id="BMD_next_action" type="hidden" />
		</form>
	</div>
	
	<div>	
		<br>
		<a id="return" class="btn btn-primary mr-0" href="<?=(base_url('housekeeping/index/0')); ?>">
			<?php echo $session->current_project[0]['back_button_text']?>
		</a>
	</div>
	
<script>	
// handle firstname actions
	$('.go_firstname_button').on("click", function()
			{
				// define the variables
				var id=$(this).data('id');
				var BMD_next_action=$(this).parents('tr').find('select[name="next_action"]').val();
				// load variables to form
				$('#Firstname').val(id);
				$('#BMD_next_action').val(BMD_next_action);
				// and submit the form
				$('form[name="form_next_action"]').submit();
			});
			
	// handle surname actions
	$('.go_surname_button').on("click", function()
			{
				// define the variables
				var id=$(this).data('id');
				var BMD_next_action=$(this).parents('tr').find('select[name="next_action"]').val();
				// load variables to form
				$('#Surname').val(id);
				$('#BMD_next_action').val(BMD_next_action);
				// and submit the form
				$('form[name="form_next_action"]').submit();
			});
</script>	
