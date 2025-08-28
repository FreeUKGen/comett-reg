<?php $session = session(); ?>

	<!-- Inject initial values for Panzoom here (x, y, zoom...) src=\"data:$session->mime_type;base64,$session->fileEncode\" -->
	<!-- panzoom-wrapper class is defined in the header and includes image height and rotation. -->
	<?php
	switch ( $session->current_transcription[0]['source_code'] )
		{
			case 'LP': 
			case 'FS':
			case 'BS':
			case 'PD': ?>
				<div class="panzoom-wrapper">
					<div class="panzoom" id='panzoom'>
						<img
							src="data:<?=$session->mime_type?>;base64,<?=$session->fileEncode?>"
							alt="<?=$session->image?>"  
							data-scroll="<?=$session->scroll_step?>">
					</div>
				</div>
				<?php break;
		} ?>								
	
	<!--  
	
	<object 
							type="<?=$session->mime_type?>"
							data="data:<?=$session->mime_type?>;base64,<?=$session->fileEncode?>"
							width="2000" 
							height="1000"
							data-scroll="<?=$session->scroll_step?>">
							<span>PDF cannot be shown</span>
						</object>
	-->
	

	



