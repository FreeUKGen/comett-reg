<?php namespace App\Models;

use CodeIgniter\Model;

class Submitters_Model extends Model
{
    //protected $DBGroup = 'FreeBMD';
    protected $table = 'Submitters';
    protected $primaryKey = 'SubmitterNumber';
    protected $allowedFields =	[
									'SignUpDate',
									'NewlyEntered',
									'ChallengeRequired',
									'EnabledDate',
									'LastLogin',
									'LastLoginFlag',
									'LastAdminLogin',
									'Disabled',
									'DisabledDate',
									'DisabledReason',
									'Locked',
									'LockedReason',
									'NotActive',
									'NotActiveDate',
									'NotActiveReason',
									'Coordinator',
									'WorkingWith',
									'Surname',
									'GivenName',
									'UserID',
									'Password',
									'NewPassword',
									'RealPassword',
									'ScanAccessRole',
									'EmailID',
									'Country',
									'PublicKey',
									'Active',
									'Challenge',
									'ChallengeGenerated',
									'ChallengeConfirmed',
									'FicheReader',
									'LookingForSyndicate',
									'UnixTimeEntered',
									'CoordinatorName',
									'CoordAccess',
									'PrivacyKey',
									'AcceptCorrections',
									'Contactable',
									'CorrectionConfig',
									'CorrectionNotification',
									'TotalEntries',
									'UserType',
									'Role',
									'Notes',
								];
    protected $returnType = 'array';
}
