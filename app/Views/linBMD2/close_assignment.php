	<?php $session = session(); ?>
	
	<div>
		<form action="<?=(base_url($session->return_route.'/0'))?>" method="POST" name="form_return"></form>
		<form action="<?=(base_url('allocation/close_freereg_assignment_step2'))?>" method="POST" name="form_submit"></form>	
	</div>
	
	<!-- data info fields -->
	<div class="row mt-2 alert">
		
		<!-- assignment name and start date -->
		<div class="form-group row d-flex align-items-center" id="assignment_group">
			<label id="ass_name_label" for="ass_name" class="col-2">Assignment Description =></label>
			<input readonly tabindex="-1" type="text" class="form-control col-5" id="ass_name" value="<?=$session->assignment_name ?>">
			<label id="ass_start_label" for="ass_start" class="col-1">Start Date =></label>
			<input readonly tabindex="-1" type="text" class="form-control col-1" id="ass_start" value="<?=$session->assignment_start ?>">
		</div>
		
		<!-- number of attached images -->
		<div class="form-group row d-flex align-items-center" id="images_group">
			<label id="ass_image_label" for="ass_image_count" class="col-2">Number of Images attached =></label>
			<input readonly tabindex="-1" type="text" class="form-control col-1" id="ass_image_count" value="<?=$session->image_count ?>">
		</div>
		
		<!-- records transcribed -->
		<div class="form-group row d-flex align-items-center" id="records_group">
			<label id="ass_records_label" for="ass_record_count" class="col-2">Number of Records transcribed =></label>
			<input readonly tabindex="-1" type="text" class="form-control col-1" id="ass_record_count" value="<?=$session->detail_count ?>">
		</div>	
		
		<!-- upload details -->
		<div class="form-group row d-flex align-items-center" id="uploads_group">
			<label id="ass_csv_label" for="ass_csv_file" class="col-2">CSV file name =></label>
			<input readonly tabindex="-1" type="text" class="form-control col-2" id="ass_csv_file" value="<?=$session->csv_file ?>">
			<label id="ass_upload_date_label" for="ass_upload_date" class="col-1">Uploaded date =></label>
			<input readonly tabindex="-1" type="text" class="form-control col-1" id="ass_upload_date" value="<?=$session->upload_date ?>">
			<label id="ass_upload_status_label" for="ass_upload_status" class="col-1">Upload status =></label>
			<input readonly tabindex="-1" type="text" class="form-control col-1" id="ass_upload_status" value="<?=$session->upload_status ?>">
		</div>	
				
		<div class="form-group row d-flex justify-content-between align-items-center" id="records_group">
			<button id="return" class="btn btn-primary mr-0" title="Previous Page">Oups! Get me out of here fast!</button>
			<span style="font-size:200%; color:red;"> Do you really want to close this assignment? You will loose ALL FreeComETT data for it!</span>
			<button id="confirm" class="btn btn-primary mr-0" title="Confirm action">Confirm Close Assignment</button>
		</div>	
		
	</div>
	
	
	
	<script>
		$(document).ready(function() 
			{		
				$('#return').on("click", function()
					{			
						$('form[name="form_return"]').submit();
					});
					
				$('#confirm').on("click", function(e)
					{			
						$('form[name="form_submit"]').submit();
					});	
			});			
	</script>
