	<?php 
		$session = session();
	?>
		
		<!-- show data entry  first line-->
		<form action="<?php echo(base_url($session->controller.'/transcribe_'.$session->controller.'_step2')) ?>" method="post">
		
		<!-- Form part to save Panzoom and Sharpen state -->
		<input type="hidden" name="panzoom_x" id="input-x">
		<input type="hidden" name="panzoom_y" id="input-y">
		<input type="hidden" name="panzoom_z" id="input-zoom">
		<input type="hidden" name="sharpen" id="input-sharpen">
		
		<!-- show transcription comment text  -->
		<div class="form-group row d-flex align-items-center">
			<label for="comment_text" class="col-2">Comment for this transcription =></label>
			<input type="text" class="form-control col-6" id="comment_text" name="comment_text" aria-describedby="userHelp" value="<?php echo esc($session->comment_text); ?>">
			<small id="userHelp" class="form-text text-muted col-4">You can enter / change a comment at any time for this transcription here if you want. If you want to remove it, just make it blank. The comment will be updated each time you enter a detail line.</small>
		</div>
		
		<div class="form-inline row d-flex align-items-center" draggable="false" id="dragme_no">

			<!-- show last line entered -->
			<?php
			// is lastEl set, if not there can't be a last line
			if ( $session->lastEl )
				{
					// loop through table element by element
					foreach ($session->current_transcription_def_fields as $td) 
						{ 
							$fn = $td['html_name']; ?>
								<!-- output data -->
								<!-- background = bootstrap alert-success colour see=https://colorswall.com/palette/3107 -->
								<input
									class=	"form-control"
									style=	"	height: 			auto; 
												width: 				<?php if (esc($td['column_width']) > 0) {echo esc($td['column_width']);}?>px; 
												font-size: 			<?= esc($td['font_size']);?>vw; 
												font-weight: 		<?= esc($td['font_weight']);?>;
												text-align: 		<?php echo esc($td['field_align']);?>;
												padding-left: 		<?= esc($td['pad_left']).'px';?>;
												background-color: 	<?= esc($td['colour']);?>;
											"
									type=		"<?= esc($td['field_format']);?>"; 
									value=		"<?php echo esc($session->lastEl[$td['table_fieldname']]);?>"
									tabindex=	"-1"
									readonly
										
								>
						<?php 
						}
				} ?>
		</div>
		
		<!-- Inject initial values for Panzoom here (x, y, zoom...) src=\"data:$session->mime_type;base64,$session->fileEncode\" -->
		<!-- panzoom-wrapper class is defined in the header and includes image height and rotation. -->
		<div class="panzoom-wrapper row">
			<div class="panzoom">
				<?php echo 
							"<img
								src=\"data:$session->mime_type;base64,$session->fileEncode\"
								alt=\"$session->image\" 
								data-s=\"$session->sharpen\"  
								data-scroll=\"$session->scroll_step\"
							>"; 
				?>
			</div>
		</div>
				
			<div class="form-inline row d-flex align-items-center" draggable="false" id="dragme_no">
			<?php
				// loop through table element by element
				foreach ($session->current_transcription_def_fields as $td) 
					{ 
						$fn = $td['html_name']; ?>
						<!-- output data -->
						<input
							class=	"form-control"
							style=	"	height: 		auto; 
										width: 			<?php if (esc($td['column_width']) > 0) {echo esc($td['column_width']);}?>px; 
										font-size: 		<?= esc($td['font_size']);?>vw; 
										font-weight: 	<?= esc($td['font_weight']);?>;
										text-align: 	<?php echo esc($td['field_align']);?>;
										padding-left: 	<?= esc($td['pad_left']).'px';?>;
										background-color: <?= esc($td['colour']);?>;
										<?php if ($session->error_field == $td['html_name']) { ?> background-color: #ff0000 <?php } ?>
									"
							type=	"<?= esc($td['field_format']);?>"; 
							id=		"<?php echo esc($td['html_id']);?>" 
							name=	"<?php echo esc($td['html_name']);?>"
							placeholder="<?php echo esc($td['column_name']);?>"
							value=	"<?php echo esc($session->$fn);?>"
							autocomplete="off"
							title=	"<?php echo "This is this column ".esc($td['column_name']);?>"
							<?php if ($session->position_cursor == $td['html_name']) { echo 'autofocus'; } ?>
							<?php if ($td['virtual_keyboard'] == 'YES' ) { echo 'virtual-keyboard'; } ?>	
						>
						<?php
							if ($td['virtual_keyboard'] == 'YES' )
								{ ?> 
									<div 
										class="z-index-master">
											<i 
												class="fa fa-keyboard-o keyboardicon" 
												aria-hidden="true" 
												id="<?php echo esc($td['html_id']).'_keyboardicon';?>">
											</i>
									</div> 
								<?php 
								} ?>
					<?php 
					} ?>
			</div>	
		
	<!-- ATTENTION - the form is closed in the transcribe_buttons view -->
