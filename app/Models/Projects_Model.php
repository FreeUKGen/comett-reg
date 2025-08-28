<?php namespace App\Models;

use CodeIgniter\Model;

class Projects_Model extends Model
{
    protected $table = 'projects';
    protected $primaryKey = 'project_index';
    protected $allowedFields = 	[	'project_index',
									'project_name',
									'project_desc',
									'project_pathtoicon',
									'project_iconname',								
									'project_autouploadurllive', 
									'project_autouploadurltest',
									'back_button_text',
									'submit_button_text',
									'allocation_text',
									'environment',
									'project_status',
									'DB_hostname',
									'DB_username',
									'DB_password',
									'DB_database',
									'DB_hostport',
									'DB_driver',
									'DB_last_known',
									'syndicate_refresh',
									'signons_to_project',
								];
    protected $returnType = 'array';
}
