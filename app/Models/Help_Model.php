<?php namespace App\Models;

use CodeIgniter\Model;

class Help_Model extends Model
{
    protected $table = 'help';
    protected $primaryKey = 'help_index';
    protected $allowedFields = 	[	
									'help_sequence',
									'help_project',
									'help_category', 
									'help_title', 
									'help_url',
									'help_permanent', 
									'Change_date' 
								];
    protected $returnType = 'array';
}
