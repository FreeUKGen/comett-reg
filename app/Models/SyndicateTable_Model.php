<?php namespace App\Models;

use CodeIgniter\Model;

class SyndicateTable_Model extends Model
{
    protected $DBGroup = 'syndicate';
    protected $table = 'SyndicateTable';
    protected $primaryKey = 'SyndicateID';
    protected $allowedFields =	[
									'SyndicateName',
									'SyndicateShortDesc',
									'SyndicateURL',
									'Recruiting',
									'NeedReader',
									'CorrectionsContact',
									'CorrectionFlag',
									'CorrectionConfig',
									'CorrectionsSuspended',
									'AmendNotAcceptCorrections',
									'CorrectionsLastProcessed',
									'SyndicateEmail',
								];
    protected $returnType = 'array';
}
