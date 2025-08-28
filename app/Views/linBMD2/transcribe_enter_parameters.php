	<?php $session = session();	?>
	
	<div class="row mt-4 d-flex justify-content-between bg-success" style="font-size:2vw;">
		<button id="return" class="btn btn-primary mr-0 fa-solid fa-backward"></button>
		<span class="font-weight-bold"><?='Data Entry Fields - Manage'?></span>
		<span>
			<?php
			switch ( $session->current_project[0]['project_name'] )
				{
					case 'FreeBMD': 
						break;
					case 'FreeREG':
						if ( $session->image_source[0]['source_images'] == 'yes' )
							{ ?>
								<button hidden id="previous_image" class="btn btn-primary mr-0 fa-solid fa-arrow-left"></button>
								<span style="font-size:1.5vw !important;">
									<span>Image</span>
									<span id="xofn_image"><?=$session->current_image_number?></span>
									<span>of</span>
									<span><?=$session->image_count?></span>
								</span>
								<button hidden id="next_image" class="btn btn-primary mr-0 fa-solid fa-arrow-right"></button>				
							<?php
							}
						break;
					case 'FreeCEN':
						break;
				} ?>
		</span>
		<span><?=ucfirst($session->current_transcription[0]['current_data_entry_format'])?></span>	
			
		<select
			id="layoutIndex"
			style="font-size:1vw !important;">
			<option
				value="-1">
				None
			</option>
			
			<optgroup label="Your Layouts">
				<?php
				foreach ( $session->layout_dropdown as $key => $layout )
					{ 
						?>
						<option
							value="<?=$key?>" 
							<?php if ( $session->current_layout == $key ) { echo ' selected'; } ?>>
							<?=$layout?>
						</option>
					<?php
					} ?>
			</optgroup>
			
			<optgroup label="Manage Your Layouts">
				<option
					value="0">
					Create Layout
				</option>
				<option
					value="9999">
					Delete Layout
				</option>
			</optgroup>
			
			<optgroup label="Pre-defined Layouts">
				<?php
				foreach ( $session->predefined_layout_dropdown as $key => $layout )
					{ 
						?>
						<option
							value="<?=$key?>" 
							<?php if ( $session->current_layout == $key ) { echo ' selected'; } ?>>
							<?=$layout?>
						</option>
					<?php
					} ?>
			</optgroup>
		</select>
		
		<input type="text" id="layoutName" placeholder="Enter layout name..." value='' hidden>
		<button id="confirm" class="btn btn-primary mr-0">Confirm</button>
	</div>
	
	<!-- Inject initial values for Panzoom here (x, y, zoom...) src=\"data:$session->mime_type;base64,$session->fileEncode\" -->
	<!-- panzoom-wrapper class is defined in the header and includes image height and rotation. -->
	<?php 	if ( $session->image_source[0]['source_images'] == 'yes' )
				{ ?>
					<div class="panzoom-wrapper row">
						<div class="panzoom" id='panzoom'>
							<img
								id="params_image"
								src=<?='data:'.$session->mime_type.';base64,'.$session->params_fileEncode?>
								alt=<?=$session->image?>  
								data-scroll=<?=$session->scroll_step?>
							> 
						</div>
								<span>Next image</span>
					</div>
	<?php 		} ?>
	
	<div class="row table-responsive w-auto text-center">
		<table class="table table-sm table-hover table-striped table-bordered" style="border-collapse: separate; border-spacing: 0;">
			<thead class="sticky-top bg-white">
				
				<tr>
					<?php
					// heading line 1
					foreach ( $session->field_parameters as $key => $parameters )
						{ ?>
							<th style='text-align:center; vertical-align:middle'><?=$parameters['head1']?></th>
						<?php
						} ?>
				</tr>
				
				<tr>
					<?php
					// heading line 2
					foreach ( $session->field_parameters as $key => $parameters )
						{ 
							if ( $parameters['checkbox'] == 'Y' )
								{ ?>
									<th style='text-align:center; vertical-align:middle'><input type="checkbox" id="<?=$key.'_all'?>" class="box_us form-control text-center"></th>
								<?php
								} 
							else
								{ ?>
									<th style='text-align:center; vertical-align:middle'><?=$parameters['head2']?></th>
								<?php
								} 
						} ?>
				</tr>
				
				<tr>
					<?php
					// heading line 3
					foreach ( $session->field_parameters as $key => $parameters )
						{ 
							if ( $parameters['setto'] == 'Y' )
								{ 
									switch ( $parameters['type'] )
										{
											case 'input': ?>
												<th>
													<div class="d-flex align-items-center justify-content-center">
														<input type="text" id="<?=$key.'_setto'?>" class="box_us text-center" value=''>
														<button class="go_button_setto" onclick="setto(event)" data-arg1=<?=$key?> data-arg2=input  data-arg3=text><span class="fa-solid fa-check"></span></button>
													</div>
												</th>
												<?php
												break;
											
											case 'colour': ?>
												<th>
													<div class="d-flex align-items-center justify-content-center">
														<input type="color" id="<?=$key.'_setto'?>" class="box_us text-center" value="#d4edda">
														<button class="go_button_setto" onclick="setto(event)" data-arg1=<?=$key?> data-arg2=input data-arg3=color><span class="fa-solid fa-check"></span></button>
													</div>
												</th>
												<?php
												break;
												
											case 'select': ?>
												<th>
													<div class="d-flex align-items-center justify-content-center">
														<select
															id="<?=$key.'_setto'?>"
															class="box_us text-center">
															<?php
															$values = explode(',', $parameters['values']);
															foreach ( $values as $value )
																{ ?>
																	<option
																		class="<?=$key?>"
																		value="<?=$value?>" 
																		<?php if ( $parameters['default'] == $value ) { echo ' selected'; } ?>>
																		<?=$value?>
																	</option>
																<?php
																} ?>
														</select>
														<button class="go_button_setto" onclick="setto(event)" data-arg1=<?=$key?> data-arg2=select data-arg3='select'><span class="fa-solid fa-check"></span></button>
													</div>												
												</th>
												<?php
												break;
										}
								} 
							else
								{ ?>
									<th>
										<div class="d-flex align-items-center justify-content-center">
											<?=$parameters['head3']?>
										</div>
									</th>
								<?php
								} 
						} ?>
				</tr>
					
			</thead>
		
			<tbody id="drag_rows">						
				<?php
				// loop through data dictionary def fields - this to get default field attribute
				foreach ( $session->current_transcription_def_fields as $field_line ) 
					{ 
						foreach ( $field_line as $field )
							{ 
								// select only records for the current data entry format
								if ( $field['data_entry_format'] == $session->current_transcription[0]['current_data_entry_format'] )
									{
										// get the default value of this field
										$dd_key = array_search($field['table_fieldname'], array_column($session->standard_def, 'table_fieldname'));
										$dd_value = $session->standard_def[$dd_key];
										?>		
										<!-- output data -->
								
										<tr class="align-items-center" id="<?=$field['field_index'].'='.$field['table_fieldname']?>">
											<?php
											foreach ( $session->field_parameters as $key => $parameters )
												{
													switch ( $parameters['type'] )
														{
															case 'readonly': ?>
																<td>
																	<span class="<?=$key?>"><?= esc($field[$key]);?></span>
																</td>
																<?php
																break;
															case 'input': ?>
																<td 
																	class="<?=$key?>">
																	<div class="d-flex flex-column align-items-center justify-content-center <?=$key?>">
																		<input 
																			type="text" 
																			class="form-control text-center <?=$key?>" 
																			value="<?php echo esc($field[$key]);?>">
																		<span class="<?=$key?>"><?='('.$dd_value[$key].')'?></span>
																	</div>
																</td>
																<?php
																break;
															case 'select': ?>
																<td 
																	class="<?=$key?>">
																	<div class="d-flex flex-column align-items-center justify-content-center <?=$key?>">
																		<select 
																			class="box_vs form-control text-center <?=$key?>">
																			<?php
																			$values = explode(',', $parameters['values']);
																			foreach ( $values as $value )
																				{ ?>
																					<option
																						class="<?=$key?>"
																						value="<?=$value?>" 
																						<?php if ( $field[$key] == $value ) { echo ' selected'; } ?>>
																						<?=$value?>
																					</option>
																				<?php
																				} ?>
																		</select>
																		<span class="<?=$key?>"><?='('.$dd_value[$key].')'?></span>
																	</div>
																</td>
																<?php
																break;
															case 'checkbox1': ?>
																<td class="<?=$key?>">
																	<div class="d-flex flex-column align-items-center justify-content-center <?=$key?>">
																		<input type="checkbox" class="form-control text-center <?=$key?>" 
																		<?php 
																		if ( $field[$key] == 'Y' ) 
																			{ 
																				echo 'checked'; 
																			}?>>
																		<span class="<?=$key?>"><?='('.$dd_value[$key].')'?></span>
																	</div>
																</td>
																<?php
																break;
															case 'checkbox2': ?>
																<td class="<?=$key?>">
																	<div class="d-flex flex-column align-items-center justify-content-center <?=$key?>">
																		<input type="checkbox" class="form-control text-center <?=$key?>"
																		<?php 
																		if ( $field[$key] == 'Y' AND $field[$parameters['attr']] == 'Y' ) 
																			{ 
																				echo 'checked'; 
																			}?>>
																		<span class="<?=$key?>">(Y)</span>
																	</div>
																</td>
																<?php
																break;
															case 'button': ?>
																<td class="<?=$key?>">
																	<div class="d-flex flex-column align-items-center justify-content-center">
																		<button class="<?=$parameters['class']?>" onclick="<?=$parameters['onclick']?>" data-arg1=<?=$field['field_index']?>><span class="<?=$parameters['icon']?>"></span></button>
																	</div>
																</td>
																<?php
																break;
															case 'colour': ?>
																<td class="<?=$key?>">
																	<div class="d-flex flex-column align-items-center justify-content-center <?=$key?>">
																		<input type="color" class="<?=$key?>" value=<?=$field[$key]?>>
																		<span class="<?=$key?>"><?='('.$dd_value[$key].')'?></span>
																	</div>
																</td>
																<?php
																break;
														}
												} ?>
										</tr>
									<?php
									}		
							} 
					} ?>
			</tbody>
		</table>
	</div>
	
	<div class="row mt-4 d-flex justify-content-between">	
		
		<?php
		if ( $session->current_project[0]['project_index'] == 1 )
			{ ?>
		
				<a id="return" class="btn btn-primary mr-0 flex-column align-items-center" href="<?php echo(base_url('/transcribe/inherit_parameters')); ?>">
					<span>Inherit parameters from last Transcription in same Allocation</span>
				</a>
		
				<?php 
				if ( $session->current_identity[0]['role_index'] <= 2 )
					{
						?>
						<a id="update_def_fields" class="btn btn-primary mr-0" href="<?php echo(base_url('database/update_def_fields')); ?>">
							<span>Co-ordinator ONLY => Update Standard def field values</span>
						</a>
						<?php
					}
			} ?>
	</div>
	
	<div>
		<form action="<?php echo(base_url('transcribe/enter_parameters_step/')) ?>" method="post" name="post_data_object">
			<input name="data_object" id="data_object" type="hidden">
			<input name="layoutIndexparm" id="layoutIndexparm" type="hidden">
			<input name="layoutNameparm" id="layoutNameparm" type="hidden">
		</form>
	</div>
	
	<div>
		<form action="<?php echo(base_url($session->controller.'/transcribe_'.$session->controller.'_step1/0')); ?>" method="POST" name="form_return" >
		</form>	
	</div>
	
	<div>
		<form action="<?php echo(base_url('transcribe/set_param_image/')); ?>" method="POST" name="form_param_image">
			<input name="direction" id="direction" type="hidden">
		</form>	
	</div>
		
