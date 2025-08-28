<?php namespace App\Models;

use CodeIgniter\Model;

class Def_Fields_Model extends Model
{
    protected $table = 'def_fields';
    protected $primaryKey = 'field_index';
    protected $allowedFields = 	[
									'project_index',
									'syndicate_index',
									'data_entry_format',
									'scan_format',
									'field_index',
									'field_order',
									'field_line',
									'field_check',
									'field_name',
									'column_name',
									'column_width',
									'column_height',
									'font_size',
									'font_weight',
									'field_align',
									'pad_left',
									'html_name',
									'html_id',
									'field_type',
									'blank_OK',
									'date_format',
									'volume_quarterformat',
									'volume_roman',
									'table_fieldname',
									'capitalise',
									'dup_fieldname',
									'dup_fromfieldname',
									'special_test',
									'virtual_keyboard',
									'input_first_line',
									'js_event',
									'je_function',
									'auto_full_stop',
									'auto_copy',
									'auto_focus',
									'colour',
									'field_format',
									'field_decription',
									'field_category',
									'field_popup_help',
								];
    protected $returnType = 'array';
}
