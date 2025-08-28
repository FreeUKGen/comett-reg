<?php namespace App\Models;

use CodeIgniter\Model;

class Licence_Model extends Model
{
    protected $table = 'licences';
    protected $primaryKey = 'Licence';
    protected $allowedFields = ['Licence', 'Licence_popularity'];
    protected $returnType = 'array';
}
