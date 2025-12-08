	<?php $session = session();	?>
	
	<div class="row mt-4 d-flex justify-content-between bg-danger" style="font-size:2vw;">
		<span id="return" class="btn btn-primary mr-0 fa-solid fa-backward"></span>
		<span class="font-weight-bold"><?php echo 'With power comes great responsibility!'?></span>
		<span></span>
	</div>
	<div class="row mt-4 d-flex justify-content-between" style="font-size:1vw;">
		<span></span>
		<span><?php echo 'Please confirm delete all FreeComETT transcription data for, '.$session->identity_user.', by entering your password.'?></span>
		<span></span>
	</div>

<br><br>

<div class="form-group row">
	<input type="password" class="form-control col-2 text-left" id="identity_password" name="identity_password">
	<button  
		class="go_button btn btn-success">
		Go
	</button>
</div>

<div>
	<form action="<?=(base_url('database/delete_user_data_step3')); ?>" method="POST" name="form_next_action" >
		<input name="identity_pw" id="identity_pw" type="hidden" />
	</form>
</div>

<div>
	<form action="<?=(base_url('database/coord_step1/0')); ?>" method="POST" name="form_syndicate" ></form>	
	<form action="<?=(base_url('database/database_step1/0')); ?>" method="POST" name="form_dbadmin" ></form>
</div>

<script>
	
$(document).ready(function()
	{	
		$('.go_button').on("click", function()
			{			
				// load variables to form
				$('#identity_pw').val(document.getElementById("identity_password").value);
				// and submit the form
				$('form[name="form_next_action"]').submit();
			});
			
		$('#return').on("click", function()
			{			
				// get the caller
				var caller = <?php echo json_encode($session->caller); ?>;
				var syndicate = caller.search("syndicate");
				// route to correct return url
				if ( syndicate != -1 )
					{
						$('form[name="form_syndicate"]').submit();
					}
				else
					{
						$('form[name="form_dbadmin"]').submit();
					}
			});
	});

</script>
