<?php namespace App\Models;

use CodeIgniter\Model;

class Districts_Model extends Model
{
    protected $table = 'districts_master';
    protected $primaryKey = 'District_index';
    protected $allowedFields =	[
									'District_name', 
									'Added_by_user',
									'active',
								];
    protected $returnType = 'array';
}
