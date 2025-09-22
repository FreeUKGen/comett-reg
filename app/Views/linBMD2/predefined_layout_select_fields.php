	<?php $session = session();	?>
	
	<div class="row mt-4 d-flex justify-content-between" style="font-size:2vw;">
		<button id="return" class="btn btn-primary mr-0 fa-solid fa-backward">Back</button>
		<span class="font-weight-bold"><?='Predefined Layout - select fields'?></span>
		
		<span><?=$session->add_event_type?></span>	
		<span><?=$session->add_predefined_layout?></span>	

		<button id="confirm" class="btn btn-primary mr-0">Confirm</button>
	</div>
	
	<div class="row table-responsive w-auto text-center">
		<table class="table table-sm table-hover table-striped table-bordered" id="sortTable" style="border-collapse: separate; border-spacing: 0;">
			<thead class="sticky-top bg-white">
				<tr>
					<th style='text-align:center; vertical-align:middle'>Field Name - ordered fields will be added to the layout.</th>
					<th style='text-align:center; vertical-align:middle'>Field Order - enter the field order.</th>
				</tr>	
			</thead>
		
			<tbody>						
				<?php
				// loop through data dictionary def fields - this to get default field attribute
				foreach ( $session->standard_def as $field ) 
					{ ?>			
						<tr class="align-items-center" id="<?=$field['field_index'].'='.$field['table_fieldname']?>">
							
							<td>
								<input
									id="fieldName"
									class="form-control text-center d-flex flex-column align-items-center"
									type="text" 
									value="<?php echo esc($field['table_fieldname']);?>"
									readonly>
							</td>
							<td>
								<input class="form-control text-center d-flex flex-column align-items-center fieldOrder"
									id="<?=$field['table_fieldname']?>"
									type="number" 
									class="form-control text-center>" 
									value="<?=$field['field_order']?>">
							</td>
							
						</tr>
					<?php
					} ?>
			</tbody>
		</table>
	</div>
	
	<div>
		<form action="<?php echo(base_url('predefined_layouts/add_predefined_layout_step2')) ?>" method="post" name="add_predefined_layout">
			<input name="data_object" id="add_data_object" type="hidden">
		</form>
	</div>
	
	<div>
		<form action="<?php echo(base_url('predefined_layouts/change_predefined_layout_step2')) ?>" method="post" name="change_predefined_layout">
			<input name="data_object" id="chg_data_object" type="hidden">
		</form>
	</div>
	
	<div>
		<form action="<?php echo(base_url('predefined_layouts/manage_predefined_layouts/0')); ?>" method="POST" name="form_return" >
		</form>	
	</div>''
		
<script>
		
	$(document).ready(function() 
		{				
			$('#return').on("click", function()
				{			
					$('form[name="form_return"]').submit();
				});
				
			$('#confirm').on("click", function()
				{
					// initialse data object
					var dataObject = {};
					// get table
					var table = document.getElementById("sortTable");
					// get rows
					rows = table.getElementsByTagName("tr");
					for (i = 1; i < (rows.length - 1); i++) 
						{
							// get values
							var order = rows[i].getElementsByTagName("input")[1].value;
							var fieldname = rows[i].getElementsByTagName("input")[0].value;
						
							// select only rows with order != 0 - means that the user has selected the field
							if ( order != 0 )
								{
									// add to data object
									dataObject[fieldname] = order;
								}
						}
											
					// submit the form
					var action = "<?=$session->BMD_cycle_code?>";
					if ( action == 'PRECH' )
						{
							// change
							$('#chg_data_object').val(JSON.stringify(dataObject));
							$('form[name="change_predefined_layout"]').submit();
						}
					else
						{
							// add
							$('#add_data_object').val(JSON.stringify(dataObject));
							$('form[name="add_predefined_layout"]').submit();
						}
				});
									
			
			$( ".fieldOrder" ).on( "change", function() 
				{
					// define variables
					var table, rows, switching, i, xValue, yValue;
					switching = true;
					
					// get table
					table = document.getElementById("sortTable");
					
					// while true
					while (switching) 
						{
							switching = false;
							// get rows
							rows = table.getElementsByTagName("tr");
							for (i = 1; i < (rows.length - 1); i++) 
								{
									// get row values
									xValue = rows[i].getElementsByTagName("input")[1].value;
									yValue = rows[i + 1].getElementsByTagName("input")[1].value;	

									// set sort value if order = 0. This sorts 0 order lines last
									if ( xValue == 0 )
										{
											xValue = 999999;
										}
									if ( yValue == 0 )
										{
											yValue = 999999;
										}
									
									// pad with 0s to six digits
									let xResult = xValue.toString().padStart(6, '0')
									let yResult = yValue.toString().padStart(6, '0')
									
									// sort	
									if (xResult > yResult) 
										{
											rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
											switching = true;
										}
								}
						}
				});
		});
				
</script>
	
