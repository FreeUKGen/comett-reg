	<?php $session = session(); ?>
	
	<div class="step1">
		<form action="<?php echo(base_url('transcribe/message_to_coord_step2')) ?>" method="post" enctype="multipart/form-data">
			
			<div class="form-group row d-flex align-items-center">
				<label for="subject1" class="col-2">To email =></label>
				<input 
					type="text" 
					class="form-control col-6" 
					id="subject1" 
					name="subject1" 
					aria-describedby="userHelp" 
					value="<?php echo esc($session->current_syndicate[0]['BMD_syndicate_email']); ?>" 
					tabindex= "-1"
					readonly
				>
				<small id="userHelp" class="form-text text-muted col-3">Your email will be sent to this address.</small>
			</div>
			
			<div class="form-group row d-flex align-items-center">
				<label for="subject1" class="col-2">Email Title =></label>
				<input 
					type="text" 
					class="form-control col-6" 
					id="subject1" 
					name="subject1" 
					aria-describedby="userHelp" 
					value="<?php echo esc($session->subject1); ?>" 
					tabindex= "-1"
					readonly
				>
				<small id="userHelp" class="form-text text-muted col-3">This is the Title of your message. It has been constructed automatically.</small>
			</div>
			
			<div class="form-group row d-flex align-items-center">
				<label for="subject1" class="col-2">Email Subject =></label>
				<input 
					type="text" 
					class="form-control col-6" 
					id="subject2" 
					name="subject2" 
					aria-describedby="userHelp" 
					value="<?php echo esc($session->subject2); ?>"
					autofocus 
				>
				<small id="userHelp" class="form-text text-muted col-3">Enter the subject for your message.</small>
			</div>
			
			<div class="form-group row d-flex align-items-center">
				<label for="body" class="col-2">Email Body (optional) =></label>
				<textarea 
					class="form-control col-6"
					rows="6"
					id="body" 
					name="body" 
					aria-describedby="userHelp" 
					value="<?php echo esc($session->body); ?>"
				> 
				</textarea>
				<small id="userHelp" class="form-text text-muted col-3">Enter your message here. Drag the lower righthand corner to add more lines.</small>
			</div>
			
			<div class="form-group row d-flex align-items-center">
				<label for="myfile" class="col-2">Attach a file (optional) =></label>
				<input 
					type="file" 
					id="myfile" 
					name="myfile"
					class="form-control col-2"
					aria-describedby="userHelp"
				>
				<small id="userHelp" class="form-text text-muted col-3">Use Print Screen to take a screen shot of an error or pick a file and attach it here.</small>
			</div> 
			
			<div class="alert row mt-2 d-flex justify-content-between">
				<?php

				switch ($session->BMD_cycle_code) 
					{
						case 'VERIT': ?>
								<a id="return" class="btn btn-primary mr-0" href="<?php echo(base_url('transcribe/verify_step1/'.$session->current_transcription[0]['BMD_header_index'])); ?>">
								<?php echo $session->current_project[0]['back_button_text']?>
								</a>
							<?php
							break;
						case 'INPRO':
							switch ($session->current_allocation[0]['BMD_type']) 
								{
									case 'B': // = Births in FreeBMD ?>
											<a id="return" class="btn btn-primary mr-0" href="<?php echo(base_url('births/transcribe_births_step1/0/')); ?>">
											<?php echo $session->current_project[0]['back_button_text']?>
											</a> 
										<?php
										break;
									case 'M': // = Marriages in FreeBMD ?>
											<a id="return" class="btn btn-primary mr-0" href="<?php echo(base_url('marriages/transcribe_marriages_step1/0/')); ?>">
											<?php echo $session->current_project[0]['back_button_text']?>
											</a> 
										<?php
										break;
									case 'D': // = Deaths in FreeBMD ?>
											<a id="return" class="btn btn-primary mr-0" href="<?php echo(base_url('deaths/transcribe_deaths_step1/0/')); ?>">
											<?php echo $session->current_project[0]['back_button_text']?>
											</a> 
										<?php
										break;
										
										// cases for types in other projects, FreeREG, FreeCEN
									default:
										break;
								}
							break;									
					} ?>
			
				<button type="submit" class="btn btn-primary mr-0">
					<span>Send your message</span>	
				</button>
			</div>		
	
	</form>
