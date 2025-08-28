<?php namespace App\Models;

use CodeIgniter\Model;

class Table_Details_Model extends Model
{
    protected $table = 'table_details';
    protected $primaryKey = 'BMD_index';
    protected $allowedFields = ['project_index','BMD_index', 'BMD_show', 'BMD_controller', 'BMD_table_attr', 'BMD_format', 'BMD_order',
								'BMD_html', 'BMD_table_line', 'BMD_id', 'BMD_name', 'BMD_span', 'BMD_align', 'BMD_pad_left'];
    protected $returnType = 'array';
}
