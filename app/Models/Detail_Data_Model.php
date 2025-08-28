<?php namespace App\Models;

use CodeIgniter\Model;

class Detail_Data_Model extends Model
{
    protected $table = 'detail_data';
    protected $primaryKey = 'BMD_index';
    protected $returnType = 'array';
								
	// build the allowed fields array
	protected function initialize()
		{
			parent::initialize();
			
			// get all fields
			$db = \Config\Database::connect();
			$detail_columns = $db->getFieldNames($this->table);
			// remove columns I don't want to be in the allowedFields
			$unwanted_columns = ['BMD_index', 'Change_date'];
			foreach ( $unwanted_columns as $col )
				{
					$key = array_search( $col, $detail_columns);
					if ( $key )
						{
							unset($detail_columns[$key]);
						}
				}
			// reorder
			$fields = array();
			foreach ( $detail_columns as $col )
				{
					$fields[] = $col;
				}
			$this->allowedFields = $fields;
		}			
}
