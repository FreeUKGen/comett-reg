<?php namespace App\Models;

use CodeIgniter\Model;

class Allocation_Model extends Model
{
    protected $table = 'allocation';
    protected $primaryKey = 'BMD_allocation_index';
    protected $allowedFields =	[
									'project_index',
									'BMD_identity_index',
									'BMD_allocation_index',
									'BMD_syndicate_index',
									'BMD_allocation_name',
									'BMD_reference',
									'BMD_start_date',
									'BMD_end_date',
									'BMD_start_page',
									'BMD_end_page',
									'BMD_year',
									'BMD_quarter',
									'BMD_letter',
									'BMD_type',
									'BMD_status',
									'BMD_sequence',
									'BMD_scan_type',
									'BMD_last_action',
									'BMD_last_uploaded',
									'BMD_syndicate_scan',
									'data_entry_format',
									'scan_format',
									'REG_assignment_id',
									'REG_county_group',
									'REG_county',
									'REG_chapman_code',
									'REG_place',
									'REG_church_name',
									'REG_church_code',
									'REG_register_type',
									'REG_image_folder_name',
									'REG_TP_seq',
									'source_code',
								];
    protected $returnType = 'array';
}
