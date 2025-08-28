<?php namespace App\Models;

use CodeIgniter\Model;

class Def_Ranges_Model extends Model
{
    protected $table = 'def_ranges';
    protected $primaryKey = 'format_index';
    protected $allowedFields = 		[
										'project_index', 
										'format_index', 
										'type', 
										'from_year', 
										'from_quarter', 
										'to_year', 								
										'to_quarter', 
										'data_entry_format',
										'volume_follows_district',
										'field_after_district',
										'field_after_volume',
									];
    protected $returnType = 'array';
}
