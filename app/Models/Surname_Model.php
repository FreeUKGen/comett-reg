<?php namespace App\Models;

use CodeIgniter\Model;

class Surname_Model extends Model
{
    protected $table = 'surnames';
    protected $primaryKey = 'Surname';
    protected $allowedFields = ['Surname', 'Surname_popularity'];
    protected $returnType = 'array';
}
