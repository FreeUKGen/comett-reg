	<?php $session = session();	?>
	
	<div class="row mt-4 d-flex justify-content-between align-items-center" style="font-size:2vw;">
		<button id="return" class="btn btn-primary mr-0 fa-solid fa-backward">Back</button>
		<span class="font-weight-bold">Manage Data Dictionary</span>
		<span style="font-size:1vw;">
			<span class="font-weight-bold">For</span>
			<select id="eventType" onchange="setEventtype()">
				<?php foreach ( $session->project_types as $event_type )
					{ ?>
						<option 
							value="<?=$event_type['type_index']?>"
							<?php if ( $event_type['type_index'] == $session->eventtype_index ) { echo ' selected'; } ?>>
							<?=$event_type['type_desc']?>
						</option>
					<?php
					} ?>
			</select>
			<span class="font-weight-bold">Event Type and for</span>
			<select id="category" onchange="setCategory()">
				<?php foreach ( $session->categories as $category )
					{ ?>
						<option 
							value="<?=$category['def_category_index']?>"
							<?php if ( $category['def_category_index'] == $session->category_index ) { echo ' selected'; } ?>>
							<?=$category['def_category_name']?>
						</option>
					<?php
					} ?>
			</select>
			<span class="font-weight-bold">Attributes.</span>
		</span>
		<span style="font-size:1vw;">
			<input class="box" id="searchMe" type="text" placeholder="Search..." >
		</span>
		<button id="confirm" class="btn btn-primary mr-0">Confirm</button>
	</div>
	
	<div class="row table-responsive w-auto text-center align-items-center" style="max-height: 500px;">
		<table class="table table-sm table-hover table-striped table-bordered" style="border-collapse: separate; border-spacing: 0;">
			<thead class="sticky-top bg-white">
				
				<tr>
					<?php
					// heading line 1
					foreach ( $session->field_parameters as $parameters )
						{ ?>
							<th style='text-align:center; vertical-align:middle'><?=$parameters['html_head1']?></th>
						<?php
						} ?>
				</tr>
				
				<tr>
					<?php
					// heading line 2
					foreach ( $session->field_parameters as $parameters )
						{ 
							if ( $parameters['html_checkbox'] == 'Y' )
								{ ?>
									<th style='text-align:center; vertical-align:middle'><input type="checkbox" id="<?=$parameters['field_attribute'].'_all'?>" class="box_us form-control text-center"></th>
								<?php
								} 
							else
								{ ?>
									<th style='text-align:center; vertical-align:middle'><?=$parameters['html_head2']?></th>
								<?php
								} 
						} ?>
				</tr>
				
				<tr>
					<?php
					// heading line 3
					foreach ( $session->field_parameters as $parameters )
						{ 
							if ( $parameters['html_setto'] == 'Y' )
								{ 
									switch ( $parameters['html_entry_type'] )
										{
											case 'input': ?>
												<th>
													<div class="d-flex align-items-center justify-content-center">
														<input type="text" id="<?=$parameters['field_attribute'].'_setto'?>" class="box_us text-center" value=''>
														<button class="go_button_setto" onclick="setto(event)" data-arg1=<?=$parameters['field_attribute']?> data-arg2=input  data-arg3=text><span class="fa-solid fa-check"></span></button>
													</div>
												</th>
												<?php
												break;
											
											case 'colour': ?>
												<th>
													<div class="d-flex align-items-center justify-content-center">
														<input type="color" id="<?=$parameters['field_attribute'].'_setto'?>" class="box_us text-center" value="#d4edda">
														<button class="go_button_setto" onclick="setto(event)" data-arg1=<?=$parameters['field_attribute']?> data-arg2=input data-arg3=color><span class="fa-solid fa-check"></span></button>
													</div>
												</th>
												<?php
												break;
												
											case 'select': ?>
												<th>
													<div class="d-flex align-items-center justify-content-center">
														<select
															id="<?=$parameters['field_attribute'].'_setto'?>"
															class="box_us text-center">
															<?php
															$values = explode(',', $parameters['html_values']);
															foreach ( $values as $value )
																{ ?>
																	<option
																		class="<?=$parameters['field_attribute']?>"
																		value="<?=$value?>" 
																		<?php if ( $parameters['html_default_value'] == $value ) { echo ' selected'; } ?>>
																		<?=$value?>
																	</option>
																<?php
																} ?>
														</select>
														<button class="go_button_setto" onclick="setto(event)" data-arg1=<?=$parameters['field_attribute']?> data-arg2=select data-arg3='select'><span class="fa-solid fa-check"></span></button>
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
											<?=$parameters['html_head3']?>
										</div>
									</th>
								<?php
								} 
						} ?>
				</tr>
					
			</thead>
		
			<tbody id="user_table">						
				<?php
				// loop through data dictionary def fields - this to get default field attribute
				foreach ( $session->event_fields as $field ) 
					{ ?>
										<!-- output data -->
								
										<tr class="align-items-center" id="<?=$field['field_index']?>">
											<?php
											foreach ( $session->field_parameters as $parameters )
												{
													switch ( $parameters['html_entry_type'] )
														{
															case 'readonly': ?>
																<td
																	class="<?=$parameters['field_attribute']?>">
																	<div class="d-flex flex-column align-items-center justify-content-center <?=$parameters['field_attribute']?>">
																		<input 
																			id="<?=$field['field_index'].$parameters['field_attribute']?>"
																			type="text" 
																			class="form-control text-center <?=$parameters['field_attribute']?>" 
																			value="<?php echo esc($field[$parameters['field_attribute']]);?>"
																			readonly>
																	</div>
																</td>
																<?php
																break;
															case 'input': ?>
																<td 
																	class="<?=$parameters['field_attribute']?>">
																	<div class="d-flex flex-column align-items-center justify-content-center <?=$parameters['field_attribute']?>">
																		<input 
																			id="<?=$field['field_index'].$parameters['field_attribute']?>"
																			type="text" 
																			class="form-control text-center <?=$parameters['field_attribute']?>" 
																			value="<?php echo esc($field[$parameters['field_attribute']]);?>">
																	</div>
																</td>
																<?php
																break;
															case 'select': ?>
																<td 
																	class="<?=$parameters['field_attribute']?>">
																	<div class="d-flex flex-column align-items-center justify-content-center <?=$parameters['field_attribute']?>">
																		<select 
																			id="<?=$field['field_index'].$parameters['field_attribute']?>"
																			class="box_vs form-control text-center <?=$parameters['field_attribute']?>">
																			<?php
																			$values = explode(',', $parameters['html_values']);
																			foreach ( $values as $value )
																				{ ?>
																					<option
																						class="<?=$parameters['field_attribute']?>"
																						value="<?=$value?>" 
																						<?php if ( $field[$parameters['field_attribute']] == $value ) { echo ' selected'; } ?>>
																						<?=$value?>
																					</option>
																				<?php
																				} ?>
																		</select>
																	</div>
																</td>
																<?php
																break;
															case 'checkbox1': ?>
																<td class="<?=$parameters['field_attribute']?>">
																	<div class="d-flex flex-column align-items-center justify-content-center <?=$parameters['field_attribute']?>">
																		<input 
																			id="<?=$field['field_index'].$parameters['field_attribute']?>"
																			type="checkbox" 
																			class="form-control text-center <?=$parameters['field_attribute']?>" 
																			<?php 
																			if ( $field[$parameters['field_attribute']] == 'Y' ) 
																				{ 
																					echo 'checked'; 
																				}?>>
																	</div>
																</td>
																<?php
																break;
															case 'checkbox2': ?>
																<td class="<?=$parameters['field_attribute']?>">
																	<div class="d-flex flex-column align-items-center justify-content-center <?=$parameters['field_attribute']?>">
																		<input
																			id="<?=$field['field_index'].$parameters['field_attribute']?>"
																			type="checkbox" 
																			class="form-control text-center <?=$parameters['field_attribute']?>"
																			<?php 
																			if ( $field[$parameters['field_attribute']] == 'Y' AND $field[$parameters['attr']] == 'Y' ) 
																				{ 
																					echo 'checked'; 
																				}?>>
																	</div>
																</td>
																<?php
																break;
															case 'button': ?>
																<td class="<?=$parameters['field_attribute']?>">
																	<div class="d-flex flex-column align-items-center justify-content-center">
																		<button
																			id="<?=$field['field_index'].$parameters['field_attribute']?>"
																			class="<?=$parameters['class']?>" 
																			onclick="<?=$parameters['onclick']?>" 
																			data-arg1=<?=$field['field_index']?>>
																			<span class="<?=$parameters['icon']?>"></span>
																		</button>
																	</div>
																</td>
																<?php
																break;
															case 'colour': ?>
																<td class="<?=$parameters['field_attribute']?>">
																	<div class="d-flex flex-column align-items-center justify-content-center <?=$parameters['field_attribute']?>">
																		<input
																			id="<?=$field['field_index'].$parameters['field_attribute']?>"
																			type="color" 
																			class="<?=$parameters['field_attribute']?>" 
																			value=<?=$field[$parameters['field_attribute']]?>>
																	</div>
																</td>
																<?php
																break;
														}
												} ?>
										</tr>
									<?php
											
							 
					} ?>
			</tbody>
		</table>
	</div>
	
	
	<div>
		<form action="<?php echo(base_url('data_dictionary/enter_parameters_step/')) ?>" method="post" name="post_data_object">
			<input name="data_object" id="data_object" type="hidden">
		</form>
	</div>
	
	<div>
		<form action="<?php echo(base_url('database/database_step1/0')); ?>" method="POST" name="form_return" >
		</form>	
	</div>
	
	<div>
		<form action="<?php echo(base_url('data_dictionary/set_category_index/')); ?>" method="POST" name="form_set_category">
			<input name="new_category" id="new_category" type="hidden">
		</form>	
	</div>
	
	<div>
		<form action="<?php echo(base_url('data_dictionary/set_eventtype_index/')); ?>" method="POST" name="form_set_eventtype">
			<input name="new_eventtype" id="new_eventtype" type="hidden">
		</form>	
	</div>
		
<script>
		
	$(document).ready(function() 
		{				
			$('#confirm').on("click", function()
			{
				// update values
				post_values()
			});
		
			$('#return').on("click", function()
				{			
					$('form[name="form_return"]').submit();
				});
				
			$('#searchMe').on("keyup", function() 
				{
					var value = $(this).val().toLowerCase();
					$("#user_table tr").filter(function() 
						{
							$(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
						});
				});
		});
		
	function post_values() 
		{
			// initialse data object
			var dataObject = {};
			// get list of attributes per row
			fieldParameters = <?php echo json_encode($session->field_parameters); ?>;
			// get all table rows
			var rows = document.getElementById("user_table").rows;
			// read table rows
			for (let [key, row] of Object.entries(rows)) 
				{
					// rowId = this row DB index 
					var rowId = row.id;
					// initialise cells object
					var cells = {};
					// read field parameters to find elements to save for each cell
					for (let [key, parameter] of Object.entries(fieldParameters)) 
						{							
							
							// if value can be saved, get its value
							if ( parameter["html_save"] == 'Y' )
								{
									// construct id
									var cellId = rowId+parameter['field_attribute'];
									// depending on parameter type, get the value of it
									switch(parameter["html_entry_type"]) 
										{
											case 'readonly':
												var value = $('#'+cellId).val();
												break;
											
											case 'input':
												var value = $('#'+cellId).val();
												break;

											case 'select':
												var value = $('#'+cellId).val();
												break;
										
											case 'checkbox1':
												var value = 'N';
												if ( $('#'+cellId).prop("checked") )
													{
														var value = 'Y';
													}
												break;
											
											case 'checkbox2':
												var value = 'N';
												if ( $('#'+cellId).prop("checked") )
												{
													var value = 'Y';
												}
												break;
											
											case 'colour':
												var value = $('#'+cellId).val();
												break;
										}
					
									// add value to cells object
									cells[parameter['field_attribute']] = value;
								}	
						}
												
						// add cells to dataObject
						dataObject[rowId] = cells;						
				}
				
										
			// load variables to form
			$('#data_object').val(JSON.stringify(dataObject));
			
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
	
	function setCategory() 
		{
			// set category when user selects a new one from dropdown
			// load variables to form
			$('#new_category').val($('#category')[0].value);
			// and submit the form
			$('form[name="form_set_category"]').submit();	
		}
		
	function setEventtype() 
		{
			// set event type when user selects a new one from dropdown
			// load variables to form
			$('#new_eventtype').val($('#eventType')[0].value);
			// and submit the form
			$('form[name="form_set_eventtype"]').submit();	
		}
		
</script>
