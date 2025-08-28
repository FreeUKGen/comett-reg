<?php namespace App\Models;

use CodeIgniter\Model;

class Messaging_Model extends Model
{
    protected $table = 'messaging';
    protected $primaryKey = 'message_index';
    protected $allowedFields =	[
									'project_index',
									'from_date',
									'to_date',
									'message',
									'colour'
								];
    protected $returnType = 'array';
}
