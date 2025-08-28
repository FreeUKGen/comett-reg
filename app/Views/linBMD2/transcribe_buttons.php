	<?php 
		$session = session();
	?>
	
	<form id="post_inputs" action="<?=(base_url($session->controller.'/transcribe_'.$session->controller.'_step2'))?>" method="POST" name="form_submitMe">	
		<!-- fields are added in the javascript -->
	</form>

<script>
	
$(document).ready(function() 
	{
		// get last entry id
		var keydownEvent = jQuery.Event("keydown");
		var last_id = "<?=$session->last_id?>";

		// handle tab press in last id and make it = to submitMe
		$('#'+last_id).on("keydown", function(e)
			{
				// only continue if TAB or ENTER was pressed
				if ( e.key === 'Tab' | e.key === 'Enter' )
					{
						e.preventDefault();
						submit_data();
					}
			});
			
		// OR if the user pressed enter in an error field do the submit
		$("input").keypress(function(e)
			{
				// only continue if ENTER or TAB was pressed and the data-error = yes
				if ( e.currentTarget.dataset.error === 'yes' )
					{
						if ( e.key === 'Tab' | e.key === 'Enter' )
							{
								e.preventDefault();
								submit_data();
							}
					}
			});
	});
	
function submit_data()
	{
		// add the panzoom elements
		const panzoomNames = ["panzoom_x", "panzoom_y", "panzoom_z", "sharpen", "defFields", "newHeight"];
		const panzoomIds = ["input-x", "input-y", "input-zoom", "input-sharpen", "input-defFields", "input-newHeight"];
		for (let i = 0; i < panzoomNames.length; i++) 
		{
			var inputElement = document.createElement("input");
				inputElement.setAttribute('type',"hidden");
				inputElement.setAttribute('name',panzoomNames[i]);
				inputElement.setAttribute('value',document.getElementById(panzoomIds[i]).value);
			// now add the element to the form
			document.querySelector('#post_inputs').appendChild(inputElement);
		}
		
		// add the data elements
		// get current transcription data dictionary
		var current_transcription_def_fields = <?=json_encode($session->current_transcription_def_fields); ?>;
		var current_data_entry_format = <?php echo json_encode($session->current_transcription[0]['current_data_entry_format']); ?>;
		
		// read current data dictionary
		for (var i in current_transcription_def_fields) 
		{
			for (var j in current_transcription_def_fields[i])
				{
					// have I found the iteration with the current ID?
					if ( current_transcription_def_fields[i][j]["data_entry_format"] == current_data_entry_format && current_transcription_def_fields[i][j]['field_check'] == 'Y' && current_transcription_def_fields[i][j]['field_show'] == 'Y')
						{
							// if so create a new input element
							var inputElement = document.createElement("input");
								inputElement.setAttribute('type',"hidden");
								inputElement.setAttribute('name',current_transcription_def_fields[i][j]["html_name"]);
								inputElement.setAttribute('value',document.getElementById(current_transcription_def_fields[i][j]["html_id"]).value);
							// now add the element to the form
							document.querySelector('#post_inputs').appendChild(inputElement);
						}
				}
		}

		// submit form
		$('form[name="form_submitMe"]').submit();
	}
			
</script>
