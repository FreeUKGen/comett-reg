<?php namespace App\Models;

use CodeIgniter\Model;

class Condition_Model extends Model
{
    protected $table = 'conditions';
    protected $primaryKey = 'Condition';
    protected $allowedFields = ['Condition', 'Condition_popularity', 'condition_sex'];
    protected $returnType = 'array';
}
