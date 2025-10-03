<?php $session = session(); ?>	

<script>
	
	// check session exists
	$(document).ready(function()
		{
			fetch("<?php echo(base_url('home/session_exists')) ?>", 
				{
					method: "POST",
					headers: 
						{
							"Content-Type": "application/json; charset=UTF-8",
						},
					body: JSON.stringify(),
				})
			.then(response => response.text()) // get the response as text because JSON doesn't work
			.then(data => 	{
								const myData = data.split('<');
								var sessionStatus = myData[0];
								sessionStatus = sessionStatus.replace(/\"/g, '');
								if ( sessionStatus.trim() === 'session_expired' )
									{
										alert("Your session has EXPIRED. Press Submit button or ENTER to signin again and continue transcribing.");
									}
							})
			.catch((error) => 	{
								})
		});
		
		// debug with console.log('message');
	
	$(document).ready(function()
		{
			// show only if $session->image_source[0]['source_images'] = "yes"
			var showImage = "<?php echo $session->image_source[0]['source_images']; ?>";
			if ( showImage == "yes" )
				{
					// Sharpen filter system
					const filterDefElement = document.querySelector("#unsharpy > feComposite");
					const filterSlider = document.querySelector("#sharpen-slider");
					const formInputSharpen = document.querySelector("#input-sharpen");
					var sharpen = <?php echo json_encode($session->sharpen); ?>;

					filterSlider.value = formInputSharpen.value = parseFloat(sharpen);
					filterSlider.addEventListener("change", (event) => 
						{
							const factor = parseFloat(event.currentTarget.value);
							formInputSharpen.value = factor;
							filterDefElement.setAttribute("k2", factor);
							filterDefElement.setAttribute("k3", 1 - factor);
						});
				}
		});
		
	// position show details transcribed to last line of table.
	$(document).ready(function()
		{							
			// are there any detail lines
			var detailLines = <?php echo json_encode($session->transcribe_detail_data); ?>;
		
			if ( detailLines.length != 0 )
				{
					if ( document.getElementById("insert_before_line") )
						{
							document.getElementById("insert_before_line").scrollIntoView({ behavior: "instant", block: "end", inline: "nearest" });
						}
					else if ( document.getElementById("inserted_line") )
						{
							document.getElementById("inserted_line").scrollIntoView({ behavior: "instant", block: "end", inline: "nearest" });
						}
					else if ( document.getElementById("modified_line") )
						{
							document.getElementById("modified_line").scrollIntoView({ behavior: "instant", block: "end", inline: "nearest" });
						}
					else if ( document.getElementById("last_line") )
						{
							document.getElementById("last_line").scrollIntoView({ behavior: "instant", block: "end", inline: "nearest" });
						}
					else
						{
							document.getElementById("last_line").scrollIntoView({ behavior: "instant", block: "end", inline: "nearest" });
						}
				}
		});

	// initalise autocomplete
	$(document).ready(function()
		{
			var sourceURL = '';
			var defFields = <?php echo json_encode($session->current_transcription_def_fields); ?>;
			var current_data_entry_format = <?php echo json_encode($session->current_transcription[0]['current_data_entry_format']); ?>;

			// find all field_types and initialise autocomplete on them
			for (var fieldsLineIndex in defFields) 
				{
					for (var fieldIndex in defFields[fieldsLineIndex])
						{
							// process for current data entry format
							if ( defFields[fieldsLineIndex][fieldIndex]["data_entry_format"] == current_data_entry_format && defFields[fieldsLineIndex][fieldIndex]['field_check'] == 'Y' && defFields[fieldsLineIndex][fieldIndex]['field_show'] == 'Y')
								{
									// set autocomplete url
									switch(defFields[fieldsLineIndex][fieldIndex]["field_type"]) 
										{
											case 'fore_name':
												sourceURL = "<?php echo(base_url('transcribe/search_firstnames')) ?>";
												break;
											case 'sur_name':
												sourceURL = "<?php echo(base_url('transcribe/search_surnames')) ?>";
												break;
											case 'sp_ouse':
												sourceURL = "<?php echo(base_url('transcribe/search_surnames')) ?>";
												break;
											case 'dis_trict':
												sourceURL = "<?php echo(base_url('transcribe/search_districts')) ?>";
												break;
											case 'occupation':
												sourceURL = "<?php echo(base_url('transcribe/search_occupations')) ?>";
												break;
											case 'parish':
												sourceURL = "<?php echo(base_url('transcribe/search_parishes')) ?>";
												break;
											case 'condition':
												// condition is now a pick list so don't need autocomplete'
												// sourceURL = "<?php echo(base_url('transcribe/search_conditions')) ?>";
												break;
											case 'title':
												// title is now a pick list so don't need autocomplete'
												// sourceURL = "<?php echo(base_url('transcribe/search_titles')) ?>";
												break;
											case 'licence':
												// licence is now a pick list so don't need autocomplete'
												// sourceURL = "<?php echo(base_url('transcribe/search_licences')) ?>";
												break;
											case 'relationship':
												sourceURL = "<?php echo(base_url('transcribe/search_relationships')) ?>";
												break;
											case 'status':
												// status is now a pick list so don't need autocomplete'
												// sourceURL = "<?php echo(base_url('transcribe/search_person_status')) ?>";
												break;
											default:
												sourceURL = null;
												break;
										}
							
									// if source URL
									if ( sourceURL != null )
										{
											// initialise autocomplete
											$('#'+defFields[fieldsLineIndex][fieldIndex]["html_id"]).autocomplete(	
												{
													minLength: 2,
													source: sourceURL,
													autoFocus: true,
												});
										}
								}
						}
				}
		});
		
	// initialise virtual keyboard
	// thanks to - Rob Garrison - https://github.com/Mottie/Keyboard
	$(document).ready(function()
		{
			var defFields = <?php echo json_encode($session->current_transcription_def_fields); ?>;
			var current_data_entry_format = <?php echo json_encode($session->current_transcription[0]['current_data_entry_format']); ?>;

			// find all fields and initialise vitual keyboard on them
			for (var fieldsLineIndex in defFields) 
				{
					for (var fieldIndex in defFields[fieldsLineIndex])
						{
							// process for current data entry format
							if ( defFields[fieldsLineIndex][fieldIndex]["data_entry_format"] == current_data_entry_format && defFields[fieldsLineIndex][fieldIndex]['field_check'] == 'Y' && defFields[fieldsLineIndex][fieldIndex]['field_show'] == 'Y' )
								{
									// is the virtual keyboard allowed for this field?
									if ( defFields[fieldsLineIndex][fieldIndex]["virtual_keyboard"] == 'YES' )
										{
											// initialse the keyboard on this id
											$('#'+defFields[fieldsLineIndex][fieldIndex]["html_id"]).keyboard(
												{
													// options here
													// don't absorb the input field into the keyboard
													usePreview: false,
													// cursor to end of any input already in input field		
													caretToEnd: true,
													// don't auto show the keyboard. This will prevent the keyboard from showing if the input field is subsequently focussed.		
													openOn: null,
													// change key names
													display: 
														{   
														   's'		: 'Capitals',
														   'c'		: 'Cancel',
														   'a'		: 'Accept',
														}, 
													// use a custom layout
													layout: 'custom',
													// define the custom layout - https://www.lexilogos.com/keyboard/latin_alphabet.htm
													customLayout: 	
														{
															'normal' : ['á à â ā ä ã å æ', 'é è ê ē ë', 'í ì î ī ï', 'ó ò ô ō ö õ ø œ', 'ú ù û ū ü', 'ŵ', 'ý ŷ ȳ ÿ', 'þ ç ð ñ ß', '{c} {s} {a}'],
															'shift'  : ['Á À Â Ā Ä Ã Å Æ', 'É È Ê Ē Ë', 'Í Ì Î Ī Ï', 'Ó Ò Ô Ō Ö Õ Ø Œ', 'Ú Ù Û Ū Ü', 'Ŵ', 'Ý Ŷ Ȳ Ÿ', 'Þ Ç Ð Ñ ẞ', '{c} {s} {a}']
														}			
												});								
										}
								}
						}
				}
		});
		
	// copy surnames
	$(document).ready(function()
		{	
			var projectIndex	= <?php echo json_encode($session->current_project[0]['project_index']); ?>;
			var dataEntryFormat = <?php echo json_encode($session->current_allocation[0]['data_entry_format']); ?>;
		
			// test for project
			switch(projectIndex)
				{
					case '1':
						break;
					case '2':
						// test for data entry format
						switch(dataEntryFormat)
							{
								case 'baptism':
									if ( document.querySelector('[name="personsurname"]') )
										{
											document.querySelector('[name="personsurname"]').addEventListener("focusout", function(e)
												{
													if ( document.querySelector('[name="fathersurname"]') )
														{
															document.querySelector('[name="fathersurname"]').value = e.target.value;
														}
												});
										}
									break;
								case 'marriage':
									if ( document.querySelector('[name="groomsurname"]') )
										{
											document.querySelector('[name="groomsurname"]').addEventListener("focusout", function(e)
												{
													if ( document.querySelector('[name="groomfathersurname"]') )
														{
															document.querySelector('[name="groomfathersurname"]').value = e.target.value;
														}
												});
										}
									if ( document.querySelector('[name="bridesurname"]') )
										{
											document.querySelector('[name="bridesurname"]').addEventListener("focusout", function(e)
												{
													if ( document.querySelector('[name="bridefathersurname"]') )
														{
															document.querySelector('[name="bridefathersurname"]').value = e.target.value;
														}
												});
										}
									break;		
							}
						break;
					case '3':
						break;
				}
		});
		
	// handle tab press in district = get volume
	$("input[name=district]").keydown(function(e)
		{
			// get the code of the key that was pressed 
			var code = e.keycode || e.which;
			
			// only get volume if TAB was pressed in district field
			if ( code === 9 )
				{
					// get def fields - held in current transcription def fields array
					defFields = <?php echo json_encode($session->current_transcription_def_fields); ?>;
					
					// loop through def fields
					for (var fieldsIndex in defFields) 
						{
							// have I found the iteration with field_type of vo_lume?
							if ( defFields[fieldsIndex]["field_type"] == 'vo_lume' )
								{	
									// get the value of district
									const district = $('#dis_trict').val();								
									// call the php method to get the volume this district, year and quarter
									fetch("<?php echo(base_url('transcribe/get_volume')) ?>", 
										{
											method: "POST",
											headers: 
												{
													"Content-Type": "application/json; charset=UTF-8",
												},
											body: JSON.stringify([district, defFields[fieldsIndex]["volume_roman"]]),
										})
									.then(response => response.text()) // x=${x}&y=${y} get the response as text because JSON doesn't work
									.then(data => 	{
														const myData = data.split('<'); 		// isolate what I want because CI adds a lot of stuff
														var volume = myData[0];					// now get first element of array as volume
														volume = volume.replace(/\"/g, '');		// take out all "
														if ( volume.trim().length !== 0 )
															{
																$('#vo_lume').val(volume);							// set volume
																$('#' + getnextID("volumeFill")).focus();			// and focus next desired input field
															}
														else
															{
																$('#vo_lume').val('');
																$('#vo_lume').focus();
															}
													})
									.catch((error) => 	{
															$('#vo_lume').val('');				// if error, set volume as blank
														})
									break;
								}
						}
				}
		});
	
	// handle click in verify on the fly
	$('#verifyonthefly').on("click touchend", function(e) 
		{
			// if more than one click is detected stop
			if( e.detail > 1 )
				{ 
					e.preventDefault(); 
				}	
		});
	
	window.onkeydown = function(dup)
			{ 
				// key codes are here -> https://www.oreilly.com/library/view/javascript-dhtml/9780596514082/apb.html
				
				// get which key was pressed
				var keyPressed = dup.key;
				var forenamesALL = '';
				
				// test if special key where pressed
				if ( dup.ctrlKey && dup.key === 'r' || dup.ctrlKey && dup.key === 'R') { keyPressed = 'Insert' }
				if ( dup.ctrlKey && dup.key === 'b' || dup.ctrlKey && dup.key === 'B') { keyPressed = 'Back' }
				if ( dup.ctrlKey && dup.key === 'a' || dup.ctrlKey && dup.key === 'A') { keyPressed = 'Insert'; forenamesALL = 'ALL'; }
				
				// if verify on the fly get keys
				verifyOnthefly = <?php echo json_encode($session->verify_onthefly); ?>;
				if ( verifyOnthefly == 1 )
					{
						if ( dup.altKey && dup.key === 'v' || dup.altKey && dup.key === 'V') { keyPressed = 'VerifyConfirm' }
					}
				
				// do actions depending on key press				
				switch (keyPressed)
					{
						case "Insert": // Insert key pressed = duplicate
							// stop the browser getting the key
							dup.preventDefault();
							
							// get the last data element array
							lastEl = <?php echo json_encode($session->lastEl); ?>;

							// get def fields - held in current transcription def fields array
							defFields = <?php echo json_encode($session->current_transcription_def_fields); ?>;
							
							// loop through def fields
							for (var fieldsIndex in defFields) 
								{
									// have I found the iteration with the current ID?
									if ( defFields[fieldsIndex]["field_type"] == dup.target.id )
										{
											// am I processing a forename? If so repeat first forename only except if all fornames requested
											if ( dup.target.id == 'fore_name' && forenamesALL == '' )
												{
													// explode on space
													const forenames = lastEl[defFields[fieldsIndex]["table_fieldname"]].split(" ");
													// remove full stop at end if there is one
													var lastChar = forenames[0].charAt(forenames[0].length - 1);
													// set value
													if ( lastChar == '.' )
														{
															var cleanForename = forenames[0].substr(0, forenames[0].length - 1);
															$('#' + dup.target.id).val(cleanForename + ' ');
														}
													else
														{
															$('#' + dup.target.id).val(forenames[0] + ' ');
														}
												}
											else
												{
													// set value
													$('#' + dup.target.id).val(lastEl[defFields[fieldsIndex]["table_fieldname"]]);
												}
											
											// break loop
											break;
										}
								}	
							break;
							
						case "Home": // Home key pressed = duplicate all
							// stop the browser getting the key
							dup.preventDefault();
							
							// get the last data element array
							var lastEl = <?php echo json_encode($session->lastEl); ?>;
							
							// get def fields - held in current transcription def fields array
							var defFields = <?php echo json_encode($session->current_transcription_def_fields); ?>;
							
							// loop through def fields
							for (var fieldsIndex in defFields) 
								{
									// set value
									$('#' + defFields[fieldsIndex]["field_type"]).val(lastEl[defFields[fieldsIndex]["table_fieldname"]]);
								}
							
							// set last field in data entry and focus
							$('#' + defFields[defFields.length - 1]["field_type"]).val(' ');
							$('#' + defFields[defFields.length - 1]["field_type"]).focus();
							break;
							
						case "End": // end pressed in mother field = duplicate surname to mother name
							// stop the browser getting the key
							dup.preventDefault();
							
							// get def fields - held in current transcription def fields array
							defFields = <?php echo json_encode($session->current_transcription_def_fields); ?>;
							
							// loop through def fields
							for (var fieldsIndex in defFields) 
								{
									// have I found the iteration with the current ID?
									if ( defFields[fieldsIndex]["field_type"] == dup.target.id )
										{
											// only do this if dup_fromfieldname is not blank
											if ( defFields[fieldsIndex]["dup_fromfieldname"] != null )
												{													
													// set value
													$('#' + dup.target.id).val($('#' + defFields[fieldsIndex]["dup_fromfieldname"]).val());
													// get next ID for focus
													// increment index
													fieldsIndex++;
													// check past end of array
													if ( fieldsIndex > defFields.length - 1 )
													{
														fieldsIndex = defFields.length - 1;
													}		
													
													// focus
													$('#' + defFields[fieldsIndex]["html_id"]).focus();
												}
											
											// break loop
											break;
										}
								}	
							break;
							
						case "PageDown": // Page down pressed = advance image by one line
							// stop the browser getting the key
							dup.preventDefault();
							var scrollStep = <?php echo json_encode($session->scroll_step); ?>;
							panzoom.pan(0, -scrollStep, { relative: true } );
							break;
							
						case "PageUp": // Page Up pressed = reverse image by one line
							// stop the browser getting the key
							dup.preventDefault();
							var scrollStep = <?php echo json_encode($session->scroll_step); ?>;
							panzoom.pan(0, scrollStep, { relative: true });
							break;
							
						case "Back": // Control pressed - position cursor at end of previous field
							// stop the browser getting the key
							dup.preventDefault();
							// get the previous ID
							getpreviousID(dup.target.id);
							const inputField = document.querySelector('#' + getpreviousID(dup.target.id));
							const cursorPos = inputField.value.length;
							inputField.focus();
							inputField.setSelectionRange(cursorPos,cursorPos);		 
							break;
							
						case "Tab": // Tab pressed - if in last data entry field stop from tabbing.
							// get def fields - held in current transcription def fields array
							defFields = <?php echo json_encode($session->current_transcription_def_fields); ?>;
							// get cycle code
							var cycleCode = <?php echo json_encode($session->BMD_cycle_code); ?>;
							// was tab pressed in last data entry field?
							// test for last field is different in VERIT because dup.target.id is a constructed id and not just the html_id
							/* if ( cycleCode == "VERIT" )
								{
									// is the html_id on the last field a substring of the composed dup.target.id 
									// if so test returns somthing other than -1
									if ( dup.target.id.indexOf(defFields[defFields.length - 1]['field_type']) !== -1)
										{
											// if so stop tab
											dup.preventDefault();
										}	
								}
							else
								{
									// is last htmlid equal to dup.target.id
									if ( defFields[defFields.length - 1]['field_type'] == dup.target.id )
										{
											// if so stop tab
											dup.preventDefault();
										}
								} */
							break;
							
						case "VerifyConfirm": // alt v pressed - do the verify confirm
							// stop the browser getting the key
							dup.preventDefault();
							// get the in verify flag
							verifyOnthefly = <?php echo json_encode($session->verify_onthefly); ?>;
							// test if in verify
							if ( verifyOnthefly == 1 )
								{
									// create the click event
									var clickEvent = new MouseEvent("click", 
										{
											"view": window,
											"bubbles": true,
											"cancelable": false
										});

									// create the element
									var element = document.getElementById("verifyonthefly");
									// and click it
									element.dispatchEvent(clickEvent);
								}								
							break;
							
						default:
							break;
					}
			};
						
	// function to get the next input field ID
	// current input id is passed as parameter
	// current input fields are held in the PHP array, so parse it to a JS array
	function getnextID(element) 
		{
			// initialise
			var nextID = null;
			
			// if called from volume fill after tab out of district, element == volume
			// next field is defined in the def range array.
			if ( element == 'volumeFill' )
				{
					var defProfile = <?php echo json_encode($session->def_range[0]); ?>;
			
					if ( defProfile['volume_follows_district'] == 'Y' )
						{
							nextID = defProfile['field_after_volume'];
						}
					else
						{
							nextID = defProfile['field_after_district'];
						}
				}
			else
				{
					// initialise
					var nextID = null;
					var nextIndex = 0;
					// if called from insert then def fields are held in current transcription def fields array
					var defFields = <?php echo json_encode($session->current_transcription_def_fields); ?>;
					// get last key
					lastKey = defFields.length - 1;
					for (var fieldsIndex in defFields) 
						{
							// have I found the iteration with the current ID? If so, pass next ID
							if ( defFields[fieldsIndex]["field_type"] == element )
								{
									// increment index
									fieldsIndex++;
									// check past end of array
									if ( fieldsIndex > lastKey )
										{
											fieldsIndex = lastKey;
										}		
									nextID = defFields[fieldsIndex]["field_type"];
									break;
								}
						}
				}
			
			return nextID;
		}
		
	// function to get the previous input field ID
	// current input id is passed as parameter
	// current input fields are held in the PHP array, so parse it to a JS array
	function getpreviousID(element) 
		{
			// initialise
			var previousID = null;
			
			// if called from ArrowLeft then def fields are held in current transcription def fields array
			var defFields = <?php echo json_encode($session->current_transcription_def_fields); ?>;
			for (var fieldsIndex in defFields) 
				{
					// have I found the iteration with the current ID? If so, pass previous ID
					if ( defFields[fieldsIndex]["field_type"] == element )
						{
							previousIndex = fieldsIndex - 1;
							if ( previousIndex < 0 )
								{
									previousIndex = 0;
								}		
							previousID = defFields[previousIndex]["field_type"];
							// console.log(previousID);
							break;
						}
				}
			
			return previousID;
		}

</script>

<script>
	// thanks to - Rob Garrison - https://github.com/Mottie/Keyboard
	// has a virtual keyboard icon been clicked?
	// the keyboard icon has an id constructed from the id of the mother+'_keyboardicon
	$("[id$='_keyboardicon']").click(function(e) 
		{
			// stop the browser getting the key
			e.preventDefault();
			
			// now reveal the keyboard - need to split the icon id to get the input id upon which the keyboard was initialised.
			$("#"+e.target.id.split('_')[0]).getkeyboard().reveal();	
		});
</script>

<script>
var cycleCode = <?php echo json_encode($session->BMD_cycle_code); ?>;
if ( cycleCode == 'INPRO' )
	{
		document.getElementsByClassName("sur_name").onmouseenter = function() 
			{
				document.getElementsByClassName("sur_name").title = document.getElementsByClassName("sur_name").value;
			}
	};
if ( cycleCode == 'VERIT' )
	{
		var index = <?php echo json_encode($session->detail_line['BMD_index']); ?>;
		document.getElementById("sur_name"+index).onmouseenter = function() 
			{
				document.getElementById("sur_name"+index).title = document.getElementById("sur_name"+index).value;
			}
	};
</script>

<script>
	// show collapsing columns depending on different situations
	// get collapsing column titles and decision tree
	var dataGroupTitlesView = Object.entries(<?php echo json_encode($session->data_group_titles_view); ?>);
	var errorDataGroup = <?php echo json_encode($session->error_data_group); ?>;
	var lineEditFlag = <?php echo json_encode($session->line_edit_flag); ?>;
	var verifyOnTheFly = <?php echo json_encode($session->verify_onthefly); ?>;
	
	// first turn off all collapsing columns
	if ( dataGroupTitlesView.length > 0 )
		{
			dataGroupTitlesView.forEach(function(title, index)
				{	
					$('#group_'+index).removeClass('show'); 
				});
		}
	
	// now set the column(s) to reveal - 
	switch ( true )
		{
			// errors = reveal column with error
			case (errorDataGroup != ''):
				$('#'+errorDataGroup).addClass('show');
				break;
				
			// for everything else reveal all columns
			default:
				dataGroupTitlesView.forEach(function(title, index)
					{	
						$('#group_'+title[0]).addClass('show'); 
					});
				break;
		}
				
</script>

<script>
	// expand the notes fields to fit text entered. 
	// Thanks to https://www.codepel.com/html-css/auto-expand-input-width-based-on-text-length/

	// trigger the event
	window.addEventListener("DOMContentLoaded", function() 
		{
			autoExpandInput(".expandable_input");
		});

	// the functions
	(function() 
		{
			function expandElementHeight(element) 
				{
					element.style.height = "auto";
					element.style.height = element.scrollHeight + "px";
				}

			function attachAutoExpand(inputSelector) 
				{
					var elements = document.querySelectorAll(inputSelector);
					elements.forEach(function(element) 
						{
							if (element.tagName.toLowerCase() === "input") 
								{
									element.addEventListener("input", function() 
										{
											this.style.width = (this.value.length + 1) + "ch";
										});
								} 
							else if (element.tagName.toLowerCase() === "textarea") 
								{
									element.addEventListener("textarea", function() 
										{
											expandElementHeight(this);
										});
								}
						});
				}

			window.autoExpandInput = function(inputSelector) 
				{
					attachAutoExpand(inputSelector);
				};
		})();


</script>

<script>
	// detect enter or tab in lastn and search fields
	$("#last_n").on("keydown", function(e) 
		{
			if ( e.key == 'Tab' || e.key == 'Enter' )
				{		
					// initialise
					e.preventDefault();
					lastN();
				}
		});
		
	$("#searchKey").on("keydown", function(e) 
		{
			if ( e.key == 'Tab' || e.key == 'Enter' )
				{		
					// initialise
					e.preventDefault();
					$('#searchMessage').text('Enter a search term to perform a search and then press the Search button.');
				}
		});
	
	// get number of records to show but only after user has pressed go
	// pass number of records to show to php
	function lastN(records = document.getElementById("last_n").value) 
		{
			$('#lastnMessage').text('');
			if ( records <= 0 )
				{
					$('#lastnMessage').text('Number of records to show must be > 0.');
				}
			else
				{
					// load variables to form
					$('#new_last_n').val(records);
					// submit
					$('form[name="set_last_n"]').submit();
				}
		}
		
	function lastNall() 
		{
			lastN(10000);
		}
		
	function searchReset() 
		{
			document.getElementById("searchKey").value = ' ';
			searchHistory();
		}
		
	function searchHistory() 
		{
			// initialise
			var value = document.getElementById("searchKey").value.toLowerCase();	
			if ( value != '' )
				{
					var searchArray = {};
					var defFields = <?php echo json_encode($session->current_used_transcription_def_fields); ?>;
					for (var fieldsIndex in defFields) 
						{
							for ( lineIndex in defFields[fieldsIndex] )
								{
									searchArray[defFields[fieldsIndex][lineIndex]["table_fieldname"]] = value;
								}
						}
					searchArray = JSON.stringify(searchArray);
					$('#searchArray').val(searchArray);

					// submit
					$('form[name="set_search"]').submit();
				}
			else
				{
					$('#searchMessage').text('Enter a search term to perform a search and then press the Search button.');
					document.getElementById("searchKey").value = '';
				}
		}

			// debounce waits the number of milliseconds before calling the last_n function.
			// see here: https://www.freecodecamp.org/news/debounce-explained-how-to-make-your-javascript-wait-for-your-user-to-finish-typing-2/
			//function debounce( callback, delay ) 
				//{
					//let timeout;
					//return function() 
						//{
							//clearTimeout( timeout );
							//timeout = setTimeout( callback, delay );
						//}
				//}

</script>

<script>

// get search elements
function getSearch() 
	{
		// get search inputs
		const matches = Array.from(document.querySelectorAll('[id^=search-]'));
		// read search inputs and create array
		var searchArray = {};
		matches.forEach((currentElement, index) => 
			{ 
				const motherArray = currentElement.id.split("-");
				searchArray[motherArray[1]] = currentElement.value;
			})
		// load variables to form
		searchArray = JSON.stringify(searchArray);
		$('#searchArray').val(searchArray);
		// submit
		$('form[name="set_search"]').submit();			
	}

</script>




