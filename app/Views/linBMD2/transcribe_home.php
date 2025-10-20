
	<?php $session = session();
	use App\Models\Transcription_Comments_Model; ?>
	
	<div class="row mt-2 mb-2">		
		<header>	
		<aside>
			<?php					
				if ( $session->status == '0' ) 
					{ 
						?>
						<a href="<?=(base_url('transcribe/toogle_transcriptions'))?>"><?php echo 'Your ACTIVE transcriptions' ?></a>
					<?php
					}
				else
					{
						?>
						<a href="<?=(base_url('transcribe/toogle_transcriptions'))?>"><?php echo 'Your CLOSED transcriptions' ?></a>
					<?php
					}
					?>
		</aside>
		</header>
	</div>
	
	<div class="row text-center table-responsive w-auto">
		<table class="table table-borderless" style="border-collapse: separate; border-spacing: 0;" id="show_table">
			<thead class="sticky-top bg-white">
				<tr class="text-primary">
					<th><?php echo $session->current_project[0]['allocation_text'].' Name'?></th>
					<th>File</th>
					<?php
					if ( $session->current_project[0]['project_index'] == 2 ) 
						{ ?>
							<th>Document Source</th>
							<th>Image Source</th>
							<th>Image Count</th>
						<?php
						} ?>
					<th>Current Scan</th>
					<th>N° lines trans</th>
					<th>Start Date</th>
					<th>Last change date/time</th>
					<?php
					if ( $session->current_project[0]['project_index'] == 1 ) 
						{ ?>
							<th>Verified</th>
						<?php
						} ?>
					<th>Upload Date</th>
					<th>Status</th>
					<th>Comments</th>
					<th>Last Action Performed</th>				
					<?php
					if ( $session->status == '0' )
						{
						?>
							<th>
								<input class="box no-sort" id="search" type="text" placeholder="Search..." >		
							</th>
							<th class="no-sort"></th>
						<?php
						} ?>
				</tr>		
			</thead>

			<tbody id="user_table">
				<?php foreach ($session->transcriptions as $transcription) 
					{
						if ( $transcription['BMD_header_index'] == $session->current_header_index )
							{ ?>
								<tr class="alert alert-success">
							<?php 
							}
						else
							{ ?>
								<tr class="alert alert-light">
							<?php 
							} ?>
									<td style="border-bottom: 2pt solid green; cursor:pointer;" 
										class="edit_assignment"
										title="ClickMe to edit assignment if in FreeREG"
										data-id="<?=esc($transcription['BMD_allocation_index'])?>"
										data-action='CHGEA'>
										<?= esc($transcription['BMD_allocation_name'])?>
									</td>
									<td style="border-bottom: 2pt solid green;"><?= esc($transcription['BMD_file_name'])?></td>
									
									<?php
									if ( $session->current_project[0]['project_index'] == 2 ) 
										{ ?>
											<td style="border-bottom: 2pt solid green; cursor:pointer;"
												class="next_action"
												title="<?=esc($transcription['source_text'])?>"
												data-id="<?=esc($transcription['BMD_header_index'])?>"
												data-action='UPCOM'>
												<?php 	
												if ( !is_null($transcription['source_text']) )
													{ 
														echo esc(ellipsize($transcription['source_text'], 100, .5, '...'));
													}
												else
													{
														echo esc($transcription['source_text']);
													}
												?>
											</td>
											<td style="border-bottom: 2pt solid green;"><?= esc($transcription['image_source'])?></td>
											<td style="border-bottom: 2pt solid green;"><?= esc($transcription['image_count'])?></td>
										<?php
										} ?>
									
									<td style="border-bottom: 2pt solid green;"><?= esc($transcription['BMD_scan_name'])?></td>
									<td style="border-bottom: 2pt solid green; cursor:pointer;"
										class="next_action"
										title="ClickMe to Transcribe from Scan"
										data-id='<?=esc($transcription['BMD_header_index'])?>'
										data-action='INPRO'>
										<?= esc($transcription['BMD_records'])?>
									</td>
									<td style="border-bottom: 2pt solid green;"><?= esc($transcription['BMD_start_date'])?></td>
									<td style="border-bottom: 2pt solid green;"><?= esc($transcription['Change_date'])?></td>
									<?php
									if ( $session->current_project[0]['project_index'] == 1 ) 
										{ ?>
											<td style="border-bottom: 2pt solid green;"><?= esc($transcription['verified'])?></td>
										<?php
										} ?>
									<td style="border-bottom: 2pt solid green;"><?= esc($transcription['BMD_submit_date'])?></td>
									<td style="border-bottom: 2pt solid green; cursor:pointer;"
										class="next_action"
										title="ClickMe for Upload detail"
										data-id='<?=esc($transcription['BMD_header_index'])?>'
										data-action='UPDET'>
										<?= esc($transcription['BMD_submit_status'])?>
									</td>
									
									<td style="border-bottom: 2pt solid green; cursor:pointer;"
										class="next_action"
										title="<?=esc($transcription['comment_text'])?>"
										data-id='<?=esc($transcription['BMD_header_index'])?>'
										data-action='UPCOM'>
										<?php 	
										if ( !is_null($transcription['comment_text']) )
											{ 
												echo esc(ellipsize($transcription['comment_text'], 100, .5, '...'));
											}
										else
											{
												echo esc($transcription['comment_text']);
											}
										?>
									</td>
									
									<td style="border-bottom: 2pt solid green;"><?= esc($transcription['BMD_last_action'])?></td>

									<?php
										if ( $session->status == '0' )
											{
											?>
												<td style="border-bottom: 2pt solid green;">
														<select class="box" name="next_action" id="next_action">
															<?php foreach ($session->transcription_cycles as $key => $transcription_cycle): ?>
																 <?php if ( $transcription_cycle['BMD_cycle_type'] == 'TRANS' ): ?>
																	<option value="<?= esc($transcription_cycle['BMD_cycle_code'])?>">
																		<?= esc($transcription_cycle['BMD_cycle_name'])?>
																	</option>
																<?php endif; ?>
															<?php endforeach; ?>
														</select>
												</td>
											<?php
											}
									?>
									<td style="border-bottom: 2pt solid green;">
										<button  
											data-id="<?= esc($transcription['BMD_header_index']); ?>" 
											class="go_button">Go
										</button>
									</td>						
							</tr>
				
					<?php 
					} ?>
			</tbody>
		</table>
	</div>
	
	<div>
		<form action="<?=(base_url('transcribe/next_action')); ?>" method="POST" name="form_next_action" >
			<input name="BMD_header_index" id="BMD_header_index" type="hidden" />
			<input name="BMD_next_action" id="BMD_next_action" type="hidden" />
		</form>
	</div>
	
	<div>
		<form action="<?=(base_url('allocation/next_action')); ?>" method="POST" name="alloc_next_action" >
			<input name="allocation_index" id="allocation_index" type="hidden" />
			<input name="alloc_next_action" id="alloc_next_action" type="hidden" />
		</form>
	</div>
	
	<form action="<?=(base_url('allocation/load_csv_file_step1/0')); ?>" method="POST" name="form_csvFile" ></form>
	
	<form action="<?=(base_url('allocation/create_assignment_step1/0')); ?>" method="POST" name="form_assig" ></form>
	
	<form action="<?=(base_url('transcribe/transcribe_step1/0')); ?>" method="POST" name="form_refresh" ></form>
	
	

