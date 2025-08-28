	<?php $session = session(); ?>
	
	<div class="row text-center table-responsive w-auto" style="max-height: 450px;">
		<table class="table table-hover table-striped table-borderless" style="border-collapse: separate; border-spacing: 0;">
			<thead class="sticky-top bg-white">
				<tr>
					<th>Help Category</th>
					<th>Help Title</th>
					<th>Help URL (Click on URL to test it)</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($session->help as $line): ?>	
					<td><?= esc($line['help_category'])?></td>
					<td><?= esc($line['help_title'])?></td>
					<td>
						<a id="help_url" target="_blank" href="<?php echo $line['help_url']; ?>"
								<span><?php echo $line['help_url']; ?></span>
							</a>
					</td>
					<td>
						<label for="next_action" class="sr-only">Next action</label>
							<select class="box" name="next_action" id="next_action">
								<?php foreach ($session->transcription_cycles as $key => $transcription_cycle): ?>
									<?php if ( $transcription_cycle['BMD_cycle_type'] == 'HELP' ): ?>
										 <option value="<?= esc($transcription_cycle['BMD_cycle_code'])?>">
											<?= esc($transcription_cycle['BMD_cycle_name'])?>
										</option>
									<?php endif; ?>
								<?php endforeach; ?>
							</select>
					</td>
					<td>
						<button  
							data-id="<?= esc($line['help_index']); ?>" 
							class="go_button btn btn-success btn-sm">Go
						</button>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	
	<div>
		<form action="<?=(base_url('help/next_action')); ?>" method="POST" name="form_next_action" >
			<input name="help_index" id="help_index" type="hidden" />
			<input name="help_next_action" id="help_next_action" type="hidden" />
		</form>
	</div>
	
	<div class="alert row mt-2 d-flex justify-content-between">	
		<a id="return" class="btn btn-primary mr-0" href="<?=(base_url('help/help_show/0')); ?>">
			<?php echo $session->current_project[0]['back_button_text']?>
		</a>
		
		<a id="return" class="btn btn-primary mr-0" href="https://drive.google.com/drive" target="_blank"> 
			<?php echo 'Google Drive and Docs'?>
		</a>
		
		<a id="help_create" class="btn btn-primary mr-0" href="<?=(base_url('help/help_create_step1/0')) ?>">Create a new HELP/HOWTO</a>
	</div>

<script>
	
$(document).ready(function()
	{	
		$('.go_button').on("click", function()
			{
				// define the variables
				var id=$(this).data('id');
				var help_next_action=$(this).parents('tr').find('select[name="next_action"]').val();
				// load variables to form
				$('#help_index').val(id);
				$('#help_next_action').val(help_next_action);
				// and submit the form
				$('form[name="form_next_action"]').submit();
			});
	});

</script>


