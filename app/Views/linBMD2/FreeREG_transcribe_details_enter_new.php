<?php $session = session(); ?>
	  
	<br>
	<div class="" id="dataEntry">
				<?php
				foreach ( $session->current_transcription_def_fields as $field_line )
					{ ?>
						<span class="row mt-2 align-items-center">
							<?php
							foreach ( $field_line as $td )
								{
									if ( $td['data_entry_format'] == $session->current_transcription[0]['current_data_entry_format'] AND $td['field_check'] == 'Y' AND $td['field_show'] == 'Y' )
										{
											$fn = $td['html_name'];?>
											
											<span class="" id="" style="position: relative;">
												
											<?php 
											switch ( $td['field_type'] )
												{
													case 'notes': ?>				
														<textarea
															class="form-control form-control-sm <?=$td['field_type']?> saveData"
															style="
																font-size:			<?=esc($td['font_size'].'vw');?>;
																font-weight: 		<?=esc($td['font_weight']);?>;
																text-align: 		<?=esc($td['field_align']);?>;
																padding-left: 		<?=esc($td['pad_left']).'px';?>;
																background-color:	<?php echo esc($td['colour']);?>;
																<?php if ($session->error_field == $td['html_name']) { ?> border:4px solid red; <?php } else { ?> border:1px solid blue; <?php } ?>"
															cols="<?php if (esc($td['column_width']) > 0) {echo esc(floor($td['column_width']/16));}?>"
															rows="<?php if (esc($td['column_height']) > 0) {echo esc(floor($td['column_height']/40));}?>"
															type="<?php if ( $td['special_test'] == 'should_be_blank' ) { echo esc('hidden'); } else { echo esc($td['field_format']); }?>"
															id="<?=esc($td['html_id'])?>" 
															name="<?=esc($td['html_name'])?>"
															placeholder="<?=$td['column_name']?>"
															autocomplete="off"
															spellcheck="true"
															title="<?=esc($td['field_popup_help']);?>"
															list="<?=esc($td['field_type'])?>"
															<?php if ($session->position_cursor == $td['html_name']) { echo 'autofocus'; } ?>
															<?php if ($session->error_field == $td['html_name']) { ?> data-error="yes" <?php } else { ?> data-error="no" <?php } ?>
														><?=$session->$fn?></textarea>
													<?php break;
													
													case 'licence': ?>				
														<select																	
															class="form-control form-control-sm <?=$td['field_type']?>  saveData"
															style="			
																font-size:			<?=esc($td['font_size'].'vw');?>;
																font-weight: 		<?=esc($td['font_weight']);?>;
																text-align: 		<?=esc($td['field_align']);?>;
																padding-left: 		<?=esc($td['pad_left']).'px';?>;
																background-color:	<?php echo esc($td['colour']);?>;
																<?php if ($session->error_field == $td['html_name']) { ?> border:4px solid red; <?php } else { ?> border:1px solid blue; <?php } ?>"
															type="<?php if ( $td['special_test'] == 'should_be_blank' ) { echo esc('hidden'); } else { echo esc($td['field_format']); }?>"
															id="<?=esc($td['html_id'])?>" 
															name="<?=esc($td['html_name'])?>"
															placeholder="<?=$td['column_name']?>"
															autocomplete="off"
															spellcheck="true"
															title="<?=esc($td['field_popup_help']);?>"
															list="<?=esc($td['field_type'])?>"
															<?php if ($session->position_cursor == $td['html_name']) { echo 'autofocus'; } ?>
															<?php if ($session->error_field == $td['html_name']) { ?> data-error="yes" <?php } else { ?> data-error="no" <?php } ?>
														>
														<option 
															value="" 
															selected>
															Select: <?=$td['field_name']?>
														</option>
														<?php
														foreach ( $session->licences as $licence )
															{ ?>
																<option 
																	value="<?=$licence?>" 
																	<?php if ($session->$fn == $licence) { echo 'selected'; } ?>>
																	<?=$licence?>
																</option>
															<?php
															} ?>
													</select>
													<?php break;
													
													case 'status': ?>				
														<select																	
															class="form-control form-control-sm <?=$td['field_type']?>  saveData"
															style="			
																font-size:			<?=esc($td['font_size'].'vw');?>;
																font-weight: 		<?=esc($td['font_weight']);?>;
																text-align: 		<?=esc($td['field_align']);?>;
																padding-left: 		<?=esc($td['pad_left']).'px';?>;
																background-color:	<?php echo esc($td['colour']);?>;
																<?php if ($session->error_field == $td['html_name']) { ?> border:4px solid red; <?php } else { ?> border:1px solid blue; <?php } ?>"
															type="<?php if ( $td['special_test'] == 'should_be_blank' ) { echo esc('hidden'); } else { echo esc($td['field_format']); }?>"
															id="<?=esc($td['html_id'])?>" 
															name="<?=esc($td['html_name'])?>"
															placeholder="<?=$td['column_name']?>"
															autocomplete="off"
															spellcheck="true"
															title="<?=esc($td['field_popup_help']);?>"
															list="<?=esc($td['field_type'])?>"
															<?php if ($session->position_cursor == $td['html_name']) { echo 'autofocus'; } ?>
															<?php if ($session->error_field == $td['html_name']) { ?> data-error="yes" <?php } else { ?> data-error="no" <?php } ?>
														>
														<option 
															value="" 
															selected>
															Select: <?=$td['field_name']?>
														</option>
														<?php
														foreach ( $session->person_statuses as $status )
															{ ?>
																<option 
																	value="<?=$status?>" 
																	<?php if ($session->$fn == $status) { echo 'selected'; } ?>>
																	<?=$status?>
																</option>
															<?php
															} ?>
													</select>
													<?php break;
													
													case 'condition': ?>				
														<select																	
															class="form-control form-control-sm <?=$td['field_type']?>  saveData"
															style="			
																font-size:			<?=esc($td['font_size'].'vw');?>;
																font-weight: 		<?=esc($td['font_weight']);?>;
																text-align: 		<?=esc($td['field_align']);?>;
																padding-left: 		<?=esc($td['pad_left']).'px';?>;
																background-color:	<?php echo esc($td['colour']);?>;
																<?php if ($session->error_field == $td['html_name']) { ?> border:4px solid red; <?php } else { ?> border:1px solid blue; <?php } ?>"
															type="<?php if ( $td['special_test'] == 'should_be_blank' ) { echo esc('hidden'); } else { echo esc($td['field_format']); }?>"
															id="<?=esc($td['html_id'])?>" 
															name="<?=esc($td['html_name'])?>"
															placeholder="<?=$td['column_name']?>"
															autocomplete="off"
															spellcheck="true"
															title="<?=esc($td['field_popup_help']);?>"
															list="<?=esc($td['field_type'])?>"
															<?php if ($session->position_cursor == $td['html_name']) { echo 'autofocus'; } ?>
															<?php if ($session->error_field == $td['html_name']) { ?> data-error="yes" <?php } else { ?> data-error="no" <?php } ?>
														>
														<option 
															value="" 
															selected>
															Select: <?=$td['field_name']?>
														</option>
														<?php
														switch ( $td['table_fieldname'] )
															{
																case 'bride_condition':
																case 'mother_condition_prior_to_marriage':
																	$conditions = $session->conditions_f;
																	break;
																case 'person_condition':
																	$conditions = $session->conditions_all;
																	break;
																default:
																	$conditions = $session->conditions_m;
																	break;
															}
														foreach ( $conditions as $condition )
															{ ?>
																<option 
																	value="<?=$condition?>" 
																	<?php if ($session->$fn == $condition) { echo 'selected'; } ?>>
																	<?=$condition?>
																</option>
															<?php
															} ?>
													</select>
													<?php break;
													
													case 'title': 
													?>				
														<select																	
															class="form-control form-control-sm <?=$td['field_type']?>  saveData"
															style="			
																font-size:			<?=esc($td['font_size'].'vw');?>;
																font-weight: 		<?=esc($td['font_weight']);?>;
																text-align: 		<?=esc($td['field_align']);?>;
																padding-left: 		<?=esc($td['pad_left']).'px';?>;
																background-color:	<?php echo esc($td['colour']);?>;
																<?php if ($session->error_field == $td['html_name']) { ?> border:4px solid red; <?php } else { ?> border:1px solid blue; <?php } ?>"
															id="<?=esc($td['html_id'])?>" 
															name="<?=esc($td['html_name'])?>"
															autocomplete="off"
															title="<?=esc($td['field_popup_help']);?>"
															<?php if ($session->position_cursor == $td['html_name']) { echo 'autofocus'; } ?>
															<?php if ($session->error_field == $td['html_name']) { ?> data-error="yes" <?php } else { ?> data-error="no" <?php } ?>
														>
															<option 
																value="" 
																selected>
																Select: <?=$td['field_name']?>
															</option>
															<?php
															$titles = $session->titles;
															foreach ( $titles as $title )
																{ ?>
																	<option 
																		value="<?=$title?>" 
																		<?php if ( $session->$fn == $title ) { echo 'selected'; } ?>
																	>
																		<?=$title?>
																	</option>
																<?php
																} ?>
														</select>
														<?php break;
													
													case 'mark': ?>				
														<select																	
															class="form-control form-control-sm <?=$td['field_type']?> saveData"
															style="			
																font-size:			<?=esc($td['font_size'].'vw');?>;
																font-weight: 		<?=esc($td['font_weight']);?>;
																text-align: 		<?=esc($td['field_align']);?>;
																padding-left: 		<?=esc($td['pad_left']).'px';?>;
																background-color:	<?php echo esc($td['colour']);?>;
																<?php if ($session->error_field == $td['html_name']) { ?> border:4px solid red; <?php } else { ?> border:1px solid blue; <?php } ?>"
															type="<?php if ( $td['special_test'] == 'should_be_blank' ) { echo esc('hidden'); } else { echo esc($td['field_format']); }?>"
															id="<?=esc($td['html_id'])?>" 
															name="<?=esc($td['html_name'])?>"
															placeholder="<?=$td['column_name']?>"
															autocomplete="off"
															spellcheck="true"
															title="<?=esc($td['field_popup_help']);?>"
															list="<?=esc($td['field_type'])?>"
															<?php if ($session->position_cursor == $td['html_name']) { echo 'autofocus'; } ?>
															<?php if ($session->error_field == $td['html_name']) { ?> data-error="yes" <?php } else { ?> data-error="no" <?php } ?>
														>
														<option 
															value="" 
															selected>
															Select: <?=$td['field_name']?>
														</option>
														<?php
														switch ( $td['table_fieldname'] )
															{
																case 'bride_marked':
																	$marked = $session->marked_f;
																	break;
																default:
																	$marked = $session->marked_m;
																	break;
															}
														foreach ( $marked as $key => $mark )
															{ ?>
																<option 
																	value="<?=$key?>" 
																	<?php if ($session->$fn == $key) { echo 'selected'; } ?>>
																	<?=$mark?>
																</option>
															<?php
															} ?>
													</select>
													<?php break;
													
													case 'sex': ?>				
														<select																	
															class="form-control form-control-sm <?=$td['field_type']?> saveData"
															style="			
																font-size:			<?=esc($td['font_size'].'vw');?>;
																font-weight: 		<?=esc($td['font_weight']);?>;
																text-align: 		<?=esc($td['field_align']);?>;
																padding-left: 		<?=esc($td['pad_left']).'px';?>;
																background-color:	<?php echo esc($td['colour']);?>;
																<?php if ($session->error_field == $td['html_name']) { ?> border:4px solid red; <?php } else { ?> border:1px solid blue; <?php } ?>"
															type="<?php if ( $td['special_test'] == 'should_be_blank' ) { echo esc('hidden'); } else { echo esc($td['field_format']); }?>"
															id="<?=esc($td['html_id'])?>" 
															name="<?=esc($td['html_name'])?>"
															placeholder="<?=$td['column_name']?>"
															autocomplete="off"
															spellcheck="true"
															title="<?=esc($td['field_popup_help']);?>"
															list="<?=esc($td['field_type'])?>"
															<?php if ($session->position_cursor == $td['html_name']) { echo 'autofocus'; } ?>
															<?php if ($session->error_field == $td['html_name']) { ?> data-error="yes" <?php } else { ?> data-error="no" <?php } ?>
														>
														<option 
															value="" 
															selected>
															Select: <?=$td['field_name']?>
														</option>
														<?php
														foreach ( $session->sex as $key => $sex )
															{ ?>
																<option 
																	value="<?=$key?>" 
																	<?php if ($session->$fn == $key) { echo 'selected'; } ?>>
																	<?=$sex?>
																</option>
															<?php
															} ?>
													</select>
													<?php break;
													
													default: ?>			
														<input
															class="form-control form-control-sm <?=$td['field_type']?> saveData"
															style="
																height:				<?php if (esc($td['column_height']) > 0) {echo esc($td['column_height']);}?>px; 
																width:				<?php if (esc($td['column_width']) > 0) {echo esc($td['column_width']);}?>px;
																font-size:			<?=esc($td['font_size'].'vw');?>;
																font-weight: 		<?=esc($td['font_weight']);?>;
																text-align: 		<?=esc($td['field_align']);?>;
																padding-left: 		<?=esc($td['pad_left']).'px';?>;
																background-color:	<?php echo esc($td['colour']);?>;
																<?php if ($session->error_field == $td['html_name']) { ?> border:4px solid red; <?php } else { ?> border:1px solid blue; <?php } ?>"
															type="<?php if ( $td['special_test'] == 'should_be_blank' ) { echo esc('hidden'); } else { echo esc($td['field_format']); }?>"
															id="<?=esc($td['html_id'])?>" 
															name="<?=esc($td['html_name'])?>"
															placeholder="<?=$td['column_name']?>"
															autocomplete="off"
															spellcheck="true"
															value="<?=esc($session->$fn)?>"
															title="<?=esc($td['field_popup_help']);?>"
															list="<?=esc($td['field_type'])?>"
															data-capi="<?=esc($td['capitalise'])?>"
															<?php if ($session->position_cursor == $td['html_name']) { echo 'autofocus'; } ?>
															<?php if ($session->error_field == $td['html_name']) { ?> data-error="yes" <?php } else { ?> data-error="no" <?php } ?>
														>
														</input>
													<?php break;
												} ?>
																
												<?php
												// add virtual keyboard icon
												if ($td['virtual_keyboard'] == 'YES' )
													{ ?> 
														<i 
															class="fa fa-keyboard-o keyboardicon"
															id="<?php echo esc($td['html_id']).'_keyboardicon';?>">
														</i>
													<?php 
													} ?>

											</span>
										<?php
										} ?>
								<?php
								} ?>
						</span> 
					<?php
					} ?>
	</div>

