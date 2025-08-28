<?php namespace App\Models;

use CodeIgniter\Model;

class Transcription_Comments_Model extends Model
{
    protected $table = 'transcription_comments';
    protected $primaryKey = 'line_index';
    protected $allowedFields = 	[
									'line_index',
									'transcription_index',
									'project_index',
									'identity_index',
									'comment_sequence',
									'comment_text',
									'source_text',
								];
    protected $returnType = 'array';
}
