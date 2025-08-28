<?php namespace App\Models;

use CodeIgniter\Model;

class Title_Model extends Model
{
    protected $table = 'titles';
    protected $primaryKey = 'Title';
    protected $allowedFields = ['Title', 'Title_popularity'];
    protected $returnType = 'array';
}
