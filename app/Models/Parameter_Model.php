<?php namespace App\Models;

use CodeIgniter\Model;

class Parameter_Model extends Model
{
    protected $table = 'parameters';
    protected $primaryKey = 'Parameter_key';
    protected $allowedFields = ['Parameter_key', 'Parameter_value'];
    protected $returnType = 'array';
}
