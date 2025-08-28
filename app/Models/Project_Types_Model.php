<?php namespace App\Models;

use CodeIgniter\Model;

class Project_Types_Model extends Model
{
    protected $table = 'project_types';
    protected $primaryKey = 'type_index';
    protected $allowedFields = ['project_index', 'type_index', 'type_code', 'fr_type_code', 'type_name_lower', 'type_name_upper', 'type_desc', 'type_controller'];
    protected $returnType = 'array';
}
