<?php $session = session(); ?>
	
		<!-- this for the font family (https://dl.dafont.com/dl/?f=modern_typewriter Free for personal and non-commercial use -->
		<style>
			@font-face 
				{
					font-family: 'webbmd';
					src: url('<?= '/Fonts/'.$session->current_transcription[0]['BMD_font_family']; ?>.eot');
					src: url('<?= '/Fonts/'.$session->current_transcription[0]['BMD_font_family']; ?>.eot?#iefix') format('embedded-opentype'),
						 url('<?= '/Fonts/'.$session->current_transcription[0]['BMD_font_family']; ?>.woff2') format('woff2'),
						 url('<?= '/Fonts/'.$session->current_transcription[0]['BMD_font_family']; ?>.woff') format('woff'),
						 url('<?= '/Fonts/'.$session->current_transcription[0]['BMD_font_family']; ?>.ttf') format('truetype'),
						 url('<?= '/Fonts/'.$session->current_transcription[0]['BMD_font_family']; ?>.svg') format('svg');
					font-weight: normal;
					font-style: normal;
				}
		</style>
		
		<!-- show data entry  first line-->
		<form action="<?php echo(base_url('/transcribe/verify_step2')) ?>" method="post">
		
		<!-- Form part to save Panzoom and Sharpen state -->
		<input type="hidden" name="panzoom_x" id="input-x">
		<input type="hidden" name="panzoom_y" id="input-y">
		<input type="hidden" name="panzoom_z" id="input-zoom">
		<input type="hidden" name="sharpen" id="input-sharpen">
		
		<!-- show transcription comment text  -->
		<div class="form-group row d-flex align-items-center">
			<label for="comment_text" class="col-2">Comment for this transcription =></label>
			<input type="text" class="form-control col-6" id="comment_text" name="comment_text" aria-describedby="userHelp" value="<?php echo esc($session->comment_text); ?>">
			<small id="userHelp" class="form-text text-muted col-4">You can enter / change a comment at any time for this transcription here if you want. If you want to remove it, just make it blank. The comment will be updated each time you validate a detail line.</small>
		</div>
		
		<div class="form-inline row d-flex align-items-center" draggable="false" id="dragme_no">

			<!-- show last line entered -->
			<?php
			// is lastEl set, if not there can't be a last line
			if ( $session->lastEl )
				{
					// loop through table element by element
					foreach ($session->current_transcription_def_fields as $fields_line) 
						{
							// loop through table element by element
							foreach ($fields_line as $td) 
								{  
									$fn = $td['html_name']; ?>
									<!-- output data -->
									<!-- background = bootstrap alert-success colour see=https://colorswall.com/palette/3107 -->
									<input
										class=	"form-control"
										style=	"	height: 			auto;
													margin-right: 		5px; 
													width: 				<?php if (esc($td['column_width']) > 0) {echo esc($td['column_width']);}?>px; 
													font-size: 			<?= esc($td['font_size']);?>vw; 
													font-weight: 		<?= esc($td['font_weight']);?>;
													text-align: 		<?php echo esc($td['field_align']);?>;
													padding-left: 		<?= esc($td['pad_left']).'px';?>;
													background-color: 	<?= esc($td['colour']);?>;
												"
										type=	"<?php if ( $td['special_test'] == 'should_be_blank' ) { echo esc('hidden'); } else { echo esc($td['field_format']); }?>";
										value=		"<?php echo esc($session->lastEl[$td['table_fieldname']]);?>"
										tabindex=	"-1"
										readonly	
									>
								<?php 
								}
						}
				} ?>
		</div>
		
		<!-- Inject initial values for Panzoom here (x, y, zoom...) src=\"data:$session->mime_type;base64,$session->fileEncode\" -->
		<!-- panzoom-wrapper class is defined in the header and includes image height and rotation. -->
		<div class="panzoom-wrapper row">
			<div class="panzoom" id='panzoom'>
				<?php echo 
							"<img
								src=\"data:$session->mime_type;base64,$session->fileEncode\"
								alt=\"$session->image\"  
								data-scroll=\"$session->scroll_step\"
							>"; 
				?>
			</div>
		</div>
				
			<div class="form-inline row d-flex align-items-center" draggable="false" id="dragme_no">
			<?php
				// loop through table element by element
				foreach ($session->current_transcription_def_fields as $fields_line) 
						{
							// loop through table element by element
							foreach ($fields_line as $td) 
								{
									$fn = $td['html_name']; ?>
									<!-- output data -->
									<input
										class=	"form-control"
										style=	"	height: 			auto;
													margin-right: 		5px;
													width: 				<?php if (esc($td['column_width']) > 0) {echo esc($td['column_width']);}?>px; 
													font-size: 			<?= esc($td['font_size']);?>vw; 
													font-weight: 		<?= esc($td['font_weight']);?>;
													text-align: 		<?php echo esc($td['field_align']);?>;
													padding-left: 		<?= esc($td['pad_left']).'px';?>;
													background-color: 	<?php 	if ( $session->detail_line['BMD_status'] == '1' )
																					{
																						echo 'pink';
																					}
																				else
																					{
																						echo '#fff3cd';
																					} ?>;
												"
										type=	"<?php if ( $td['special_test'] == 'should_be_blank' ) { echo esc('hidden'); } else { echo esc($td['field_format']); }?>";
										id=								"<?php echo esc($td['html_id'].$session->detail_line['BMD_index']);?>"
										name=							"<?php echo esc($td['html_name']);?>"
										placeholder=					"<?php echo esc($td['column_name']);?>"
										value=							"<?php echo esc($session->detail_line[$td['table_fieldname']]);?>"
										readonly
										<?php if ($session->position_cursor == $td['html_name']) { echo 'autofocus'; } ?>
									>
								<?php 
								} 
						} ?>
			</div>
			
		<div class="alert row mt-2 d-flex justify-content-between">
			
				<a id="return" class="btn btn-outline-primary mr-0" href="<?php echo(base_url('transcribe/transcribe_step1/0')); ?>">
					<?php echo $session->current_project[0]['back_button_text']?>
				</a>
				
				<a id="delete_line" class="btn btn-outline-primary mr-0" href="<?=(base_url('transcribe/verify_delete_line/')) ?>">
					<span><?= 'Delete this line, '.$session->detail_line['BMD_line_sequence'];?></span>
				</a>
				
				<a id="modify_line" class="btn btn-outline-primary mr-0" href="<?=(base_url($session->controller.'/select_line/'.esc($session->detail_line['BMD_index']))) ?>">
					<span><?= 'Modify this line, '.$session->detail_line['BMD_line_sequence'];?></span>
				</a>
				
				<a id="insert_line" class="btn btn-outline-primary mr-0" href="<?=(base_url('transcribe/insert_line_step1/'.esc($session->detail_line['BMD_index']))) ?>">
					<span><?= 'Insert a line before line, '.$session->detail_line['BMD_line_sequence'];?></span>
				</a>
				
				<a id="back_line" class="btn btn-outline-primary mr-0" href="<?=(base_url('transcribe/verify_back_one_line/')) ?>">
					<span><?= 'Go back one line from, '.$session->detail_line['BMD_line_sequence'];?></span>
				</a>
				
				<input id="goto_line" name="goto_line" type="number" placeholder="Jump to line"></input>
				
				<!-- show sharpen slider -->
				<div class="">
					<input class="" type="range" id="sharpen-slider" min="1" max="5" step=".5" value="$session->sharpen" />
				</div>
				
				<!-- Sharpen filter for image using SVG -->
				<svg id="filters">
					<defs>
						<filter id="unsharpy" x="0" y="0" width="100%" height="100%">
							<feGaussianBlur result="blurOut" in="SourceGraphic" stdDeviation="1" />
							<feComposite operator="arithmetic" k1="0" k2="4" k3="-3" k4="0" in="SourceGraphic" in2="blurOut" />
						</filter>
					</defs>
				</svg>
		</div>
		
		<div class="alert row mt-2 d-flex justify-content-between">
				<a id="message_button" class=" col-3 btn btn-outline-primary btn-lg mr-0" href="<?php echo(base_url('transcribe/message_to_coord_step1/0')); ?>">
					<span>Send a message to your Co-ordinator</span>
				</a>
				
				<button type="submit" class="col-8 btn btn-warning btn-lg mr-0">
					<span>Have you VERIFIED this line? Click ONCE to confirm...</span>	
				</button>
		</div>
	</form>	
		
	<!-- ATTENTION - the form is closed in the transcribe_buttons view -->
	

		
	



