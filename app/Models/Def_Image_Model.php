<?php namespace App\Models;

use CodeIgniter\Model;

class Def_Image_Model extends Model
{
    protected $table = 'def_image';
    protected $primaryKey = 'image_index';
    protected $allowedFields = 	[
									'project_index',
									'syndicate_index',
									'data_entry_format',
									'scan_format',
									'image_x',
									'image_y',
									'image_rotate',
									'image_scroll_step',
									'panzoom_x',
									'panzoom_y',
									'panzoom_z',
									'sharpen',
									'zoom_lock',
									'reference_scan',
									'reference_path',
									'calib_x',
									'calib_y',
								];
    protected $returnType = 'array';
}
