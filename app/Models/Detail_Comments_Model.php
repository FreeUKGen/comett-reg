<?php namespace App\Models;

use CodeIgniter\Model;

class Detail_Comments_Model extends Model
{
    protected $table = 'detail_comments';
    protected $primaryKey = 'BMD_index';
    protected $allowedFields = ['project_index','BMD_index', 'BMD_identity_index', 'BMD_header_index', 'BMD_line_index', 'BMD_line_sequence', 'BMD_comment_type', 'BMD_comment_span', 'BMD_comment_text' ];
    protected $returnType = 'array';
}