<script>	

$(document).ready(function()
	{	
		$('input').dynamicWidth(
			{
				minWidth: 100,
			});
		
		$("input").on("keydown", function(e) 
			{									
				// tab or enter
				if ( e.key == 'Tab' || e.key == 'Enter' )
					{
						if ( e.target.dataset.capi != 'none' )
							{
								var fieldCapi = capitalise(e.target.value, e.target.dataset.capi);
								e.target.value = fieldCapi;
							}
					}		
			});

		// force value of a select to blank if it is null
		// for some reason $session->$fn was being made null when returning from adding a select field to the data entry fields.
		$("#dataEntry select").each(function() 
			{
				if($(this).val() == null) 
					{
						$(this).val('');
					}
			});
			
		// if in marriages and father forname starts to be entered, make father's surname = groom surname
		var eventType = "<?php echo $session->current_transcription[0]['current_data_entry_format']; ?>";
		if ( eventType == 'marriage' )
			{
				$("#groomfatherforename").on("keypress", function(e)
					{
						const key = e.key;
						let isAlpha = /^(?:[a-zA-Z0-9][\u0300-\u036f]*)+$/.test(key.normalize('NFD'));
						if ( isAlpha  ) 
							{
								{
									$("#groomfathersurname").val($("#groomsurname").val());
								}
							}	
					});
				
				$("#bridefatherforename").on("keypress", function(e)
					{	
						const key = e.key;
						let isAlpha = /^(?:[a-zA-Z0-9][\u0300-\u036f]*)+$/.test(key.normalize('NFD'));
						if ( isAlpha  ) 
							{
								if ( $("#bridecondition").val() == '' || $("#bridecondition").val() == 'Spinster' )
									{								
										$("#bridefathersurname").val($("#bridesurname").val());
									}
							}
					});
			}
	});
	
function capitalise(value, capi)
	{
		switch (capi)
			{
				case 'UPPER':
					var valueCapi = value.toUpperCase();
					break;
				case 'First':
					var valueCapi = '';
					var valueArray = value.toLowerCase().split(" ");
					for ( let i = 0; i < valueArray.length; i++ ) 
						{
							valueArray[i] = valueArray[i].charAt(0).toUpperCase() + valueArray[i].slice(1);
							valueCapi = valueCapi + " " + valueArray[i];
						}					
					break;
				case 'lower':
					var valueCapi = value.tolowerCase();
					break;
			}
		
		return valueCapi;			
	}

</script>

