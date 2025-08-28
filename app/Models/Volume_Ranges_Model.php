<?php namespace App\Models;

use CodeIgniter\Model;

class Volume_Ranges_Model extends Model
{
    protected $table = 'volume_ranges';
    protected $primaryKey = 'BMD_index';
    protected $allowedFields = ['BMD_index', 'BMD_range_from', 'BMD_range_to', 'BMD_range_name'];
    protected $returnType = 'array';
}
