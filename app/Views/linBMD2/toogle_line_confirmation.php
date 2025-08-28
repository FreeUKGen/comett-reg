	<?php $session = session(); ?>
	
	<div>
		<div class="alert alert-dark row" role="alert">
			<span class="col-12 text-center"><b><?= esc(ucfirst($session->current_allocation[0]['data_entry_format'])) ?></b></span>
		</div>
		<div class="row table-responsive w-auto text-center" style="height:250px">
			<table class="table table-sm table-hover table-striped table-bordered" style="border-collapse: separate; border-spacing: 0;">
				<thead class="sticky-top bg-white">
					<tr>
						<?php
							foreach ( $session->current_used_transcription_def_fields as $i => $fields_line )
								{
									// loop through table element by element
									foreach ($fields_line as $table_header) 
										{ 
											if ( $table_header['data_entry_format'] == $session->current_transcription[0]['current_data_entry_format'])
												{ ?>		
													<th><?=$table_header['column_name'];?></th>
												<?php
												}
										}
								} ?>
					</tr>
				</thead>

				<tbody id="content">
					<?php 
					if( $session->current_line )
						{
							// read each line in turn
							foreach ($session->current_line as $detail)
							{ ?>
								<tr>
									<!-- get all elements for this type and year from DB -->
									<?php
									// loop through element by element
									foreach ( $session->current_used_transcription_def_fields as $i => $fields_line )
										{
											// loop through table element by element
											foreach ($fields_line as $table_line) 
												{	
													if ( $table_line['data_entry_format'] == $session->current_transcription[0]['current_data_entry_format'])
														{ 		
															// highlight last line
															if ( $session->last_detail_index == $detail['BMD_index'] ) 
																{ ?>
																	<td class="alert alert-success" style="font-family: sans-serif;">
																<?php 
																} 
															else 
																{ ?> 
																	<td style="font-family: sans-serif;"> 
																<?php 
																} ?>
																
																<!-- output data -->
																<?= esc($detail[$table_line['table_fieldname']]); ?>
																</td>
														<?php
														}
												}
										} ?>
								</tr>
							<?php
							}
						} ?>
				</tbody>
			</table>
		</div>
	</div>

	
	<div class="step1">
		<form action="<?php echo(base_url('transcribe/toogle_line_step2')) ?>" method="post">
			
			<div class="form-group row mt-3 align-items-center">
				<label for="confirm" class="col-2 pl-0"><?= 'Confirm '.$session->action.' =>'?></label>
				<select name="confirm" id="confirm" class="box col-1">
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
	
