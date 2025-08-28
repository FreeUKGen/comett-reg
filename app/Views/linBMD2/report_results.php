<?php $session = session(); ?>
	
	<div class="row text-center table-responsive w-auto" style="max-height: 500px;">
		<table class="table table-hover table-striped table-borderless" style="border-collapse: separate; border-spacing: 0;" id="report_sets">
			<thead class="sticky-top bg-white">				
				<tr class="table-success">
					<th>
						<input class="text-center rounded" style="height:40px; border:1px solid black; font-size:18px; color:black; background-color:Isabelline;" type="text" size="8" id="from_date_input" value="<?php echo esc($session->from_date);?>">	
					</th>
					<th>
						<select name="report_level_0" id="report_level_0"  class="text-center rounded box" style="height:40px; border:1px solid black; font-size:18px; color:black; background-color:Isabelline;">
							<?php foreach ($session->report_axes as $axis): ?>
								<option value="<?php echo esc($axis['field']);?>"<?php if ( $axis['field'] == $session->report_index[0] ) {echo esc(' selected');} ?>><?php echo esc($axis['title']);?></option>
							<?php endforeach; ?>
						</select> 
					</th>
					<th>
						<select name="report_level_1" id="report_level_1" class="text-center rounded box" style="height:40px; border:1px solid black; font-size:18px; color:black; background-color:Isabelline;">
							<?php foreach ($session->report_axes as $axis): ?>
								<option value="<?php echo esc($axis['field']);?>"<?php if ( $axis['field'] == $session->report_index[1] ) {echo esc(' selected');} ?>><?php echo esc($axis['title']);?></option>
							<?php endforeach; ?>
						</select> 
					</th>
					<th>
						<select name="report_level_2" id="report_level_2" class="text-center rounded box" style="height:40px; border:1px solid black; font-size:18px; color:black; background-color:Isabelline;">
							<?php foreach ($session->report_axes as $axis): ?>
								<option value="<?php echo esc($axis['field']);?>"<?php if ( $axis['field'] == $session->report_index[2] ) {echo esc(' selected');} ?>><?php echo esc($axis['title']);?></option>
							<?php endforeach; ?>
						</select> 
					</th>
					<th>
						<select name="report_level_3" id="report_level_3" class="text-center rounded box" style="height:40px; border:1px solid black; font-size:18px; color:black; background-color:Isabelline;">
							<?php foreach ($session->report_axes as $axis): ?>
								<option value="<?php echo esc($axis['field']);?>"<?php if ( $axis['field'] == $session->report_index[3] ) {echo esc(' selected');} ?>><?php echo esc($axis['title']);?></option>
							<?php endforeach; ?>
						</select> 
					</th>
					<th>
						<select name="report_level_4" id="report_level_4" class="text-center rounded box" style="height:40px; border:1px solid black; font-size:18px; color:black; background-color:Isabelline;">
							<?php foreach ($session->report_axes as $axis): ?>
								<option value="<?php echo esc($axis['field']);?>"<?php if ( $axis['field'] == $session->report_index[4] ) {echo esc(' selected');} ?>><?php echo esc($axis['title']);?></option>
							<?php endforeach; ?>
						</select> 
					</th>
					<th>
						<select name="report_level_5" id="report_level_5" class="text-center rounded box" style="height:40px; border:1px solid black; font-size:18px; color:black; background-color:Isabelline;">
							<?php foreach ($session->report_axes as $axis): ?>
								<option value="<?php echo esc($axis['field']);?>"<?php if ( $axis['field'] == $session->report_index[5] ) {echo esc(' selected');} ?>><?php echo esc($axis['title']);?></option>
							<?php endforeach; ?>
						</select> 
					</th>
					<th>
						<select name="report_level_6" id="report_level_6" class="text-center rounded box" style="height:40px; border:1px solid black; font-size:18px; color:black; background-color:Isabelline;">
							<?php foreach ($session->report_axes as $axis): ?>
								<option value="<?php echo esc($axis['field']);?>"<?php if ( $axis['field'] == $session->report_index[6] ) {echo esc(' selected');} ?>><?php echo esc($axis['title']);?></option>
							<?php endforeach; ?>
						</select> 
					</th>
					<th>
						<select name="report_level_7" id="report_level_7" class="text-center rounded box" style="height:40px; border:1px solid black; font-size:18px; color:black; background-color:Isabelline;">
							<?php foreach ($session->report_axes as $axis): ?>
								<option value="<?php echo esc($axis['field']);?>"<?php if ( $axis['field'] == $session->report_index[7] ) {echo esc(' selected');} ?>><?php echo esc($axis['title']);?></option>
							<?php endforeach; ?>
						</select> 
					</th>
					<th>
						<select name="report_level_8" id="report_level_8" class="text-center rounded box" style="height:40px; border:1px solid black; font-size:18px; color:black; background-color:Isabelline;">
							<?php foreach ($session->report_axes as $axis): ?>
								<option value="<?php echo esc($axis['field']);?>"<?php if ( $axis['field'] == $session->report_index[8] ) {echo esc(' selected');} ?>><?php echo esc($axis['title']);?></option>
							<?php endforeach; ?>
						</select> 
					</th>
					<th>
						<input class="text-center rounded" style="height:40px; border:1px solid black; font-size:18px; color:black; background-color:Isabelline;" type="text" size="8" placeholder="Search..." >
					</th>
				</tr>
				
				<tr class="table-info">
					<th>
						<input class="text-center rounded" style="height:40px; border:1px solid black; font-size:18px; color:black; background-color:Isabelline;" type="text" size="8" id="to_date_input" value="<?php echo esc($session->to_date);?>">
					</th>
					<th>
						<select id="filter_level_0" class="text-center rounded box" style="height:40px; border:1px solid black; font-size:16px; color:black; background-color:Isabelline;">
							<?php foreach ($session->filters[0] as $filter): ?>
								<option value="<?php echo esc($filter);?>"<?php if ( $filter == $session->selected_filter ) {echo esc(' selected');} ?>><?php echo esc($filter);?></option>
							<?php endforeach; ?>
						</select> 
					</th>
					<th>
						<select id="filter_level_1" class="text-center rounded box" style="height:40px; border:1px solid black; font-size:18px; color:black; background-color:Isabelline;">
							<?php foreach ($session->filters[1] as $filter): ?>
								<option value="<?php echo esc($filter);?>"<?php if ( $filter == $session->selected_filter ) {echo esc(' selected');} ?>><?php echo esc($filter);?></option>
							<?php endforeach; ?>
						</select> 
					</th>
					<th>
						<select id="filter_level_2" class="text-center rounded box" style="height:40px; border:1px solid black; font-size:18px; color:black; background-color:Isabelline;">
							<?php foreach ($session->filters[2] as $filter): ?>
								<option value="<?php echo esc($filter);?>"<?php if ( $filter == $session->selected_filter ) {echo esc(' selected');} ?>><?php echo esc($filter);?></option>
							<?php endforeach; ?>
						</select>
					</th>
					<th>
						<select id="filter_level_3" class="text-center rounded box" style="height:40px; border:1px solid black; font-size:18px; color:black; background-color:Isabelline;">
							<?php foreach ($session->filters[3] as $filter): ?>
								<option value="<?php echo esc($filter);?>"<?php if ( $filter == $session->selected_filter ) {echo esc(' selected');} ?>><?php echo esc($filter);?></option>
							<?php endforeach; ?>
						</select>
					</th>
					<th>
						<select id="filter_level_4" class="text-center rounded box" style="height:40px; border:1px solid black; font-size:18px; color:black; background-color:Isabelline;">
							<?php foreach ($session->filters[4] as $filter): ?>
								<option value="<?php echo esc($filter);?>"<?php if ( $filter == $session->selected_filter ) {echo esc(' selected');} ?>><?php echo esc($filter);?></option>
							<?php endforeach; ?>
						</select> 
					</th>
					<th>
						<select id="filter_level_5" class="text-center rounded box" style="height:40px; border:1px solid black; font-size:18px; color:black; background-color:Isabelline;">
							<?php foreach ($session->filters[5] as $filter): ?>
								<option value="<?php echo esc($filter);?>"<?php if ( $filter == $session->selected_filter ) {echo esc(' selected');} ?>><?php echo esc($filter);?></option>
							<?php endforeach; ?>
						</select> 
					</th>
					<th>
						<select id="filter_level_6" class="text-center rounded box" style="height:40px; border:1px solid black; font-size:18px; color:black; background-color:Isabelline;">
							<?php foreach ($session->filters[6] as $filter): ?>
								<option value="<?php echo esc($filter);?>"<?php if ( $filter == $session->selected_filter ) {echo esc(' selected');} ?>><?php echo esc($filter);?></option>
							<?php endforeach; ?>
						</select> 
					</th>
					<th>
						<select id="filter_level_7" class="text-center rounded box" style="height:40px; border:1px solid black; font-size:18px; color:black; background-color:Isabelline;">
							<?php foreach ($session->filters[7] as $filter): ?>
								<option value="<?php echo esc($filter);?>"<?php if ( $filter == $session->selected_filter ) {echo esc(' selected');} ?>><?php echo esc($filter);?></option>
							<?php endforeach; ?>
						</select> 
					</th>
					<th>
						<select id="filter_level_8" class="text-center rounded box" style="height:40px; border:1px solid black; font-size:18px; color:black; background-color:Isabelline;">
							<?php foreach ($session->filters[8] as $filter): ?>
								<option value="<?php echo esc($filter);?>"<?php if ( $filter == $session->selected_filter ) {echo esc(' selected');} ?>><?php echo esc($filter);?></option>
							<?php endforeach; ?>
						</select> 
					</th>
					<th class="text-center rounded" style="border:2px solid black; font-size:18px; color:black; background-color:Isabelline;"><?php echo 'Quantity:'.$session->total_qty;?></th>
				</tr>
			</thead>
			<tbody  id="user_table">
				<tr>
				<?php foreach ( $session->reporting_data as $data ) 
					{ ?>	
						<td></td>
							<?php
							foreach ( $session->report_index as $index )
								{
									if ( $index == 'none' )
										{ ?>
											<td>-----</td>
										<?php
										} 
									else
										{ ?>
											<td><?= esc($data[$index])?></td>
										<?php
										} 
								} ?>							
						<td><?= esc($data['report_quantity'])?></td>
				</tr>
				<?php
					} ?>
			</tbody>
		</table>
	</div>
	
	<div>
		<form action="<?=(base_url('report/report_axes/')); ?>" method="POST" name="form_report" >
			<input name="level_0" id="level_0" type="hidden" />
			<input name="level_1" id="level_1" type="hidden" />
			<input name="level_2" id="level_2" type="hidden" />
			<input name="level_3" id="level_3" type="hidden" />
			<input name="level_4" id="level_4" type="hidden" />
			<input name="level_5" id="level_5" type="hidden" />
			<input name="level_6" id="level_6" type="hidden" />
			<input name="level_7" id="level_7" type="hidden" />
			<input name="level_8" id="level_8" type="hidden" />
			
			<input name="filter_0" id="filter_0" type="hidden" />
			<input name="filter_1" id="filter_1" type="hidden" />
			<input name="filter_2" id="filter_2" type="hidden" />
			<input name="filter_3" id="filter_3" type="hidden" />
			<input name="filter_4" id="filter_4" type="hidden" />
			<input name="filter_5" id="filter_5" type="hidden" />
			<input name="filter_6" id="filter_6" type="hidden" />
			<input name="filter_7" id="filter_7" type="hidden" />
			<input name="filter_8" id="filter_8" type="hidden" />
			
			<input name="from_date" id="from_date" type="hidden" />
			<input name="to_date" id="to_date" type="hidden" />
		</form>
	</div>
	
	<br>
	
	<div class="alert row mt-2 d-flex justify-content-between">	
		<a id="return" class="btn btn-primary mr-0" href="<?=(base_url('report/report_step1/0')); ?>">
			<?php echo $session->current_project[0]['back_button_text']?>
		</a>
		
		<a id="return" class="btn btn-primary mr-0" href="<?=(base_url('report/report_create_csv')); ?>">
			Create and Download CSV 
		</a>
		
		<button class="btn btn-primary mr-0" id="run_report">Refresh Report</button>		
	</div>

