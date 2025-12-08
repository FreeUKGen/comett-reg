<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
//$routes->setTranslateURIDashes(false);
//$routes->set404Override();
//$routes->setAutoRoute(true);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Home::index');

// home routes group
$routes->group
	("home", ['filter' => 'sessionexists'], function($routes)
		{
			$routes->post('test_javascript', 'Home::test_javascript');
			$routes->get('no_javascript', 'Home::no_javascript');
			$routes->get('signout', 'Home::signout');
			$routes->get('close', 'Home::close');

			// DS CI v4.6
			$routes->get('index', 'Home::index');
			$routes->get('home', 'Home::index');
            $routes->get('issue_step1/(:segment)', 'Home::issue_step1/$1');
            $routes->post('issue_step2', 'Home::issue_step2');
            $routes->get('issue_see/(:segment)', 'Home::issue_see/$1');
            $routes->post('session_exists', 'Home::session_exists');

		}
	);
	
// help routes group
$routes->group
	("help", ['filter' => 'sessionexists'], function($routes)
		{
			$routes->get('help_show/(:segment)', 'Help::help_show/$1');
			$routes->get('help_manage/(:segment)', 'Help::help_manage/$1');
			$routes->get('help_change_step1/(:segment)', 'Help::help_change_step1/$1');
			$routes->get('help_change_step2', 'Help::help_change_step2');
			$routes->get('help_create_step1/(:segment)', 'Help::help_create_step1/$1');
			$routes->get('help_create_step2', 'Help::help_create_step2');
		}
	);
	
// identity routes group
$routes->group
	("identity", ['filter' => 'sessionexists'], function($routes)
		{
			$routes->get('signin_step1/(:segment)', 'Identity::signin_step1/$1');
			$routes->get('signin_step3', 'Identity::signin_step3');
			$routes->post('signin_step2', 'Identity::signin_step2');
			$routes->get('admin_user_step1/(:segment)', 'Identity::admin_user_step1/$1');
			$routes->get('delete_user_data_step1/(:segment)', 'Identity::delete_user_data_step1/$1');
			$routes->post('delete_user_data_step2/(:segment)', 'Identity::delete_user_data_step2/s1');
			$routes->post('delete_user_data_step3', 'Identity::delete_user_data_step3');
			// DS new routes
			$routes->post('change_details_step2/(:segment)', 'Identity::change_details_step2/$1');
			$routes->get('change_details_step2/(:segment)', 'Identity::change_details_step2/$1');
		}
	);

