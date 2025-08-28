<?php $session = session(); ?>	

<script>
	// Thanks to https://github.com/timmywil/panzoom/
	
	// debug with console.log('message'); or window.alert('message');

	// HTML elements to hold Panzoom

	// panzoom must be in global scope
	
	// get vars to control panzoom, zoomLock etc
	var zoomLock = <?php echo json_encode($session->zoom_lock); ?>;
	var cycleCode = <?php echo json_encode($session->BMD_cycle_code); ?>;
	var calibStage = <?php echo json_encode($session->calibrate); ?>;
	var verifytranscribeCalibrate = <?php echo json_encode($session->verifytranscribe_calibrate); ?>;
	var scrollStep = <?php echo json_encode($session->current_transcription[0]['BMD_image_scroll_step']); ?>;
	var image_y = <?php echo json_encode($session->current_transcription[0]['BMD_image_y']); ?>;
	var lastEl = <?php echo json_encode($session->lastEl); ?>;
	var panzoomID = document.getElementById("panzoom");
	var client_x = panzoomID.clientWidth;
	var client_y = panzoomID.clientHeight;
	var calib_x = <?php echo json_encode($session->current_transcription[0]['header_x']); ?>;
	var calib_y = <?php echo json_encode($session->current_transcription[0]['header_y']); ?>;
	var defFields = <?php echo json_encode($session->current_transcription_def_fields); ?>;
	var defUpdateflag = <?php echo json_encode($session->def_update_flag); ?>;
	const position = { x: 0, y: 0 }
		
	// get panzoom x,y,z
	var panzoom_x = <?php echo json_encode($session->panzoom_x); ?>;
	var panzoom_y = <?php echo json_encode($session->panzoom_y); ?>;
	var panzoom_z = <?php echo json_encode($session->panzoom_z); ?>;
	
	// calculate x and y if in INPRO and new transcription
	if ( cycleCode == 'INPRO' && lastEl.length === 0 && defUpdateflag == 0)
		{
			// calculate panzoom x
			var header_panzoom_x = <?php echo json_encode($session->current_transcription[0]['BMD_panzoom_x']); ?>;
			var panzoom_x = header_panzoom_x * client_x / calib_x;

			// calculate panzoom y
			var header_panzoom_y = <?php echo json_encode($session->current_transcription[0]['BMD_panzoom_y']); ?>;
			var panzoom_y = (header_panzoom_y * client_y / calib_y) + (scrollStep * panzoom_z);
			
			// calculate and apply field width
			for (let i = 0; i < defFields.length; i++) 
				{
					var element = document.getElementById(defFields[i].html_id);
					var newWidth = defFields[i].column_width * client_x / calib_x;
					element.style.width = newWidth+'px';
					
					updateDeffields(defFields, element.id, newWidth);
				}
		}

	// has user applied zoom
	if ( document.getElementById("input-zoom") )
		{
			document.getElementById("input-zoom").oninput = function() 
				{
					panzoom_z = $("#input-zoom").val();
					panzoom.zoom(parseFloat(panzoom_z));
					panzoom.pan(parseFloat(panzoom_x), parseFloat(panzoom_y));
				}
		}
	
	// get html 
	const panzoomElementWrapper = document.querySelector(".panzoom-wrapper");
	const panzoomElement = panzoomElementWrapper.querySelector(".panzoom");

	// Instantiate Panzoom
	const panzoom = Panzoom(panzoomElement, {minScale: 1, maxScale: 10});
			
	// Setup default view using image element data attributes
	setTimeout(pan);
		
	// Update image position and zoom values in input on Panzoom change in INPRO
	switch (cycleCode) 
		{
			case 'INPRO':
				panzoomElement.addEventListener("panzoomchange", (event) => 
					{
						const formInputX = document.querySelector("#input-x");
						formInputX.value = event.detail.x;
						const formInputY = document.querySelector("#input-y");
						formInputY.value = event.detail.y;
						const formInputZoom = document.querySelector("#input-zoom");
						formInputZoom.value = event.detail.scale;
					});
				break;
			case 'CALIB':
				if ( calibStage == 0 );
					{	
						panzoomElement.addEventListener("panzoomchange", (event) => 
							{
								const formInputX = document.querySelector("#input-x");
								formInputX.value = event.detail.x;
								const formInputY = document.querySelector("#input-y");
								formInputY.value = event.detail.y;
								const formInputZoom = document.querySelector("#input-zoom");
								formInputZoom.value = event.detail.scale;
							});
					}
				break;
		}
				
	// if in calibrate or calib called from verit or inpro
	if ( cycleCode == 'CALIB' || verifytranscribeCalibrate == 'Y' )
		{
			if ( calibStage == '0' )
				{
					// and in stage 0, allow zoom
					zoomLock = 'N';
				}
			else
				{
					// otherwise lock zoom
					zoomLock = 'Y';
				}
		}
				
	// set zoomLock
	if ( zoomLock == 'N' )
		{
			panzoomElement.addEventListener("wheel", panzoom.zoomWithWheel);
		}
		
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
			
	function updateDeffields(defFields, elementId, width, height)
		{
			// find the id in the defFields array of arrays
			for (var fieldsLineIndex in defFields) 
				{
					for (var fieldIndex in defFields[fieldsLineIndex])
						{
							// have I found the iteration with the current ID?
							if ( defFields[fieldsLineIndex][fieldIndex]["html_id"] == elementId )
								{
									defFields[fieldsLineIndex][fieldIndex]["column_width"] = width;
									defFields[fieldsLineIndex][fieldIndex]["column_height"] = height;
								}
						}
				}
			// update form field
			$('#input-defFields').val(JSON.stringify(defFields));
		}
		
	// make image resizable by dragging with mouse
	// set new height to existing image y in case no drag
	$('#input-newHeight').val(image_y);
	
	// drag image size
	$('.panzoom-wrapper').resizable(
		{
			// do the resize
			resize: function(e, ui) 
				{
				},
			
			// when stopped dragging, get new image height
			stop: function(e, ui) 
				{
					// set new image height
					$('#input-newHeight').val(ui.size.height);
				}
		});
	
	// make field resizable by dragging with mouse
	// drag field size
	$('.input_wrapper').resizable(
		{
			// do the resize
			handles: 'e, w',
			resize: function(e, ui) 
				{
				},
		
		// when stopped dragging, get new field width
		stop: function(e, ui) 
			{			
				// set new field width and height
				updateDeffields(defFields, e.target.id, ui.size.width, ui.size.height);
			}
		});
		
	// add sortable function
	$( function() 
		{
			var fieldsSelectedObject = <?php echo json_encode($session->fields_selected); ?>;
			if ( fieldsSelectedObject != null )
				{
					const fieldsSelectedArray = Object.entries(fieldsSelectedObject);
					var fieldsSelectedcount = <?php echo json_encode($session->fields_selected_count); ?>;

					if ( fieldsSelectedArray != null && fieldsSelectedcount > 0 )
						{
							fieldsSelectedArray.forEach(function(fieldsLine)
								{	
									$( "#sortable_"+fieldsLine[0] ).sortable();
								});
						}
				}
		});
	
</script>


