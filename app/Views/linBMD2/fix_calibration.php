<?php $session = session(); ?>
	
	<div class="row text-center table-responsive w-auto" style="height:150px">
		<table class="table table-striped table-hover table-bordered table-sm" style="border-collapse: separate; border-spacing: 0;">
			<thead class="sticky-top bg-white">
				<tr class="text-primary">
					<th>LineNo</th>
					<?php
					foreach ($session->fix_calib_def_fields as $table_header) 
							{ ?>		
								<th><?=$table_header['column_name'];?></th>
							<?php
							} ?>
				</tr>
			</thead>

			<tbody id="user_table">
				<?php foreach ($session->fix_calib_details as $detail)
					{?>
						<tr>	
							<!-- line no -->
							<td>
								<span><?= esc($detail['BMD_line_sequence']); ?></span>
							</td>
							<?php
							foreach ( $session->fix_calib_def_fields as $table_line ) 
								{?>				
									<td style="font-family: sans-serif;"> 
									<!-- output data -->
									<?php echo esc($detail[$table_line['table_fieldname']]); ?>
									</td>
								<?php } ?>
						</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
	
	<div class="row text-center table-responsive w-auto">
		<table class="table table-striped table-hover table-bordered table-sm" style="border-collapse: separate; border-spacing: 0;">
			<thead class="sticky-top bg-white">
				<tr class="header text-primary">
					<th>This Column #</th>
					<th>First LineNo</th>
					<th>Last LineNo</th>
					<th>Panzoom X top left</th>
					<th>Panzoom Y</th>
				</tr>
			</thead>

			<tbody id="user_inputs"œ>
				<?php foreach ($session->columns as $column): ?>
					<tr>	
						<td><input class="box_sm" style="text-align:right;" type="number" id="<?php echo('col_'.$column['column']); ?>" value="<?php echo($column['column']) ?>" readonly></td>
						<td><input class="box_sm" style="text-align:right;" type="number" id="<?php echo('first_line_'.$column['column']); ?>" value="<?php echo($column['first_line']) ?>"></td>
						<td><input class="box_sm" style="text-align:right;" type="number" id="<?php echo('last_line_'.$column['column']); ?>" value="<?php echo($column['last_line']) ?>"></td>
						<td><input class="box_sm" style="text-align:right;" type="number" step="0.0001" id="<?php echo('panzoom_x_'.$column['column']); ?>" value="<?php echo($column['panzoom_x']) ?>"></td>
						<td><input class="box_sm" style="text-align:right;" type="number" step="0.0001" id="<?php echo('panzoom_y_'.$column['column']); ?>" value="<?php echo($column['panzoom_y']) ?>"></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	
	<div>
		<form action="<?=(base_url('database/fix_calibration_step3/0')); ?>" method="POST" name="form_next_action" >
			<input name="columns" id="columns" type="hidden" />
		</form>
	</div>
						
	<div class="form-group row d-flex">
		<!-- horizontal position -->
		<p class="col-3 pl-0 text-right"><?php echo 'Horizontal Position: ';?>
		<input class="box_sm" type="number" id="input-x" name="panzoom_x" readonly value="<?php echo($session->panzoom_x); ?>" tabindex="-1"></p>
		<!-- vertical position -->
		<p class="col-3 pl-0 text-right"><?php echo 'Vertical Position: ';?>
		<input class="box_sm" type="number" id="input-y" name="panzoom_y" readonly value="<?php echo($session->panzoom_y); ?>" tabindex="-1"></p>
		<button  
			class="go_button btn btn-success col-6 pl-0">Fill in each row with details for each column and then click HERE
		</button>								
	</div>
	
	<!-- show image -->
	<!-- Inject initial values for Panzoom here (x, y, zoom ...) -->
	<div class="panzoom-wrapper">
		<div class="panzoom" id="panzoom_image">
			<?php
				echo 
					"<img 
						src=\"data:$session->mime_type;base64,$session->fileEncode\" 
						alt=\"$session->image\"   
						data-scroll=\"$session->panzoom_s\"
					>"; 
			?>
		</div>
	</div>

	<div class="alert row mt-2 d-flex justify-content-between">
		<a id="return" class="btn btn-primary mr-0" href="<?php echo(base_url('syndicate/show_all_transcriptions_step1/'.$session->saved_syndicate_index)); ?>">
		<?php echo $session->current_project[0]['back_button_text']?>
		</a>		
	</div>
	
<script>
	// Thanks to https://github.com/timmywil/panzoom/
	
	// debug with console.log('message'); or window.alert('message');

	// HTML elements to hold Panzoom

	// panzoom must be in global scope
	
	// get vars to control panzoom and zoomLock
	var panzoom_x = <?php echo json_encode($session->panzoom_x); ?>;
	var panzoom_y = <?php echo json_encode($session->panzoom_y); ?>;
	var panzoom_z = <?php echo json_encode($session->panzoom_z); ?>;
	
	// get html 
	const panzoomElementWrapper = document.querySelector(".panzoom-wrapper");
	const panzoomElement = panzoomElementWrapper.querySelector(".panzoom");

	// Instantiate Panzoom
	const panzoom = Panzoom(panzoomElement, {minScale: 1, maxScale: 10});
			
	// Setup default view using image element data attributes
	setTimeout(pan);	
	function pan() 
		{
			// sometimes x and y can be 0, which causes a problem in image view.
			// protect by checking for x and y zero and putting in reasonable start values.
			if ( panzoom_x == 0 )
				{
					panzoom_x = 1;
				}
			if ( panzoom_y == 0 )
				{
					panzoom_y = 1;
				}
			if ( panzoom_z == 0 )
				{
					panzoom_z = 1;
				}
			// then pan
			panzoom.zoom(parseFloat(panzoom_z));
			panzoom.pan(parseFloat(panzoom_x), parseFloat(panzoom_y));
		}	
			
	// Update image position
	panzoomElement.addEventListener("panzoomchange", (event) => 
		{
			const formInputX = document.querySelector("#input-x");
			formInputX.value = event.detail.x;
			const formInputY = document.querySelector("#input-y");
			formInputY.value = event.detail.y;
		});
		
$(document).ready(function()
	{	
		// define table
		const table = document.getElementById('user_inputs');
		const rows = table.getElementsByTagName('tr');
		
		// detect click in table row
		Array.from(rows).forEach((row, index) => 
			{
				row.addEventListener('click', () => 
					{
						const cells = row.getElementsByTagName('input');
						$('#panzoom_x_'+cells[0].value).val(document.getElementById("input-x").value);
						$('#panzoom_y_'+cells[0].value).val(document.getElementById("input-y").value);
					});
			});
		
		// detect submit button
		$('.go_button').on("click", function()
			{
				var arr = new Array();
				Array.from(rows).forEach((row, index) => 
					{
						const cells = row.getElementsByTagName('input');
						arr[index] = {
										"cl" : cells[0].value,
										"fl" : cells[1].value,
										"ll" : cells[2].value,
										"px" : cells[3].value,
										"py" : cells[4].value,
									 }
					});
				// get last line number
				var lastLineno = <?php echo json_encode($session->fix_calib_last_lineno); ?>;
				// validate
				var error = 0;
				for (var i = 0; i < arr.length; i++) 
					{
						if ( arr[i]['fl'] == 0 ) {alert('You MUST enter a first line number for column '+arr[i]['cl'] ); error = 1;}
						if ( arr[i]['ll'] == 0 ) {alert('You MUST enter a last line number for column '+arr[i]['cl'] ); error = 1;}
						if ( arr[i]['px'] == 0 ) {alert('You MUST enter a panzoom X value for column '+arr[i]['cl'] ); error = 1;}
						if ( arr[i]['py'] == 0 ) {alert('You MUST enter a panzoom Y value for column '+arr[i]['cl'] ); error = 1;}

						if ( parseInt(arr[i]['fl'], 10) > parseInt(arr[i]['ll'], 10) ) {alert('First line cannot be greater than or equal to last line for column '+arr[i]['cl'] ); error = 1;}

						if ( i > 0 )
							{
								if ( parseInt(arr[i]['fl'], 10) <= parseInt(arr[i-1]['ll'], 10) ) {alert('First line number for column '+arr[i]['cl']+' cannot be less than or equal to last line for column '+arr[i-1]['cl'] ); error = 1;}
								if ( parseInt(arr[i]['ll'], 10) <= parseInt(arr[i-1]['ll'], 10) ) {alert('Last line number for column '+arr[i]['cl']+' cannot be less than or equal to last line for column '+arr[i-1]['cl'] ); error = 1;}
							}
							
						if ( i == arr.length-1 )
							{
								if ( arr[i]['ll'] < lastLineno ) {alert('Last line number for last colomn '+arr[i]['cl']+' cannot be less than the last line number in the details list '+lastLineno ); error = 1;}
							}
					}
				// submit to controller
				if ( error == 0 )
					{
						$('#columns').val(JSON.stringify(arr));
						$('form[name="form_next_action"]').submit();
					}
			});
	});
	
</script>
