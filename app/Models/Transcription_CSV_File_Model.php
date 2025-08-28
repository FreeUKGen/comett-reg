<?php namespace App\Models;

use CodeIgniter\Model;

class Transcription_CSV_File_Model extends Model
{
    protected $table = 'transcription_csv_file';
    protected $primaryKey = 'transcription_index';
    protected $allowedFields = 	[
									'project_index',
									'transcription_index',
									'identity_index',
									'data_entry_format',
									'csv_file_name',
									'csv_string',
								];
    protected $returnType = 'array';
}
