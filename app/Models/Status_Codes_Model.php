<?php namespace App\Models;

use CodeIgniter\Model;

class Status_Codes_Model extends Model
{
    protected $table = 'status_codes';
    protected $primaryKey = 'status_code_index';
    protected $allowedFields = 	[	
									'project_index',
									'status_project_code',
									'status_text', 
									'status_freecomett_code',  
									'Change_date' 
								];
    protected $returnType = 'array';
}
