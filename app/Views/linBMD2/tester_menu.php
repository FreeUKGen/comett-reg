<?php $session = session(); ?>
	
	<div class="row">
		<p class="bg-danger col-12 pl-0 text-center" style="font-size:1vw;">YOU ARE A FreeComETT TESTER. HERE ARE TASKS YOU CAN PERFORM. BE CAREFUL!</p>
	</div>
	
	<div class="row">
		<label for="issues" class="col-8 pl-0">Issue Tracker</label>
		<a id="issues" class="btn btn-outline-primary btn-sm col-4 d-flex flex-column align-items-center" target="_blank" href="https://docs.google.com/spreadsheets/d/1quaP9rhInmqlLeRzSZGDxi-Xlbto_U80ucV1f7J0Nek/edit?usp=sharing">
			<span>Issue Tracker</span>
		</a>
	</div>
		
	<br>
	
	<div class="row mt-4 d-flex justify-content-between">	
		<a id="return" class="btn btn-primary mr-0" href="<?php echo(base_url('housekeeping/index/0')); ?>">
			<?php echo $session->current_project[0]['back_button_text']?>
		</a>
	</div>
