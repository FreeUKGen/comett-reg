<?php namespace App\Models;

use CodeIgniter\Model;

class Freeukgen_Sources_Model extends Model
{
    protected $table = 'freeukgen_sources';
    protected $primaryKey = 'source_index';
    protected $allowedFields =	[
									'project_index',
									'source_key',
									'source_def',
									'source_protocol',
									'source_URL',
									'source_port',
									'source_user',
									'source_password',
									'source_folder',
									'source_path',
									'source_name',
									'source_field',
									'source_separator',
									'source_value_start_delim',
									'source_value_end_delim',
									'source_return_type',
									'source_purpose',
									'source_description',
									'Change_date',
								];
    protected $returnType = 'array';
}
