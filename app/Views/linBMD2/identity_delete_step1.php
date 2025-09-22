	<?php $session = session();	?>
	
	<div class="row mt-4 d-flex justify-content-between" style="font-size:2vw;">
		<span id="return" class="btn btn-primary mr-0 fa-solid fa-backward"></span>
		<span class="font-weight-bold"><?php echo 'Delete FreeComETT transcriber data for a transcriber in project => '.$session->current_project[0]['project_name'].'.'?></span>
		<span class="font-weight-bold"></span>
	</div>

	<div class="row table-responsive w-auto text-center" style="height:450px">
		<table class="table table-sm table-hover table-striped table-bordered" style="border-collapse: separate; border-spacing: 0;">
			<thead class="sticky-top bg-white">
				<tr>
					<th>Identity (click to delete data) <input class="box no-sort" id="search" type="text" placeholder="Search..." ></th>
				</tr>
			</thead>

			<tbody id="user_table">
				<?php 
				if ( $session->delete_ids ) 
					{		 
						// read each line in turn
						foreach ( $session->delete_ids as $key => $identity ) 
							{
								?>
									<tr>
										<td class="align-middle">
											<button  
												data-index="<?= esc($identity['BMD_identity_index'])?>"
												data-user="<?= esc($identity['BMD_user'])?>" 
												class="btn btn-success">
												<span><?=$identity['BMD_user']?></span>
											</button>
										</td>
									</tr>
							<?php
							} ?>
					<?php
					} ?>
			</tbody>
		</table>
	</div>


<div>
	<form action="<?=(base_url('database/delete_user_data_step2/0')); ?>" method="POST" name="form_next_action" >
		<input name="identity_index" id="identity_index" type="hidden" />
		<input name="identity_user" id="identity_user" type="hidden" />
	</form>
</div>

<div>
	<form action="<?=(base_url('database/coord_step1/0')); ?>" method="POST" name="form_syndicate" ></form>	
	<form action="<?=(base_url('database/database_step1/0')); ?>" method="POST" name="form_dbadmin" ></form>
</div>
	
	
<script>
	
$(document).ready(function()
	{	
		$('.btn-success').on("click", function()
			{			
				// define the variables
				var index=$(this).data('index');
				var user=$(this).data('user');

				// load variables to form
				$('#identity_index').val(index);
				$('#identity_user').val(user);
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
