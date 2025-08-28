<?php $session = session(); ?>

	<div>
		<label for="cols">Number of columns in this scan =></label>
		<input class="box_sm" style="text-align:right;" type="number" id="cols" value="<?php echo($session->cols) ?>">
		<button  
			class="go_button btn btn-success">Go
		</button>
	</div>
	
	<div>
		<form action="<?=(base_url('database/fix_calibration_step2/0')); ?>" method="POST" name="form_next_action" >
			<input name="columns" id="columns" type="hidden" />
		</form>
	</div>					

	<div class="alert row mt-2 d-flex justify-content-between">
		<a id="return" class="btn btn-primary mr-0" href="<?php echo(base_url('syndicate/show_all_transcriptions_step1/'.$session->saved_syndicate_index)); ?>">
		<?php echo $session->current_project[0]['back_button_text']?>
		</a>
	</div>
	
	<br>
	
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

<script>
	
$(document).ready(function()
	{	
		$('.go_button').on("click", function()
			{
				// load variables to form
				$('#columns').val(document.getElementById("cols").value);
				// and submit the form
				$('form[name="form_next_action"]').submit();
			});
	});

</script>
