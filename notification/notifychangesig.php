<?php
/**
*
* @package phpBB Extension - NotifyChangeSig
* @copyright (c) 2016 Sumanai
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace Sumanai\NotifyChangeSig\notification;

class notifychangesig extends \phpbb\notification\type\base
{
	/**
	* Get notification type name
	*
	* @return string
	*/
	public function get_type()
	{
		return 'sumanai.notification.type.notifychangesig';
	}

	/**
	* Language key used to output the text
	*
	* @var string
	*/
	protected $language_key = 'NOTIFICATION_CHANGE_SIG';

	/**
	* Notification option data (for outputting to the user)
	*
	* @var bool|array False if the service should use it's default data
	*                   Array of data (including keys 'id', 'lang', and 'group')
	*/
	public static $notification_option = array(
		'lang'  => 'NOTIFICATION_TYPE_CHANGE_SIG',
		'group' => 'NOTIFICATION_GROUP_MISCELLANEOUS',
	);

	/**
	* Is available
	*/
	public function is_available()
	{
		return in_array($this->user->data['user_id'], explode(',', $this->config['notifychangesig_access_list']));
	}

	/**
	* Get the id of the item
	*/
	public static function get_item_id($notify_data)
	{
		return $notify_data['count_sig'];
	}

	/**
	* Get the id of the parent
	*/
	public static function get_item_parent_id($notify_data)
	{
		return $notify_data['user_id'];
	}

	/**
	* Find the users who want to receive notifications
	*
	* @param array $options Options for finding users for notification
	*
	* @return array
	*/
	public function find_users_for_notification($notify_data, $options = array())
	{
		$users = explode(',', $this->config['notifychangesig_access_list']);

		// We will not notify himself
		if(($key = array_search($notify_data['user_id'], $users)) !== false){
			unset($users[$key]);
		}

		return $this->check_user_notification_options($users, $options);
	}

	/**
	* Get the user's avatar
	*/
	public function get_avatar()
	{
		return $this->user_loader->get_avatar($this->get_data('user_id'));
	}

	/**
	* Get the HTML formatted title of this notification
	*
	* @return string
	*/
	public function get_title()
	{
		$username = $this->user_loader->get_username($this->get_data('user_id'), 'no_profile');

		return $this->user->lang($this->language_key, $username);
	}

	/**
	* Users needed to query before this notification can be displayed
	*
	* @return array Array of user_ids
	*/
	public function users_to_query()
	{
		$users = explode(',', $this->config['notifychangesig_access_list']);
		$users[] = $this->get_data('user_id');

		return $users;
	}

	/**
	* Get the url to this item
	*
	* @return string URL
	*/
	public function get_url()
	{
		return append_sid($this->phpbb_root_path . 'memberlist.' . $this->php_ext, "mode=viewprofile&amp;u={$this->item_parent_id}");
	}

	/**
	* {inheritDoc}
	*/
	public function get_redirect_url()
	{
		return $this->get_url();
	}

	/**
	* Get email template
	*
	* @return string|bool
	*/
	public function get_email_template()
	{
		return '@Sumanai_NotifyChangeSig/user_change_sig';
	}

	/**
	* Get the HTML formatted reference of the notification
	*
	* @return string
	*/
	public function get_reference()
	{
		$signature = generate_text_for_display($this->get_data('signature'), $this->get_data('uid'), $this->get_data('bitfield'), $this->get_data('flags'));

		return $this->user->lang('NOTIFICATION_REFERENCE', $signature);
	}

	/**
	* Get email template variables
	*
	* @return array
	*/
	public function get_email_template_variables()
	{
		$username = $this->user_loader->get_username($this->get_data('user_id'), 'username');
		$signature = $this->get_data('signature');
		strip_bbcode($signature, $this->get_data('uid'));

		return array(
				'CHANGE_SIG_USERNAME'   => htmlspecialchars_decode($username),
				'SIG'                   => htmlspecialchars_decode($signature),
		);
	}

	/**
	* Function for preparing the data for insertion in an SQL query
	* (The service handles insertion)
	*
	* @return array Array of data ready to be inserted into the database
	*/
	public function create_insert_array($notify_data, $pre_create_data = array())
	{
		foreach ($notify_data as $data_name => $data_value)
		{
			$this->set_data($data_name, $data_value);
		}

		return parent::create_insert_array($notify_data, $pre_create_data);
	}
}
