<?php
/**
*
* @package phpBB Extension - NotifyChangeSig
* @copyright (c) 2016 Sumanai
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	'ACP_NOTIFY_CHANGE_SIG_TITLE'               => 'Notification settings',
	'ACP_NOTIFY_CHANGE_SIG_ACCESS_LIST'         => 'List of users that receive the notification about the change in user signatures',
	'ACP_NOTIFY_CHANGE_SIG_ACCESS_LIST_EXPLAIN' => 'List ID separated by commas, spaces or other characters are not allowed.',

	'NOTIFICATION_CHANGE_SIG'       => 'User %1$s <strong>has changed the signature</strong> to:',
	'NOTIFICATION_TYPE_CHANGE_SIG'  => 'User has changed the signature',
));