<script>
		
	$(document).ready(function() 
		{
			// initialise image : previous and next buttons for FreeREG only
			var project_name = "<?=$session->current_project[0]['project_name']?>";
			var source_images = "<?=$session->image_source[0]['source_images']?>";
			if ( project_name == 'FreeREG' && source_images == 'yes' )
			{
				// set buttons
				var current_image_number = document.getElementById('xofn_image').innerHTML;
				var image_count = <?=$session->image_count?>;
				if ( image_count > 0 )
					{
						set_image_buttons(current_image_number, image_count);
					}
			}

			// Initialise the table
			$("#drag_rows").tableDnD(
				{
					onDragClass: "myDragClass",
				});
				
			$('#confirm').on("click", function()
			{
				// confirm button is used to save the changed field attributes in two cases
				// 1) the user didn't select a layout
				// 2) the user did select a layout
				// did user select layout? ie is layoutName visible
				// if layout name is visible don't check for layout
				// get layout elements
				var layoutIndex = document.getElementById("layoutIndex");
				var layoutName = document.getElementById("layoutName");
				// is name visible
				if ( window.getComputedStyle(layoutName).display != 'none' ) 
					{
						// visible
						switch ( layoutIndex.value )
							{
								case '0':
									// when adding a name must be entered
									if ( layoutName.value == null || layoutName.value == "" )
										{
											alert("Please give your new layout a name.");
										}
									else
										{
											post_values( layoutIndex.value, layoutName.value );
										}
									break;
								case '9999':
									// when deleting a name must be entered
									if ( layoutName.value == null || layoutName.value == "" )
										{
											alert("Please enter layout name to delete.");
										}
									else
										{
											// test for valid layout name
											var error = 0;
											let layouts = Object.values(<?php echo json_encode($session->layout_dropdown);?>);
											layouts.forEach((layout) => 
												{
													if ( layout == layoutName.value )
														{
															error = 1;
														}
												});
											if ( error == 0 )
												{
													alert("Please enter a valid layout name to delete. You cannot delete pre-defined layouts.");
												}
											else
												{
													// test that layout selected is not current
													var error = 1;
													var currentLayoutindex = <?php echo $session->current_layout;?>;
													let layouts = Object.entries(<?php echo json_encode($session->layout_dropdown);?>);
													for ( [key, value] of layouts )
														{
															if ( key == currentLayoutindex && value == layoutName.value)
																{		
																	error = 0;
																}
														}
													if ( error == 0 )
														{
															alert("You cannot delete your current layout.");
														} 
													else
														{
															post_values( layoutIndex.value, layoutName.value );
														}
												}
										}
									break;
							}
					}
				else
					{
						// not visible
						// is this a pre-defined layout - in which case issue warning
						var error = 0;
						const selectedIndex = document.getElementById('layoutIndex').selectedIndex;
						const selectedOption = document.getElementById('layoutIndex').options[selectedIndex];
						let layouts = Object.values(<?php echo json_encode($session->predefined_layout_dropdown);?>);
						layouts.forEach((layout) => 
							{
								if ( layout == selectedOption.text )
									{
										error = 1;
									}
							});
						if ( error == 1 )
							{
								// user is attempting to change a pre-defined layout - issue warning
								alert("Please be aware that you are attempting to change a pre-defined layout. Changed field attributes, such as font size, will be applied to this transcription but any changes to the layout will not be applied.");
							}
						post_values( layoutIndex.value, layoutName.value );
					}				
			});
		
			$('#return').on("click", function()
				{			
					$('form[name="form_return"]').submit();
				});
				
			$('#previous_image').on("click", function()
				{							
					show_param_image(-1);
				});
				
			$('#next_image').on("click", function()
				{			
					show_param_image(1);
				});
				
			$('#layoutIndex').on("click", function()
				{	
					var layoutIndex = document.getElementById("layoutIndex").value;
					if ( layoutIndex == 0 || layoutIndex == 9999 )
						{
							document.getElementById('layoutName').removeAttribute('hidden');
							document.getElementById('layoutName').value = '';
						}
					else
						{
							document.getElementById('layoutName').setAttribute('hidden', "");
							document.getElementById('layoutName').value = '';
						}
				});
		});
		
	function show_param_image(direction) 
		{				
			// increment array key
			var current_image_array_key = (document.getElementById('xofn_image').innerHTML - 1) + direction;
			var image_count = <?=$session->image_count?>;
			if ( current_image_array_key < 0 ) { current_image_array_key = 0; }
			if ( current_image_array_key > image_count - 1 ) { current_image_array_key = image_count - 1; }
				
			// convert image to base64 using php
			$.post("<?=base_url('transcribe/set_param_image/')?>", { url: <?=json_encode($session->image_records)?>[current_image_array_key]['image_url'] }, function(params_fileEncode) 
				{
					// set image
					document.getElementById('params_image').src = 'data:'+"<?=$session->mime_type?>"+';base64,'+params_fileEncode;
					// set xofn
					var current_image_number = current_image_array_key + 1;
					document.getElementById('xofn_image').innerHTML = current_image_number;
					// set buttons
					document.getElementById('previous_image').setAttribute("hidden", "hidden"); 
					document.getElementById('next_image').setAttribute("hidden", "hidden"); 
					set_image_buttons(current_image_number, image_count)
				});
		}
		
	function set_image_buttons(current_image_number, image_count)
		{	
			if ( current_image_number != 1 )
				{
					document.getElementById('previous_image').removeAttribute('hidden'); 
				}
			if ( current_image_number != image_count )
				{
					document.getElementById('next_image').removeAttribute('hidden'); 
				}
		}
		
	function post_values(layoutIndex, layoutName) 
		{
			// initialse data object
			var dataObject = {};
			// get list of fields per row
			fieldParameters = <?php echo json_encode($session->field_parameters); ?>;
			
			// read all table rows
			$("#drag_rows tr").each(function()
				{
					// get current row
					var row = $(this);

					// rowId = this row DB index=this row table fieldname
					var rowId = row.attr('id').split('=')[0];
					var rowTableFieldname = row.attr('id').split('=')[1];				
					// rowIndex = this row number = row order
					var rowIndex = row.index(); 
					
					// create object to hold cell values to be passed to server
					var cells = {};
					// add DB index to cells
					cells['rowId'] = rowId;
					cells['rowTableFieldname'] = rowTableFieldname;
													
					// read field parameters to find elements to save
					for (let [key, parameter] of Object.entries(fieldParameters)) 
						{
							// if value can be saved, get its value
							if ( parameter["save"] == 'Y' )
								{
									// depending on parameter type, get the value of it
									switch(parameter["type"]) 
										{
											case 'readonly':
												var value = $(row.find("span."+key))[0].textContent;
												break;
											
											case 'input':
												var value = $(row.find("input."+key)).val();
												break;

											case 'select':
												var value = $(row.find("select."+key)).val();
												break;
										
											case 'checkbox1':
											if ( $(row.find("input."+key+":checkbox")).prop("checked") )
												{
													var value = 'Y';
												}
											else
												{
													var value = 'N';
												}
												break;
											
											case 'checkbox2':
												if ( $(row.find("input."+key+":checkbox")).prop("checked") )
												{
													var value = 'Y';
												}
											else
												{
													var value = 'N';
												}
												break;
											
											case 'colour':
												var value = $(row.find("input."+key)).val();
												break;
										}
					
									// add value to object cells
									cells[key] = value;
								}	
						}
						
						// add cells to dataObject
						dataObject[rowIndex] = cells;
				});
										
			// load variables to form
			$('#data_object').val(JSON.stringify(dataObject));
			$('#layoutIndexparm').val(layoutIndex);
			$('#layoutNameparm').val(layoutName);
						
			// and submit the form
			$('form[name="post_data_object"]').submit();
		}

