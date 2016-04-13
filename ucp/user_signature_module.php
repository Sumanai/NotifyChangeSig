<?php
/**
*
* @package phpBB Extension - NotifyChangeSig
* @copyright (c) 2016 Sumanai
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace Sumanai\NotifyChangeSig\ucp;

class user_signature_module
{
	private $p_master;
	public $u_action;

	function __construct(&$p_master)
	{
		$this->p_master = &$p_master;
	}

	function main($id, $mode)
	{
		global $phpbb_container, $template, $user;

		$signature_service = $phpbb_container->get('Sumanai.NotifyChangeSig.ucp_signature');

		$user->add_lang('posting');

		$template->assign_vars(array(
			'S_UCP_ACTION'	=> $this->u_action,
			'L_TITLE'		=> $user->lang['UCP_PROFILE_SIGNATURE'],
		));

		$signature_service->run($this->u_action);

		// Set desired template
		$this->tpl_name = 'ucp_profile_signature';
		$this->page_title = $user->lang['UCP_PROFILE_SIGNATURE'];
	}
}
