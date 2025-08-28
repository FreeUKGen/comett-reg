	<?php $session = session(); ?>
	
	<div class="row">
		<p class="bg-danger col-12 pl-0 text-center" style="font-size:1vw;">You are a  FreeComETT DATABASE ADMINISTRATOR. Here are tasks you can perform. BE CAREFUL!</p>
	</div>
	
	<div class="row">
		<label for="manage_districts" class="col-8 pl-0">Manage Districts and Volumes</label>
		<a id="manage_districts" class="btn btn-outline-primary btn-sm col-4" href="<?php echo(base_url('district/manage_districts/0')) ?>">
		<span>Manage Districts and Volumes</span>
		</a>
	</div>
	
	<div class="row">
		<label for="manage_parameters" class="col-8 pl-0">Manage FreeComETT Global Parameters</label>
		<a id="manage_parameters" class="btn btn-outline-primary btn-sm col-4" href="<?php echo(base_url('parameter/manage_parameters_step1/0')) ?>">
		<span>Manage FreeComETT Global Parameters</span>
		</a>
	</div>
	
	<div class="row">
		<label for="manage_projects" class="col-8 pl-0">Manage FreeComETT Projects</label>
		<a id="manage_parameters" class="btn btn-outline-primary btn-sm col-4" href="<?php echo(base_url('projects/manage_projects_step1/0')) ?>">
		<span>Manage FreeComETT Projects</span>
		</a>
	</div>	
	
	<div class="row">
		<label for="manage_def_image" class="col-8 pl-0">Manage FreeComETT Def_Images - add syndicate records</label>
		<a id="manage_def_image" class="btn btn-outline-primary btn-sm col-4" href="<?php echo(base_url('database/add_syndicate_to_def_image_table')) ?>">
		<span>Manage FreeComETT Def_Images Table - BE PATIENT!</span>
		</a>
	</div>
	
	<div class="row">
		<label for="manage_def_image" class="col-8 pl-0">Manage FreeComETT Def_Fields - add syndicate records</label>
		<a id="manage_def_image" class="btn btn-outline-primary btn-sm col-4" href="<?php echo(base_url('database/add_syndicate_to_def_fields_table')) ?>">
		<span>Manage FreeComETT Def_Fields Table - BE VERY PATIENT!</span>
		</a>
	</div>
	
	<div class="row">
		<label for="manage_def_image" class="col-8 pl-0">Update role index for coords in Identity table</label>
		<a id="manage_def_image" class="btn btn-outline-primary btn-sm col-4" href="<?php echo(base_url('database/set_coord_role')) ?>">
		<span>Set Role Index for existing users in FreeComETT Identity table</span>
		</a>
	</div>
	
	<div class="row">
		<label for="phpinfo" class="col-8 pl-0">Show PHP Info</label>
		<a id="manage_def_image" class="btn btn-outline-primary btn-sm col-4 d-flex flex-column align-items-center" href="<?php echo(base_url('phpinfo')) ?>">
		<span>Show PHP info</span>
		</a>
	</div>
	
	<div class="row">
		<label for="mongoinfo" class="col-8 pl-0">Show Mongodb Info</label>
		<a id="manage_def_image" class="btn btn-outline-primary btn-sm col-4 d-flex flex-column align-items-center" href="<?php echo(base_url('phpmongo')) ?>">
		<span>Show MongoDB info</span>
		</a>
	</div>
	
	<div class="row">
		<label for="manage_def_image" class="col-8 pl-0">Delete FreeComETT data for a user.</label>
		<a id="delete_user_data" class="btn btn-outline-primary btn-sm col-4" href="<?php echo(base_url('database/delete_user_data_step1/0')) ?>">
		<span>Delete FreeComETT data for a user</span>
		</a>
	</div>
	
	<div class="row">
		<label for="manage_def_image" class="col-8 pl-0">FreeREG ONLY - Manage Occupations.</label>
		<a id="manage_def_image" class="btn btn-outline-primary btn-sm col-4" href="<?php echo(base_url('occupation/manage_occupations/0')) ?>">
		<span>FreeREG ONLY - Manage Occupations</span>
		</a>
	</div>
	
	<div class="row">
		<label for="manage_def_image" class="col-8 pl-0">FreeREG ONLY - Manage Parishes.</label>
		<a id="manage_def_image" class="btn btn-outline-primary btn-sm col-4" href="<?php echo(base_url('parish/manage_parishes/0')) ?>">
		<span>FreeREG ONLY - Manage Parishes</span>
		</a>
	</div>
	
	<div class="row">
		<label for="manage_def_image" class="col-8 pl-0">FreeREG ONLY - Manage Conditions.</label>
		<a id="manage_def_image" class="btn btn-outline-primary btn-sm col-4" href="<?php echo(base_url('condition/manage_conditions/0')) ?>">
		<span>FreeREG ONLY - Manage Conditions</span>
		</a>
	</div>
	
	<div class="row">
		<label for="manage_def_image" class="col-8 pl-0">FreeREG ONLY - Manage Titles.</label>
		<a id="manage_def_image" class="btn btn-outline-primary btn-sm col-4" href="<?php echo(base_url('title/manage_titles/0')) ?>">
		<span>FreeREG ONLY - Manage Titles</span>
		</a>
	</div>
	
	<div class="row">
		<label for="manage_def_image" class="col-8 pl-0">FreeREG ONLY - Manage Licences.</label>
		<a id="manage_def_image" class="btn btn-outline-primary btn-sm col-4" href="<?php echo(base_url('licence/manage_licences/0')) ?>">
		<span>FreeREG ONLY - Manage Licences</span>
		</a>
	</div>
	
	<div class="row">
		<label for="manage_def_image" class="col-8 pl-0">FreeREG ONLY - Manage Relationships.</label>
		<a id="manage_def_image" class="btn btn-outline-primary btn-sm col-4" href="<?php echo(base_url('relationship/manage_relationships/0')) ?>">
		<span>FreeREG ONLY - Manage Relationships</span>
		</a>
	</div>
	
	<div class="row">
		<label for="manage_def_image" class="col-8 pl-0">FreeREG ONLY - Manage Person Statuses.</label>
		<a id="manage_def_image" class="btn btn-outline-primary btn-sm col-4" href="<?php echo(base_url('person_status/manage_person_statuses/0')) ?>">
		<span>FreeREG ONLY - Manage Person Statuses</span>
		</a>
	</div>
	
	<div class="row">
		<label for="manage_def_image" class="col-8 pl-0">FreeREG ONLY - Manage Document Sources.</label>
		<a id="manage_def_image" class="btn btn-outline-primary btn-sm col-4" href="<?php echo(base_url('document_sources/manage_document_sources/0')) ?>">
		<span>FreeREG ONLY - Manage Document Sources</span>
		</a>
	</div>
	
	<div class="row">
		<label for="manage_def_image" class="col-8 pl-0">FreeREG ONLY - List all churches.</label>
		<a id="manage_def_image" class="btn btn-outline-primary btn-sm col-4" href="<?php echo(base_url('database/list_all_churches/')) ?>">
		<span>FreeREG ONLY - List all churches</span>
		</a>
	</div>
	
	<div class="row">
		<label for="manage_def_image" class="col-8 pl-0">FreeREG ONLY - Refresh FreeComETT data dictionary from FreeREG.</label>
		<a id="manage_def_image" class="btn btn-outline-primary btn-sm col-4" href="<?php echo(base_url('database/freereg_build_datadictionary')) ?>">
		<span>FreeREG ONLY - Refresh FreeComETT data dictionary from FreeREG</span>
		</a>
	</div>
	
	<div class="row">
		<label for="manage_data_dictionary" class="col-8 pl-0">FreeREG ONLY - Manually Modify FreeComETT Data Dictionary.</label>
		<a id="manage_data_dictionary" class="btn btn-outline-primary btn-sm col-4" href="<?php echo(base_url('data_dictionary/manage_data_dictionary/0')) ?>">
		<span>FreeREG ONLY - Manually Modify FreeComETT Data Dictionary</span>
		</a>
	</div>
	
	<div class="row">
		<label for="manage_data_dictionary" class="col-8 pl-0">FreeREG ONLY - Manage Predefined data entry layouts.</label>
		<a id="manage_data_dictionary" class="btn btn-outline-primary btn-sm col-4" href="<?php echo(base_url('predefined_layouts/manage_predefined_layouts/0')) ?>">
		<span>FreeREG ONLY - Manage Predefined data entry layouts</span>
		</a>
	</div>
	
	<div class="row">
		<label for="create_report_data" class="col-6 pl-0">Build / Rebuild report data. BE PATIENT! NOTE - you don't have to do this each time you want to look at the report. Use in exceptional circumstances only. Do you know what you are doing?</label>
		<input class="col-2" type="password" id="report_password" placeholder="Enter rebuild password">
		<button class="btn btn-outline-primary btn-sm col-4 d-flex flex-column align-items-center" onclick="buildReportData()" id="create_report_data">Build / Rebuild report data?
			<span class="spinner-border"  role="status">
				<span class="sr-only">Loading...</span>
			</span>
		</button>
	</div>
		
	<br>
	
	<div class="row mt-4 d-flex justify-content-between">	
		<a id="return" class="btn btn-primary mr-0 flex-column align-items-center" href="<?php echo(base_url('housekeeping/index/0')); ?>">
			<?php echo $session->current_project[0]['back_button_text']?>
		</a>
	</div>
	
	<div>
		<form action="<?=(base_url('report/create_report_data/0')); ?>" method="POST" name="form_report_rebuild" >
			<input name="report_rebuild_password" id="report_rebuild_password" type="hidden" />
		</form>
	</div>
	
	<script type="text/javascript">
		$( document ).ready(function() 
		{	
			let create_report_data = $('#create_report_data');
			create_report_data.on("click",function()
				{
					let spinner = $('.spinner-border');
					spinner.addClass("active");
				});
		});
		
	function buildReportData() 
		{
			// load variables to form
			$('#report_rebuild_password').val(document.getElementById("report_password").value);
				
			// and submit the form
			$('form[name="form_report_rebuild"]').submit();	
		}
		
	</script>
