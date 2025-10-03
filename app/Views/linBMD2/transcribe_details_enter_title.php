<?php $session = session(); ?>

	<style>
	#overlay {
	  position: fixed;
	  display: none;
	  width: 60%;
	  height: 75%;
	  top: 12.5%;
	  left: 20%;
	  right: 0;
	  bottom: 0;
	  background-color: rgba(0,0,0,0.85);
	  z-index: 2;
	  cursor: pointer;
	}

	#text{
	  position: absolute;
	  color: white;
	  font-weight: bold;
	}
	</style>

	<div class="row mt-2 justify-content-between align-items-center alert alert-primary">
		
		<span id="return" class="" title="<?=$session->current_project[0]['back_button_text']?>" style="cursor: pointer">
			<i class="fa-solid fa-backward"></i>
		</span>
		
		<?php 	if ( $session->image_source[0]['source_images'] == 'yes' )
					{ ?>
						<span id="no_image" class="" title="No Image" style="cursor: pointer">
							<i class="fa-solid fa-circle-xmark"></i>
						</span>
		<?php		} ?>
		
		<?php 	if ( $session->image_source[0]['source_images'] == 'yes' )
					{ ?>
						<span class="font-weight-bold"><?=$session->current_image_file_name.' => '.$session->current_transcription[0]['BMD_records'].' records transcribed.';?></span>
		<?php		}
				else
					{ ?>
						<span class="font-weight-bold"><?=$session->current_transcription[0]['BMD_records'].' records transcribed.';?></span>
		<?php		} ?>
				
		
		<?php
		switch ( $session->current_project[0]['project_name'] )
			{
				case 'FreeBMD': 
					break;
				case 'FreeREG':
					?>
					<?php 	if ( $session->image_source[0]['source_images'] == 'yes' )
								{ ?>
									<span>
										<span hidden id="previous_image" class="" title="Previous Image" style="cursor: pointer">
											<i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
										</span>
									
										<span class="font-weight-bold">
											<span>Image</span>
											<span id="xofn_image"><?=$session->current_image_number?></span>
											<span>of</span>
											<span><?=$session->image_count?></span>
										</span>
									
										<span hidden id="next_image" class="" title="Next Image" style="cursor: pointer">
											<i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
										</span>
									</span>
					<?php		} ?>
					
					<?php
					foreach ( $session->event_types as $event_type )
						{ ?>
							<span class="d-flex flex-column justify-content-between align-items-center">
								<span class="justify-content-between align-items-center">
									<span 
										id="<?=$event_type['type_name_lower']?>" 
										title="<?=$event_type['type_controller']?>" 
										style="cursor:pointer;">
										<?=$event_type['type_name_upper']?>
									</span>
									<span>
										<?=$session->counts[$event_type['type_name_lower']]?>
									</span>
								</span>
								<span>
									<?php
									if ( $session->current_transcription[0]['current_data_entry_format'] == $event_type['type_name_lower'] AND ( count($session->layout_dropdown) > 0 OR count($session->predefined_layout_dropdown) > 0 ) )
										{ ?>
											<select
												id="layoutChange"
												style="font-size:1vw !important;">
												<optgroup label="Your Layouts">
													<?php
													foreach ( $session->layout_dropdown as $key => $layout )
														{ 
															?>
															<option
																value="<?=$key?>" 
																<?php if ( $session->current_layout == $key ) { echo ' selected'; } ?>>
																<?=$layout?>
															</option>
														<?php
														} ?>
												</optgroup>
												<optgroup label="Pre-defined Layouts">
													<?php
													foreach ( $session->predefined_layout_dropdown as $key => $layout )
														{ 
															?>
															<option
																value="<?=$key?>" 
																<?php if ( $session->current_layout == $key ) { echo ' selected'; } ?>>
																<?=$layout?>
															</option>
														<?php
														} ?>
												</optgroup>
											</select>
										<?php
										} ?>
								</span>	
							</span>	
						<?php
						} ?>
					
					<?php
					break;
				case 'FreeCEN':
					break;
			} ?>
		
		<?php 	if ( $session->image_source[0]['source_images'] == 'yes' )			
					{ ?>
						<span id="image_parameters" class="" title="Change Image Parameters" style="cursor: pointer">
							<i class="fa-solid fa-dice-d6">IP</i>
						</span>
		<?php		} ?>
		
		<span hidden id="field_parameters" class="" title="Change Field Parameters" style="cursor: pointer">
			<i class="fa-solid fa-dice-d20">FP</i>
		</span>
		
		<!-- show sharpen slider -->
		<?php 	if ( $session->image_source[0]['source_images'] == 'yes' )			
					{ ?>
						<div class="">
							<input class="" type="range" id="sharpen-slider" min="1" max="5" step=".5" value="$session->sharpen" />
						</div>
						
						<!-- Sharpen filter for image using SVG -->
						<svg id="filters">
							<defs>
								<filter id="unsharpy" x="0" y="0" width="100%" height="100%">
									<feGaussianBlur result="blurOut" in="SourceGraphic" stdDeviation="1" />
									<feComposite operator="arithmetic" k1="0" k2="4" k3="-3" k4="0" in="SourceGraphic" in2="blurOut" />
								</filter>
							</defs>
						</svg>
		<?php		} ?>			
		
		<span id="show_shortcuts" class="" title="Show Shortcuts" style="cursor: pointer" onclick="on()">
			<i class="fa-solid fa-exclamation"></i>
		</span>
		
		<span id="send_message" class="" title="Send Message to Co-ordinator" style="cursor: pointer">
			<i class="fa-solid fa-envelope-open-text"></i>
		</span>	
	</div>
	
	
	<div id="overlay" onclick="off()">
		<div class="col-12 ml-5 mb-10" id="text" style="font-size:1rem;">
			<div class="row">
				<p></p>
			</div>
			<div class="row">
				<p><u>Data Entry Shortcuts KEYS and CODES (Equivalent MAC codes in brackets)</u></p>
			</div>
			<div class="row">
				<p>INSERT key (Ctrl+r) = Repeat data from same field in last line transcribed. If in forenames, repeat first only</p>
			</div>
			<div class="row">
				<p>Ctrl+a key = Repeat data from same field in last line transcribed. If in forenames, repeat all</p>		
			</div>
			<div class="row">
				<p>HOME key (Fn+LeftArrow) = Repeat data for all fields except last field from last line transcribed and position cursor in last field.</p>
			</div>
			<div class="row">
				<p>END key (Fn+RightArrow) = Copy contents of Surname to Mother and move cursor to next field.</p>
			</div>
			<div class="row">
				<p>TAB out of District = Autofill Volume and position cursor to next field.</p>
			</div>
			<div class="row">
				<p>CTRL+b key = Move cursor to end of data in previous field.</p>
			</div>
			<div class="row">
				<p>PAGEDOWN key (Fn+DownArrow) = Move image by one line (ScrollStep) towards end of image.</p>
			</div>
			<div class="row">
				<p>PAGEUP key (Fn+UpArrow) = Move image by one line (ScrollStep) towards beginning of image.</p>	
			</div>
			<div class="row">
				<p>Alt+v key = Simulate mouse click when in Verify line by line</p>
			</div>
			<div class="row">
				<p># at end of data in field = Ignore data integrity checks for this field.</p>
			</div>
			<div class="row">
				<p>@ at end of data in field = Ignore capitalisation for this field.</p>	
			</div>
			<div class="row">
				<p class="text-info">The line before which you are inserting a new line.</p>
			</div>
			<div class="row">
				<p class="text-warning">The line that you added.</p>	
			</div>
			<div class="row">
				<p class="text-success">The last line that you transcribed.</p>
			</div>
			<div class="row">
				<p class="text-danger">A De-activated line unless insert before. You can insert a line before a de-activated line.</p>
			</div>
			<div class="row">
				<p>Click anywhere to exit this screen.</p>
			</div>
		</div>
	</div>
	
	<div>
		<form action="<?=(base_url('transcribe/transcribe_step1/0'))?>" method="POST" name="form_return"></form>	
	</div>
	
	<div>
		<form action="<?=(base_url('transcription/FreeREG_action_image/0/prev'))?>" method="POST" name="form_previous_image"></form>	
	</div>
	
	<div>
		<form action="<?=(base_url('transcription/FreeREG_action_image/0/next'))?>" method="POST" name="form_next_image"></form>	
	</div>
	
	<div>
		<form action="<?=(base_url('transcribe/no_image'))?>" method="POST" name="form_no_image"></form>	
	</div>
	
	<div>
		<form action="<?=(base_url('transcribe/image_parameters_step1/0'))?>" method="POST" name="form_image_parameters"></form>	
	</div>
	
	<div>
		<form action="<?=(base_url('transcribe/enter_parameters_step1/0'))?>" method="POST" name="form_field_parameters"></form>	
	</div>
	
	<div>
		<form action="<?=(base_url('transcribe/message_to_coord_step1/0'))?>" method="POST" name="form_send_message"></form>	
	</div>
	
	<div>
		<form action="<?=(base_url('transcription/update_data_entry_format'))?>" method="POST" name="form_update_data_entry_format">
			<input name="data_entry_format" id="data_entry_format" type="hidden">
		</form>	
	</div>
	
	<div>
		<form action="<?=(base_url('transcribe/change_layout'))?>" method="POST" name="form_change_layout">
			<input name="layoutSubmit" id="layoutSubmit" type="hidden">
		</form>	
	</div>
	
	
				
