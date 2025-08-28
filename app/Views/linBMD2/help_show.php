	<?php $session = session(); ?>	
	
	<?php
	if ( $session->help )
		{ ?>
			<div class="row">
				<h3 class="col-12 pl-0 p-3 alert-primary text-center">HELP</h3>
			</div>
			
			<?php
			foreach ( $session->help as $line )
				{ 
					if ( $line['help_category'] == 'HELP' )
						{?>
							<div class="row">
									<label for="help" class="col-4 pl-0"><?php echo $line['help_title']; ?></label>
									<a id="help" class="btn btn-outline-primary btn-sm col-8 d-flex" target="_blank" href="<?php echo $line['help_url']; ?>"
										<span><?php echo $line['help_title']; ?></span>
									</a>
							</div>
							<br>
						<?php
						}?>
				<?php
				}?>
		<?php
		}?>

	<br>
	
	<?php
	if ( $session->help )
		{ ?>
			<div class="row">
				<h3 class="col-12 pl-0 p-3 alert-success text-center">HOW TO</h3>
			</div>
			
			<?php
			foreach ( $session->help as $line )
				{
					if ( $line['help_category'] == 'HOWTO' )
						{?>	
							<div class="row">
									<label for="help" class="col-4 pl-0"><?php echo $line['help_title']; ?></label>
									<a id="help" class="btn btn-outline-primary btn-sm col-8 d-flex" target="_blank" href="<?php echo $line['help_url']; ?>"
										<span><?php echo $line['help_title']; ?></span>
									</a>
							</div>
							<br>
						<?php
						}?>
				<?php
				}?>
		<?php
		}?>

	<br>
	
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
									<a id="return" class="btn btn-primary mr-0 flex-column align-items-center" href="<?php echo(base_url('births/transcribe_births_step1/0/')); ?>">
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
					default: ?>
						<a id="return" class="btn btn-primary mr-0" href="<?php echo(base_url('transcribe/transcribe_step1/0') ); ?>">
						<?php echo $session->current_project[0]['back_button_text']?>
						</a>
			<?php
			} ?>
			
		<?php 
		if ( $session->current_identity[0]['role_index'] <= 2 )
			{
				?>
				<a id="manage_help" class="btn btn-primary mr-0" href="<?php echo(base_url('help/help_manage/0')); ?>">
					<span>Co-ordinator ONLY => Manage Help</span>
				</a>
				<?php
			}
		?>	
		
	</div>

