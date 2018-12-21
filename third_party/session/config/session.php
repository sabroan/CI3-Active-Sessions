<?php defined('BASEPATH') OR exit('No direct script access allowed');
/*
| ------------------------------------------------------------------------------
|  Session
| ------------------------------------------------------------------------------
|
| 'session_uid_key' - Session key which will store user ID.
| 'database_table' - Table name where active sessions will be stored, if doesn't
|   set, active sessions will not be tracked.
|
*/

$config['session_uid_key']  = 'id';
$config['database_table']   = 'users_active_sessions';