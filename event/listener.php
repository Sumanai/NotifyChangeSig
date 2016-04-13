<?php
/**
*
* @package phpBB Extension - NotifyChangeSig
* @copyright (c) 2016 Sumanai
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace Sumanai\NotifyChangeSig\event;

/**
* Event listener
*/
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class listener implements EventSubscriberInterface
{
	public function __construct()
	{
	}

	static public function getSubscribedEvents()
	{
		return array(
			'core.user_setup'                   => 'load_language_on_setup',
			'core.acp_board_config_edit_add'    => 'add_config',
		);
	}

	public function load_language_on_setup($event)
	{
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = array(
			'ext_name' => 'Sumanai/NotifyChangeSig',
			'lang_set' => 'NotifyChangeSig',
		);
		$event['lang_set_ext'] = $lang_set_ext;
	}

	public function add_config($event)
	{
		$mode = $event['mode'];
		if ($mode == 'signature')
		{
			$display_vars = $event['display_vars'];
			/* We add a new legend, but we need to search for the last legend instead of hard-coding */
			$submit_key = array_search('ACP_SUBMIT_CHANGES', $display_vars['vars']);
			$submit_legend_number = substr($submit_key, 6);
			$display_vars['vars']['legend'.$submit_legend_number] = 'ACP_NOTIFY_CHANGE_SIG_TITLE';
			$new_vars = array(
				'notifychangesig_access_list'           => array(
					'lang' => 'ACP_NOTIFY_CHANGE_SIG_ACCESS_LIST',
					'validate' => 'string',
					'type' => 'text:40:255',
					'explain' => true,
				),
				'legend'.($submit_legend_number + 1)    => 'ACP_SUBMIT_CHANGES',
			);
			$display_vars['vars'] = phpbb_insert_config_array($display_vars['vars'], $new_vars, array('after' => $submit_key));
			$event['display_vars'] = $display_vars;
		}
	}
}
