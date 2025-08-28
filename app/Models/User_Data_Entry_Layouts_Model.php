<?php namespace App\Models;

use CodeIgniter\Model;

class User_Data_Entry_Layouts_Model extends Model
{
    protected $table = 'user_data_entry_layouts';
    protected $primaryKey = 'layout_index';
    protected $allowedFields = 	[
									'project_index',
									'identity_index',
									'event_type',
									'layout_index',
									'layout_name',
								];
    protected $returnType = 'array';
}
