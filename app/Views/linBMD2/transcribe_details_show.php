	<?php
	// initialise 
	$session = session();
	$lines_span_C = 0;
	$lines_span_T = 0;
	$lines_span_N = 0;
	$lines_span_B = 0;
	use App\Models\Detail_Comments_Model;
	use App\Models\Transcription_Detail_Def_Model;
	$transcription_detail_def_model = new Transcription_Detail_Def_Model();
	?>
<style>
	th 
		{
		  background: white;
		  position: sticky;
		  top: 0; /* Don't forget this, required for the stickiness */
		  box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
		}
</style>
<br>		
<div>			
	<!-- show header -->
	<div class="row table-responsive w-auto text-center" style="height:350px">
		<table class="table table-sm table-hover table-striped table-bordered" style="border-collapse: separate; border-spacing: 0;" id="show_table">
			<thead>
				<tr>
					<th class="no-sort"><?=$session->pagination['total_records']?></th>
					<?php
					if ( $session->BMD_cycle_code != 'VERIT' )
						{ ?>
							<th><input type="number" class="box_us ml-2 mr-2 no-sort" id="last_n" value="<?=$session->last_n?>"></th>
							<th class="no-sort" colspan=2>
								<a id="submit_search" class="box_sm2 ml-2 mr-2" onclick="getSearch()"> Search => </a>
							</th>
						<?php
						} ?>
					<?php
						foreach ( $session->current_transcription_def_fields as $i => $fields_line )
						{
							// loop through table element by element
							foreach ($fields_line as $th) 
								{ 
									$sn = 'search-'.$th['table_fieldname'];
									$fn = $th['column_name'];
									?>		
									<th class="no-sort"><input type="text" class="box_sm1 ml-2 mr-2" id=<?=$sn?> value="<?=$session->$sn?>" placeholder=<?=$fn?>></th>
									<?php
								} 
						} ?>
					<th class="no-sort">Annotations</th>
					<th class="no-sort">Verified?</th>
				</tr>
			</thead>

		<!-- only show data table if there are details -->		
					<tbody id="user_table">
						<?php if( $session->transcribe_detail_data )
							{
								// read each line in turn
								foreach ( $session->transcribe_detail_data as $detail )
									{ ?>
										<tr>
											<!-- line no -->
											<td>
												<span><?= esc($detail['BMD_line_sequence']); ?></span>
											</td>
											
											<?php
											if ( $session->BMD_cycle_code != 'VERIT' )
												{ ?>
													<!-- status -->
													<td>
														<?php 
														if ( $detail['BMD_status'] == 0 )
															{ ?>
																<a id="toogle_line" href="<?=(base_url('transcribe/toogle_line_step1/'.esc($detail['BMD_index']))) ?>">
																<span><?= 'ACTIVE';?></span>
															<?php }
														else
															{ ?>
																<a id="toogle_line" href="<?=(base_url('transcribe/toogle_line_step1/'.esc($detail['BMD_index']))) ?>">
																<span><?= 'DE-ACTIVATED';?></span>
															<?php } ?>
													</td>
													<!-- insert -->
													<td>
														<a id="insert_line" href="<?=(base_url('transcribe/insert_line_step1/'.esc($detail['BMD_index']))) ?>">
														<span><?= 'Insert';?></span>
													</td>
													<!-- change -->
													<td>
														<a id="select_line" href="<?=(base_url($session->controller.'/select_line/'.esc($detail['BMD_index']))) ?>">
														<span><?= 'Modify';?></span>
													</td>
												<?php
												} ?>
											<!-- get all elements for this type and year from DB -->
											<?php
												// loop through element by element
												foreach ( $session->current_transcription_def_fields as $i => $fields_line )
													{
														// loop through table element by element
														foreach ($fields_line as $td) 
															{		
																// highlight lines
																switch (TRUE)
																	{
																		case $session->insert_before_line_sequence == $detail['BMD_line_sequence']:
																			?>
																				<td id="insert_before_line" class="alert alert-info" style="font-family: sans-serif;">
																		<?php
																		break;
																		case $session->insert_line_sequence == $detail['BMD_line_sequence']:
																			?>
																				<td id="inserted_line" class="alert alert-warning" style="font-family: sans-serif;">
																			<?php
																			break;
																		case $session->modify_line_sequence == $detail['BMD_line_sequence']:
																			?>
																				<td id="modified_line" class="alert alert-primary" style="font-family: sans-serif;">
																			<?php
																			break;
																		case $detail['BMD_status'] == 1:
																			?>
																				<td class="alert alert-danger" style="font-family: sans-serif;">
																			<?php
																			break;
																		case $session->last_detail_index == $detail['BMD_index']:
																			if ( $session->BMD_cycle_code == 'VERIT' )
																				{ ?>
																					<td id="last_line" class="alert alert-warning" style="font-family: sans-serif;">
																				<?php
																				}
																			else
																				{ ?>
																					<td id="last_line" class="alert alert-success" style="font-family: sans-serif;">
																				<?php
																				} ?>
																			<?php
																			break;
																		default:
																			?> 
																				<td style="font-family: sans-serif;"> 
																			<?php
																			break;
																	} ?>
														
																<!-- output data -->

																<?php echo esc($detail[$td['table_fieldname']]);
															}
													} 
													
													?>
													</td>
													
														<!-- Handle comments -->
														<td>
															<?php
																// find the comment lines ($cl) line index
																$search_for = $detail['BMD_index'];
																$cl = array_filter($session->transcribe_detail_comments, function($element) use($search_for)
																	{
																	  return isset($element['BMD_line_index']) && $element['BMD_line_index'] == $search_for;
																	});
																
																$comment = '';
																// read the comments if there are any
																if ( $cl )
																	{
																		foreach ($cl as $dc)
																			{
																				if ( $dc['BMD_comment_type'] == 'C' )
																					{
																						$lines_span_C = $dc['BMD_comment_span'] - 1;
																					}
																				if ( $dc['BMD_comment_type'] == 'T' )
																					{
																						$lines_span_T = $dc['BMD_comment_span'] - 1;
																					}
																				if ( $dc['BMD_comment_type'] == 'N' )
																					{
																						$lines_span_N = $dc['BMD_comment_span'] - 1;
																					}
																				if ( $dc['BMD_comment_type'] == 'B' )
																					{
																						$lines_span_B = $dc['BMD_comment_span'] - 1;
																					}
																				if ( $dc['BMD_comment_type'] == 'P' )
																					{
																						$lines_span_B = $dc['BMD_comment_span'] - 1;
																					}
																				$lines_index = $detail['BMD_index'];
																				$comment = $comment.$dc['BMD_comment_type'].$dc['BMD_comment_span'].' ';
																			}
																	}
																	else
																	{
																		$found = 'N';
																		if ( $lines_span_C > 0 )
																			{
																				$comment = $comment.'c ';
																				$lines_span_C = $lines_span_C - 1;
																				$found = 'Y';
																			}
																		if ( $lines_span_T > 0 )
																			{
																				$comment = $comment.'t ';
																				$lines_span_T = $lines_span_T - 1;
																				$found = 'Y';
																			}
																		if ( $lines_span_N > 0 )
																			{
																				$comment = $comment.'n ';
																				$lines_span_N = $lines_span_N - 1;
																				$found = 'Y';
																			}
																		if ( $lines_span_B > 0 )
																			{
																				$comment = $comment.'b ';
																				$lines_span_B = $lines_span_B - 1;
																				$found = 'Y';
																			}
																		if ( $found == 'N' )
																			{
																				$lines_index = $detail['BMD_index'];
																			}
																	}
															?>
															<a id="select_line" href="<?=(base_url($session->controller.'/select_comment/'.esc($lines_index))) ?>"</a>
															<?php if ( empty($comment) ) 
																{ ?> 
																	<span><?= '+';?></span>
																<?php }
																else 
																	{?>
																	<span><?= esc($comment);?></span>
																<?php }?>
														</td>
														
														<!-- Handle verify flag -->
														<td>
															<?php echo esc($detail['line_verified']);?>
														</td>
										</tr>
									<?php
									}
							} ?>
					</tbody>
				</table>
	</div>
</div>

<form action="<?=(base_url('transcribe/set_last_n/')); ?>" method="POST" name="set_last_n" >
	<input name="new_last_n" id="new_last_n" type="hidden" >
</form>

<form action="<?=(base_url('transcribe/set_search/')); ?>" method="POST" name="set_search" >
	<input name="searchArray" id="searchArray" type="hidden" >
</form>




