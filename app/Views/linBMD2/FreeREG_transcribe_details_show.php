
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
	
<div>
	<!-- only show data table if there are details -->		
			<div class="row mt-2 d-flex align-items-center justify-content-between">
				<span>
					<input class="" style="width: 5em;" id="last_n" type="number" value="<?=$session->last_n?>">
					<button class="" onclick="lastN()">Show</button>
					<button class="" onclick="lastNall()">ShowAll</button>
					<span id="lastnMessage"></span>
				</span>
				<span>
					<span id="searchMessage"><?=$session->search_message?></span>
					<input class="" id="searchKey" type="text" placeholder="Search..." >
					<button class="" onclick="searchHistory()">Search</button>
					<button class="" onclick="searchReset()">Reset</button>
				</span>

				<?php
				if ( $session->data_groups > 1 )
					{
						foreach ( $session->current_transcription_def_fields as $i => $field_line )
							{ ?>
								<a id="image_parms" style="border:1px solid; border-radius:0.25rem;" href="<?php echo(base_url('transcribe/set_data_group/'.$i)); ?>">
									<span class="ml-2 mr-2" style="font-size:0.75em;"><?=$session->data_group_titles_view[$i]?> +</span>
								</a>
							<?php
							}
					} ?>
			</div>
			
			<div class="table-responsive" style="max-height:50vh;">
				<table class="table table-sm table-striped table-bordered table-hover" id="show_table">
					<thead class="" style="position: sticky;top: 0" >
						<tr>
							<?php
							if ( $session->BMD_cycle_code != 'VERIT' )
								{ ?>
									<th class="no-sort"></th>
									<th class="no-sort"></th>
									<th class="text-center">Delete</th>
									<th class="text-center">Line</th>
								<?php
								} ?>
							<?php
								foreach ( $session->current_used_transcription_def_fields as $i => $fields_line )
								{
									// loop through table element by element
									foreach ($fields_line as $table_header) 
										{ 
											if ( $session->show_data_group == $table_header['field_line'] )
												{ ?>		
													<th class="text-center"><?=$table_header['field_name']?></th>
												<?php
												}
										} 
								} ?>
						</tr>
					</thead>

					<tbody id="user_table">
						<?php if( $session->transcribe_detail_data )
							{
								// read each line in turn
								foreach ( $session->transcribe_detail_data as $detail )
									{
										// select only current data entry format
										if ( $detail['data_entry_format'] == $session->current_transcription[0]['current_data_entry_format'])
											{ ?>
												<tr>
													<!-- insert -->
													<td class="text-center">
														<a id="insert_line" href="<?=(base_url('transcribe/insert_line_step1/'.esc($detail['BMD_index']))) ?>">
														<span style="font-size:0.75em;" title="Insert"><?='Ins'?></span>
													</td>
													<!-- change -->
													<td class="text-center">
														<a id="select_line" href="<?=(base_url($session->controller.'/select_line/'.esc($detail['BMD_index']))) ?>">
														<span style="font-size:0.75em;" title="Modify"><?='Mod'?></span>
													</td>
													<?php
													if ( $session->BMD_cycle_code != 'VERIT' )
														{ ?>
															<!-- status -->
															<td class="text-center">
																<?php 
																if ( $detail['BMD_status'] == 0 )
																	{ ?>
																		<a id="toogle_line" href="<?=(base_url('transcribe/toogle_line_step1/'.esc($detail['BMD_index']))) ?>">
																		<span style="font-size:0.75em;" title="Active"><?='DEL'?></span>
																	<?php }
																else
																	{ ?>
																		<a id="toogle_line" href="<?=(base_url('transcribe/toogle_line_step1/'.esc($detail['BMD_index']))) ?>">
																		<span class="" style="font-size:0.75em;"title="De-activated"><?='DE-ACT'?></span>
																	<?php } ?>
															</td>	
														<?php
														} ?>
													<!-- line no -->
													<td class="text-center">
														<?php echo esc($detail['BMD_line_sequence'] / 10 ); ?>
													</td>
													
													<!-- get all elements for this type and year from DB -->
													<?php
														// loop through element by element
														foreach ($session->current_used_transcription_def_fields as $i => $fields_line )
															{
																// loop through table element by element
																foreach ($fields_line as $td) 
																	{		
																		if ( $session->show_data_group == $td['field_line'] )
																			{ 
																				// highlight lines
																				switch (TRUE)
																					{
																						case $session->insert_before_line_sequence == $detail['BMD_line_sequence']:
																							?>
																								<td id="insert_before_line" class="text-center alert alert-info search-highlight" style="font-family: sans-serif;">
																						<?php
																						break;
																						case $session->insert_line_sequence == $detail['BMD_line_sequence']:
																							?>
																								<td id="inserted_line" class="text-center alert alert-warning search-highlight" style="font-family: sans-serif;">
																							<?php
																							break;
																						case $session->modify_line_sequence == $detail['BMD_line_sequence']:
																							?>
																								<td id="modified_line" class="text-center alert alert-primary search-highlight" style="font-family: sans-serif;">
																							<?php
																							break;
																						case $detail['BMD_status'] == 1:
																							?>
																								<td class="text-center alert alert-danger search-highlight" style="font-family: sans-serif;">
																							<?php
																							break;
																						case $session->last_detail_index == $detail['BMD_index']:
																							if ( $session->BMD_cycle_code == 'VERIT' )
																								{ ?>
																									<td id="last_line" class="text-center alert alert-warning search-highlight" style="font-family: sans-serif;">
																								<?php
																								}
																							else
																								{ ?>
																									<td id="last_line" class="text-center alert alert-success search-highlight" style="font-family: sans-serif;">
																								<?php
																								} ?>
																							<?php
																							break;
																						default:
																							?> 
																								<td class="text-center search-highlight" style="font-family: sans-serif;"> 
																							<?php
																							break;
																					} ?>

																					<!-- output data -->
																					<?php echo esc($detail[$td['table_fieldname']]);
																			}
																	}
															} ?>
															</td>
												</tr>
											<?php
											}
									} 
							}?>
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

<script>
	$(document).ready(function()
		{	
			// highlight all instances of search needle
			var needle = <?php echo json_encode($session->needle); ?>;
			
			// if needle then find all instances and highlight
			if ( needle !== '' && needle != ' ' )
				{
					var i = 0;
					var count = 0;
					var collection = document.getElementsByClassName("search-highlight");
					for ( i; i < collection.length; i++ )
						{
							if (collection[i].innerHTML.toLowerCase().indexOf(needle) != -1)
								{
									collection[i].style.backgroundColor = "yellow";
									++count;
								}
						}
					document.getElementById("searchMessage").innerHTML = count+' instance(s) found for "'+needle+'"';
				}
			
			// instantiate resizeable columns in history table			
			$("#show_table").colResizable(
				{
					liveDrag:true, 
					postbackSafe:true,
					gripInnerHtml:"<div class='grip'></div>", 
					draggingClass:"dragging",
					resizeMode:"flex",					
				});    		
		});
</script>

