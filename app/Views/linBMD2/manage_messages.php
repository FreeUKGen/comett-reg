	<?php $session = session(); ?>
	
	<div class="row text-center table-responsive w-auto" style="max-height: 450px;">
		<table class="table table-hover table-striped table-borderless" style="border-collapse: separate; border-spacing: 0;">
			<thead class="sticky-top bg-white">
				<tr>
					<th>From Date</th>
					<th>To Date</th>
					<th>Colour</th>
					<th>Message</th>
				</tr>
			</thead>
			<tbody>
				<?php 
				foreach ( $session->messages as $message )
					{ ?>
						<tr>
							<?php
							$display_message = $message['message'];
							// shorten message
							if ( strlen($display_message) > 80 )
								{
									$display_message = substr($display_message, 0, 80).' ... FIRST 80 CHARACTERS SHOWN';
								} ?>	
							<td><?= esc($message['from_date'])?></td>
							<td><?= esc($message['to_date'])?></td>
							<td><?= esc($message['colour'])?></td>
							<td><?= esc($display_message)?></td>
							
							<td>
								<label for="next_action" class="sr-only">Next action</label>
									<select class="box" name="next_action" id="next_action">
										<?php foreach ($session->transcription_cycles as $key => $transcription_cycle): ?>
											<?php if ( $transcription_cycle['BMD_cycle_type'] == 'MESSA' ): ?>
												 <option value="<?= esc($transcription_cycle['BMD_cycle_code'])?>">
													<?= esc($transcription_cycle['BMD_cycle_name'])?>
												</option>
											<?php endif; ?>
										<?php endforeach; ?>
									</select>
							</td>
							<td>
								<button  
									data-id="<?= esc($message['message_index']); ?>" 
									class="go_button btn btn-success btn-sm">Go
								</button>
							</td>
						</tr>
					<?php
					} ?>
						
			</tbody>
		</table>
	</div>
	
	<div>
		<form action="<?=(base_url('messaging/next_action')); ?>" method="POST" name="form_next_action" >
			<input name="message_index" id="message_index" type="hidden"></input>
			<input name="message_next_action" id="message_next_action" type="hidden"></input>
		</form>
	</div>
	
	<div class="row mt-4 d-flex justify-content-between">	
		<a id="return" class="btn btn-primary mr-0" href="<?=(base_url('database/coord_step1/0')); ?>">
			<span><?php echo $session->current_project[0]['back_button_text']?></span>
		</a>
		<a class="btn btn-primary mr-0" href="<?=(base_url('messaging/create_message_step1/0')) ?>">Create a new message</a>
	</div>

<script>
	
$(document).ready(function()
	{	
		$('.go_button').on("click", function()
			{
				// define the variables
				var message_index=$(this).data('id');
				var message_next_action=$(this).parents('tr').find('select[name="next_action"]').val();
				// load variables to form
				$('#message_index').val(message_index);
				$('#message_next_action').val(message_next_action);
				// and submit the form
				$('form[name="form_next_action"]').submit();
			});
	});

</script>


