	<?php $session = session(); ?>
	
	<div class="row">
		<label for="firstnames" class="col-8 pl-0">Show given names in popularity order</label>
		<a id="firstnames" class="btn btn-outline-primary btn-sm col-4" href="<?php echo(base_url('housekeeping/firstnames')) ?>">
			<span>Show given names</span>
		</a>
	</div>
	
	<div class="row">
		<label for="surnames" class="col-8 pl-0">Show family names in popularity order</label>
		<a id="surnames" class="btn btn-outline-primary btn-sm col-4" href="<?php echo(base_url('housekeeping/surnames')) ?>">
			<span>Show family names</span>
		</a>
	</div>
		
	<br>
	
	<div class="row mt-4 d-flex justify-content-between">	
		<a id="return" class="btn btn-primary mr-0" href="<?php echo(base_url('transcribe/transcribe_step1/0')); ?>">
			<span><?php echo $session->current_project[0]['back_button_text']?></span>
		</a>
		
		<?php
		if ( $session->current_identity[0]['role_index'] <= 1 )
			{
				?>
				<a id="dbadmin-user" class="btn btn-primary mr-0" href="<?php echo(base_url('database/database_step1/0')) ?>">
					<span>DBADMIN = Actions</span>
				</a>
				
				<a id="report-user" class="btn btn-primary mr-0" href="<?php echo(base_url('report/report_step1/0')) ?>">
						<span>DATA ANALYSIS</span>
					</a>
			<?php
			}
			?>
					
		<?php
		if ( $session->current_identity[0]['role_index'] <= 2 )
			{
				?>
					<a id="coadmin-user" class="btn btn-primary mr-0" href="<?php echo(base_url('database/coord_step1/0')) ?>">
						<span>COORDINATOR = Actions</span>
					</a>
					
					
			<?php
			}
			?>
		
		<?php
		if ( $session->current_identity[0]['role_index'] <= 3 )
			{
				?>
					<a id="tester-user" class="btn btn-primary mr-0" href="<?php echo(base_url('database/tester_step1/0')) ?>">
						<span>TESTER = Actions</span>
					</a>
			<?php
			}
			?>
	</div>

