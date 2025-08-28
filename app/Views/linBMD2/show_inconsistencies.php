<?php $session = session(); ?>
<div>
	
	<?php
	if ( count($session->messages) != 0 )
		{ ?>
			<p>
				<?php echo count($session->messages).' Potential inconsistencies found'; ?> 
				<a id="show" class="btn btn-primary mr-0" href="<?php echo(base_url('district/show_incons_type/all')); ?>"><span>Show All</span></a>
			</p>
		<?php
		} ?>
		
	<?php
	if ( $session->messages_count['novol'] != 0 )
		{ ?>
			<p>
				<?php echo $session->messages_count['novol'].' Districts with no volume records.'; ?> 
				<a id="show" class="btn btn-primary mr-0" href="<?php echo(base_url('district/show_incons_type/novol')); ?>"><span>Show</span></a>
			</p>
		<?php
		} ?>
		
	<?php
	if ( $session->messages_count['lte3'] != 0 )
		{ ?>
			<p>
				<?php echo $session->messages_count['lte3'].' Districts with no volume records.'; ?> 
				<a id="show" class="btn btn-primary mr-0" href="<?php echo(base_url('district/show_incons_type/lte3')); ?>"><span>Show</span></a>
			</p>
		<?php
		} ?>

	<?php
	if ( $session->messages_count['notb'] != 0 )
		{ ?>
			<p>
				<?php echo $session->messages_count['notb'].' Districts with no volume records.'; ?> 
				<a id="show" class="btn btn-primary mr-0" href="<?php echo(base_url('district/show_incons_type/notb')); ?>"><span>Show</span></a>
			</p>
		<?php
		} ?>
		
	<?php
	if ( $session->messages_count['notm'] != 0 )
		{ ?>
			<p>
				<?php echo $session->messages_count['notm'].' Districts with no volume records.'; ?> 
				<a id="show" class="btn btn-primary mr-0" href="<?php echo(base_url('district/show_incons_type/notm')); ?>"><span>Show</span></a>
			</p>
		<?php
		} ?>
		
	<?php
	if ( $session->messages_count['notd'] != 0 )
		{ ?>
			<p>
				<?php echo $session->messages_count['notd'].' Districts with no volume records.'; ?> 
				<a id="show" class="btn btn-primary mr-0" href="<?php echo(base_url('district/show_incons_type/notd')); ?>"><span>Show</span></a>
			</p>
		<?php
		} ?>
	
	<?php
	if ( $session->messages_count['nodis'] != 0 )
		{ ?>
			<p>
				<?php echo $session->messages_count['nodis'].' Districts with no volume records.'; ?> 
				<a id="show" class="btn btn-primary mr-0" href="<?php echo(base_url('district/show_incons_type/nodis')); ?>"><span>Show</span></a>
			</p>
		<?php
		} ?>
	
	<table class="table table-hover table-striped table-borderless" style="border-collapse: separate; border-spacing: 0;">
			<thead class="sticky-top bg-white">
				<tr>
					<th>District</th>
					<th>Added by</th>
					<th>Active?</th>
					<th>Issue</th>
				</tr>
			</thead>

			<tbody>
				<?php foreach ( $session->messages as $message ) 
					{ 
						?>
						<tr>
							<td><?= esc($message[0])?></td>
							<td><?= esc($message[1])?></td>
							<td><?= esc($message[2])?></td>
							<td><?= esc($message[3])?></td>
							<td>
								<label for="next_action" class="sr-only">Next action</label>
									<select class="box" name="next_action" id="next_action">
										<?php foreach ($session->districts_cycle as $key => $districts_cycle): ?>
												<option value="<?= esc($districts_cycle['BMD_cycle_code'])?>">
													<?= esc($districts_cycle['BMD_cycle_name'])?>
												</option>
										<?php endforeach; ?>
									</select>
							</td>
							<td>
								<button  
									data-id="<?= esc($message[0]); ?>" 
									class="go_district_button btn btn-success btn-sm">Go
								</button>
							</td>
						</tr>
					<?php
					} ?>
			</tbody>
		</table>	
</div>

<div>
		<form action="<?=(base_url('district/next_action')); ?>" method="POST" name="form_next_action" >
			<input name="District_name" id="District_name" type="hidden" />
			<input name="BMD_next_action" id="BMD_next_action" type="hidden" />
		</form>
</div>

<div class="row mt-2 d-flex justify-content-between">
			
				<a id="return" class="btn btn-primary mr-0" href="<?php echo(base_url('district/manage_districts/0')); ?>">
					<?php echo $session->current_project[0]['back_button_text']?>
				</a>
			
		</div>
		
<script>	
// handle district actions
	$('.go_district_button').on("click", function()
			{
				// define the variables
				var id=$(this).data('id');
				var BMD_next_action=$(this).parents('tr').find('select[name="next_action"]').val();
				// load variables to form
				$('#District_name').val(id);
				$('#BMD_next_action').val(BMD_next_action);
				// and submit the form
				$('form[name="form_next_action"]').submit();
			});
</script>	
		
	
