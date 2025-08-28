<?php 

	$session = session();
	
?>
    
	<div class="row mt-2" id="user_div"> 
	  <?php
	  // read data groups
	  foreach ( $session->current_transcription_def_fields as $i => $field_line )
		{ 
			// set anything to show in this group
			$anything_to_show = 0;
			foreach ( $field_line as $td )
				{
					if ( $td['field_check'] == 'Y' AND $td['field_show'] == 'Y' )
						{
							$anything_to_show = 1;
						}
				}
				
			// is there anything to show in this group
			if ( $anything_to_show == 1 )
				{ ?>
					<div class="col-sm-<?=$session->bootstrap_cols?> text-center">
						<button type="button" class="btn-info mb-1 col-12" data-toggle="collapse" data-target="#group_<?=$i?>" aria-expanded="false" aria-controls="group_<?=$i?>" tabindex="-1">
						<?=$session->data_group_titles_view[$i]?> +</button>
						<div id="group_<?=$i?>" class="collapse">
							<div class="row mt-1" >
								<?php
								foreach ( $field_line as $j => $td )
									{			
										// only show if check and show == Y
										if ( $td['field_check'] == 'Y' AND $td['field_show'] == 'Y' )
											{
												$fn = $td['html_name']; ?>
										
												<div class="col-sm-6" id="" >
													<?php
													if ( $td['field_type'] == 'notes' )
														{ ?>
															<textarea
																rows="1"
																class=	"form-control form-control-sm"
																style=	"	
																		font-weight: 		<?= esc($td['font_weight']);?>;
																		text-align: 		<?php echo esc($td['field_align']);?>;
																		padding-left: 		<?= esc($td['pad_left']).'px';?>;
																		background-color:	<?php echo esc($td['colour']);?>;
																		<?php if ($session->error_field == $td['html_name']) { ?> border:4px solid red; <?php } else { ?> border:1px solid blue; <?php } ?>; 
																	"
																id=		"<?php echo esc($td['html_id']);?>" 
																name=	"<?php echo esc($td['html_name']);?>"
																placeholder="<?php echo esc($td['column_name']);?>"
																value=	"<?php echo esc($session->$fn);?>"
																title=	"<?php echo "This is this column ".esc($td['column_name']);?>"
																<?php if ($session->position_cursor == $td['html_name']) { echo 'autofocus'; } ?>
																<?php if ($td['virtual_keyboard'] == 'YES' ) { echo 'virtual-keyboard'; } ?>
																>
															</textarea>
														<?php
														}
													else
														{ ?>
															<input
																class=	"form-control form-control-sm <?php if ( $td['field_type'] == 'notes' ) { echo esc('expandable_input'); }?> <?= esc($td['field_type']); ?>"
																style=	"	
																		font-weight: 		<?= esc($td['font_weight']);?>;
																		text-align: 		<?php echo esc($td['field_align']);?>;
																		padding-left: 		<?= esc($td['pad_left']).'px';?>;
																		background-color:	<?php echo esc($td['colour']);?>;
																		<?php if ($session->error_field == $td['html_name']) { ?> border:4px solid red; <?php } else { ?> border:1px solid blue; <?php } ?>; 
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
															</input>
														<?php
														}
														// add virtual keyboard icon
													if ($td['virtual_keyboard'] == 'YES' )
														{ ?> 
															<i 
																class="fa fa-keyboard-o keyboardicon"
																id="<?php echo esc($td['html_id']).'_keyboardicon';?>">
															</i>
														<?php 
														} ?>
												</div>
											
											<?php
											}
									} ?>
							</div>
						</div>
					</div>
				<?php
				} 
		} ?>
	</div>
	



