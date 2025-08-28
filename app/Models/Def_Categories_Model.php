<?php namespace App\Models;

use CodeIgniter\Model;

class Def_Categories_Model extends Model
{
    protected $table = 'def_categories';
    protected $primaryKey = 'def_category_index';
    protected $allowedFields = 	[
									'project_index',
									'def_category_name',
									'def_category_desc',
									'def_category_order',
									'def_category_active',
								];
    protected $returnType = 'array';
}
