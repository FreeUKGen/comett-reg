	<?php $session = session(); ?>
	
	<div class="row mt-4 d-flex justify-content-between bg-success font-weight-bold" style="font-size:2vw;">
		<button id="return" class="btn btn-primary mr-0 fa-solid fa-backward"></button>
		<span> 
			<?php
				if ( $session->allocation_status == 'Open' ) 
					{ 
						?>
						<a style="font-size:2vw !important;" href="<?=(base_url('allocation/toogle_allocations'))?>"><?php echo 'ACTIVE '.$session->current_project[0]['allocation_text'].'s for => '.$session->identity_userid.', transcribing for => '.$session->syndicate_name;?></a>
					<?php
					}
				else
					{
						?>
						<a style="font-size:2vw !important;" href="<?=(base_url('allocation/toogle_allocations'))?>"><?php echo 'CLOSED '.$session->current_project[0]['allocation_text'].'s for => '.$session->identity_userid.', transcribing for => '.$session->syndicate_name; ?></a>
					<?php
					}
			?>
		</span>
		<?php
		if ( $session->current_project[0]['project_index'] == '2' )
			{ ?>
				<button id="csv_file" class="btn btn-primary mr-0">Load CSV File</button>
			<?php
			} ?>
		<button id="alloc" class="btn btn-primary mr-0"><?='Create '.$session->current_project[0]['allocation_text']?></button>
	</div>
	
	<div class="row text-center table-responsive w-auto" style="max-height: 450px;">
		<table class="table table-hover table-borderless" style="border-collapse: separate; border-spacing: 0;">
			<thead class="sticky-top bg-white">
				<tr class="text-primary">
					<th><?php echo $session->current_project[0]['allocation_text'].' Name'?></th>
					<th>Start Date</th>
					<th>End Date</th>
					<th>Last page uploaded</th>
					<th>Status</th>
					<?php
					if ( $session->current_project[0]['project_index'] == '2' )
						{ ?>
							<th>Country</th>
							<th>County</th>
							<th>Place</th>
							<th>Church</th>
							<th>Register Type</th>
							<th>Image Source</th>
					<?php
						} ?>		
					<th>Last change date/time</th>
					<th>Last Action Performed</th>
					<th>
						<input class="box no-sort" id="search" type="text" placeholder="Search..." >		
					</th>
					<th class="no-sort"></th>
				</tr>
			</thead>

			<tbody  id="user_table">
				<?php foreach ($session->allocations as $allocation): ?>
					<?php 	if ( $allocation['BMD_status'] == 'Open' )
									{ ?>
										<tr class="alert alert-success">
									<?php 
									}
								else
									{ ?>
										<tr class="alert alert-light">
									<?php 		
									} ?>	
											<td class="align-middle"><?= esc($allocation['BMD_allocation_name'])?></td>
											<td class="align-middle"><?= esc($allocation['BMD_start_date'])?></td>
											<td class="align-middle"><?= esc($allocation['BMD_end_date'])?></td>
											<td class="align-middle"><?= esc($allocation['BMD_last_uploaded'])?></td>
											<td class="align-middle"><?= esc($allocation['BMD_status'])?></td>
											<?php
											if ( $session->current_project[0]['project_name'] == 'FreeREG' )
												{ ?>
													<td class="align-middle"><?= esc($allocation['REG_county_group'])?></td>
													<td class="align-middle"><?= esc($allocation['REG_county'].':'.$allocation['REG_chapman_code'])?></td>
													<td class="align-middle"><?= esc($allocation['REG_place'])?></td>
													<td class="align-middle"><?= esc($allocation['REG_church_name'].':'.$allocation['REG_church_code'])?></td>
													<td class="align-middle"><?= esc($allocation['register_description'])?></td>
													<td class="align-middle"><?= esc($allocation['source_name'])?></td>
												<?php
												} ?>		
											<td class="align-middle"><?= esc($allocation['Change_date'])?></td>
											<td class="align-middle"><?= esc($allocation['BMD_last_action'])?></td>
											<td class="align-middle">
												<label for="next_action" class="sr-only">Next action</label>
													<select class="box" name="next_action" id="next_action">
														<?php foreach ($session->transcription_cycles as $key => $transcription_cycle): ?>
															<?php if ( $transcription_cycle['BMD_cycle_type'] == 'ALLOC' ): ?>
																 <option value="<?= esc($transcription_cycle['BMD_cycle_code'])?>">
																	<?= esc($transcription_cycle['BMD_cycle_name'])?>
																</option>
															<?php endif; ?>
														<?php endforeach; ?>
													</select>
											</td>
											<td class="align-middle">
												<button  
													data-id="<?= esc($allocation['BMD_allocation_index']); ?>" 
													class="go_button btn btn-success">Go
												</button>
											</td>
										</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	
	<div>
		<form action="<?=(base_url('allocation/next_action')); ?>" method="POST" name="form_next_action" >
			<input name="allocation_index" id="allocation_index" type="hidden" />
			<input name="alloc_next_action" id="alloc_next_action" type="hidden" />
		</form>
	</div>
	
	<div>
		<form action="<?=(base_url('transcribe/transcribe_step1/0')); ?>" method="POST" name="form_return" ></form>
		<form action="<?=(base_url('allocation/create_allocation_step1/0')); ?>" method="POST" name="form_alloc" ></form>
		<form action="<?=(base_url('allocation/create_assignment_step1/0')); ?>" method="POST" name="form_assig" ></form>
		<form action="<?=(base_url('allocation/load_csv_file_step1/0')); ?>" method="POST" name="form_csvFile" ></form>	
	</div>
	
	

<script>
	
$(document).ready(function()
	{	
		$('.go_button').on("click", function()
			{
				// define the variables
				var id = $(this).data('id');
				var BMD_next_action = $(this).parents('tr').find('select[name="next_action"]').val();
				// load variables to form
				$('#allocation_index').val(id);
				$('#alloc_next_action').val(BMD_next_action);
				// and submit the form
				$('form[name="form_next_action"]').submit();
			});
			
		$('#return').on("click", function()
			{			
				$('form[name="form_return"]').submit();
			});
			
		$('#alloc').on("click", function()
			{			
				// get project
				var project_name = "<?=$session->current_project[0]['project_name']?>";
				switch(project_name) 
					{
						case 'FreeBMD':
							$('form[name="form_alloc"]').submit();
							break;
						case 'FreeREG':
							$('form[name="form_assig"]').submit();
							break;
						case 'FreeREG':
							break;
					}
			});
			
		$('#csv_file').on("click", function()
			{			
				$('form[name="form_csvFile"]').submit();				
			});
	});

</script>


