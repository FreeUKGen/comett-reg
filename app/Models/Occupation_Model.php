<?php namespace App\Models;

use CodeIgniter\Model;

class Occupation_Model extends Model
{
    protected $table = 'occupations';
    protected $primaryKey = 'Occupation';
    protected $allowedFields = ['Occupation', 'Occupation_popularity'];
    protected $returnType = 'array';
}
