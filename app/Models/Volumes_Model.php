<?php namespace App\Models;

use CodeIgniter\Model;

class Volumes_Model extends Model
{
    protected $table = 'volumes';
    protected $primaryKey = 'volume_index';
    protected $allowedFields = ['BMD_type', 'volume_index', 'district_index', 'volume_from', 'volume_to', 'volume'];
    protected $returnType = 'array';
}
