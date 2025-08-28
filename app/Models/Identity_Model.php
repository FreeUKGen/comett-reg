<?php namespace App\Models;

use CodeIgniter\Model;

class Identity_Model extends Model
{
    protected $table = 'identity';
    protected $primaryKey = 'BMD_identity_index';
    protected $allowedFields = 	[
									'BMD_identity_index',
									'BMD_user',
									'role_index',
									'project_index',
									'environment',
									'default_dataentryfont',
									'last_syndicate',
									'last_allocation',
									'last_transcription',
									'last_page_in_last_transcription',
									'verify_mode'
								];
    protected $returnType = 'array';
}
