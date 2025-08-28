<?php namespace App\Models;

use CodeIgniter\Model;

class Parish_Model extends Model
{
    protected $table = 'parishes';
    protected $primaryKey = 'Parish';
    protected $allowedFields = ['Parish', 'Parish_popularity'];
    protected $returnType = 'array';
}
