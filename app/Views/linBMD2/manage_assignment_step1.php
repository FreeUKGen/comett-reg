	<?php $session = session(); ?>
	
	<div>
		<form action="<?=(base_url('allocation/manage_allocations/0'))?>" method="POST" name="form_return"></form>	
	</div>
	
	<div>
		<form action="<?=(base_url('transcribe/transcribe_step1/0'))?>" method="POST" name="form_TP"></form>	
	</div>
	
	<div>
		<form action="<?=(base_url('allocation/change_assignment_step1/0'))?>" method="POST" name="change_form_reset"></form>
	</div>
	
	<div>
		<form action="<?=(base_url('allocation/create_assignment_step1/0'))?>" method="POST" name="create_form_reset"></form>
	</div>
	
	<div class="row mt-3 d-flex justify-content-between font-weight-bold">
		<button id="return" class="btn btn-primary mr-0 fa-solid fa-backward" title="Previous Page">Back</button>
		
		<?php
		if ( $session->assignment_mode === 'change' )
			{ ?>
				<h3><?='Change Assignment => '.$session->current_allocation[0]['BMD_allocation_name'] ?></h2>
			<?php
			}
		else
			{ ?>
				<span class="font-weight-bold"><?='Create Assignment'?></span>
			<?php
			} ?>
		
		<button id="reset" class="btn btn-primary mr-0 fa-solid fa-rotate-left" title="Reset this page">Reset</button>
		<button id="confirm" class="btn btn-primary mr-0 fa-solid fa-check" title="Confirm action">Confirm</button>
	</div>
	
	<!-- data entry fields -->
	<div class="row mt-2 justify-content-between align-items-center alert">
		
		<!-- assignment decription -->
		<div class="form-group row d-flex align-items-center" id="ass_name_group">
			<label id="ass_name_label" for="ass_name" class="col-2">Description =></label>
			<input type="text" class="form-control col-5" id="ass_name"	placeholder="Assignment Description...">
		</div>	
				
		<div class="form-group row d-flex align-items-center" id="county_group_group">
			<!-- country -->	
			<label id="county_group_label" for="county_group" class="col-2">Country/County/Chapman Code =></label>
			<select class="form-control col-2" id="county_group"></select>
			<!-- county -->	
			<i id="county_label" class="fa-solid fa-right-long"></i>
			<select readonly class="form-control col-2" id="county"></select>
			<!-- chapman code -->
			<i id="chapman_code_label" class="fa-solid fa-right-long"></i>
			<input readonly type="text" class="form-control col-1" id="chapman_code">
		</div>
			
		<div class="form-group row d-flex align-items-center" id="place_group">
			<!-- place -->
			<label id="place_label" for="place" class="col-2">Place/Church/Church Code =></label>
			<select readonly class="form-control col-2" id="place"></select>
			<!-- church -->
			<i id="church_label" class="fa-solid fa-right-long"></i>
			<select readonly class="form-control col-2" id="church"></select>
			<!-- church code -->
			<i id="church_code_label" class="fa-solid fa-right-long"></i>
			<input readonly type="text" class="form-control col-1" id="church_code">
		</div>
		
		<!-- register -->
		<div class="form-group row d-flex align-items-center" id="register_group">
			<label id="register_label" for="register" class="col-2">Register =></label>
			<select readonly class="form-control col-5" id="register"></select>
		</div>
		
		<!-- source -->
		<div class="form-group row d-flex align-items-center" id="source_inputs">
			<label id="source_label" for="source" class="col-2">Source of images =></label>
			<select readonly class="form-control col-2" id="source"></select>
		</div>
		
		<!-- document source -->
		<div class="form-group row d-flex align-items-center"  id="doc_source_group">
			<label id="doc_source_label" for="doc_source" class="col-2">Document Source =></label>
			<select readonly class="form-control col-5" id="doc_source"></select>
		</div>
		
		<!-- document comment -->
		<div class="form-group row d-flex align-items-center" id="doc_comment_group">
			<label id="doc_comment_label" for="doc_comment" class="col-2">Document Comment =></label>
			<textarea readonly rows="3" class="form-control col-5" id="doc_comment"></textarea>
		</div>
		
		<!-- progress -->
		<div class="form-group row d-flex align-items-center" id="progress">
			<label hidden id="progress_label" for="progress_bar" class="col-2">Create Assignment =></label>
			<progress hidden class="form-control col-10" id="progress_bar" value="0" max="100"></progress>
		</div>
		
	</div>
	
	<!-- show images for assignmants that have them -->
	<?php 
	if ( $session->assignment_mode === 'change' )
		{
			if ( $session->current_allocation[0]['source_code'] == 'LP' )
				{ ?>
					<div class="row text-center table-responsive w-auto" style="max-height: 200px;">
						<table class="table table-hover table-borderless" style="border-collapse: separate; border-spacing: 0;">
							<thead class="sticky-top bg-white">
								<tr class="text-primary">
									<th></th>
									<th><input class="" id="search" type="text" placeholder="Search..." ></th>
									<th></th>
									<th></th>	
								</th>
								<tr class="text-primary">
									<th>Remove</th>
									<th>Image</th>
									<th>Transcription Start Date</th>
									<th>Transcription Complete Date</th>
								</tr>
							</thead>

							<tbody  id="user_table">
								<?php foreach ( $session->allocation_images as $image )
									{ 
										$remove = '';
										if ( $image['trans_start_date'] == null ) {	$remove = 'Remove from assignment'; } 
										?>
										<tr>
											<td 
												style="cursor:pointer;"
												id="<?=esc($image['image_index'])?>"
												class="align-middle remove_image"
												title="ClickMe to remove this image from the assignment"
												data-allocid="<?=esc($image['allocation_index'])?>"
												data-imageid="<?=esc($image['image_index'])?>">
												<?=$remove?>
											</td>
											<td class="align-middle"><?= esc($image['original_image_file_name'])?></td>
											<td class="align-middle"><?= esc($image['trans_start_date'])?></td>
											<td class="align-middle"><?= esc($image['trans_complete_date'])?></td>
											<td></td>
										</tr>
									<?php
									} ?>
							</tbody>
						</table>
					</div>
				<?php
				}
		} ?>	
	
	<script>
		$(document).ready(function() 
			{
				// declare variables
				var error_email = "<?=$session->linbmd2_email?>";
				var sources = [];
				var counties = [];
				var chapman_codes = [];
				var places = [];
				var churches = [];
				var church_codes = [];
				var doublons = [];
				var result = 0;
				var error_messages = [];
				var instruction_messages = [];
				var elements = [];
				var current_assignment = [];
				var current_value = "";
				var ctys = [];
				var mode = "";
				
				// this view handles creation and change of assignments
				// so, I need to know which is incoming 
				mode = "<?=$session->assignment_mode?>";
				
				// if assignment_mode == 'change', I am making a change to an existing assignment, so load existing data.
				if ( mode === 'change' )
					{
						// load current data
						current_assignment = <?=json_encode($session->current_allocation[0])?>;
						
						// assignment name
						document.getElementById("ass_name").value = current_assignment['BMD_allocation_name'];
						
						// county group
						sources = <?=json_encode($session->county_groups)?>;					
						if ( sources ) { load_sources(sources, 'county_group', null, null); }
						else { alert('Cannot create assignment. County Groups (Countries) cannot be loaded. Report to '+error_email); }
						document.getElementById("county_group").value = current_assignment['REG_county_group'];
						document.getElementById("county_group").removeAttribute("readonly");
				
						// county
						switch ( $.trim(document.getElementById("county_group").value) )
							{
								case 'England':
									ctys = <?=json_encode($session->freeukgen_source_values['counties_England'])?>;
									break;
								case 'Wales':
									ctys = <?=json_encode($session->freeukgen_source_values['counties_Wales'])?>;
									break;
								case 'Scotland':
									ctys = <?=json_encode($session->freeukgen_source_values['counties_Scotland'])?>;
									break;
								case 'Islands':
									ctys = <?=json_encode($session->freeukgen_source_values['counties_Islands'])?>;
									break;
								case 'Special':
									ctys = <?=json_encode($session->freeukgen_source_values['counties_Special'])?>;
									break;
							}
						// create county and chapman code arrays
						for ( let i = 0; i < ctys.length; i++ ) 
							{
								counties[i] = ctys[i].split(' => ')[0];
								chapman_codes[i] = ctys[i].split(' => ')[1];
							}							
						if ( counties ) { load_sources(counties, 'county', null, null); }
						else { alert('Cannot create assignment. Counties cannot be loaded for this Country. Report to '+error_email); }
						document.getElementById("county").value = current_assignment['REG_county'];
						document.getElementById("county").removeAttribute("readonly");
						document.getElementById("chapman_code").value = current_assignment['REG_chapman_code'];
				
						// place
						var formData = new FormData(); 
							formData.append('search_term', $.trim(document.getElementById("county").value));
						var url = "<?=base_url('allocation/get_places')?>";
						places = getData(url, formData, 'Places');
						// load to select
						if ( places.length > 0 ) { load_sources(places, 'place', null, null); }
						else { alert('Cannot create assignment. Places cannot be loaded for this County. Report to '+error_email); }	
						document.getElementById("place").value = current_assignment['REG_place'];
						document.getElementById("place").removeAttribute("readonly");
				
						// church
						var formData = new FormData(); 
							formData.append('country', document.getElementById("county_group").value);
							formData.append('county', document.getElementById("county").value);
							formData.append('place', document.getElementById("place").value);
						var url = "<?=base_url('allocation/get_churches')?>";
						chrs = getData(url, formData, 'Churches');
						// create church and church code arrays
						for ( let i = 0; i < chrs.length; i++ ) 
							{
								churches[i] = chrs[i].split(' => ')[0];
								church_codes[i] = chrs[i].split(' => ')[1];
							}
						// blank church_codes = 198 = default which should not be used
						if ( church_codes.length > 0 )
							{
								for ( let i = 0; i < church_codes.length; i++ ) 
									{
										if ( church_codes[i] == '198' ) // default church code
											{
												church_codes[i] = '';
											}
									}
							}
						// load to select
						if ( churches.length > 0 ) { load_sources(churches, 'church', null, null); }
						else { alert('Cannot create assignment. Churches cannot be loaded for this Place. Report to '+error_email); }
						document.getElementById("church").value = current_assignment['REG_church_name'];
						document.getElementById("church").removeAttribute("readonly");	
						document.getElementById("church_code").value = current_assignment['REG_church_code'];	
					
						// register
						sources = <?php echo json_encode($session->register_types); ?>;							
						if ( sources ) { load_sources(sources, 'register', 'register_code', 'register_description'); }
						else { alert('Cannot create assignment. Register Types cannot be loaded. Report to '+error_email); }	
						document.getElementById("register").value = current_assignment['REG_register_type'];
						document.getElementById("register").removeAttribute("readonly");	
					
						// source
						sources = <?php echo json_encode($session->allocation_image_sources); ?>;
						if ( sources ) { load_sources(sources, 'source', 'source_code', 'source_name'); }
						else { alert('Cannot create assignment. Sources Types cannot be loaded. Report to '+error_email); }
						document.getElementById("source").value = current_assignment['source_code'];
						document.getElementById("source").removeAttribute("readonly");
						if (document.getElementById("source").value == 'LP' )
							{
								var input = document.createElement("input");
								input.setAttribute('type', 'file');
								input.setAttribute('class', 'form-control col-3 remove');
								input.setAttribute('id', 'images_local');
								input.setAttribute('accept', '.jpg, .jpeg, .png, .pdf');
								input.setAttribute('multiple', '');
								var label = document.createElement("I");
								label.setAttribute('class', 'fa-solid fa-right-long remove');								
								document.getElementById('source_inputs').appendChild(label);
								document.getElementById('source_inputs').appendChild(input);
							}
				
						// document source
						sources = <?php echo json_encode($session->document_sources); ?>;
						if ( sources ) { load_sources(sources, 'doc_source', 'document_source', 'document_source'); }
						else { alert('Cannot create assignment. Document Sources cannot be loaded. Report to '+error_email); }
						document.getElementById("doc_source").value = current_assignment['source_text'];
						document.getElementById("doc_source").removeAttribute("readonly");
						
						// document comment
						document.getElementById("doc_comment").value = current_assignment['comment_text'];
						document.getElementById("doc_comment").removeAttribute("readonly");

					}

				// load error messages
				error_messages[0] = 'Please select a Description for this assignment.';
				error_messages[1] = 'Please select a Country for this assignment.';
				error_messages[2] = 'Please select a County for this assignment.';
				error_messages[3] = 'Please select a Place for this assignment.';
				error_messages[4] = 'Please select a Church for this assignment.';
				error_messages[5] = 'Church Code must be three characters long.';
				error_messages[6] = 'Church Code must be characters only, A-Z.';
				error_messages[7] = 'Please select a Register for this assignment.';
				error_messages[8] = 'Please select a Source for this assignment.';
				error_messages[9] = 'Please select a Document Source for this assignment.';
				error_messages[10] = 'Please select at least one image.';
				error_messages[11] = 'Total of all files selected cannot exceed 500M.';
				error_messages[12] = 'Only jpeg, jpg, png or pdf files can be selected.';
				error_messages[13] = 'Please select files with content.';
				error_messages[14] = 'Please select files no greater than 20M each.';
				error_messages[15] = 'As you have already started transcribing images from this assignment, you cannot change the source.';
				error_messages[15] = 'As you have already started transcribing images from this assignment, you cannot change the source.';
				
				// load instruction messages
				instruction_messages[0] = 'Now select the COUNTRY. TAB to continue...';
				instruction_messages[1] = 'Now select the COUNTY. TAB to continue...';
				instruction_messages[2] = 'Now select the PLACE. TAB to continue...';
				instruction_messages[3] = 'Now select the CHURCH. TAB to continue...';
				instruction_messages[4] = 'Now select the REGISTER TYPE. TAB to continue...';
				instruction_messages[5] = 'Now select the IMAGE SOURCE. TAB to continue...';
				instruction_messages[6] = 'Now select the IMAGES to transcribe. TAB to continue...';
				instruction_messages[7] = 'Now select the DOCUMENT SOURCE. TAB to continue...';
				instruction_messages[8] = 'Now type a DOCUMENT COMMENT if you wish. TAB to continue...';
				instruction_messages[9] = 'You have completed the create assignment form. Now confirm...';
				
				// focus first input field
				document.getElementById('ass_name').focus();
				
				// process events
				// assignment name
				$("#ass_name").on("keydown", function(e) 
					{
						if ( e.key == 'Tab' || e.key == 'Enter' )
							{
								// initialise
								e.preventDefault();
								// validate, element, element_value, test_type, test_value, display_group, error_message
								if ( verify_element('ass_name', $.trim(document.getElementById("ass_name").value), 'empty', null, 'ass_name_group', error_messages[0]) === 1 ){ return; }
								// set this_element, next_element, focus?	
								next_element('ass_name', 'county_group', 'yes');
								// load county groups
								sources = <?=json_encode($session->county_groups)?>;
								// sources, element_id, field for code, field for name							
								if ( sources ) { load_sources(sources, 'county_group', null, null); }
								else { alert('Cannot create assignment. County Groups (Countries) cannot be loaded. Report to '+error_email); }
								// instruction display group, instruction message index
								instruction('county_group_group', instruction_messages[0]);
							}
					});
				
				$("#county_group").on("keydown", function(e) 
					{									
						// tab or enter
						if ( e.key == 'Tab' || e.key == 'Enter' )
							{
								// initialise
								e.preventDefault();
								elements = ["county", "chapman_code", "place", "church", "church_code"];
								elements.forEach(blankFields);
								ctys = [];
								counties = [];
								chapman_codes = [];
								// validate, element, element_value, type_of_test, test_value
								if ( verify_element('county_group', $.trim(document.getElementById("county_group").value), 'value', 'SL', 'county_group_group', error_messages[1]) === 1 ) { return; }
								// set this_element, next_element, focus?
								next_element('county_group', 'county', 'yes');
								// load counties to select depending on county group
								switch ( $.trim(document.getElementById("county_group").value) )
									{
										case 'England':
											ctys = <?=json_encode($session->freeukgen_source_values['counties_England'])?>;
											break;
										case 'Wales':
											ctys = <?=json_encode($session->freeukgen_source_values['counties_Wales'])?>;
											break;
										case 'Scotland':
											ctys = <?=json_encode($session->freeukgen_source_values['counties_Scotland'])?>;
											break;
										case 'Islands':
											ctys = <?=json_encode($session->freeukgen_source_values['counties_Islands'])?>;
											break;
										case 'Special':
											ctys = <?=json_encode($session->freeukgen_source_values['counties_Special'])?>;
											break;
									}
								// create county and chapman code arrays
								for ( let i = 0; i < ctys.length; i++ ) 
									{
										counties[i] = ctys[i].split(' => ')[0];
										chapman_codes[i] = ctys[i].split(' => ')[1];
									}
								// sources, element_id, field for code, field for name							
								if ( counties ) { load_sources(counties, 'county', null, null); }
								else { alert('Cannot create assignment. Counties cannot be loaded for this Country. Report to '+error_email); }
								instruction('county_group_group', instruction_messages[1]);
							}
					});
				
				$("#county").on("keydown", function(e) 
					{							
						// tab or enter
						if ( e.key == 'Tab' || e.key == 'Enter' )
							{
								// initialise
								e.preventDefault();
								elements = ["chapman_code", "place", "church", "church_code"];
								elements.forEach(blankFields);
								
								// validate, element, element_value, type_of_test, test_value
								if ( verify_element('county', $.trim(document.getElementById("county").value), 'value', 'SL', 'county_group_group', error_messages[2]) === 1 ){ return; }
								// set this_element, next_element, focus?
								next_element('county', 'place', 'yes');
								// load chapman code
								var index = $.inArray( $.trim(document.getElementById("county").value), counties );
								document.getElementById("chapman_code").value = chapman_codes[index];		
								// call the php method to get the places for the entered county	
								var formData = new FormData(); 
									formData.append('search_term', $.trim(document.getElementById("county").value));
								var url = "<?=base_url('allocation/get_places')?>";
								places = getData(url, formData, 'Places');
								// load to select
								if ( places.length > 0 ) { load_sources(places, 'place', null, null); }
								else { alert('Cannot create assignment. Places cannot be loaded for this County. Report to '+error_email); }
								instruction('place_group', instruction_messages[2]);
							}
					});	
					
				$("#place").on("keydown", function(e) 
					{						
						// tab or enter
						if ( e.key == 'Tab' || e.key == 'Enter' )
							{
								// initialise
								e.preventDefault();
								elements = ["church", "church_code"];
								elements.forEach(blankFields);
								var chrs = [];
								churches = [];
								church_codes = [];
								
								// validate, element, element_value, type_of_test, test_value
								if ( verify_element('place', $.trim(document.getElementById("place").value), 'value', 'SL', 'place_group', error_messages[3]) === 1 ){ return; }
								// set this_element, next_element, focus?
								next_element('place', 'church', 'yes');
								// call the php method to get the churches for the entered place
								var formData = new FormData(); 
									formData.append('country', document.getElementById("county_group").value);
									formData.append('county', document.getElementById("county").value);
									formData.append('place', document.getElementById("place").value);
								var url = "<?=base_url('allocation/get_churches')?>";
								chrs = getData(url, formData, 'Churches');
								// create church and church code arrays
								for ( let i = 0; i < chrs.length; i++ ) 
									{
										churches[i] = chrs[i].split(' => ')[0];
										church_codes[i] = chrs[i].split(' => ')[1];
									}
								// blank church_codes = 198 = default which should not be used
								if ( church_codes.length > 0 )
									{
										for ( let i = 0; i < church_codes.length; i++ ) 
											{
												if ( church_codes[i] == '198' ) // default church code
													{
														church_codes[i] = '';
													}
											}
									}
								// load to select
								if ( churches.length > 0 ) { load_sources(churches, 'church', null, null); }
								else { alert('Cannot create assignment. Churches cannot be loaded for this Place. Report to '+error_email); }
								instruction('place_group', instruction_messages[3]);
							}
					});						
					
				$("#church").on("keydown", function(e) 
					{						
						// tab or enter
						if ( e.key == 'Tab' || e.key == 'Enter' )
							{
								// initialise
								e.preventDefault();
								elements = ["church_code"];
								elements.forEach(blankFields);
								
								// validate, element, element_value, type_of_test, test_value
								if ( verify_element('church', $.trim(document.getElementById("church").value), 'value', 'SL', 'place_group', error_messages[4]) === 1 ){ return; }
								// set this_element, next_element, focus?
								next_element('church', 'register', 'yes');
								// load church code
								var index = $.inArray( $.trim(document.getElementById("church").value), churches );
								document.getElementById("church_code").value = church_codes[index];
								// load registers
								sources = <?php echo json_encode($session->register_types); ?>;							
								if ( sources ) { load_sources(sources, 'register', 'register_code', 'register_description'); }
								else { alert('Cannot create assignment. Register Types cannot be loaded. Report to '+error_email); }
								instruction('register_group', instruction_messages[4]);
							}
					});
				
				$("#register").on("keydown", function(e) 
					{				
						if ( e.key == 'Tab' || e.key == 'Enter' )
							{		
								// initialise
								e.preventDefault();
						
								// validate, element, element_value, type_of_test, test_value
								if ( verify_element('register', $.trim(document.getElementById("register").value), 'value', 'SL', 'register_group', error_messages[7]) === 1 ){ return; }
								// set this_element, next_element, focus?
								next_element('register', 'source', 'yes');
								// load sources
								sources = <?php echo json_encode($session->allocation_image_sources); ?>;
								if ( sources ) { load_sources(sources, 'source', 'source_code', 'source_name'); }
								else { alert('Cannot create assignment. Sources Types cannot be loaded. Report to '+error_email); }
								instruction('source_inputs', instruction_messages[5]);
							}
					});
					
				$("#source").on("keydown", function(e) 
					{
						if ( e.key == 'Tab' || e.key == 'Enter' )
							{		
								// initialise
								e.preventDefault();

								// validate, element, element_value, type_of_test, test_value
								if ( verify_element('source', $.trim(document.getElementById("source").value), 'value', 'SL', 'source_inputs', error_messages[8]) === 1 ){ return; }
								// if in change mode
								// and transition from LP to another source
								// only possible if user has not started transcribing
								if ( mode == 'change' && current_assignment['source_code'] == 'LP' && $.trim(document.getElementById("source").value) != 'LP' )
									{
										var allocation_images = <?php echo json_encode($session->allocation_images); ?>;
										var number_null = 0;
										for ( let i = 0; i < allocation_images.length; i++ ) 
										{
											if ( allocation_images[i]['trans_start_date'] == null )
												{
													number_null = number_null + 1;
												}
										}
										if ( number_null != allocation_images.length )
											{
												error_style("source");
												error_field('source_inputs', error_messages[15]);
												document.getElementById("source").value = current_assignment['source_code'];
												return;
											}
									}
								// load source data input fields depending on input source
								document.querySelectorAll(".remove").forEach(el => el.remove());
								if (document.getElementById("source").value == 'LP' )
									{
										var input = document.createElement("input");
										input.setAttribute('type', 'file');
										input.setAttribute('class', 'form-control col-3 remove');
										input.setAttribute('id', 'images_local');
										input.setAttribute('accept', '.jpg, .jpeg, .png, .pdf');
										input.setAttribute('multiple', '');
										var label = document.createElement("I");
										label.setAttribute('class', 'fa-solid fa-right-long remove');								
										document.getElementById('source_inputs').appendChild(label);
										document.getElementById('source_inputs').appendChild(input);

										// set this_element, next_element, focus?
										next_element('source', 'images_local', 'yes');
									}
								else
									{
										// set this_element, next_element, focus?
										next_element('source', 'doc_source', 'yes');
									}
								// load doc sources	
								sources = <?php echo json_encode($session->document_sources); ?>;
								if ( sources ) { load_sources(sources, 'doc_source', 'document_source', 'document_source'); }
								else { alert('Cannot create assignment. Document Sources cannot be loaded. Report to '+error_email); }
								instruction('doc_source_group', instruction_messages[7]);
							}						
					});
					
				$("#doc_source").on("keydown", function(e) 
					{
						if ( e.key == 'Tab' || e.key == 'Enter' )
							{		
								e.preventDefault();
								if ( verify_element('doc_source', $.trim(document.getElementById("doc_source").value), 'value', 'SL', 'doc_source_group', error_messages[9]) === 1 ){ return; }
								// set this_element, next_element, focus?
								next_element('doc_source', 'doc_comment', 'yes');
								instruction('doc_comment_group', instruction_messages[8]);
							}
					});
				
				$("#doc_comment").on("keydown", function(e) 
					{
						if ( e.key == 'Tab' || e.key == 'Enter' )
							{		
								e.preventDefault();
								// remove instruction messages
								document.querySelectorAll(".instruction_field").forEach(el => el.remove());
							}
					});
									
						
				$('#return').on("click", function()
					{			
						$('form[name="form_return"]').submit();
					});
					
				$('#reset').on("click", function()
					{			
						// mode is either change or create
						$('form[name="'+mode+'_form_reset"]').submit();
					});
					
				$('.remove_image').on("click", removeImage)
					
				$('#confirm').on("click", function(e)
					{			
						// validate
						if ( verify_element('ass_name', $.trim(document.getElementById("ass_name").value), 'empty', null, 'ass_name_group', error_messages[0]) === 1 ){ return; }
						if ( verify_element('county_group', $.trim(document.getElementById("county_group").value), 'value', 'SL', 'county_group_group', error_messages[1]) === 1 ) { return; }
						if ( verify_element('county', $.trim(document.getElementById("county").value), 'value', 'SL', 'county_group_group', error_messages[2]) === 1 ) { return; }
						if ( verify_element('place', $.trim(document.getElementById("place").value), 'value', 'SL', 'place_group', error_messages[3]) === 1 ) { return; }
						if ( verify_element('church', $.trim(document.getElementById("church").value), 'value', 'SL', 'place_group', error_messages[4]) === 1 ) { return; }
						if ( verify_element('register', $.trim(document.getElementById("register").value), 'value', 'SL', 'register_group', error_messages[7]) === 1 ){ return; }
						if ( verify_element('source', $.trim(document.getElementById("source").value), 'value', 'SL', 'source_inputs', error_messages[8]) === 1 ){ return; }
						if ( verify_element('doc_source', $.trim(document.getElementById("doc_source").value), 'value', 'SL', 'doc_source_group', error_messages[9]) === 1 ){ return; }

						// tests by source
						switch (document.getElementById("source").value)
							{
								case 'LP': // local PC images
									// get images
									var images = document.getElementById('images_local').files;
									var newImages = Array.from(images);
									// test that images selected have not already been attached to an assignment
									// do this first because images are removed from selection if doublon detected. 
									// set url
									var url = "<?=base_url('allocation/doublons')?>";
									// load image names to an array
									var selImages = [];
									for ( let i = 0; i < images.length; i++ ) 
										{
											selImages.push(images[i].name);
										}
									// call controller method to find doublons
									var formData = new FormData();
									formData.append('sel_images', selImages);
									doublons = getData(url, formData, 'Doublons');
									// any found?
									if ( doublons.length > 0 )
										{
											// tell user; doublons are ignored when uploading images to form data
											alert("The following images are already attached to an assignment and will be de-selected for this assignment creation.\n\n"+doublons.join('\n'));
											// create array of unique doublons image names only
											var duoImages = [];
											for ( let i = 0; i < doublons.length; i++ ) 
												{
													if ( !duoImages.includes(doublons[i].split(' => ')[0]) )
														{
															duoImages.push(doublons[i].split(' => ')[0]);
														}
												}
											// update fileList
											const dt = new DataTransfer();
											for (let i = 0; i < images.length; i++) 
												{
													if ( !duoImages.includes(images[i].name) )
														{
															dt.items.add(images[i]);
														}
												}
											document.getElementById('images_local').files = dt.files;
											images = document.getElementById('images_local').files;
										}
									// any images selected
									if ( mode == 'create' )
										{
											if ( verify_element('images_local', document.getElementById("images_local").value, 'empty', null, 'source_inputs', error_messages[10]) === 1 ){ return; }
										}
									if ( images )
										{
											// user did enter files so check inividual image files
											var totalSize = 0;
											for (let i = 0; i < images.length; i++) 
												{
													// validate this image
													if ( verify_element('images_local', images[i].type, 'array', ['image/jpeg', 'image/png', 'application/pdf'], 'source_inputs', error_messages[12]) === 1 ){ return; }
													if ( verify_element('images_local', images[i].size, 'empty', null, 'source_inputs', error_messages[13]) === 1 ){ return; }
													if ( verify_element('images_local', images[i].size, 'gt', 200000000, 'source_inputs', error_messages[14]) === 1 ){ return; }
													// accum total images sizes	
													totalSize = totalSize + images[i].size;
												}
											// check total size
											if ( verify_element('images_local', totalSize, 'gt', 500000000, 'source_inputs', error_messages[11]) === 1 ){ return; }
										}
									// set this_element, next_element, focus?
									next_element('images_local', 'doc_source', 'yes');
									break;		
							}
						
						// manage assignment
						// set url
						var url = "<?=base_url('allocation/'.$session->assignment_mode.'_assignment_step2/0')?>";
						// load standard variables to formdata
						var formData = new FormData();
							formData.append('ass_name', $('#ass_name').val());
							formData.append('county_group', $('#county_group').val());
							formData.append('county', $('#county').val());
							formData.append('chapman_code', $('#chapman_code').val());
							formData.append('place', $('#place').val());
							formData.append('church', $('#church').val());
							formData.append('church_code', $('#church_code').val());
							formData.append('source', $('#source').val());
							formData.append('register', $('#register').val());
							formData.append('doc_source', $('#doc_source').val());
							formData.append('doc_comment', $('#doc_comment').val());

						// load source specific variables to formData
						switch (document.getElementById("source").value)
							{
								case 'LP': // local PC
									for ( let i = 0; i < images.length; i++ ) 
										{
											formData.append('images[]', images[i]);
										}
									break;	
							}
					
						// submit the form - cannot use fetch as it doesn't give any feedback about upload progress
						// see here - https://javascript.info/xmlhttprequest
						// create the progress bar
						document.getElementById('progress_label').removeAttribute("hidden");
						document.getElementById('progress_bar').removeAttribute("hidden");
						// initialise the request
						let xhr = new XMLHttpRequest();
						xhr.open("POST", url);
						// set the trackers
						xhr.onload = function() 
							{
								if ( xhr.status === 200 ) 
									{
										alert(xhr.response.split('<script')[0]);
										$('form[name="form_TP"]').submit();
									} 
								else 
									{
										alert(xhr.response.split('<script')[0]);
									}
							};
						// the progress bar
						xhr.upload.onprogress = function(event) 
							{
								var progress = (event.loaded / event.total) * 100;
								document.getElementById("progress_bar").value = progress;
							};
						  
						// send the request
						xhr.send(formData);															
					});	
			});
	
		function getData(url, formData, element) 
			{		
				// initialise return value
				var myData = [];
				// initialise the request
				let xhr = new XMLHttpRequest();
				xhr.open("POST", url, false);
				xhr.send(formData);
				if ( xhr.status === 200 && xhr.readyState === 4 )
					{
						// get response
						var myText = xhr.responseText;
						// cleanup data
						myText = myText.split('<script')[0];
						myText = myText.replaceAll('[','');
						myText = myText.replaceAll(']','');
						myText = myText.replaceAll('"', '');
						myText = myText.replaceAll('\n', '');
						// create data array
						myData = myText.split(',');
						myData = myData.sort();
						if ( myData[0] == '' )
							{
								myData.length = 0;
							}
					}
				else
					{
						alert("Failed to get essential data for "+element+". Cannot continue.");
						return;
					}
						
				// return found data
				return myData;
			}
		
		// sources, element_id, field for code, field for name	
		function load_sources(sources, element, source_code, source_name, current_value) 
			{
				// create select
				var optionsAsString = "";
				optionsAsString += "<option value='" + "SL" + "'>" + "Select:" + "</option>";
				for( var i = 0; i < sources.length; i++ ) 
				{
					( source_code === null )
					? optionsAsString += "<option value='" + sources[i] + "'>" + sources[i] + "</option>" 
					: optionsAsString += "<option value='" + sources[i][source_code] + "'>" + sources[i][source_name] + "</option>";
				}
				$("select[id="+element+"]").find('option').remove().end().append($(optionsAsString));
			}
			
			
		function blankFields(element) 
			{
				document.getElementById(element).value = '';
				document.getElementById(element).setAttribute('readonly', true);
				document.getElementById(element).style.backgroundColor = "";
			}
			
		function next_element(this_element, next_element, focus) 
			{
				// this_element
				document.getElementById(this_element).style.backgroundColor = "azure";
				// next element
				document.getElementById(next_element).removeAttribute("readonly");
				if ( focus == 'yes' )
					{
						document.getElementById(next_element).focus();
					}
			}
			
		function verify_element(element, element_value, test_type, test_value, display_group, error_message) 
			{
				// remove error fields
				document.querySelectorAll(".error_field").forEach(el => el.remove());
				// test type
				switch (test_type)
					{
						case 'empty': 
							if ( element_value.length === 0 )
								{
									error_style(element);
									error_field(display_group, error_message);
									return 1;
								}
							break;
						case 'length': 
							if ( element_value.length !== test_value )
								{
									error_style(element);
									error_field(display_group, error_message);
									return 1;
								}
							break;
						case 'value': 
							if ( element_value === test_value )
								{
									error_style(element);
									error_field(display_group, error_message);
									return 1;
								}
							break;
						case 'array':
							if ( !test_value.includes(element_value) )
								{
									error_style(element);
									error_field(display_group, error_message);
									return 1;
								}
							break;
						case 'alpha':
							const isAlpha = str => /^[a-zA-Z]*$/.test(str);
							if ( !isAlpha($.trim(document.getElementById(element).value)) )
								{
									error_style(element);
									error_field(display_group, error_message);
									return 1;
								}
							break;
						case 'gt':
							if ( element_value > test_value )
								{
									error_style(element);
									error_field(display_group, error_message);
									return 1;
								}
							break;
						
					}
				return 0;
			}
		
		function error_style(element) 
			{
				document.getElementById(element).style.backgroundColor = "pink";
				document.getElementById(element).focus();
			}
			
		function error_field(display_group, error_message) 
			{
				var label = document.createElement("I");
				label.setAttribute('class', 'fa-solid fa-right-long error_field');
				var span = document.createElement("span");
				span.setAttribute('class', 'error_field');
				span.innerHTML = error_message;
				span.style.color = "red";
				document.getElementById(display_group).appendChild(label);
				document.getElementById(display_group).appendChild(span);
			}
			
		function removeImage(event)
			{
				// call controller method to remove the image
				var url = "<?=base_url('allocation/remove_image_from_assignment')?>";
				var formData = new FormData();
				formData.append('allocation_index', event.target.dataset.allocid);
				formData.append('image_index', event.target.dataset.imageid);
				let xhr = new XMLHttpRequest();
				xhr.open("POST", url, false);
				xhr.send(formData);
				if ( xhr.status === 200 && xhr.readyState === 4 )
					{
						// get response
						document.getElementById(event.target.dataset.imageid).style.color = "green";
						document.getElementById(event.target.dataset.imageid).innerHTML = "Image has been removed from assignment";
					}
				else
					{
						alert("Failed to remove image from assignment. Report to "+error_email);
						return;
					}
			}
			
		function testSource()
			{
				// call controller method to test if source can be changed from LP
				// it can't if user has started to transcribe this assignment
				var url = "<?=base_url('allocation/change_source')?>";
				var formData = new FormData();
				formData.append('allocation_index', current_assignment['BMD_allocation_index']);
				let xhr = new XMLHttpRequest();
				xhr.open("POST", url, false);
				xhr.send(formData);
				if ( xhr.status === 200 && xhr.readyState === 4 )
					{
						// get response
						document.getElementById(event.target.dataset.imageid).style.color = "green";
						document.getElementById(event.target.dataset.imageid).innerHTML = "Image has been removed from assignment";
					}
				else
					{
						alert("Failed to remove image from assignment. Report to "+error_email);
						return;
					}
			}
			
		function instruction(display_group, instruction_message)
			{	
				// remove instruction messages
				document.querySelectorAll(".instruction_field").forEach(el => el.remove());
				// set instruction message
				var label = document.createElement("I");
				label.setAttribute('class', 'fa-solid fa-right-long instruction_field');
				var span = document.createElement("span");
				span.setAttribute('class', 'instruction_field');
				span.innerHTML = instruction_message;
				span.style.color = "green";
				document.getElementById(display_group).appendChild(label);
				document.getElementById(display_group).appendChild(span);		
			}
			
	</script>
