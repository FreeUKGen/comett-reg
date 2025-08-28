<?php namespace App\Models;

use CodeIgniter\Model;

class Reporting_Model extends Model
{
    protected $table = 'reporting';
    protected $primaryKey = 'report_index';
    protected $returnType = 'array';
    
    protected $allowedFields =	[	
									'report_project', 
									'report_syndicate',
									'report_transcriber',
									'report_allocation',
									'report_transcription',
									'report_last_action',
									'report_year',
									'report_quarter',
									'report_mon',
									'report_yweek',
									'report_mweek',
									'report_yday',
									'report_mday',
									'report_wday',
									'report_month',
									'report_weekday',
									'report_quantity',
									'Change_date',
								];
}
