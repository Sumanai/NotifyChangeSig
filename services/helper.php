<?php
/**
*
* @package phpBB Extension - NotifyChangeSig
* @copyright (c) 2016 Sumanai
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace Sumanai\NotifyChangeSig\services;

class helper
{
	private $db;
	private $notification_manager;
	private $notifications_table;
	private $type_name = 'sumanai.notification.type.notifychangesig';

	public function __construct(
		\phpbb\db\driver\driver_interface $db,
		\phpbb\notification\manager $notification_manager,
		$notifications_table
	) {
		$this->db = $db;
		$this->notification_manager = $notification_manager;
		$this->notifications_table = $notifications_table;
	}

	public function add_notification($notify_data)
	{
		$this->notification_manager->add_notifications($this->type_name, $notify_data);
	}

	public function get_counter_signature()
	{
		$type_id = $this->notification_manager->get_notification_type_id($this->type_name);

		$sql = 'SELECT MAX(item_id) AS count_sig
			FROM ' . $this->notifications_table . '
			WHERE notification_type_id = ' . (int) $type_id;
		$result = $this->db->sql_query($sql);
		$item_id = (int) $this->db->sql_fetchfield('count_sig');
		$this->db->sql_freeresult($result);

		if ($item_id)
		{
			return $item_id + 1;
		}
		else
		{
			return 1;
		}
	}
}
