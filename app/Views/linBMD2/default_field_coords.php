<?php 
		$session = session();
	?>
	
	<div>
		<div class="row small text-muted">
			<p class="col-1 pl-0" style="margin: 0px 0;">Font Size</p>
			<p class="col-4" style="margin: 0px 0;">Allows you to set the font size for this field.</p>
			<p class="col-1" style="margin: 0px 0;"></p>
			<p class="col-1 pl-0" style="margin: 0px 0;">Font Weight</p>
			<p class="col-4" style="margin: 0px 0;">Allows you to set the font weight for this field.</p>
		</div>
	</div>
	
	<div>
		<div class="row small text-muted">
			<p class="col-1 pl-0" style="margin: 0px 0;">Alignment</p>
			<p class="col-4" style="margin: 0px 0;">Allows you to set the alignment of text in this field.</p>
			<p class="col-1" style="margin: 0px 0;"></p>
			<p class="col-1 pl-0" style="margin: 0px 0;">Capitalise</p>
			<p class="col-4" style="margin: 0px 0;">Allows you to set the type of capitalisation to be applied to this field.</p>
		</div>
	</div>
	
	<div>
		<div class="row small text-muted">
			<p class="col-1 pl-0" style="margin: 0px 0;">Roman Volume</p>
			<p class="col-4" style="margin: 0px 0;">Are volumes on this scan in roman numeral format?</p>
			<p class="col-1" style="margin: 0px 0;"></p>
			<p class="col-1 pl-0" style="margin: 0px 0;">Auto Full-stop?</p>
			<p class="col-4" style="margin: 0px 0;">Should FreeComETT automatically insert a full-stop at end of field?</p>
		</div>
	</div>
	
	<div>
		<div class="row small text-muted">
			<p class="col-1 pl-0" style="margin: 0px 0;">Auto copy?</p>
			<p class="col-4" style="margin: 0px 0;">Should FreeComETT automatically copy previous field value to current line?</p>
			<p class="col-1" style="margin: 0px 0;"></p>
			<p class="col-1 pl-0" style="margin: 0px 0;">Auto focus?</p>
			<p class="col-4" style="margin: 0px 0;">Should FreeComETT automatically position the cursor in this field?</p>
		</div>
	</div>
	
	<div>
		<div class="row small text-muted">
			<p class="col-1 pl-0" style="margin: 0px 0;">Colour?</p>
			<p class="col-4" style="margin: 0px 0;">Pick the background colour you want for this field.</p>
			<p class="col-1" style="margin: 0px 0;"></p>
			<p class="col-1 pl-0" style="margin: 0px 0;">Format?</p>
			<p class="col-4" style="margin: 0px 0;">Force a field to allow entry of text or numbers only.</p>
		</div>
	</div>
	
	<br>
	
	<div>
		<b>
			<div class="row">
				<p 
					class="bg-warning col-12 pl-0 text-center font-weight-bold" 
					style="font-size:1.0vw;">
					
					<?php
						echo 'Calibration by Syndicate - '.$session->reference_synd_name.' for Reference Scan - '.$session->reference_scan.', Reference Path - '.$session->reference_path.', Reference Scan Format - '.$session->reference_scan_format.', Data Entry Format - '.$session->reference_data_entry_format.'.'; 
					?>
				</p>
			</div>
			<div class="row">
				<p class="bg-warning col-1 pl-0 text-left font-weight-bold" style="font-size:1.0vw;"><?php echo $session->default_field_parms[$session->current_field_key]['column_name']; ?></p>
				<p class="col-1 text-center">Font Size</p>
				<p class="col-1 text-center">Font Weight</p>
				<p class="col-1 text-center">Pad Left</p>
				<p class="col-1 text-center">Alignment</p>
				<p class="col-1 text-center">Capitalise</p>
				<p class="col-1 text-center">Roman Volume</p>
				<p class="col-1 text-center">Auto Full-stop</p>
				<p class="col-1 text-center">Auto Copy</p>
				<p class="col-1 text-center">Auto Focus</p>
				<p class="col-1 text-center">Colour</p>
				<p class="col-1 text-center">Format</p>
			</div>
			<div class="row">
				<p class="col-1 text-center"></p>
				<p class="col-1 text-center font-weight-normal">(eg 0.5, 1, 1.2456, 2.55, 3)</p>
				<p class="col-1 text-center font-weight-normal">(normal, bold)</p>
				<p class="col-1 text-center font-weight-normal">(eg 0, 1, 2, 10 ...)</p>
				<p class="col-1 text-center font-weight-normal">(left, center, right)</p>
				<p class="col-1 text-center font-weight-normal">(UPPER, lower, First, none)</p>
				<p class="col-1 text-center font-weight-normal">(roman, none)</p>
				<p class="col-1 text-center font-weight-normal">(Y, N)</p>
				<p class="col-1 text-center font-weight-normal">(Y, N)</p>
				<p class="col-1 text-center font-weight-normal">(Y, N)</p>
				<p class="col-1 text-center font-weight-normal">(Click box to pick colour)</p>
				<p class="col-1 text-center font-weight-normal">(text, number)</p>
			</div>
		</b>
	</div>
	
	<div>
			<div class="row">
				<p class="col-1 pl-0 font-weight-bold">Default</p>
				<p class="col-1 text-center"><?= $session->default_field_parms[$session->current_field_key]['font_size'] ?></p>
				<p class="col-1 text-center"><?= $session->default_field_parms[$session->current_field_key]['font_weight'] ?></p>
				<p class="col-1 text-center"><?= $session->default_field_parms[$session->current_field_key]['pad_left'] ?></p>
				<p class="col-1 text-center"><?= $session->default_field_parms[$session->current_field_key]['field_align'] ?></p>
				<p class="col-1 text-center"><?= $session->default_field_parms[$session->current_field_key]['capitalise'] ?></p>
				<p class="col-1 text-center"><?= $session->default_field_parms[$session->current_field_key]['volume_roman'] ?></p>
				<p class="col-1 text-center"><?= $session->default_field_parms[$session->current_field_key]['auto_full_stop'] ?></p>
				<p class="col-1 text-center"><?= $session->default_field_parms[$session->current_field_key]['auto_copy'] ?></p>
				<p class="col-1 text-center"><?= $session->default_field_parms[$session->current_field_key]['auto_focus'] ?></p> 
				<p 
					class="col-1 text-center" 
					style="background-color: #d4edda">
					<?= $session->default_field_parms[$session->current_field_key]['colour'] ?>
				</p>
				<p class="col-1 text-center"><?= $session->default_field_parms[$session->current_field_key]['field_format'] ?></p>
			</div>
	</div>

	<div>
		<form action="<?php echo(base_url('transcribe/default_field_parms_coord_step3')) ?>" method="post">
			<div class="form-group row">
				<label for="image_zoom" class="col-1 pl-0 font-weight-bold">New Default</label>
				<input type="text" class="form-control col-1 text-center" id="font_size" name="font_size" autofocus value="<?php echo esc($session->default_field_parms[$session->current_field_key]['font_size']);?>">
				<input type="text" class="form-control col-1 text-center" id="font_weight" name="font_weight" autofocus value="<?php echo esc($session->default_field_parms[$session->current_field_key]['font_weight']);?>">
				<input type="text" class="form-control col-1 text-center" id="pad_left" name="pad_left" autofocus value="<?php echo esc($session->default_field_parms[$session->current_field_key]['pad_left']);?>">
				<input type="text" class="form-control col-1 text-center" id="field_align" name="field_align" autofocus value="<?php echo esc($session->default_field_parms[$session->current_field_key]['field_align']);?>">
				<input type="text" class="form-control col-1 text-center" id="capitalise" name="capitalise" autofocus value="<?php echo esc($session->default_field_parms[$session->current_field_key]['capitalise']);?>">
				<input type="text" class="form-control col-1 text-center" id="volume_roman" name="volume_roman" autofocus value="<?php echo esc($session->default_field_parms[$session->current_field_key]['volume_roman']);?>">
				<input type="text" class="form-control col-1 text-center" id="auto_full_stop" name="auto_full_stop" autofocus value="<?php echo esc($session->default_field_parms[$session->current_field_key]['auto_full_stop']);?>">
				<input type="text" class="form-control col-1 text-center" id="auto_copy" name="auto_copy" autofocus value="<?php echo esc($session->default_field_parms[$session->current_field_key]['auto_copy']);?>">
				<input type="text" class="form-control col-1 text-center" id="auto_focus" name="auto_focus" autofocus value="<?php echo esc($session->default_field_parms[$session->current_field_key]['auto_focus']);?>">
				<input type="color" class="form-control col-1 text-center" style="background-color: <?= esc($session->default_field_parms[$session->current_field_key]['colour']);?>;" id="colour" name="colour" autofocus value="<?php echo esc($session->default_field_parms[$session->current_field_key]['colour']);?>">
				<input type="text" class="form-control col-1 text-center" id="field_format" name="field_format" autofocus value="<?php echo esc($session->default_field_parms[$session->current_field_key]['field_format']);?>">
			</div>
	</div>
	
	<div>
			<div class="row">
				<p class="col-1 pl-0 font-weight-bold">Apply to all fields</p>
				<select class="col-1 text-center" name="font_applytoall" id="font_applytoall" class="col-2">
					<?php foreach ($session->yesno as $key => $value):?>
						 <option value="<?php echo esc($key)?>"<?php if ( $key == 'N' ) {echo esc(' selected');} ?>><?php echo esc($value)?></option>
					<?php endforeach; ?>
				</select>
				<p class="col-1 text-center"></p>
				<p class="col-1 text-center"></p>
				<p class="col-1 text-center"></p>
				<p class="col-1 text-center"></p>
				<p class="col-1 text-center"></p>
				<p class="col-1 text-center"></p>
				<p class="col-1 text-center"></p>
				<p class="col-1 text-center"></p>
				<select class="col-1 text-center" name="colour_applytoall" id="colour_applytoall" class="col-2">
					<?php foreach ($session->yesno as $key => $value):?>
						 <option value="<?php echo esc($key)?>"<?php if ( $key == 'N' ) {echo esc(' selected');} ?>><?php echo esc($value)?></option>
					<?php endforeach; ?>
				</select>
				<p class="col-1 text-center"></p>
			</div>
	</div>
		
	<div class="row mt-4 d-flex justify-content-between">	
		
		<a id="return" class="btn btn-primary mr-0" href="<?php echo(base_url('transcribe/default_field_parms_coord_step1/1')); ?>">
			<span><?php echo $session->current_project[0]['back_button_text']?></span>
		</a>
		
		<button type="submit" class="btn btn-primary mr-0 d-flex">
			<span>Apply</span>	
		</button>
	</div>
		
			
		</form>
	</div>
