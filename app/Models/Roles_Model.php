<?php namespace App\Models;

use CodeIgniter\Model;

class Roles_Model extends Model
{
    protected $table = 'roles';
    protected $primaryKey = 'role_index';
    protected $allowedFields =	[
									'role_index',
									'role_name', 
									'role_precedence',
								];
    protected $returnType = 'array';
}
