<?php 
		$session = session();
	?>
		
	<div>
		<!-- form to capture image parms -->
		<form action="<?php echo(base_url('/transcribe/calibrate_coord_step2')) ?>" method="post">
			<!-- hidden fields to capture screen size -->
			<input type="hidden" name="client_x" id="client-x" readonly>
			<input type="hidden" name="client_y" id="client-y" readonly>
			<input type="hidden" name="defFields" id="input-defFields">
			<div class="row">
				<p 
					class="bg-warning col-12 pl-0 text-center font-weight-bold" 
					style="font-size:1.0vw;">
					
					<?php
						echo 'Calibration by Syndicate - '.$session->reference_synd_name.' for Reference Scan - '.$session->reference_scan.', Reference Path - '.$session->reference_path.', Reference Scan Format - '.$session->reference_scan_format.', Data Entry Format - '.$session->reference_data_entry_format.'.'; 
					?>
				</p>
			</div>
				
			<?php
			switch ($session->calibrate) 
				{
					// rotation, zoom, position
					case 0: ?> 
						<div class="form-group row d-flex align-items-center">
							<!-- rotation -->
							<label for="rotation" class="col-1 pl-0">Rotation:</label>
							<input type="text" class="form-control col-1" id="rotation" name="rotation" aria-describedby="userHelp" value="<?php echo($session->rotation) ?>">
							<small id="userHelp" class="form-text text-muted col-2">Enter the rotation required in degrees; -ve for rotate left, +ve for rotate right. Can be decimals of a degree. The image will be re-positioned as you change the rotation.</small>
							
							<!-- zoom -->
							<label for="input-zoom" class="col-1 pl-0">Zoom:</label>
							<input type="number" step="0.1" min="1" max="10" class="form-control col-1" id="input-zoom" name="panzoom_z" aria-describedby="userHelp" value="<?php echo($session->panzoom_z); ?>">
							<small id="userHelp" class="form-text text-muted col-2">Use the mouse scroll wheel to zoom the image as required or enter a zoom factor.</small>
							
							<!-- zoom lock -->
							<label for="input-zoom-lock" class="col-1 pl-0">Zoom Locked?:</label>
							<select name="zoom_lock" id="input-zoom-lock" class="box col-1">
								<?php foreach ($session->yesno as $key => $value): ?>
									 <option value="<?php echo esc($key)?>"<?php if ( $key == $session->zoom_lock ) {echo esc(' selected');} ?>><?php echo esc($value)?></option>
								<?php endforeach; ?>
							</select>
							<small id="userHelp" class="form-text text-muted col-2">Stop zoom on Transcribe and Verify screens?</small>	
						</div>
						
						<div class="form-group row d-flex">
							<!-- horizontal position -->
							<p class="col-4 pl-0 text-right"><?php echo 'Horizontal Position: '.$session->x_min.' <= ';?>
							<input class="box_sm" type="number" id="input-x" name="panzoom_x" readonly value="<?php echo($session->panzoom_x); ?>" tabindex="-1">
							<?php echo ' >= '.$session->x_max;?></p>
							<!-- vertical position -->
							<p class="col-4 pl-0 text-right"><?php echo 'Vertical Position: '.$session->y_min.' <= ';?>
							<input class="box_sm" type="number" id="input-y" name="panzoom_y" readonly value="<?php echo($session->panzoom_y); ?>" tabindex="-1">
							<?php echo ' >= '.$session->y_max;?></p>
															
						</div>
						<?php
						break;
								
					// scroll step, image height	
					case 1: ?>
						<div class="form-group row d-flex align-items-center">	
							<!-- number of lines to be shown -->
							<label for="input_height_lines" class="col-2 pl-0">Number of scan lines to show in Transcribe and Verify :</label>
							<input type="number" class="form-control col-1" id="input_height_lines" name="height_l" value="<?php echo($session->height_l); ?>">
							<small id="userHelp" class="form-text text-muted col-5">Enter the number of scan lines that you wish to see in the transcribe and verify screens. The height of the image will be calculated automatically.</small>
							
							<!-- height of image -->
							<label for="input_height_image" class="col-1 pl-0">Image Height :</label>
							<input type="number" class="form-control col-1" id="input_height_image" name="image_y" readonly aria-describedby="userHelp" value="<?php echo($session->image_y); ?>" tabindex="-1">
						</div>
					
						<!-- scroll step -->
						<div class="form-group row d-flex align-items-center">
							<!-- number of lines -->
							<label for="input_scroll_lines" class="col-2 pl-0">Number of lines to use for scroll step calculation :</label>
							<input type="number" class="form-control col-1" autofocus id="input_scroll_lines" name="panzoom_l" value="<?php echo($session->panzoom_l); ?>">
							<small id="userHelp" class="form-text text-muted col-5">Enter the number of lines you can see in the image to calculate the scroll step. Only enter the number of WHOLE lines that you can see. </small>
							
							<!-- scroll step -->
							<label for="input_scroll_step" class="col-1 pl-0">Scroll Step :</label>
							<input type="number" step="0.1" min="1" max="100" class="form-control col-1" id="input_scroll_step" name="panzoom_s" aria-describedby="userHelp" value="<?php echo($session->panzoom_s); ?>">
							<small id="userHelp" class="form-text text-muted col-2">You can refine the scroll step manually entering a value here by using the up/down arrows in the input field. </small>
						</div>

						<?php
						break;	
							
					// data entry fields	
					case 2: ?>
					
						<!-- instructions -->
						<div class="row d-flex align-items-center">
							<p class="col-12 pl-0">Drag the fields to the required position above the image. Starting with the leftmost field,  drag the left or right edges to make the field size match the image fields. </p>
						</div>
						
						<div class="draggable row d-flex align-items-center">
						<?php
							// loop through table element by element
							foreach ($session->default_field_parms as $td) 
								{ ?>
									<!-- output data -->
									<input
										class=	"resizable form-control"
										style=	"	
													height: 		auto; 
													width: 			<?php if (esc($td['column_width']) > 0) {echo esc($td['column_width']);}?>px; 
													font-size: 		<?= esc($td['font_size']);?>vw; 
													font-weight: 	<?= esc($td['font_weight']);?>;
													text-align: 	<?php echo esc($td['field_align']);?>;
													padding-left: 	<?= esc($td['pad_left']).'px';?>;
													background-color: 	<?= esc($td['colour']);?>;
													border: 		2px  solid rgba(0,0,0,0.5);
													border-radius: 	4px;	
												"
										type=	"<?php if ( $td['special_test'] == 'should_be_blank' ) { echo esc('hidden'); } else { echo esc($td['field_format']); }?>";
										id=		"<?php echo esc($td['html_id']);?>" 
										name=	"<?php echo esc($td['html_name']);?>"
										value=	"<?php echo esc($td['field_name']); ?>"
										readonly
									>
								<?php
								} ?>
						</div>
						<?php
						break;
				} ?>			
	
			<!-- show image -->
			<!-- Inject initial values for Panzoom here (x, y, zoom ...) -->
			<br>
			<div class="panzoom-wrapper">
				<div class="panzoom" id="panzoom">
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
				<?php
				if ( $session->calibrate == 0 ) 
					{ ?>
						<a id="return" class="btn btn-primary mr-0" href="<?php echo(base_url('transcribe/calibrate_reference_step1/1')); ?>">
						<?php echo $session->current_project[0]['back_button_text']?>
						</a>
					<?php
				} ?>
				
				<?php
				if ( $session->calibrate != 0 ) 
					{ ?>
						<a id="return" class="btn btn-primary mr-0" href="<?php echo(base_url('/transcribe/calibrate_coord_step1/0/back')); ?>">
						<?php echo $session->current_project[0]['back_button_text']?>
						</a>
					<?php
					} ?>
					
				<?php
				if ( $session->calibrate < 2 ) 
					{ ?>
						<button type="submit" class="btn btn-primary mr-0">
							<span>Continue calibration</span>	
						</button>
					<?php
					} ?>

				<?php
				if ( $session->calibrate == 2 ) 
					{ ?>
						<a id="return" class="btn btn-primary mr-0" href="<?php echo(base_url(	'transcribe/default_field_parms_coord_step1/0')); ?>">
							<span>Set Default Transcription Set field parameters</span>
						</a>
						
						<button type="submit" class="btn btn-primary mr-0">
							<span>Complete calibration.</span>	
						</button>
					<?php
					} ?>
			</div>
		</form>
	</div>