// transcribe routes group
$routes->group
	("transcribe", ['filter' => 'sessionexists'], function($routes)
		{
			$routes->get('/', 'Transcribe::index');
			$routes->get('transcribe_step1/(:segment)', 'Transcribe::transcribe_step1/$1');
			$routes->post('next_action', 'Transcribe::transcribe_next_action');
			$routes->get('create_BMD_file/(:segment)', 'Transcribe::create_BMD_file/$1');
			$routes->get('upload_BMD_file', 'Transcribe::upload_BMD_file');
			$routes->get('submit_details/(:segment)', 'Transcribe::submit_details/$1');
			$routes->get('close_header_step1/(:segment)', 'Transcribe::close_header_step1/$1');
			$routes->post('close_header_step2', 'Transcribe::close_header_step2');
			$routes->get('verify_BMD_file_step1/(:segment)', 'Transcribe::verify_BMD_file_step1/$1');
			$routes->get('verify_BMD_trans_step1/(:segment)', 'Transcribe::verify_BMD_trans_step1/$1');
			$routes->get('search_synonyms', 'Transcribe::search_synonyms');
			$routes->get('search_districts', 'Transcribe::search_districts');
			$routes->get('search_volumes', 'Transcribe::search_volumes');
			$routes->get('search_firstnames', 'Transcribe::search_firstnames');
			$routes->get('search_surnames', 'Transcribe::search_surnames');
			$routes->get('update_firstnames/(:segment)', 'Transcribe::update_firstnames/$1');
			$routes->get('update_surnames/(:segment)', 'Transcribe::update_surnames/$1');
			$routes->get('image_parameters_step1/(:segment)', 'Transcribe::image_parameters_step1/$1');
			$routes->post('image_parameters_step2/(:segment)', 'Transcribe::image_parameters_step2/$1');
			$routes->get('enter_parameters_step/(:segment)', 'Transcribe::enter_parameters_step/$1');
			$routes->get('enter_parameters_step1/(:segment)', 'Transcribe::enter_parameters_step1/$1');
			$routes->post('reset_defaults', 'Transcribe::reset_defaults');
			$routes->get('toggle_line_step1/(:segment)', 'Transcribe::toogle_line_step1/$1');
			$routes->post('toogle_line_step2', 'Transcribe::toogle_line_step2');
			$routes->post('insert_line_step1/(:segment)', 'Transcribe::insert_line_step1/$1');
			$routes->get('toogle_transcriptions', 'Transcribe::toogle_transcriptions');
			$routes->get('calibrate_step1/(:segment)', 'Transcribe::calibrate_step1/$1');
			$routes->post('calibrate_step2', 'Transcribe::calibrate_step2');
			$routes->post('sort/(:segment)', 'Transcribe::sort/$1');
			$routes->get('message_to_coord_step1/(:segment)', 'Transcribe::message_to_coord_step1/$1');
			$routes->post('message_to_coord_step2', 'Transcribe::message_to_coord_step2');
			$routes->post('verify_onthefly_confirm', 'Transcribe::verify_onthefly_confirm');

			// DS CI 4.6
			$routes->get('transcribe_next_action', 'Transcribe::transcribe_next_action');
			$routes->post('change_layout', 'Transcribe::change_layout');
			$routes->post('transcribe_step1/(:segment)', 'Transcribe::transcribe_step1/$1');
			$routes->get('show_raw_BMD_file/(:segment)', 'Transcribe::show_raw_BMD_file/$1');
			$routes->get('store_BMD_file/(:segment)', 'Transcribe::store_BMD_file/$1');
			$routes->get('submit_details', 'Transcribe::submit_details');
			$routes->get('send_BMD_file_to_syndicate_leader', 'Transcribe::send_BMD_file_to_syndicate_leader');
            $routes->get('set_last_n', 'Transcribe::set_last_n');
            $routes->post('set_search', 'Transcribe::set_search');
            $routes->get('set_search', 'Transcribe::set_search');
			$routes->post('enter_parameters_step', 'Transcribe::enter_parameters_step');

            // HBW new routes for CI4.6
            $routes->get('calibrate_reference_step0/(:segment)', 'Transcribe::calibrate_reference_step0/$1');
            $routes->get('calibrate_reference_step1/(:segment)', 'Transcribe::calibrate_reference_step1/$1');
            $routes->post('calibrate_reference_step1/(:segment)', 'Transcribe::calibrate_reference_step1/$1');
            $routes->get('calibrate_reference_step2', 'Transcribe::calibrate_reference_step2');
            $routes->get('verify_onthefly', 'Transcribe::verify_onthefly');
            $routes->get('verify_onthefly_confirm', 'Transcribe::verify_onthefly_confirm');
            $routes->get('inherit_parameters', 'Transcribe::inherit_parameters');
            $routes->get('verify_step1/(:segment)', 'Transcribe::verify_step1/$1');
            $routes->get('toogle_line_step1/(:segment)', 'Transcribe::toogle_line_step1/$1');
            $routes->get('insert_line_step1/(:segment)', 'Transcribe::insert_line_step1/$1');
            $routes->post('enter_parameters_step1/(:segment)', 'Transcribe::enter_parameters_step1/$1');
            $routes->post('image_parameters_step1/(:segment)', 'Transcribe::image_parameters_step1/$1');

		}
	);

// transcription routes group
$routes->group
	("transcription", ['filter' => 'sessionexists'], function($routes)
		{
			$routes->get('create_BMD_step1/(:segment)', 'Transcription::create_BMD_step1/$1');
			$routes->post('create_BMD_step2', 'Transcription::create_BMD_step2');
			$routes->get('reopen_BMD_step1/(:segment)', 'Transcription::reopen_BMD_step1/$1');
			$routes->post('reopen_BMD_step2', 'Transcription::reopen_BMD_step2');
			$routes->get('download_transcription_step1/(:segment)', 'Transcription::download_transcription_step1/$1');
			$routes->post('download_transcription_step2', 'Transcription::download_transcription_step2');
			$routes->get('delete/(:segment)', 'Transcription::delete/$1');

			// DS
			$routes->get('FreeREG_get_data_entry_format', 'Transcription::FreeREG_get_data_entry_format');
			$routes->post('update_data_entry_format', 'Transcription::update_data_entry_format');
			$routes->get('comments_step1/(:segment)',  'Transcription::comments_step1/$1');
			$routes->post('comments_step2', 'Transcription::comments_step2');
		}
	);
	
