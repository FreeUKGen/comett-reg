	<?php $session = session(); ?>
	
	<div class="row text-center table-responsive w-auto" style="max-height: 450px;">
		<table class="table table-hover table-striped table-borderless" style="border-collapse: separate; border-spacing: 0;">
			<thead class="sticky-top bg-white">
				<tr class="text-primary">
					<th>TranscriberID</th>
					<th>Role</th>
					<th>Environment</th>
					<th>Verify Mode</th>
					<th>Last Allocation</th>
					<th>Last Transcription</th>
					<th>
						<input class="box no-sort" id="search" type="text" placeholder="Search..." >		
					</th>
				</tr>
			</thead>
			<tbody id="user_table">
				<?php foreach ($session->freecomett_transcribers as $key => $transcriber): ?>	
					<td><?= esc($transcriber['BMD_user'])?></td>
					<td><?= esc($transcriber['role_name'])?></td>
					<td><?= esc($transcriber['environment'])?></td>
					<td><?= esc($transcriber['verify_mode'])?></td>
					<td><?= esc($transcriber['last_allocation_name'])?></td>
					<td><?= esc($transcriber['last_transcription_name'])?></td>
					<td>
						<label for="next_action" class="sr-only">Next action</label>
							<select class="box" name="next_action" id="next_action">
								<?php foreach ($session->transcription_cycles as $key => $transcription_cycle): ?>
									 <?php if ( $transcription_cycle['BMD_cycle_type'] == 'SYUSR' ): ?>
										 <option value="<?= esc($transcription_cycle['BMD_cycle_code'])?>">
											<?= esc($transcription_cycle['BMD_cycle_name'])?>
										</option>
									<?php endif; ?>
								<?php endforeach; ?>
							</select>
					</td>
					<td>
						<button  
							data-id="<?= esc($transcriber['BMD_identity_index']); ?>" 
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
			<input name="BMD_next_action" id="BMD_next_action" type="hidden" />
			<input name="BMD_identity_index" id="BMD_identity_index" type="hidden" />
			
		</form>
	</div>
	
	<br>
	
	<div class="row mt-4 d-flex justify-content-between">
		<a id="return" class="btn btn-primary mr-0" href="<?=(base_url('syndicate/manage_syndicates/0')); ?>">
			<?php echo $session->current_project[0]['back_button_text']?>
		</a>
		<?php
		if ( $session->masquerade == 1 )
			{ ?>
				<a class="btn btn-primary mr-0 d-flex" href="<?=(base_url('syndicate/stop_masquerading/')) ?>">Stop Masquerading as Coordinator</a>
			<?php
			} ?>
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
				$('#BMD_identity_index').val(id);
				$('#BMD_next_action').val(BMD_next_action);
				// and submit the form
				$('form[name="form_next_action"]').submit();
			});
	});

</script>
