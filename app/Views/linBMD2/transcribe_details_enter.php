<?php 
	$session = session();
?>
	<?php
	// loop through table element by element
	foreach ( $session->current_transcription_def_fields as $i => $fields_line )
		{ ?>
			<div class="form-inline row">
				<?php
				// loop through table element by element
				foreach ($fields_line as $td) 
					{ 
						if ( $td['field_show'] == 'Y' )
							{
								$fn = $td['html_name']; ?>
								<!-- output data -->
								<div class="input_wrapper" id="<?php echo esc($td['html_id']);?>" style="height:<?php if (esc($td['column_height']) > 0) {echo esc($td['column_height']);}?>px; width:<?php if (esc($td['column_width']) > 0) {echo esc($td['column_width']);}?>px; font-size:<?= esc($td['font_size']);?>vw; margin-right:5px; position:relative; padding:4px 0px 4px 0px;">
									<input
										class=	"form-control <?= esc($td['field_type']); ?>" draggable="true" id="dragme_yes"
										style=	"	
													height:100%;
													width:100%;
													font-weight: 		<?= esc($td['font_weight']);?>;
													text-align: 		<?php echo esc($td['field_align']);?>;
													padding-left: 		<?= esc($td['pad_left']).'px';?>;
													background-color:	<?php echo esc($td['colour']);?>;
													<?php if ($session->error_field == $td['html_name']) { ?> border:4px solid red; <?php } ?>; 
												"
										type=	"<?php if ( $td['special_test'] == 'should_be_blank' ) { echo esc('hidden'); } else { echo esc($td['field_format']); }?>";
										id=		"<?php echo esc($td['html_id']);?>" 
										name=	"<?php echo esc($td['html_name']);?>"
										placeholder="<?php echo esc($td['column_name']);?>"
										value=	"<?php echo esc($session->$fn);?>"
										autocomplete="off"
										title=	"<?php echo "This is this column ".esc($td['column_name']);?>"
										<?php if ($session->position_cursor == $td['html_name']) { echo 'autofocus'; } ?>
										<?php if ($td['virtual_keyboard'] == 'YES' ) { echo 'virtual-keyboard'; } ?>
										list=	"<?php echo esc($td['field_type']);?>"
									>
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
									<?php
										// add list
										switch ($td['field_type']) 
											{
												case 'condition': ?>
													<datalist id=<?php echo esc($td['field_type']);?>>
														<?php
														foreach ( $session->conditions as $condition ) 
															{ ?>
																<option value=<?php echo esc($condition['Condition']);?>>
															<?php
															} ?>
													</datalist>
													<?php
													break;
												case 'licence': ?>
													<datalist id=<?php echo esc($td['field_type']);?>>
														<?php
														foreach ( $session->licences as $licence ) 
															{ ?>
																<option value=<?php echo esc($licence['Licence']);?>>
															<?php
															} ?>
													</datalist>
													<?php
													break;
												case 'title': ?>
													<datalist id=<?php echo esc($td['field_type']);?>>
														<?php
														foreach ( $session->titles as $title ) 
															{ ?>
																<option value=<?php echo esc($title['Title']);?>>
															<?php
															} ?>
													</datalist>
													<?php
													break;
												case 'relationship': ?>
													<datalist id=<?php echo esc($td['field_type']);?>>
														<?php
														foreach ( $session->relationships as $relationship ) 
															{ ?>
																<option value=<?php echo esc($relationship['Relationship']);?>>
															<?php
															} ?>
													</datalist>
													<?php
													break;
											} 
							}?>
						</div>
					<?php
					} ?>
			</div>
		<?php
		} ?>
	



