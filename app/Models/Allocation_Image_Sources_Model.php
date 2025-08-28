<?php namespace App\Models;

use CodeIgniter\Model;

class Allocation_Image_Sources_Model extends Model
{
    protected $table = 'allocation_image_sources';
    protected $primaryKey = 'source_index';
    protected $allowedFields =	[
									'project_index',
									'source_name',
									'source_description',
									'source_order',
									'source_code',
									'source_images',
									'Change_date',
								];
    protected $returnType = 'array';
}
