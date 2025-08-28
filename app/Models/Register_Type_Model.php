<?php namespace App\Models;

use CodeIgniter\Model;

class Register_Type_Model extends Model
{
    protected $table = 'register_type';
    protected $primaryKey = 'register_index';
    protected $allowedFields =	[
									'project_index',
									'register_code',
									'register_description',
									'register_order',
									'register_active',
									'register_alternative',
									'Change_date',
								];
    protected $returnType = 'array';
}
