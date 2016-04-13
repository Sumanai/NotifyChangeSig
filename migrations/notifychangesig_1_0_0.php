<?php
/**
*
* @package phpBB Extension - NotifyChangeSig
* @copyright (c) 2016 Sumanai
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/
namespace Sumanai\NotifyChangeSig\migrations;

class notifychangesig_1_0_0 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['notifychangesig_version']) && version_compare($this->config['notifychangesig_version'], '1.0.0', '>=');
	}

	static public function depends_on()
	{
		return array('\phpbb\db\migration\data\v310\dev');
	}

	public function update_schema()
	{
		return array();
	}

	public function update_data()
	{
		return array(
			array('config.add', array('notifychangesig_access_list', '2')),

			// Current version
			array('config.add', array('notifychangesig_version', '1.0.0')),
		);
	}
}
