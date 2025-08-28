<?php namespace App\Models;

use CodeIgniter\Model;

class SyndicateTable_Model extends Model
{
    protected $DBGroup = 'syndicate';
    protected $table = 'SyndicateMembers';
    protected $primaryKey = 'SyndicateID';
    protected $allowedFields =	[
									'SyndicateID',
									'UserID',
									'CoOrdinator',
									'JoinedDate',
									'SyndicateCorrections',
									'SyndicateRole',
								];
    protected $returnType = 'array';
}
