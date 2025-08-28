<?php namespace App\Models;

use CodeIgniter\Model;

class User_Parameters_Model extends Model
{
    protected $table = 'user_parameters';
    protected $primaryKey = 'BMD_index';
    protected $allowedFields = ['BMD_index', 'BMD_identity_index', 'BMD_syndicate_index', 'BMD_allocation_index', 'BMD_header_index', 
													'BMD_language_index', 'BMD_lastkeyedname', 'BMD_lastkeyedfirstname', 'BMD_lastkeyedinitials', 
													'BMD_lastkeyedpartnername', 'BMD_lastkeyeddistrictname', 'BMD_lastkeyeddistrictcode', 'BMD_lastkeyedpage', 
													'BMD_lastkeyedregistration', 'BMD_totalnumberrecordskeyed'];
    protected $returnType = 'array';
}
