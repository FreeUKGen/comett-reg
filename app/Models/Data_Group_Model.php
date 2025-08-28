<?php namespace App\Models;

use CodeIgniter\Model;

class Data_Group_Model extends Model
{
    protected $table = 'data_groups';
    protected $primaryKey = 'data_group_index';
    protected $allowedFields = ['project_index', 'data_group_number', 'data_group_title', 'data_roup_type', 'data_set'];
    protected $returnType = 'array';
}