</script>

<script>
	
	// define variables
	var clickedRowindex;
	
	$(function() 
		{
			//If check_all checked then check all table rows 
			$("#reset_all").on("click", function() { checkChange($("input#reset_all:checkbox"), 'reset'); });
			$("#field_check_all").on("click", function() { checkChange($("input#field_check_all:checkbox"), 'field_check'); });
			$("#field_show_all").on("click", function() { checkChange($("input#field_show_all:checkbox"), 'field_show'); });
			$("#auto_full_stop_all").on("click", function() { checkChange($("input#auto_full_stop_all:checkbox"), 'auto_full_stop'); });
			$("#auto_copy_all").on("click", function() { checkChange($("input#auto_copy_all:checkbox"), 'auto_copy'); });
			
			// if auto focus checked, make sure that only one is checked and ensure that selected and shown are clicked
			$("input.auto_focus:checkbox").on('click', function () 
				{ 
					// get current cell
					var cell = $(this);
					// get row and row index
					var clickedRow = cell.closest("tr");
					clickedRowindex = cell.closest("tr").index();
					
					// always check selected and shown if auto_focus checked
					$(clickedRow.find("input.field_check:checkbox")[0]).prop("checked", true);
					$(clickedRow.find("input.field_show:checkbox")[0]).prop("checked", true);
					
					// read all auto_focus check boxes
					$("input.auto_focus:checkbox").each(function()
						{
							// get current row index
							var currentRow = $(this).closest("tr") 
							var currentRowindex = currentRow.index();
							// if not on clicked row make false, otherwise leave clicked row as is
							if ( clickedRowindex != currentRowindex	)
								{
									// set row as unchecked
									$(this).prop("checked", false);
								}
						});
				});
				
			// if field check unchecked, make sure that field_show and auto_focus are unchecked
			$("input.field_check:checkbox").on('click', function () 
				{ 
					// get current cell
					var cell = $(this);
					// get row
					var clickedRow = cell.closest("tr");					
					if ( cell.prop("checked") == false ) 
						{
							$(clickedRow.find("input.field_show:checkbox")[0]).prop("checked", false);
							$(clickedRow.find("input.auto_focus:checkbox")[0]).prop("checked", false);
						}
				});
				
			// if field show unchecked, make sure that auto_focus is unchecked
			$("input.field_show:checkbox").on('click', function () 
				{ 
					// get current cell
					var cell = $(this);
					// get row
					var clickedRow = cell.closest("tr");					
					if ( cell.prop("checked") == false ) 
						{
							$(clickedRow.find("input.auto_focus:checkbox")[0]).prop("checked", false);
						}
				});
			
			// if reset clicked, apply defaults to fields in the row
			$("input.reset:checkbox").on('click', function () 
				{ 
					// get current cell
					var cell = $(this);
					// get row
					var clickedRow = cell.closest("tr");
					// get list of fields per row
					fieldParameters = <?php echo json_encode($session->field_parameters); ?>;
					
					// reset field parameters
					if ( cell.prop("checked") == true ) 
						{
							// call function to reset parameters
							resetParms(clickedRow, fieldParameters);
						}
						
					// turn off clicked reset
					$(clickedRow.find("input.reset:checkbox")[0]).prop("checked", false);
				});
		});
		
	function checkChange(checkboxAll, myClass) 
		{
			// change checked status on all myClass elements
			if (checkboxAll.prop("checked")) 
				{
					$("input."+myClass+":checkbox").prop("checked", true);
					
					// if reset get all lines and reset values
					if ( myClass == 'reset' )
						{
							$("input."+myClass+":checkbox").each(function() 
								{
									// get current cell
									var cell = $(this);
									// get row
									var clickedRow = cell.closest("tr");
									// get list of fields per row
									fieldParameters = <?php echo json_encode($session->field_parameters); ?>;
									
									// reset field parameters
									if ( cell.prop("checked") == true ) 
										{
											// call function to reset parameters
											resetParms(clickedRow, fieldParameters);
										}
										
									// turn off clicked reset
									$(clickedRow.find("input.reset:checkbox")[0]).prop("checked", false);
								});
						}
				} 
			else 
				{
					$("input."+myClass+":checkbox").prop("checked", false);
				}
				
			// turn off checkbox all
			$(checkboxAll).prop("checked", false);
		}
	
	function setto(event)
		{
			// initialise
			var settoParm = event.target.getAttribute('data-arg1');
			var settoTag = event.target.getAttribute('data-arg2');
			var settoType = event.target.getAttribute('data-arg3');
			var settoId = settoParm+'_setto';
			var settoValue = $(settoTag+"#"+settoId).val();		

			// test input for blank
			if ( settoValue == '' )
				{
					alert("Please enter a value for this Set To parameter = "+settoParm);
				}
			else
				{
					// apply setto value to all tags of the class
					$(settoTag+"."+settoParm).each(function()
						{
							$(this).prop('value', settoValue);
						});
						
					// clear setto
					switch(settoType) 
						{
							case 'text':
								$(settoTag+"#"+settoId).prop('value', '');
								break;
							
							case 'color':
								$(settoTag+"#"+settoId).prop('value', '#d4edda');
								break;
								
							case 'select':
								$(settoTag+"#"+settoId).prop('value', '');
								break;
						}
				}	
		}
		
	function resetField(event) 
		{
			// if reset clicked, apply defaults to fields in the row
			// initialise
			var resetId = event.target.parentElement.getAttribute('data-arg1');
			var clickedRow = $('#'+resetId);

			// get list of fields per row
			fieldParameters = <?php echo json_encode($session->field_parameters); ?>;
					
			// call function to reset parameters
			resetParms(clickedRow, fieldParameters);		
		}
		
	function resetParms(clickedRow, fieldParameters)
		{
			// get field parameters
			for (let [key, parameter] of Object.entries(fieldParameters)) 
				{
					if ( parameter["reset"] == 'Y' )
						{
							// get span value for this td and clean off the brackets at start and end
							var defaultValue = $(clickedRow.find("span."+key))[0].textContent;
							var defaultValue = defaultValue.substr(1).slice(0, -1);
							
							// depending on parameter type, modify the value of it
							switch(parameter["type"]) 
								{
									case 'input':
										$(clickedRow.find("input."+key)).prop('value', defaultValue);
										break;

									case 'select':
										$(clickedRow.find("option."+key)).each(function()
											{
												$(this).prop("selected", false);
												if ( $(this).val() == defaultValue )
													{
														$(this).prop("selected", true);
													}
											});
										break;
										
									case 'checkbox1':
										if ( defaultValue == 'N' )
											{
												$(clickedRow.find("input."+key+":checkbox")[0]).prop("checked", false);
											}
										else
											{
												$(clickedRow.find("input."+key+":checkbox")[0]).prop("checked", true);
											}
										break;
											
									case 'checkbox2':
										if ( defaultValue == 'N' )
											{
												$(clickedRow.find("input."+key+":checkbox")[0]).prop("checked", false);
											}
										else
											{
												$(clickedRow.find("input."+key+":checkbox")[0]).prop("checked", true);
											}
										break;
											
									case 'colour':
										$(clickedRow.find("input."+key)).prop('value', defaultValue);
										break;
								}
						}
				}
		}
		
</script>
	
