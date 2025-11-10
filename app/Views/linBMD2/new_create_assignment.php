	<?php $session = session(); ?>
	
	<div>
		<form action="<?=(base_url('allocation/manage_allocations/0'))?>" method="POST" name="form_return"></form>	
	</div>
	
	<div>
		<form action="<?=(base_url('transcribe/transcribe_step1/0'))?>" method="POST" name="form_TP"></form>	
	</div>
	
	<div>
		<form action="<?=(base_url('allocation/new_create_assignment/0'))?>" method="POST" name="form_reset"></form>
	</div>
	
	<div class="row d-flex mt-2 justify-content-between">
		<a id="return" title="Previous Page">Back</a>
		<a id="reset" title="Reset this page">Reset</a>
	</div>
	
	<!-- data entry fields -->
	<div class="flex mt-1">
		<h2 class="heading inline">Create File</h2><span><?=' for '.$session->identity_userid.' in syndicate '.$session->syndicate_name ?></span>
	</div>
	<form>

    <section class="mb-3">
        <label id="county_group_label" for="county_group" class="w5 right">Country
		</label>
        <select id="county_group">
			<option selected="selected" disabled="disabled">Select here</option>
			<option>England</option>
			<option>Scotland</option>
			<option>Wales</option>
			<option>Islands</option>
			<option>Specials</option>
		</select>
		<span id="county_container" class="ml-2 hidden">
       		<label for="county" class="right">County</label>
        	<select id="county"></select>
    	</span>
        <label for="chapman_code" id="chapman_container" class="ml-2 hidden">Chapman
      	  <input type="text" size="3" id="chapman_code">
		</label>
    </section>

    <section id="place_container" class="hidden mb-3">
        <label id="place_label" for="place" class="w5 right">Place</label>
        <select id="place"></select>
        <label id="church_label" class="ml-2 hidden" for="church">Church
        <select id="church"></select>
		</label>
        <label id="church_code_label" class="ml-2 hidden" for="church_code">Church Code
       	 <input type="text" size="3" id="church_code">
		</label>
    </section>

	<section id="register_container" class="hidden mb-3">
        <label id="register_label" class="w5 right" for="register">Register</label>
		<select id="register"></select>
    </section>

    <section class="hidden mb-3" id="source_inputs">
        <label id="source_label" class="w5 right" for="source">Images source
		</label>
			<select id="source"></select>
    </section>

	<section id="doc_source_container" class="hidden mb-3">
		<label class="w5 right" for="doc_source">Doc Source</label>
			<select id="doc_source"></select>
	</section>

    <section id="credit_container" class="hidden mb-3">
        <label for="credit" class="w5 right">Credit To</label>
        <input type='text' id='credit' />
    </section>

    <section id="doc_comment" class="hidden mb-3">
        <label class="w5 right" for="comments">Doc Comments</label>
        <textarea class="w20" id='comments'></textarea>
    </section>

     <!-- progress -->
	<section>
     <div id="progress_wrapper" class="row none">
            <label id="progress_label" for="progress_bar">Upload file:
            <progress class="" id="progress_bar" value="0" max="100"></progress>
			</label>
	 </div>
     </section>

		<a id="confirm" class="btn inline-block ml-10 mb-2 hidden" title="Create assignment">Confirm</a>
	</form>


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
			var error_messages = [];
			var elements = [];
				
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
				
			// focus first input field
			$('county_group').focus();

			// process events
			$("#county_group").on("change", function(e) {									
					// initialise
					e.preventDefault();
					elements = ["county", "chapman_code", "place", "church", "church_code"];
					elements.forEach(blankFields);
					var ctys = [];
					counties = [];
					chapman_codes = [];
					// validate, element, element_value, type_of_test, test_value
					if ( verify_element('county_group', $.trim(document.getElementById("county_group").value), 'value', 'SL', 'county_group_group', error_messages[1]) === 1 ) { return; }
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
					for ( let i = 0; i < ctys.length; i++ ) {
						counties[i] = ctys[i].split(' => ')[0];
						chapman_codes[i] = ctys[i].split(' => ')[1];
					}
					// sources, element_id, field for code, field for name							
					if ( counties ) { 
						load_sources(counties, 'county', null, null); 
						const cc = document.getElementById('county_container');
						cc.classList.remove('hidden');	
						next_element('county_group', 'county', 'yes');
					}
					else { alert('Cannot create assignment. Counties cannot be loaded for this Country. Report to '+error_email); }
			});
				
			$("#county").on("change", function(e) {							
				e.preventDefault();
				elements = ["chapman_code", "place", "church", "church_code"];
				elements.forEach(blankFields);
							
				// validate, element, element_value, type_of_test, test_value
				if ( verify_element('county', $.trim(document.getElementById("county").value), 'value', 'SL', 'county_group_group', error_messages[2]) === 1 ){ return; }
				// load chapman code
				var index = $.inArray( $.trim(document.getElementById("county").value), counties );
				document.getElementById("chapman_code").value = chapman_codes[index];		
				document.getElementById('chapman_container').classList.remove('hidden');	
				document.getElementById('place_container').classList.remove('hidden');	

				// call the php method to get the places for the entered county	
				var formData = new FormData(); 
				formData.append('search_term', $.trim(document.getElementById("county").value));
				var url = "<?=base_url('allocation/get_places')?>";
				places = getData(url, formData, 'Places');
				// load to select
				if ( places.length > 0 ) { 
					load_sources(places, 'place', null, null); 
					// set this_element, next_element, focus
					next_element('county', 'place', 'yes');
				}
				else { alert('Cannot create assignment. Places cannot be loaded for this County. Report to '+error_email); }
			});	
					
			$("#place").on("change", function(e) {						
				// initialise
				e.preventDefault();
				elements = ["church", "church_code"];
				elements.forEach(blankFields);
				let chrs = [];
				churches = [];
				church_codes = [];
						
				// validate, element, element_value, type_of_test, test_value
				if ( verify_element('place', $.trim(document.getElementById("place").value), 'value', 'SL', 'place_group', error_messages[3]) === 1 ){ return; }
				// set this_element, next_element, focus
				next_element('place', 'church', 'yes');
				// call the php method to get the churches for the entered place
				var formData = new FormData(); 
				formData.append('country', document.getElementById("county_group").value);
				formData.append('county', document.getElementById("county").value);
				formData.append('place', document.getElementById("place").value);
				var url = "<?=base_url('allocation/get_churches')?>";
				chrs = getData(url, formData, 'Churches');
				// create church and church code arrays
				for ( let i = 0; i < chrs.length; i++ ) {
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
				if ( churches.length > 0 ) { 
					load_sources(churches, 'church', null, null); 
					const cl = document.getElementById('church_label');
					const ccl = document.getElementById('church_code_label');
					cl.classList.remove('hidden');
					ccl.classList.remove('hidden');
				}
				else { alert('Cannot create assignment. Churches cannot be loaded for this Place. Report to '+error_email); }
		});						
					
		$("#church").on("change", function(e) {
			// initialise
			e.preventDefault();
			elements = ["church_code"];
			elements.forEach(blankFields);
				
			// validate, element, element_value, type_of_test, test_value
			if ( verify_element('church', $.trim(document.getElementById("church").value), 'value', 'SL', 'place_group', error_messages[4]) === 1 ){ return; }
			// set this_element, next_element, focus
			next_element('church', 'register', 'yes');
			// load church code
			var index = $.inArray( $.trim(document.getElementById("church").value), churches );
			document.getElementById("church_code").value = church_codes[index];
			// load registers
			sources = <?php echo json_encode($session->register_types); ?>;							
			if ( sources ) { 
				load_sources(sources, 'register', 'register_code', 'register_description'); 
				const rc = document.getElementById("register_container");
				rc.classList.remove('hidden');
				
			}
			else { alert('Cannot create assignment. Register Types cannot be loaded. Report to '+error_email); }
		});
					
		$("#register").on("change", function(e) {				
			e.preventDefault();
			
			// validate, element, element_value, type_of_test, test_value
			if ( verify_element('register', $.trim(document.getElementById("register").value), 'value', 'SL', 'register_group', error_messages[7]) === 1 ){ return; }
			// set this_element, next_element, focus
			next_element('register', 'source', 'yes');
			// load sources
			sources = <?php echo json_encode($session->allocation_image_sources); ?>;
			if ( sources ) { 
				load_sources(sources, 'source', 'source_code', 'source_name'); 
				const sc = document.getElementById("source_inputs");
				sc.classList.remove('hidden');
			}
			else { alert('Cannot create assignment. Sources Types cannot be loaded. Report to '+error_email); }
		});
					
			$("#source").on("change", function(e) {
				// initialise
				e.preventDefault();

				// validate, element, element_value, type_of_test, test_value
				if ( verify_element('source', $.trim(document.getElementById("source").value), 'value', 'SL', 'source_inputs', error_messages[8]) === 1 ){ return; }

				// load source data input fields depending on input source
				document.querySelectorAll(".remove").forEach(el => el.remove());
				if (document.getElementById("source").value === 'LP' )
				{
					var input = document.createElement("input");
					input.setAttribute('type', 'file');
					input.setAttribute('class', 'remove');
					input.setAttribute('id', 'images_local');
					input.setAttribute('accept', '.jpg, .jpeg, .png, .pdf');
					input.setAttribute('multiple', '');
					var label = document.createElement("label");
					label.setAttribute('class', 'remove');								
					let si = document.getElementById('source_inputs');
					if (si) {
						si.appendChild(label);
						si.appendChild(input);
					}
					else 
						console.warn('DS: SI NOT FOUND!!!');

					// set this_element, next_element, focus
					const ds = document.getElementById("doc_source_container");
					ds.classList.remove('hidden');
					next_element('source', 'images_local', 'yes');
				}
				else
				{
					const ds = document.getElementById("doc_source_container");
					ds.classList.remove('hidden');

					// set this_element, next_element, focus
					next_element('source', 'doc_source', 'yes');
				}
				// load doc sources	
				sources = <?php echo json_encode($session->document_sources); ?>;
				if ( sources ) { 
					load_sources(sources, 'doc_source', 'document_source', 'document_source'); 
				}
				else { alert('Cannot create assignment. Document Sources cannot be loaded. Report to '+error_email); }
			});						
					
			$("#doc_source").on("change", function(e) {
				 e.preventDefault();
				if ( verify_element('doc_source', $.trim(document.getElementById("doc_source").value), 'value', 'SL', 'doc_source_group', error_messages[9]) === 1 ){ return; }

				// set this_element, next_element, focus
				const cc = document.getElementById("credit_container");
				cc.classList.remove('hidden');
				const dc = document.getElementById("doc_comment");
				dc.classList.remove('hidden');
				const co = document.getElementById("confirm");
				co.classList.remove('hidden');
				next_element('doc_source', 'credit', 'yes');
			});

			$("#credit").on("change", function(e) {
				 e.preventDefault();
				// set this_element, next_element, focus
				const cc = document.getElementById("doc_comment");
				cc.classList.remove('hidden');
				next_element('credit', 'comments', 'yes');
			});

									
				$('#return').on("click", function()
					{			
						$('form[name="form_return"]').submit();
					});
					
				$('#reset').on("click", function()
					{			
						$('form[name="form_reset"]').submit();
					});
					
				$('#confirm').on("click", function()
					{			
						// validate
						if ( verify_element('county_group', $.trim(document.getElementById("county_group").value), 'value', 'SL', 'county_group_group', error_messages[1]) === 1 ) { return; }
						if ( verify_element('county', $.trim(document.getElementById("county").value), 'value', 'SL', 'county_group_group', error_messages[2]) === 1 ) { return; }
						if ( verify_element('place', $.trim(document.getElementById("place").value), 'value', 'SL', 'place_group', error_messages[3]) === 1 ) { return; }
						if ( verify_element('church', $.trim(document.getElementById("church").value), 'value', 'SL', 'place_group', error_messages[4]) === 1 ) { return; }
						if ( verify_element('church_code', $.trim(document.getElementById("church_code").value), 'length', 3, 'place_group', error_messages[5]) === 1 ){ return; }
						if ( verify_element('church_code', $.trim(document.getElementById("church_code").value), 'alpha', null, 'place_group', error_messages[6]) === 1 ){ return; }
						if ( verify_element('register', $.trim(document.getElementById("register").value), 'value', 'SL', 'register_group', error_messages[7]) === 1 ){ return; }
						//if ( verify_element('source', $.trim(document.getElementById("source").value), 'value', 'SL', 'source_inputs', error_messages[8]) === 1 ){ return; }
						//if ( verify_element('doc_source', $.trim(document.getElementById("doc_source").value), 'value', 'SL', 'doc_source_group', error_messages[9]) === 1 ){ return; }

						// tests by source
						if (document.getElementById('source')) {
							switch (document.getElementById("source").value)
							{
								case 'LP': // local PC images
									// get images
									const ielem = document.getElementById('images_local');
	
									if (!ielem) 
										break;

									var images = ielem.files;
                                    // test that images selected have not already been attached to an assignment
									// do this first because images are removed from selection if doublon detected. 
									// set url
									let url = "<?=base_url('allocation/doublons')?>";
									// load image names to an array
									let selImages = [];
									for ( let i = 0; i < images.length; i++ ) 
										{
											selImages.push(images[i].name);
										}
									// call controller method to find doublons
									let formData = new FormData();
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
									if ( verify_element('images_local', document.getElementById("images_local").value, 'empty', null, 'source_inputs', error_messages[10]) === 1 ){ return; }	
									// user did enter files so check inividual image files
									var totalSize = 0;
									for (let i = 0; i < images.length; i++) 
										{
											// validate this image
											if ( verify_element('images_local', images[i].type, 'array', ['image/jpeg', 'image/png', 'application/pdf'], 'source_inputs', error_messages[12]) === 1 ){ return; }
											if ( verify_element('images_local', images[i].size, 'empty', null, 'source_inputs', error_messages[13]) === 1 ){ return; }
											if ( verify_element('images_local', images[i].size, 'gt', 20000000, 'source_inputs', error_messages[14]) === 1 ){ return; }
											// accum total images sizes	
											totalSize = totalSize + images[i].size;
										}
									// check total size
									if ( verify_element('images_local', totalSize, 'gt', 500000000, 'source_inputs', error_messages[11]) === 1 ){ return; }
									// set this_element, next_element, focus
									next_element('images_local', 'doc_source', 'yes');
									break;		
							}
						}
						
						// create assignment
						// set url
						var url = "<?=base_url('allocation/create_assignment_step2/0')?>";
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
						if (document.getElementById("source")) {
							switch (document.getElementById("source").value)
							{
								case 'LP': // local PC
									for ( let i = 0; i < images.length; i++ ) 
										{
											formData.append('images[]', images[i]);
										}
									break;	
							}
						}
					
						// submit the form - cannot use fetch as it doesn't give any feedback about upload progress
						// see here - https://javascript.info/xmlhttprequest
						// create the progress bar
						const e = document.getElementById('progress_wrapper');
						if (e) e.className = '';

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
                                document.getElementById("progress_bar").value = (event.loaded / event.total) * 100;
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
						if ( myData[0] === '' )
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
			
		function load_sources(sources, element, source_code, source_name) 
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
				//document.getElementById(element).value = '';
				document.getElementById(element).setAttribute('readonly', true);
				document.getElementById(element).style.backgroundColor = "";
			}
			
		function next_element(this_element, next_element, focus) 
			{
				if (next_element) {
				// next element
					$(next_element).removeAttr("readonly");
					if ( focus === 'yes' ) {
						$(next_element).focus();
					}
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
				label.setAttribute('class', 'error_field');
				var span = document.createElement("span");
				span.setAttribute('class', 'error_field');
				span.setAttribute('ml-2', 'error_field');
				span.innerHTML = error_message;
				span.style.color = "red";
				if (display_group) {
					let displayGroupElem = document.getElementById(display_group);
					if (displayGroupElem) {
						document.getElementById(display_group).appendChild(label);
						document.getElementById(display_group).appendChild(span);
					}
				}	
				else
					console.warn('No display_group found');
			}
					
	</script>