<?php namespace App\Models;

use CodeIgniter\Model;

class Project_DB_Model extends Model
{
    protected $table = 'project_DB';
    protected $primaryKey = 'record_index';
    protected $allowedFields = 	[	'project_index',
									'environment',
									'DB_hostname',
									'DB_username',
									'DB_password',								
									'DB_database', 
									'DB_hostport',
									'DB_driver',
									'DB_lastknown',
									'change_date',
								];
    protected $returnType = 'array';
}
