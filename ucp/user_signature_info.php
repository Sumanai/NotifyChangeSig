<?php
/**
*
* @package phpBB Extension - NotifyChangeSig
* @copyright (c) 2016 Sumanai
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace Sumanai\NotifyChangeSig\ucp;

class user_signature_info
{
	function module()
	{
		return array(
			'filename'  => '\Sumanai\NotifyChangeSig\ucp\user_signature_module',
			'title'     => 'UCP_PROFILE',
			'version'   => '1.0.0',
			'modes'     => array(
				'signature'     => array(
					'title' => 'UCP_PROFILE_SIGNATURE',
					'auth'  => 'ext_Sumanai/NotifyChangeSig && acl_u_sig',
					'cat'   => array('UCP_PROFILE'),
				),
			),
		);
	}

	function install()
	{
	}

	function uninstall()
	{
	}
}
