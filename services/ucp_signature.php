<?php
/**
*
* @package phpBB Extension - NotifyChangeSig
* @copyright (c) 2016 Sumanai
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace Sumanai\NotifyChangeSig\services;

class ucp_signature
{
	private $auth;
	private $config;
	private $db;
	private $request;
	private $template;
	private $user;
	private $phpbb_root_path;
	private $php_ext;

	public function __construct(
		\phpbb\auth\auth $auth,
		\phpbb\config\config $config,
		\phpbb\db\driver\driver_interface $db,
		\phpbb\request\request $request,
		\Sumanai\NotifyChangeSig\services\helper $helper,
		\phpbb\template\template $template,
		\phpbb\user $user,
		$phpbb_root_path,
		$php_ext
	) {
		$this->auth = $auth;
		$this->config = $config;
		$this->db = $db;
		$this->helper = $helper;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->php_ext = $php_ext;
	}

	public function run($u_action)
	{
		if (!$this->auth->acl_get('u_sig'))
		{
			trigger_error('NO_AUTH_SIGNATURE');
		}

		$submit = $this->request->variable('submit', '');
		$preview = $this->request->variable('preview', '');
		$error = array();

		include($this->phpbb_root_path . 'includes/functions_posting.' . $this->php_ext);
		include($this->phpbb_root_path . 'includes/functions_display.' . $this->php_ext);

		$signature = utf8_normalize_nfc($this->request->variable('signature', (string) $this->user->data['user_sig'], true));

		add_form_key('ucp_sig');

		if ($submit || $preview)
		{
			include($this->phpbb_root_path . 'includes/message_parser.' . $this->php_ext);

			$enable_bbcode  = ($this->config['allow_sig_bbcode']) ? (($this->request->variable('disable_bbcode', false)) ? false : true) : false;
			$enable_smilies = ($this->config['allow_sig_smilies']) ? (($this->request->variable('disable_smilies', false)) ? false : true) : false;
			$enable_urls    = ($this->config['allow_sig_links']) ? (($this->request->variable('disable_magic_url', false)) ? false : true) : false;

			// Signature Lines Limit
			if($this->config['max_sig_lines'])
			{
				$brcount = 1;
				$brpos = 0;
				while( ($brpos = strpos($signature, "\n", $brpos)) !== false )
				{
					$brcount++;
					if($brcount > $this->config['max_sig_lines'])
					{
						$signature{$brpos}=' ';
					}
					$brpos++;
				}
			}

			$message_parser = new \parse_message($signature);

			// Allowing Quote BBCode
			$message_parser->parse($enable_bbcode, $enable_urls, $enable_smilies, $this->config['allow_sig_img'], $this->config['allow_sig_flash'], true, $this->config['allow_sig_links'], true, 'sig');

			if (sizeof($message_parser->warn_msg))
			{
				$error[] = implode('<br />', $message_parser->warn_msg);
			}

			if (!check_form_key('ucp_sig'))
			{
				$error[] = 'FORM_INVALID';
			}

			if (!sizeof($error) && $submit)
			{
				$this->user->optionset('sig_bbcode', $enable_bbcode);
				$this->user->optionset('sig_smilies', $enable_smilies);
				$this->user->optionset('sig_links', $enable_urls);

				$sql_ary = array(
					'user_sig'                  => (string) $message_parser->message,
					'user_options'              => $this->user->data['user_options'],
					'user_sig_bbcode_uid'       => (string) $message_parser->bbcode_uid,
					'user_sig_bbcode_bitfield'  => $message_parser->bbcode_bitfield,
				);

				$sql = 'UPDATE ' . USERS_TABLE . '
					SET ' . $this->db->sql_build_array('UPDATE', $sql_ary) . '
					WHERE user_id = ' . $this->user->data['user_id'];
				$this->db->sql_query($sql);

				// Add up the flag options...
				$flags = ($enable_bbcode ? OPTION_FLAG_BBCODE : 0) +
					($enable_smilies ? OPTION_FLAG_SMILIES : 0) + 
					($enable_urls ? OPTION_FLAG_LINKS : 0);

				$notify_data = array(
					'user_id'   => $this->user->data['user_id'],
					'signature' => (string) $message_parser->message,
					'uid'       => (string) $message_parser->bbcode_uid,
					'bitfield'  => $message_parser->bbcode_bitfield,
					'flags'     => $flags,
					'count_sig' => $this->helper->get_counter_signature(),
				);
				$this->helper->add_notification($notify_data);

				$message = $this->user->lang['PROFILE_UPDATED'] . '<br /><br />' . sprintf($this->user->lang['RETURN_UCP'], '<a href="' . $u_action . '">', '</a>');
				trigger_error($message);
			}

			// Replace "error" strings with their real, localised form
			$error = array_map(array($this->user, 'lang'), $error);
		}
		else
		{
			$enable_bbcode  = ($this->config['allow_sig_bbcode']) ? (bool) $this->user->optionget('sig_bbcode') : false;
			$enable_smilies = ($this->config['allow_sig_smilies']) ? (bool) $this->user->optionget('sig_smilies') : false;
			$enable_urls    = ($this->config['allow_sig_links']) ? (bool) $this->user->optionget('sig_links') : false;
		}

		if ($preview)
		{
			// Now parse it for displaying
			$signature_preview = $message_parser->format_display($enable_bbcode, $enable_urls, $enable_smilies, false);
			unset($message_parser);
		}
		else
		{
			$signature_preview = '';
		}

		decode_message($signature, $this->user->data['user_sig_bbcode_uid']);

		$this->template->assign_vars(array(
			'ERROR'             => (sizeof($error)) ? implode('<br />', $error) : '',
			'SIGNATURE'         => $signature,
			'SIGNATURE_PREVIEW' => $signature_preview,

			'S_BBCODE_CHECKED'      => (!$enable_bbcode) ? ' checked="checked"' : '',
			'S_SMILIES_CHECKED'     => (!$enable_smilies) ? ' checked="checked"' : '',
			'S_MAGIC_URL_CHECKED'   => (!$enable_urls) ? ' checked="checked"' : '',

			'BBCODE_STATUS'         => ($this->config['allow_sig_bbcode']) ? sprintf($this->user->lang['BBCODE_IS_ON'], '<a href="' . append_sid("{$this->phpbb_root_path}faq.$this->php_ext", 'mode=bbcode') . '">', '</a>') : sprintf($this->user->lang['BBCODE_IS_OFF'], '<a href="' . append_sid("{$this->phpbb_root_path}faq.$this->php_ext", 'mode=bbcode') . '">', '</a>'),
			'SMILIES_STATUS'        => ($this->config['allow_sig_smilies']) ? $this->user->lang['SMILIES_ARE_ON'] : $this->user->lang['SMILIES_ARE_OFF'],
			'IMG_STATUS'            => ($this->config['allow_sig_img']) ? $this->user->lang['IMAGES_ARE_ON'] : $this->user->lang['IMAGES_ARE_OFF'],
			'FLASH_STATUS'          => ($this->config['allow_sig_flash']) ? $this->user->lang['FLASH_IS_ON'] : $this->user->lang['FLASH_IS_OFF'],
			'URL_STATUS'            => ($this->config['allow_sig_links']) ? $this->user->lang['URL_IS_ON'] : $this->user->lang['URL_IS_OFF'],
			'MAX_FONT_SIZE'         => (int) $this->config['max_sig_font_size'],

			'L_SIGNATURE_EXPLAIN'   => $this->user->lang('SIGNATURE_EXPLAIN', (int) $this->config['max_sig_chars'], ($this->config['max_sig_lines'] ? (string) $this->config['max_sig_lines'] : $this->user->lang['NO'])),

			'S_BBCODE_ALLOWED'      => $this->config['allow_sig_bbcode'],
			'S_SMILIES_ALLOWED'     => $this->config['allow_sig_smilies'],
			'S_BBCODE_IMG'          => ($this->config['allow_sig_img']) ? true : false,
			'S_BBCODE_FLASH'        => ($this->config['allow_sig_flash']) ? true : false,
			'S_LINKS_ALLOWED'       => ($this->config['allow_sig_links']) ? true : false)
		);

		// Build custom bbcodes array
		display_custom_bbcodes();

		// Generate smiley listing
		generate_smilies('inline', 0);
	}
}