<script>
	
	$(document).ready(function()
		{
			$('#run_report').click(function()
				{
					// load form
					$('#level_0').val(document.getElementById("report_level_0").value);
					$('#level_1').val(document.getElementById("report_level_1").value);
					$('#level_2').val(document.getElementById("report_level_2").value);
					$('#level_3').val(document.getElementById("report_level_3").value);
					$('#level_4').val(document.getElementById("report_level_4").value);
					$('#level_5').val(document.getElementById("report_level_5").value);
					$('#level_6').val(document.getElementById("report_level_6").value);
					$('#level_7').val(document.getElementById("report_level_7").value);
					$('#level_8').val(document.getElementById("report_level_8").value);
					
					$('#filter_0').val(document.getElementById("filter_level_0").value);
					$('#filter_1').val(document.getElementById("filter_level_1").value);
					$('#filter_2').val(document.getElementById("filter_level_2").value);
					$('#filter_3').val(document.getElementById("filter_level_3").value);
					$('#filter_4').val(document.getElementById("filter_level_4").value);
					$('#filter_5').val(document.getElementById("filter_level_5").value);
					$('#filter_6').val(document.getElementById("filter_level_6").value);
					$('#filter_7').val(document.getElementById("filter_level_7").value);
					$('#filter_8').val(document.getElementById("filter_level_8").value);
					
					$('#from_date').val(document.getElementById("from_date_input").value);
					$('#to_date').val(document.getElementById("to_date_input").value);

					// and submit the form
					$('form[name="form_report"]').submit();	
				});
		});

</script>
