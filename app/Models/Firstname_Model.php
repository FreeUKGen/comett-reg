<?php namespace App\Models;

use CodeIgniter\Model;

class Firstname_Model extends Model
{
    protected $table = 'firstnames';
    protected $primaryKey = 'Firstname';
    protected $allowedFields = ['Firstname', 'Firstname_popularity'];
    protected $returnType = 'array';
}