<script>
	
$(document).ready(function()
	{	
		$('.go_button').on("click", function()
			{
				// define the variables
				var id=$(this).data('id');
				var BMD_next_action=$(this).parents('tr').find('select[name="next_action"]').val();
				// load variables to form
				$('#BMD_header_index').val(id);
				$('#BMD_next_action').val(BMD_next_action);
				// and submit the form
				$('form[name="form_next_action"]').submit();
			});
		
		$('.next_action').on("click", nextAction)
		
		$('.edit_assignment').on("click", editAssignment)
		
		$('#csv_file').on("click", function()
			{			
				// get project
				var project_name = "<?=$session->current_project[0]['project_name']?>";
				switch(project_name) 
					{
						case 'FreeBMD':
							break;
						case 'FreeREG':
							$('form[name="form_csvFile"]').submit();
							break;
						case 'FreeREG':
							break;
					}			
			});
			
		$('#alloc').on("click", function()
			{			
				// get project
				var project_name = "<?=$session->current_project[0]['project_name']?>";
				switch(project_name) 
					{
						case 'FreeBMD':
							break;
						case 'FreeREG':
							$('form[name="form_assig"]').submit();
							break;
						case 'FreeREG':
							break;
					}
			});
			
		$('#refresh').on("click", function()
			{			
				$('form[name="form_refresh"]').submit();
			});
			
	});
	
function nextAction(event)
	{
		$('#BMD_header_index').val(event.target.dataset.id);
		$('#BMD_next_action').val(event.target.dataset.action);
		// and submit the form
		$('form[name="form_next_action"]').submit();
	}
	
function editAssignment(event)
	{
		$('#allocation_index').val(event.target.dataset.id);
		$('#alloc_next_action').val(event.target.dataset.action);
		// and submit the form
		$('form[name="alloc_next_action"]').submit();
	}

</script>


