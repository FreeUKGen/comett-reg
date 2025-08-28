<?php namespace App\Models;

use CodeIgniter\Model;

class Allocation_Images_Model extends Model
{
    protected $table = 'allocation_images';
    protected $primaryKey = 'image_index';
    protected $allowedFields =	[
									'project_index',
									'allocation_index',
									'transcription_index',
									'identity_index',
									'image_id',
									'original_image_file_name',
									'image_file_name',
									'image_url',
									'image_status',
									'trans_start_date',
									'trans_complete_date',
									'Change_date',
								];
    protected $returnType = 'array';
}