// allocation routes group
$routes->group
	("allocation", ['filter' => 'sessionexists'], function($routes)
		{
			$routes->get('create_allocation_step1/(:segment)', 'Allocation::create_allocation_step1/$1');
			$routes->post('create_allocation_step2', 'Allocation::create_allocation_step2');
			$routes->get('manage_allocations/(:segment)', 'Allocation::manage_allocations/$1');
			$routes->post('next_action', 'Allocation::next_action');
			$routes->post('sort/(:segment)', 'Allocation::sort/$1');
			
			// DS added routes
			$routes->get('list_images', 'Allocation::list_images');
			$routes->post('create_assignment_step1/(:segment)', 'Allocation::create_assignment_step1/$1');
			$routes->post('create_assignment_step2/(:segment)', 'Allocation::create_assignment_step2/$1');
			$routes->post('load_csv_file_step1/(:segment)', 'Allocation::load_csv_file_step1/$1');
			$routes->get('toogle_allocations', 'Allocation::toogle_allocations');
			$routes->post('get_places', 'Allocation::get_places');
			$routes->post('get_churches', 'Allocation::get_churches');
			$routes->get('manage_allocations/(:segment)', 'Allocation::manage_allocations/$0');
			$routes->post('manage_allocations/(:segment)', 'Allocation::manage_allocations/$0');
			$routes->get('close_freereg_assignment_step1/(:segment)/(:segment)', 'Allocation::close_freereg_assignment_step1/$1/$2');
			$routes->post('close_freereg_assignment_step2', 'Allocation::close_freereg_assignment_step2');
			$routes->get('change_assignment_step1/(:segment)', 'Allocation::change_assignment_step1/$1');
			$routes->post('change_assignment_step2/(:segment)', 'Allocation::change_assignment_step2/$1');
			$routes->post('load_csv_file_step2', 'Allocation::load_csv_file_step2');

			// DS NEW
			$routes->get('new_create_assignment', 'Allocation::new_create_assignment');
			// DS TODO - should be GET not POST
			$routes->post('doublons', 'Allocation::doublons');
			$routes->get('new_list_images', 'Allocation::new_list_images');
		}
	);

// births routes group
$routes->group
	("births", ['filter' => 'sessionexists'], function($routes)
		{
			$routes->get('transcribe_births_step1/(:segment)', 'Births::transcribe_births_step1/$1');
			$routes->post('transcribe_births_step2', 'Births::transcribe_births_step2');
			$routes->get('select_line/(:segment)', 'Births::select_line/$1');
			$routes->get('delete_line_step1/(:segment)', 'Births::delete_line_step1/$1');
			$routes->post('delete_line_step2', 'Births::delete_line_step2');
			$routes->get('comment_step1/(:segment)', 'Births::comment_step1/$1');
			$routes->post('comment_step2', 'Births::comment_step2');
			$routes->get('select_comment/(:segment)', 'Births::select_comment/$1');
			$routes->get('remove_comments/(:segment)/(:segment)', 'Births::remove_comments/$1/$2');
		}
	);

// marriages routes group
$routes->group
	("marriages", ['filter' => 'sessionexists'], function($routes)
		{
			$routes->get('transcribe_marriages_step1/(:segment)', 'Marriages::transcribe_marriages_step1/$1');
			$routes->post('transcribe_marriages_step2', 'Marriages::transcribe_marriages_step2');
			$routes->get('select_line/(:segment)', 'Marriages::select_line/$1');
			$routes->get('delete_line_step1/(:segment)', 'Marriages::delete_line_step1/$1');
			$routes->post('delete_line_step2', 'Marriages::delete_line_step2');
			$routes->get('comment_step1/(:segment)', 'Marriages::comment_step1/$1');
			$routes->post('comment_step2', 'Marriages::comment_step2');
			$routes->get('select_comment/(:segment)', 'Marriages::select_comment/$1');
			$routes->get('remove_comments/(:segment)/(:segment)', 'Marriages::remove_comments/$1/$2');
		}
	);

// deaths routes group
$routes->group
	("deaths", ['filter' => 'sessionexists'], function($routes)
		{
			$routes->get('transcribe_deaths_step1/(:segment)', 'Deaths::transcribe_deaths_step1/$1');
			$routes->post('transcribe_deaths_step2', 'Deaths::transcribe_deaths_step2');
			$routes->get('select_line/(:segment)', 'Deaths::select_line/$1');
			$routes->get('delete_line_step1/(:segment)', 'Deaths::delete_line_step1/$1');
			$routes->post('delete_line_step2', 'Deaths::delete_line_step2');
			$routes->get('comment_step1/(:segment)', 'Deaths::comment_step1/$1');
			$routes->post('comment_step2', 'Deaths::comment_step2');
			$routes->get('select_comment/(:segment)', 'Deaths::select_comment/$1');
			$routes->get('remove_comments/(:segment)/(:segment)', 'Deaths::remove_comments/$1/$2');
            // DS CI 4.6
            $routes->post('transcribe_deaths_step1/(:segment)', 'Deaths::transcribe_deaths_step1/$1');
		}
	);

