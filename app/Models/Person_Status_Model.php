<?php namespace App\Models;

use CodeIgniter\Model;

class Person_Status_Model extends Model
{
    protected $table = 'person_status';
    protected $primaryKey = 'Person_status';
    protected $allowedFields = ['Person_status', 'Person_status_popularity'];
    protected $returnType = 'array';
}
