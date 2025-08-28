<?php namespace App\Models;

use CodeIgniter\Model;

class Identity_Last_Indexes_Model extends Model
{
    protected $table = 'identity_last_indexes';
    protected $primaryKey = 'identity_index';
    protected $allowedFields = 	[
									'identity_index',
									'project_index',
									'data_entry_format',
									'transcription_index',
									'allocation_index',
									'syndicate_index',
								];
    protected $returnType = 'array';
}