<script>
			
	<!-- get calibration stage -->
	var calibrateStage = <?php echo json_encode($session->calibrate); ?>;
	
	<!-- apply rotate if stage 0 -->
	if ( calibrateStage == '0' )
		{
			document.getElementById("rotation").addEventListener("input", (event) => 
				{
					const rotateImage = $("#rotation").val();
					var imgs = document.querySelectorAll(".panzoom > img");
					for( var i = 0; i < imgs.length; i++ ) 
						{
							imgs[i].style.transform = "rotate("+rotateImage+"deg)";
						}
				});
		}
		
	<!-- apply client_x and client_y if stage 0 -->
	if ( calibrateStage == '0' )
		{
			var panzoomID = document.getElementById("panzoom");
			var client_x = panzoomID.clientWidth;
			var client_y = panzoomID.clientHeight;
			$('#client-x').val(JSON.stringify(client_x));
			$('#client-y').val(JSON.stringify(client_y));
		}
		
	<!-- calculate scroll step and window height -->
	document.getElementById("input_scroll_lines").addEventListener("blur", calcSS);
	function calcSS() 
		{
			// get input
			var inputLines = document.getElementById("input_scroll_lines").value;
			if ( inputLines == 0 )
				{
					alert('Please enter the number of whole lines you can see in the image below.');
				}
				
			// get required data
			var imageY = <?php echo json_encode($session->image_y); ?>;
			var panzoomZ = <?php echo json_encode($session->panzoom_z); ?>;
			var heightL = <?php echo json_encode($session->height_l); ?>;
				
			// calculate scroll step
			var scrollStep = imageY / inputLines / panzoomZ;
			$('#input_scroll_step').val(scrollStep);
			
			// calculate image height
			var imageHeight = heightL * scrollStep;
			$('#input_height_image').val(imageHeight);
		}
</script>
