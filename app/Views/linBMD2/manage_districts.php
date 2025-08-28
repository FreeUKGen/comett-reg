<?php $session = session(); ?>
	
	<div class="row text-center table-responsive w-auto" style="max-height: 450px;">
		<table class="table table-hover table-striped table-borderless" style="border-collapse: separate; border-spacing: 0;">
			<thead class="sticky-top bg-white">
				<tr class="text-primary">
					<th>District</th>
					<th>Active?</th>
					<form action="<?=(base_url('district/search')) ?>" method="post">
						<th class="no-sort">
							<input class="box" type="text" class="form-control" id="search" name="search" placeholder="Search" value="<?=($session->search) ?>">
						</th>
						<th  class="no-sort">
							<button type="submit" class="btn btn-primary mr-0">
								<span>Search</span>	
							</button>
						</th>
					</form>
				</tr>
			</thead>

			<tbody>
				<?php foreach ( $session->districts as $district ) 
					{ 
						?>
						<tr>
							<td><?= esc($district['District_name'])?></td>
							<td><?= esc($district['active'])?></td>
							<td>
								<label for="next_action" class="sr-only">Next action</label>
									<select class="box" name="next_action" id="next_action">
										<?php foreach ($session->districts_cycle as $key => $districts_cycle): ?>
												<option value="<?= esc($districts_cycle['BMD_cycle_code'])?>">
													<?= esc($districts_cycle['BMD_cycle_name'])?>
												</option>
										<?php endforeach; ?>
									</select>
							</td>
							<td>
								<button  
									data-id="<?= esc($district['District_name']); ?>" 
									class="go_district_button btn btn-success">Go
								</button>
							</td>
						</tr>
					<?php
					} ?>
			</tbody>
		</table>
	</div>
	
	<div>
		<form action="<?=(base_url('district/next_action')); ?>" method="POST" name="form_next_action" >
			<input name="District_name" id="District_name" type="hidden" />
			<input name="BMD_next_action" id="BMD_next_action" type="hidden" />
		</form>
	</div>
	
	<div class="row mt-4 d-flex justify-content-between">
		<a id="return" class="btn btn-primary mr-0" href="<?=(base_url('database/database_step1/0')); ?>">
			<?php echo $session->current_project[0]['back_button_text']?>
		</a>
		
		<a 
			id="add_district" 
			class="dis_vol btn btn-primary mr-0" 
			href="<?php echo(base_url('district/dis_vol_problems')) ?>">
			<span>District / Volume inconsistency</span>
			<span class="spinner-border"  role="status">
			<span class="sr-only">Loading...</span>
		</a>
		
		<a class="btn btn-primary mr-0" href="https://www.ukbmd.org.uk/reg/districts/index.html" target="_blank">UKBMD Districts</a>
		
		<a id="add_district" class="btn btn-primary mr-0" href="<?php echo(base_url('district/add_district_step1/0')) ?>">
			<span>Add New District</span>
		</a>
	</div>
	
<script>	
// handle district actions
	$('.go_district_button').on("click", function()
			{
				// define the variables
				var id=$(this).data('id');
				var BMD_next_action=$(this).parents('tr').find('select[name="next_action"]').val();
				// load variables to form
				$('#District_name').val(id);
				$('#BMD_next_action').val(BMD_next_action);
				// and submit the form
				$('form[name="form_next_action"]').submit();
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
