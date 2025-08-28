<?php $session = session(); ?>

	<!-- show last line entered -->
	<?php
	// is lastEl set, if not there can't be a last line
	if ( $session->lastEl )
		{
			foreach ( $session->current_transcription_def_fields as $i => $fields_line )
				{ ?>
					<div class="form-inline row d-flex align-items-center" style="flex-flow: row nowrap !important" draggable="false" id="dragme_no">
						<?php
						// loop through table element by element
						foreach ($fields_line as $td) 
							{ 
								$fn = $td['html_name']; ?>
									<!-- output data -->
									<div id="<?php echo esc($td['html_id'].'_also');?>"style="height:<?php if (esc($td['column_height']) > 0) {echo esc($td['column_height']);}?>px; margin-right:5px; width:<?php if (esc($td['column_width']) > 0) {echo esc($td['column_width']);}?>px; font-size:<?= esc($td['font_size']);?>vw;">
										<input
											class=	"form-control"
											style=	"	
														height:100%;
														width:100%; 
														font-weight: 		<?= esc($td['font_weight']);?>;
														text-align: 		<?php echo esc($td['field_align']);?>;
														padding-left: 		<?= esc($td['pad_left']).'px';?>;
														background-color: 	<?= esc($td['colour']);?>;
													"
											type=		"<?php if ( $td['special_test'] == 'should_be_blank' ) { echo esc('hidden'); } else { echo esc($td['field_format']); }?>"; 
											value=		"<?php echo esc($session->lastEl[$td['table_fieldname']]);?>"
											tabindex=	"-1"
											readonly	
										>
									</div>
							<?php 
							} ?>
					</div>
				<?php
				}
		} ?>

	



