<?php namespace App\Models;

use CodeIgniter\Model;

class Speedtest_Results_Model extends Model
{
    protected $table = 'speedtest_results';
    protected $primaryKey = 'index';
    protected $allowedFields = 	[
								'project_index',
								'identity_index', 
								'timestamp',
								'ip',
								'ispinfo',
								'distance',
								'dl',
								'ul',
								'ping',
								'jitter',								
								];
    protected $returnType = 'array';
}
