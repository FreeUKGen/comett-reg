<?php namespace App\Models;

use CodeIgniter\Model;

class Relationship_Model extends Model
{
    protected $table = 'relationships';
    protected $primaryKey = 'Relationship';
    protected $allowedFields = ['Relationship', 'Relationship_popularity'];
    protected $returnType = 'array';
}
