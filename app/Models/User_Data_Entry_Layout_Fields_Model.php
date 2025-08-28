<?php namespace App\Models;

use CodeIgniter\Model;

class User_Data_Entry_Layout_Fields_Model extends Model
{
    protected $table = 'user_data_entry_layout_fields';
    protected $primaryKey = 'line_index';
    protected $allowedFields = 	[
									'line_index',
									'layout_index',
									'field_name',
									'field_width',
									'field_order',
								];
    protected $returnType = 'array';
}
