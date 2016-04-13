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
	'ACP_NOTIFY_CHANGE_SIG_TITLE'               => 'Настройки уведомлений',
	'ACP_NOTIFY_CHANGE_SIG_ACCESS_LIST'         => 'Список пользователей, получающих уведомление об изменении подписей пользователей',
	'ACP_NOTIFY_CHANGE_SIG_ACCESS_LIST_EXPLAIN' => 'Список ID через запятую, пробелы и другие символы недопустимы.',

	'NOTIFICATION_CHANGE_SIG'       => 'Пользователь %1$s <strong>изменил подпись</strong> на:',
	'NOTIFICATION_TYPE_CHANGE_SIG'  => 'Пользователь изменил подпись',
));
