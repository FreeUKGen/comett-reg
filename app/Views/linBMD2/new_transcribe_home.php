
	<?php $session = session(); ?>

	<div class="row mt-2 mb-2">		
		<header>	
		<aside>
			<?php					
				if ( $session->status == '0' ) 
					{ 
						?>
						<a href="<?=(base_url('transcribe/toggle_transcriptions'))?>"><?php echo 'Your ACTIVE transcriptions' ?></a>
					<?php
					}
				else
					{
						?>
						<a href="<?=(base_url('transcribe/toggle_transcriptions'))?>"><?php echo 'Your CLOSED transcriptions' ?></a>
					<?php
					}
					?>
		</aside>
		</header>
	</div>
	
	<div class="row text-center table-responsive w-auto">
		<table class="table table-borderless" style="border-collapse: separate; border-spacing: 0;" id="show_table">
			<thead class="sticky-top bg-white">
				<tr class="pb-1 text-primary">
<!--					<th>--><?php //echo $session->current_project['allocation_text'].' Name'?><!--</th>-->
					<th>File</th>
					<?php
					if ( $session->current_project['project_index'] == 2 )
						{ ?>
							<th>Document Source</th>
							<th>Image Source</th>
							<th>Images</th>
						<?php
						} ?>
					<th>Current Scan</th>
					<th>Lines trans</th>
					<th>Start Date</th>
					<th>Last change date/time</th>
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
<!--									<td class="edit_assignment" title="ClickMe to edit assignment if in FreeREG"-->
<!--										data-id="--><?php //=esc($transcription['BMD_allocation_index'])?><!--"-->
<!--										data-action='CHGEA'>-->
<!--										--><?php //= esc($transcription['BMD_allocation_name'])?>
<!--									</td>-->
									<td><?= esc($transcription['BMD_file_name'])?></td>
									
									<?php
									if ( $session->current_project['project_index'] == 2 )
										{ ?>
											<td class="next_action"
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
											<td><?= esc($transcription['image_source'])?></td>
											<td class="centre"><?= esc($transcription['image_count'])?></td>
										<?php
										} ?>
									
									<td><?= esc($transcription['BMD_scan_name'])?></td>
									<td class="centre next_action"
										title="ClickMe to Transcribe from Scan"
										data-id='<?=esc($transcription['BMD_header_index'])?>'
										data-action='INPRO'>
										<?= esc($transcription['BMD_records'])?>
									</td>
									<td><?= esc($transcription['BMD_start_date'])?></td>
									<td><?= esc($transcription['Change_date'])?></td>
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
				var project_name = "<?=$session->current_project['project_name']?>";
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
				var project_name = "<?=$session->current_project['project_name']?>";
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