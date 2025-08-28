<?php namespace App\Models;

use CodeIgniter\Model;

class Header_Table_Details_Model extends Model
{
    protected $table = 'header_table_details';
    protected $primaryKey = 'BMD_index';
    protected $allowedFields = ['BMD_index', 'BMD_header_index', 'BMD_table_details_index', 'BMD_header_align',
											'BMD_header_span', 'BMD_header_pad_left'];
    protected $returnType = 'array';
}
