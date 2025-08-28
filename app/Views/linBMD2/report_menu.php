<?php $session = session(); ?>
	
	<div class="row">
		<label for="show_report_data" class="col-8 pl-0">Show Report</label>
		<a id="show_report_data" class="btn btn-outline-primary btn-sm col-4 d-flex" href="<?php echo(base_url('report/show_report_data')) ?>">
		<span>Show Report</span>
		</a>
	</div>
	
	<br>
	
	<div class="row mt-4 d-flex justify-content-between">	
		<a id="return" class="btn btn-primary mr-0" href="<?php echo(base_url('housekeeping/index/0')); ?>">
			<?php echo $session->current_project[0]['back_button_text']?>
		</a>
	</div>
