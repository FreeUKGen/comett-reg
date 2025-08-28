<?php $session = session(); ?>
	
	<div class="row">
				<p 
					class="bg-warning col-12 pl-0 text-center font-weight-bold" 
					style="font-size:1.0vw;">
					
					<?php
						echo 'Default Transcription Sets by Syndicate - '.$session->reference_synd_name.' for Transcription Type - '.$session->reference_type_name.'.'; 
					?>
				</p>
	</div>
	
	<div class="row text-center table-responsive w-auto" style="max-height: 450px;">
		<table class="table table-hover table-striped table-borderless" style="border-collapse: separate; border-spacing: 0;" id="transcription_sets">
			<thead class="sticky-top bg-white">
				<tr class="text-primary">
					<th>Index</th>
					<th>Transcription Set</th>
					<th>Scan format</th>
					<th>From year/quarter</th>
					<th>To year/quarter</th>
					<th>X start</th>
					<th>Y start</th>
					<th>Zoom</th>
					<th>Reference Scan</th>
					<th>Reference Path</th>
					<th>Base on Index</th>
					<th>
						<input class="box_sm" id="search" type="text" placeholder="Search..." >		
					</th>
					<th></th>
				</tr>
				<tr class="text-secondary">
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th>eg. 1880D3-A-0022.jpg</th>
					<th>eg. GUS/1880/Deaths/September/ANC-05/A-C</th>
					<th></th>
					<th></th>
					<th></th>
				</tr>
			</thead>
			<tbody  id="user_table">
				<tr>
				<?php foreach ($session->transcription_sets as $key => $transcription_set): ?>
					<td><?= esc($transcription_set['image_index'])?></td>
					<td><?= esc($transcription_set['data_entry_format'])?></td>
					<td><?= esc($transcription_set['scan_format'])?></td>
					<td><?= esc($transcription_set['from_year'].' / '.$transcription_set['from_quarter'])?></td>
					<td><?= esc($transcription_set['to_year'].' / '.$transcription_set['to_quarter'])?></td>
					<td><input class="box" style="width:100px; text-align:right;" type="number" value="<?php echo($transcription_set['panzoom_x']) ?>"></td>
					<td><input class="box" style="width:100px; text-align:right;" type="number" value="<?php echo($transcription_set['panzoom_y']) ?>"></td>
					<td><input class="box" style="width:100px; text-align:right;" type="number" value="<?php echo($transcription_set['panzoom_z']) ?>"></td>
					<td><input class="box" style="width: 180px;" type="text" value="<?php echo($transcription_set['reference_scan']) ?>"></td>
					<td><input class="box" style="width: 350px;" type="text" value="<?php echo($transcription_set['reference_path']) ?>"></td>
					<td><input class="text-center box" style="width: 50px;" type="text" value="<?php echo('') ?>"></td>
					<td><button class="btn btn-primary mr-0" onclick="calibrate()">Calibrate?</button></td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	
	<div>
		<form action="<?=(base_url('transcribe/calibrate_reference_step2/')); ?>" method="POST" name="form_calibrate" >
			<input name="reference_image_index" id="reference_image_index" type="hidden" />
			<input name="reference_data_entry_format" id="reference_data_entry_format" type="hidden" />
			<input name="reference_scan_format" id="reference_scan_format" type="hidden" />
			<input name="reference_x_start" id="reference_x_start" type="hidden" />
			<input name="reference_y_start" id="reference_y_start" type="hidden" />
			<input name="reference_z_start" id="reference_z_start" type="hidden" />
			<input name="reference_scan" id="reference_scan" type="hidden" />
			<input name="reference_path" id="reference_path" type="hidden" />
			<input name="base_on" id="base_on" type="hidden" />
		</form>
	</div>
	
	<br>
	
	<div>	
		<a id="return" class="btn btn-primary mr-0" href="<?=(base_url('transcribe/calibrate_reference_step1/0')); ?>">
			<?php echo $session->current_project[0]['back_button_text']?>
		</a>
	</div>

<script>
	function calibrate() 
		{
			var table = document.getElementById("transcription_sets");
			if (table) 
				{
					for (var i = 0; i < table.rows.length; i++) 
						{
							table.rows[i].onclick = function() 
								{
									tableText(this);
								};
						}
				}
		}

	function tableText(tableRow) 
		{
			// get data from selected row
			var reference_image_index = tableRow.children[0].innerText;
			var reference_data_entry_format = tableRow.children[1].innerText;
			var reference_scan_format = tableRow.children[2].innerText;
			var reference_x_start = tableRow.children[5].lastChild.value;
			var reference_y_start = tableRow.children[6].lastChild.value;
			var reference_z_start = tableRow.children[7].lastChild.value;
			var reference_scan = tableRow.children[8].lastChild.value;
			var reference_path = tableRow.children[9].lastChild.value;
			var base_on = tableRow.children[10].lastChild.value;
			
			// load variables to form
			$('#reference_image_index').val(reference_image_index);
			$('#reference_data_entry_format').val(reference_data_entry_format);
			$('#reference_scan_format').val(reference_scan_format);
			$('#reference_x_start').val(reference_x_start);
			$('#reference_y_start').val(reference_y_start);
			$('#reference_z_start').val(reference_z_start);
			$('#reference_scan').val(reference_scan);
			$('#reference_path').val(reference_path);
			$('#base_on').val(base_on);
				
			// and submit the form
			$('form[name="form_calibrate"]').submit();	
		}

</script>
