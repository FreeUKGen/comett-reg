	<?php $session = session(); ?>
			
	<div class="reference_extension">
		<div class="form-group row">
			<label for="scan_path" class="col-4 pl-0">Current Reference Extension</label>
			<span id="scan_path" class="col-6 pl-0"><?php echo $session->scan_path ?></span>
		</div>
		<form action="<?php echo(base_url('allocation/create_allocation_step2')) ?>" method="post">
			<div class="form-group row">
				<label for="reference_extension" class="box col-4 pl-0">Select a Reference Extension</label>
				<select name="reference_extension" id="reference_extension" class="col-4">
					<?php if ( count($session->reference_extension_array) != 0 ) 
						{
							foreach ($session->reference_extension_array as $key => $value): ?>
							<option value="<?php echo esc($key)?>"><?php echo esc($value)?></option>
							<?php endforeach;
						}?>		
				</select>
				<small id="userHelp" class="form-text text-muted col-4">Reference extensions tell the system where the scan files for this allocation are stored.</small>
			</div>
			
			<div class="row d-flex justify-content-between mt-4">
				<a id="return" class="btn btn-primary mr-0" href="<?php echo(base_url('allocation/create_allocation_step1/0')); ?>">
					<span>Start Again</span>
				</a>
				
				<button type="submit" class="create_allocation btn btn-primary mr-0 d-flex">
					<span>Continue with Create Allocation</span>
					<span class="spinner-border"  role="status">
						<span class="sr-only">Loading...</span>
					</span>		
				</button>
			</div>
	</div>
	
	</form>

	<script type="text/javascript">
		$( document ).ready(function() 
		{	
			let $create_allocation = $('.create_allocation');
			$create_allocation.on("click",function()
				{
					let $spinner = $('.spinner-border');
					$spinner.addClass("active");
				});
		});
	</script>
