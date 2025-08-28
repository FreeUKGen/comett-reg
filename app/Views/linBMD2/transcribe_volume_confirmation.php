	<?php $session = session(); ?>
	
	<div>
		<div class="alert alert-dark row" role="alert">
			<span class="col-12 text-center"><b><?= 'You entered this data' ?></b></span>
		</div>
		<div class="row table-responsive w-auto text-center" style="">
			<table class="table table-sm table-hover table-striped table-bordered" style="border-collapse: separate; border-spacing: 0;">
				<thead class="sticky-top bg-white">
					<tr>
						<?php
							foreach ( $session->current_transcription_def_fields as $i => $fields_line )
								{
									// loop through table element by element
									foreach ($fields_line as $th) 
										{ ?>		
											<th><?=$th['column_name'];?></th>
										<?php 
										}
								} ?>
					</tr>
				</thead>

				<tbody id="content">
					<tr>
						<!-- get all elements for this type and year from DB -->
						<?php
							// loop through element by element
							foreach ( $session->current_transcription_def_fields as $i => $fields_line )
								{
									// loop through table element by element
									foreach ($fields_line as $th) 
										{ ?>		
											<td style="font-family: sans-serif;"> 
												<!-- output data -->
												<?php 	
												$fn = $th['html_name'];
												echo esc($session->$fn); ?>
											</td>
										<?php
										} 
								} ?>									
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	
	<div class="alert alert-dark row" role="alert">
			<span class="col-12 text-center"><b><?= 'This is the image line' ?></b></span>
		</div>
	<!-- Inject initial values for Panzoom here (x, y, zoom) -->
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
	
	<br>
	
	<div class="alert alert-dark row" role="alert">
			<span class="col-12 text-center"><b><?= 'Confirmation' ?></b></span>
		</div>
	<div class="step1">
		<form action="<?php echo(base_url($session->return_route)) ?>" method="post">
			
			<div class="form-group row">
				<label for="volume" class="col-2 pl-0">Enter volume from scan</label>
				<input type="text" class="form-control col-2 pl-0" id="volume" name="volume" autofocus value="<?php echo esc($session->volume);?>">
				<label for="confirm" class="col-2 pl-0">Confirm volume?</label>
				<select name="confirm" id="confirm" class="box col-2">
					<?php foreach ($session->yesno as $key => $value): ?>
						 <option value="<?php echo esc($key)?>"<?php if ( $key == $session->confirm ) {echo esc(' selected');} ?>><?php echo esc($value)?></option>
					<?php endforeach; ?>
				</select>
			</div>
			
		
		
		<div class="row d-flex justify-content-end mt-4">
				<button type="submit" class="btn btn-primary mr-0 d-flex">
					<span>Continue</span>	
				</button>
			</div>
			
		</form>
	</div>
	
