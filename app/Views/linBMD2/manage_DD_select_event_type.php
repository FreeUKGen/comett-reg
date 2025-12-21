	<?php $session = session(); ?>
	
	<form action="<?=(base_url('data_dictionary/load_event_fields/')); ?>" method="POST" >
	
		<div class="row mt-2 justify-content-between align-items-center alert">
					
			<div class="form-group row d-flex align-items-center" id="county_group_group">
				<!-- event type -->
				<label id="event_type_label" for="event_type" class="col-1">Event Type =></label>
				<select name="event_type">
					<option value="">--- Select an Event Type ---</option>
					<?php foreach ( $session->project_types as $event_type )
						{ ?>
							<option value="<?=$event_type['type_desc']?>"><?=$event_type['type_desc']?></option>
						<?php
						} ?>
				</select>
				<input type="submit" value="<?=$session->current_project['submit_button_text']?>" />
			</div>
			
		</div>
	
	</form>
	
<br>
	
	<div class="row mt-4 d-flex justify-content-between">	
		<a id="return" class="btn btn-primary mr-0" href="<?= (base_url('housekeeping/index/0')); ?>">
			<?php echo $session->current_project['back_button_text']?>
		</a>
	</div>