<script>
	
$(document).ready(function() 
	{
		// initialise image : previous and next buttons for FreeREG only if images are required
		var project_name = "<?=$session->current_project[0]['project_name']?>";
		var source_images = "<?=$session->image_source[0]['source_images']?>";
		if ( project_name == 'FreeREG' && source_images == 'yes' )
			{
				// set buttons
				var current_image_number = document.getElementById('xofn_image').innerHTML;
				var image_count = <?=$session->image_count?>;
				if ( image_count > 0 )
					{
						set_image_buttons(current_image_number, image_count);
					}
			}
			
		// initialise event type
		var event_type = "<?=$session->current_transcription[0]['current_data_entry_format']?>";
		switch(event_type) 
			{
				case 'baptism':
					document.getElementById("baptism").style.fontSize = "20px";
					document.getElementById("baptism").style.fontWeight = "bold";
					document.getElementById("baptism").style.textDecoration = "underline";
					document.getElementById('field_parameters').removeAttribute('hidden');
					break;
				case 'marriage':
					document.getElementById("marriage").style.fontSize = "20px";
					document.getElementById("marriage").style.fontWeight = "bold";
					document.getElementById("marriage").style.textDecoration = "underline";
					document.getElementById('field_parameters').removeAttribute('hidden');
					break;
				case 'burial':
					document.getElementById("burial").style.fontSize = "20px";
					document.getElementById("burial").style.fontWeight = "bold";
					document.getElementById("burial").style.textDecoration = "underline";
					document.getElementById('field_parameters').removeAttribute('hidden');
					break;
				default:
					document.getElementById("baptism").style.fontSize = "20px";
					document.getElementById("baptism").style.fontWeight = "bold";
					document.getElementById("baptism").style.textDecoration = "underline";
					
					document.getElementById("marriage").style.fontSize = "20px";
					document.getElementById("marriage").style.fontWeight = "bold";
					document.getElementById("marriage").style.textDecoration = "underline";
					
					document.getElementById("burial").style.fontSize = "20px";
					document.getElementById("burial").style.fontWeight = "bold";
					document.getElementById("burial").style.textDecoration = "underline";
					break;
			}
			
		// restore fields if data was stored 
		if ( sessionStorage.getItem("saved") == "yes" )
			{
				// initialise
				var defFields = document.getElementsByClassName("saveData");
				// read data elements
				for (let i = 0; i < defFields.length; i++) 
					{
						// restore value
						document.getElementById(defFields[i].id).value = sessionStorage.getItem(defFields[i].id);
					}
				// clear session storage for next time
				sessionStorage.clear();
			}
					
		$('#return').on("click", function()
			{			
				$('form[name="form_return"]').submit();
			});
			
		$('#previous_image').on("click", function()
			{			
				$('form[name="form_previous_image"]').submit();
			});
			
		$('#next_image').on("click", function()
			{			
				$('form[name="form_next_image"]').submit();
			});
			
		$('#no_image').on("click", function()
			{			
				// save current values
				saveCurrentValues();
				// submit form
				$('form[name="form_no_image"]').submit();
			});
			
		$('#image_parameters').on("click", function()
			{			
				// save current values
				saveCurrentValues();
				// submit form
				$('form[name="form_image_parameters"]').submit();
			});
			
		$('#field_parameters').on("click", function()
			{			
				// save current values
				saveCurrentValues();
				// submit form
				$('form[name="form_field_parameters"]').submit();
			});
			
		$('#send_message').on("click", function()
			{			
				// save current values
				saveCurrentValues();
				// submit form
				$('form[name="form_send_message"]').submit();
			});
			
		$('#baptism').on("click", function()
			{			
				// set colours and show field parameters
				document.getElementById("baptism").style.color = "DarkMagenta";
				document.getElementById("baptism").style.fontSize = "30px";
				document.getElementById("marriage").style.color = "red";
				document.getElementById("marriage").style.fontSize = "10px";
				document.getElementById("burial").style.color = "red";
				document.getElementById("burial").style.fontSize = "10px";
				document.getElementById('field_parameters').removeAttribute('hidden');
				// set event type
				set_event_type("baptism");
			});
			
		$('#marriage').on("click", function()
			{			
				document.getElementById("baptism").style.color = "red";
				document.getElementById("baptism").style.fontSize = "10px";
				document.getElementById("marriage").style.color = "Green";
				document.getElementById("marriage").style.fontSize = "30px";
				document.getElementById("burial").style.color = "red";
				document.getElementById("burial").style.fontSize = "10px";
				document.getElementById('field_parameters').removeAttribute('hidden');
				// set event type
				set_event_type("marriage");
			});
			
		$('#burial').on("click", function()
			{			
				document.getElementById("baptism").style.color = "red";
				document.getElementById("baptism").style.fontSize = "10px";
				document.getElementById("marriage").style.color = "red";
				document.getElementById("marriage").style.fontSize = "10px";
				document.getElementById("burial").style.color = "Gray";
				document.getElementById("burial").style.fontSize = "30px";
				document.getElementById('field_parameters').removeAttribute('hidden');
				// set event type
				set_event_type("burial");
			});
		
		$('#layoutChange').on("click", function()
			{	
				// load variables to form
				$('#layoutSubmit').val(document.getElementById("layoutChange").value);
		
				// and submit the form
				$('form[name="form_change_layout"]').submit();		
			});
	});
		
function on() {
  document.getElementById("overlay").style.display = "block";
}

function off() {
  document.getElementById("overlay").style.display = "none";
}

function set_image_buttons(current_image_number, image_count)
	{	
		if ( current_image_number != 1 )
			{
				document.getElementById('previous_image').removeAttribute('hidden'); 
			}
		if ( current_image_number != image_count )
			{
				document.getElementById('next_image').removeAttribute('hidden'); 
			}
	}
	
function set_event_type(eventType)
	{
		// load variables to form
		$('#data_entry_format').val(eventType);
			
		// and submit the form
		$('form[name="form_update_data_entry_format"]').submit();
	}

function saveCurrentValues()
	{
		// initialise
		var defFields = document.getElementsByClassName("saveData");
		sessionStorage.clear();
		sessionStorage.setItem("saved", "yes");
		// read def fields
		for (let i = 0; i < defFields.length; i++) 
			{
				// store value
				sessionStorage.setItem(defFields[i].id, document.getElementById(defFields[i].id).value);
			}
	}	

</script>
	



