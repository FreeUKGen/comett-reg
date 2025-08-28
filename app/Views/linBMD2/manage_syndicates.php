	<?php $session = session(); ?>
	
	<div class="row text-center table-responsive w-auto" style="max-height: 450px;">
		<table class="table table-hover table-striped table-borderless" style="border-collapse: separate; border-spacing: 0;">
			<thead class="sticky-top bg-white">
				<tr class="text-primary">
					<th>Syndicate Name</th>
					<th>Add Credit Line to CSV file? Y or N</th>
					<th>New Transcriber Environment? LIVE or TEST</th>
					<th>New Transcriber Verification? onthefly or after</th>
					<th>Syndicate Email</th>
					<th>Recruiting? 1=Yes</th>
					<th>Status? 0=Open, 1=Closed</th>
					<th>
						<input class="box no-sort" id="search" type="text" placeholder="Search..." >
					</th>
					<th class="no-sort"></th>
				</tr>
			</thead>
			<tbody  id="user_table">
				<?php foreach ($session->temp_synd as $key => $syndicate): ?>	
					<td><?= esc($syndicate['syndicate_name'])?></td>
					<td><?= esc($syndicate['BMD_syndicate_credit'])?></td>
					<td><?= esc($syndicate['new_user_environment'])?></td>
					<td><?= esc($syndicate['verify_mode'])?></td>
					<td><?= esc($syndicate['syndicate_email'])?></td>
					<td><?= esc($syndicate['recruiting'])?></td>
					<td><?= esc($syndicate['status'])?></td>
					<td>
						<label for="next_action" class="sr-only">Next action</label>
							<select class="box" name="next_action" id="next_action">
								<?php foreach ($session->transcription_cycles as $key => $transcription_cycle): ?>
									 <?php if ( $transcription_cycle['BMD_cycle_type'] == 'SYNDC' ): ?>
										 <option value="<?= esc($transcription_cycle['BMD_cycle_code'])?>">
											<?= esc($transcription_cycle['BMD_cycle_name'])?>
										</option>
									<?php endif; ?>
								<?php endforeach; ?>
							</select>
					</td>
					<td>
						<button  
							data-id="<?= esc($syndicate['syndicate_id']); ?>" 
							class="go_button btn btn-success">Go
						</button>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	
	<div>
		<form action="<?=(base_url('syndicate/next_action')); ?>" method="POST" name="form_next_action" >
			<input name="BMD_syndicate_index" id="BMD_syndicate_index" type="hidden" />
			<input name="BMD_next_action" id="BMD_next_action" type="hidden" />
		</form>
	</div>
	
	<br>
	
	<div>	
		<a id="return" class="btn btn-primary mr-0" href="<?=(base_url('database/coord_step1/0')); ?>">
			<?php echo $session->current_project[0]['back_button_text']?>
		</a>
	</div>

<script>
	
$(document).ready(function()
	{	
		$('.go_button').on("click", function()
			{
				// define the variables
				var id=$(this).data('id');
				var BMD_next_action=$(this).parents('tr').find('select[name="next_action"]').val();
				// load variables to form
				$('#BMD_syndicate_index').val(id);
				$('#BMD_next_action').val(BMD_next_action);
				// and submit the form
				$('form[name="form_next_action"]').submit();
			});
	});

</script>


