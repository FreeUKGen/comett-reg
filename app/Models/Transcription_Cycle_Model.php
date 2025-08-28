<?php namespace App\Models;

use CodeIgniter\Model;

class Transcription_Cycle_Model extends Model
{
	protected $table = 'transcription_cycle';
    protected $primaryKey = 'BMD_cycle_index';
    protected $allowedFields = ['project_index', 'BMD_cycle_index', 'BMD_cycle_name',  'BMD_cycle_type', 'BMD_cycle_sort', 'BMD_cycle_code', 'available'];
    protected $returnType = 'array';
}
