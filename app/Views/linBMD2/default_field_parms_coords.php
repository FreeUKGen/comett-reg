	<?php $session = session();	?>
	
	<div class="row table-responsive w-auto text-center">
		<table class="table table-sm table-hover table-striped table-bordered" style="border-collapse: separate; border-spacing: 0;">
			<thead class="sticky-top bg-white">
				<div class="row">
					<p 
						class="bg-warning col-12 pl-0 text-center font-weight-bold" 
						style="font-size:1.0vw;">
						
						<?php
							echo 'Calibration by Syndicate - '.$session->reference_synd_name.' for Reference Scan - '.$session->reference_scan.', Reference Path - '.$session->reference_path.', Reference Scan Format - '.$session->reference_scan_format.', Data Entry Format - '.$session->reference_data_entry_format.'. DEFAULT FIELD PARAMETERS'; 
						?>
					</p>
				</div>
				
				<tr>					
					<th>Field</th>
					<th class="text-center">Font size </th>
					<th class="text-center">Font weight</th>
					<th class="text-center">Pad left</th>
					<th class="text-center">Field align</th>
					<th class="text-center">Capitalise</th>
					<th class="text-center">Roman Volume?</th>
					<th class="text-center">Auto Full-stop?</th>
					<th class="text-center">Auto Copy?</th>
					<th class="text-center">Auto Focus?</th>
					<th class="text-center">Colour</th>
					<th class="text-center">Format</th>
				</tr>
			</thead>
		
			<tbody id="content">						
				<?php
				// loop through element by element
				foreach ($session->default_field_parms as $key => $def) 
					{ 
						?>		
						<!-- output data -->
						<tr>
							<!-- change -->
							<td>
								<a id="select_line" href="<?=(base_url('transcribe/default_field_parms_coord_step2/0/'.esc($key))) ?>">
								<span><?= esc($def['column_name']);?></span>
								</a>
							</td>
							<td><?= esc($def['font_size']);?></td>
							<td><?= esc($def['font_weight']);?></td>
							<td><?= esc($def['pad_left']);?></td>
							<td><?= esc($def['field_align']);?></td>
							<td><?= esc($def['capitalise']);?></td>
							<td><?= esc($def['volume_roman']);?></td>
							<td><?= esc($def['auto_full_stop']);?></td>
							<td><?= esc($def['auto_copy']);?></td>
							<td><?= esc($def['auto_focus']);?></td>
							<td><?= esc($def['colour']);?></td>
							<td><?= esc($def['field_format']);?></td>
						</tr>
					<?php
					} ?>
			</tbody>
		</table>
	</div>
	
	<div class="row mt-4 d-flex justify-content-between">	
		
		<a id="return" class="btn btn-primary mr-0" href="<?php echo(base_url($session->controller.'/transcribe/calibrate_coord_step1/0')); ?>">
			<span><?php echo $session->current_project[0]['back_button_text']?></span>
		</a>
		
	</div>
		
			
		</form>
	</div>
