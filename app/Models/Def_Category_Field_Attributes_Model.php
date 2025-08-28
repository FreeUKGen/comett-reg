<?php namespace App\Models;

use CodeIgniter\Model;

class Def_Category_Field_Attributes_Model extends Model
{
    protected $table = 'def_category_field_attributes';
    protected $primaryKey = 'attribute_index';
    protected $allowedFields = 	[
									'project_index',
									'category_index',
									'field_attribute',
									'attribute_order',
									'html_entry_type',
									'html_head1',
									'html_head2',
									'html_head3',
									'html_checkbox',
									'html_setto',
									'html_reset',
									'html_save',
									'html_values',
									'html_default_value',
								];
    protected $returnType = 'array';
}
