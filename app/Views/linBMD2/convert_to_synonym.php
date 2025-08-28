<?php $session = session(); ?>
	
	<form action="<?php echo(base_url('district/convert_to_synonym_step2')) ?>" method="post">
	  
		<div class="form-group row">
				<label class="col-1" for="district">District => </label>
				<input type="text" class="form-control col-2" id="district" name="district" value="<?php echo($session->district) ?>">
				<small id="userHelp" class="form-text text-muted col-4">Enter the District that you want to base this synonym on.</small>
		</div>
		
		<div class="row mt-2 d-flex justify-content-between">
			
				<?php

				if ( $session->incon > 0 )
					{ ?> 
							<a 
								class="dis_vol btn btn-primary mr-0"
								href="<?php echo(base_url('district/dis_vol_problems')); ?>">
								<span><?php echo $session->current_project[0]['back_button_text']?></span>
								<span class="spinner-border"  role="status">
								<span class="sr-only">Loading...</span>
							</a>
					<?php
					}
				else
					{ ?>
						<a id="return" class="btn btn-primary mr-0" href="<?php echo(base_url('district/manage_volumes/0')); ?>">
						<?php echo $session->current_project[0]['back_button_text']?>
						</a>
					<?php
					} ?>

				<button type="submit" class="btn btn-primary mr-0">
					<span>Convert to Synonym</span>	
				</button>
			
		</div>
		
	</form>

<script>
	
	document.addEventListener("DOMContentLoaded", () => 
		{
			// handle keypress requests
			$(document).keypress(function(e)
				{			
					$( "#district" ).autocomplete(
						{
							minLength: 2,
							delay: 500,
							source: "<?php echo(base_url('transcribe/search_districts')) ?>",
							
						})
				});
		});
		
</script>

<script>
	
		$( document ).ready(function() 
		{	
			let $show_spinner = $('.dis_vol');
			$show_spinner.on("click",function()
				{
					let $spinner = $('.spinner-border');
					$spinner.addClass("active");
				});
		});
		
</script>