// housekeeping routes group
$routes->group
	("housekeeping", ['filter' => 'sessionexists'], function($routes)
		{
			$routes->get("index/(:segment)", "Housekeeping::index/$1");
			$routes->get("export_names", "Housekeeping::export_names");
			$routes->get("import_names", "Housekeeping::import_names");
			$routes->get("firstnames", "Housekeeping::firstnames");
			$routes->get("surnames", "Housekeeping::surnames");
			$routes->get("phpinfo", "Housekeeping::phpinfo");
			$routes->get("merge_names", "Housekeeping::merge_names");
			$routes->get("create_header_data_entry_dimensions", "Housekeeping::create_header_data_entry_dimensions");
		}
	);
	
// syndicate routes group
$routes->group
	("syndicate", ['filter' => 'sessionexists'], function($routes)
		{
			$routes->get('refresh_syndicates', 'Syndicate::refresh_syndicates');
			$routes->get('manage_syndicates/(:segment)', 'Syndicate::manage_syndicates/$1');
			$routes->post('next_action', 'Syndicate::next_action');


            // HBW new routes for CI4.6
            $routes->get('manage_users_step1/(:segment)', 'Syndicate::manage_users_step1/$1');
            $routes->get('show_all_allocations_step1/(:segment)', 'Syndicate::show_all_allocations_step1/$1');
            $routes->get('show_all_transcriptions_step1/(:segment)', 'Syndicate::show_all_transcriptions_step1/$1');
		}
	);

// surname routes group
$routes->group
	("surname", ['filter' => 'sessionexists'], function($routes)
		{
			$routes->get('manage_surnames/(:segment)', 'Surname::manage_surnames/$1');
			$routes->get('search', 'Surname::search');
			$routes->get('correct_surname_step1/(:segment)', 'Surname::correct_surname_step1/$1');
			$routes->get('correct_surname_step2', 'Surname::correct_surname_step2');
		}
	);

// firstname routes group
$routes->group
	("firstname", ['filter' => 'sessionexists'], function($routes)
		{
			$routes->get('manage_firstnames/(:segment)', 'Firstname::manage_firstnames/$1');
			$routes->get('search', 'Firstname::search');
			$routes->get('correct_firstname_step1/(:segment)', 'Firstname::correct_firstname_step1/$1');
			$routes->get('correct_Firstname_step2', 'Firstname::correct_firstname_step2');
		}
	);

// projects routes group
$routes->group
	("projects", ['filter' => 'sessionexists'], function($routes)
		{
			$routes->get('load_project/(:segment)', 'Projects::load_project/$1');

            // HBW new routes for CI4.6
            $routes->get('manage_projects_step1/(:segment)', 'Projects::manage_projects_step1/$1');
            $routes->get('manage_projects_step2/(:segment)', 'Projects::manage_projects_step2/$1');
            $routes->post('manage_projects_step3', 'Projects::manage_projects_step3');

		}
	);
	
// database routes group
$routes->group
	("database", ['filter' => 'sessionexists'], function($routes)
		{
			$routes->get('database_step1/(:segment)', 'Database::database_step1/$1');
			$routes->get('def_step1/(:segment)', 'Database::def_step1/$1');
			$routes->post('def_step2', 'Database::def_step2');

            // HBW new routes for CI4.6
            $routes->get('coord_step1/(:segment)', 'Database::coord_step1/$1');
            $routes->get('tester_step1/(:segment)', 'Database::tester_step1/$1');
            $routes->get('add_syndicate_to_def_image_table', 'Database::add_syndicate_to_def_image_table');
            $routes->get('add_syndicate_to_def_fields_table', 'Database::add_syndicate_to_def_fields_table');
            $routes->get('set_coord_role', 'Database::set_coord_role');
            $routes->get('delete_user_data_step1/(:segment)', 'Database::delete_user_data_step1/$1');
            $routes->get('delete_user_data_step2/(:segment)', 'Database::delete_user_data_step2/$1');
            $routes->post('delete_user_data_step3', 'Database::delete_user_data_step3');
            $routes->post('database_step1/(:segment)', 'Database::database_step1/$1');
            $routes->get('update_def_fields', 'Database::update_def_fields');

		}
	);

// DS Added 14 Nov 2025
$routes->post('file/upload', 'File::upload');

// DS Added 15 Nov 2025
$routes->get('issue', 'Issue::index');
$routes->post('issue/create', 'Issue::create');

// DS Added 18 Nov 2025
$routes->post('image/rotate', 'Image::rotate');

// DS Added 24 Nov 2025
$routes->get('allocation/load_csv_file', 'Allocation::load_csv_file');

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}