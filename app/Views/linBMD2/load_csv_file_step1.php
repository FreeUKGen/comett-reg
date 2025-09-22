	<?php $session = session(); ?>
	
	<div class="row mt-4 d-flex justify-content-between font-weight-bold" style="font-size:2vw;">
		<button id="return" class="btn btn-primary mr-0 fa-solid fa-backward" title="Previous Page">Back</button>
		
		<span class="font-weight-bold"><?='Load CSV File => Choose the CSV file you wish to load.'?></span>
		
		<span class="font-weight-bold"><?=' '?></span>
	</div>
	
	<!-- show physical files -->
	<div class="row text-center table-responsive w-auto" style="max-height: 500px;">
		<table class="table table-hover table-borderless" style="border-collapse: separate; border-spacing: 0;">
			<thead class="sticky-top bg-white">
				<tr class="text-primary">
					<th><input class="" id="search" type="text" placeholder="Search..." ></th>
					<th></th>
					<th></th>
					<th></th>	
				</th>
				<tr class="text-primary">
					<th>CSV File Name</th>
					<th>Uploaded Date</th>
					<th>Processed Date</th>
					<th>Select File</th>
				</tr>
			</thead>

			<tbody  id="user_table">
				<?php foreach ( $session->physical_files as $physical_file )
					{ 
						$select = 'Select';?>	
						<tr>
							<td class="align-middle"><?= esc($physical_file['file_name'])?></td>
							<td class="align-middle"><?= esc($physical_file['base_date'])?></td>
							<td class="align-middle"><?= esc($physical_file['proc_date'])?></td>
							<td 
								style="cursor:pointer;"
								id="<?=esc($physical_file['file_name'])?>"
								data-csvid="<?=esc($physical_file['_id']->__toString())?>"
								class="go_button btn btn-primary mr-0 align-middle fa-solid fa-check"
								title="ClickMe to load this CSV file">
							</td>
						</tr>
					<?php
					} ?>
			</tbody>
		</table>
	</div>
	
	<div>
		<form action="<?=(base_url('allocation/manage_allocations/0')); ?>" method="POST" name="form_return" ></form>
		<form action="<?=(base_url('allocation/load_csv_file_step2')); ?>" method="POST" name="form_load_step2" >
			<input name="csv_file_name" id="csv_file_name" type="hidden" />
			<input name="csv_file_id" id="csv_file_id" type="hidden" />
		</form>	
	</div>
	
	<script>
		$(document).ready(function()					
			{				
				$('#return').on("click", function()
					{			
						$('form[name="form_return"]').submit();
					});
					
				$('.go_button').on("click", function()
					{
						// define the variables
						var csvFile = $(this)[0].id;
						var csvId = $(this)[0].dataset.csvid;
						// load variables to form
						$('#csv_file_name').val(csvFile);
						$('#csv_file_id').val(csvId);
						// show spinner
						$.LoadingOverlay("show", 
							{
								background  : "rgba(255, 255, 255, 0.4)"
							}); 
						// and submit the form
						$('form[name="form_load_step2"]').submit();
					});
			});			
						
			
	</script>
