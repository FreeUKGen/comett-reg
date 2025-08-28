<?php namespace App\Models;

use CodeIgniter\Model;

class Transcription_Current_Layout_Model extends Model
{
    protected $table = 'transcription_current_layout';
    protected $primaryKey = 'line_index';
    protected $allowedFields = 	[
									'line_index',
									'project_index',
									'identity_index',
									'transcription_index',
									'event_type',
									'current_layout_index',
								];
    protected $returnType = 'array';
}
