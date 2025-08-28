<?php $session = session(); ?>
	  
	<br>
	<div class="row text-center table-responsive w-auto" id="user_div">
		<table class="table table-sm table-hover table-striped table-bordered" style="border-collapse: separate; border-spacing: 0;" id="show_table">
			<tbody id="user_table">
				<?php
				foreach ( $session->current_transcription_def_fields as $field_line )
					{ ?>
						<tr>
							<?php
							foreach ( $field_line as $td )
								{
									if ( $td['field_check'] == 'Y' AND $td['field_show'] == 'Y' )
										{
											$fn = $td['html_name']; ?>
									
											<td class="" id="" style="height:<?php if (esc($td['column_height']) > 0) {echo esc($td['column_height']);}?>px; width:<?php if (esc($td['column_width']) > 0) {echo esc($td['column_width']);}?>px; font-size:<?= esc($td['font_size']);?>vw; position:relative;">			
															
												<?php
												if ( $td['field_type'] == 'notes' )
													{ ?>
														<textarea 
															rows="1"
															class="form-control form-control-sm <?= esc($td['field_type']); ?>"
															style="
																	font-weight: 		<?=esc($td['font_weight']);?>;
																	text-align: 		<?=esc($td['field_align']);?>;
																	padding-left: 		<?= esc($td['pad_left']).'px';?>;
																	background-color:	<?php echo esc($td['colour']);?>;
																	<?php if ($session->error_field == $td['html_name']) { ?> border:4px solid red; <?php } else { ?> border:1px solid blue; <?php } ?>
																"
															id="<?php echo esc($td['html_id']);?>" 
															name="<?php echo esc($td['html_name']);?>"
															placeholder="<?php echo esc($td['column_name']);?>"
															title="<?="This is this column ".esc($td['column_name']);?>"
															<?php if ($session->position_cursor == $td['html_name']) { echo 'autofocus'; } ?>
															<?php if ($td['virtual_keyboard'] == 'YES' ) { echo 'virtual-keyboard'; } ?>
															><?=esc($session->$fn);?></textarea>
													<?php
													}
												else
													{ ?>
														<input
															class=	"form-control form-control-sm <?= esc($td['field_type']); ?>"
															style=	"	
																	font-weight: 		<?= esc($td['font_weight']);?>;
																	text-align: 		<?php echo esc($td['field_align']);?>;
																	padding-left: 		<?= esc($td['pad_left']).'px';?>;
																	background-color:	<?php echo esc($td['colour']);?>;
																	<?php if ($session->error_field == $td['html_name']) { ?> border:4px solid red; <?php } else { ?> border:1px solid blue; <?php } ?>?>; 
																"
															type=	"<?php if ( $td['special_test'] == 'should_be_blank' ) { echo esc('hidden'); } else { echo esc($td['field_format']); }?>";
															id=		"<?php echo esc($td['html_id']);?>" 
															name=	"<?php echo esc($td['html_name']);?>"
															placeholder="<?=esc($td['column_name']);?>"
															value=	"<?php echo esc($session->$fn);?>"
															autocomplete="off"
															title=	"<?php echo "This is this column ".esc($td['column_name']);?>"
															<?php if ($session->position_cursor == $td['html_name']) { echo 'autofocus'; } ?>
															<?php if ($td['virtual_keyboard'] == 'YES' ) { echo 'virtual-keyboard'; } ?>
															list=	"<?php echo esc($td['field_type']);?>"
															>
														</input>
													<?php
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
													} 
										} ?>
									</td>
								<?php
								} ?>
						</tr> 
					<?php
					} ?>
			</tbody>
		</table>
	</div>
	



