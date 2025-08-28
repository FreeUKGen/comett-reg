<?php namespace App\Models;

use CodeIgniter\Model;

class Validation_Sets_Model extends Model
{
    protected $table = 'validation_sets';
    protected $primaryKey = 'validation_index';
    protected $allowedFields = ['project_index', 'validation_index', 'validation_set', 'validation_test', 'validation_value'];
    protected $returnType = 'array';
}
