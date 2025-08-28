<?php namespace App\Models;

use CodeIgniter\Model;

class Signins_Model extends Model
{
    protected $table = 'signins';
    protected $primaryKey = 'signin_index';
    protected $allowedFields = 	[	'identity_index',
									'identity_role',
									'signin_date',
									'syndicate_index',
									'signin_x',
									'signin_y',
								];
    protected $returnType = 'array';
}
