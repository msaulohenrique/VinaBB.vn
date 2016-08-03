<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\acp;

use vinabb\web\includes\constants;

class settings_module
{
	/** @var string */
	public $u_action;

	public function main($id, $mode)
	{
		global $phpbb_container, $phpEx;

		$this->auth = $phpbb_container->get('auth');
		$this->config = $phpbb_container->get('config');
		$this->db = $phpbb_container->get('dbal.conn');
		$this->log = $phpbb_container->get('log');
		$this->request = $phpbb_container->get('request');
		$this->template = $phpbb_container->get('template');
		$this->user = $phpbb_container->get('user');
		$this->language = $phpbb_container->get('language');
		$this->ext_manager = $phpbb_container->get('ext.manager');
		$this->filesystem = $phpbb_container->get('filesystem');

		$this->tpl_name = 'acp_settings';
		$this->page_title = $this->language->lang('ACP_VINABB_SETTINGS');
		$this->ext_root_path = $this->ext_manager->get_extension_path('vinabb/web', true);
		$this->language->add_lang('acp_settings', 'vinabb/web');

		add_form_key('vinabb/web');

		$errors = array();

		// Submit
		if ($this->request->is_set_post('submit'))
		{
			if (!check_form_key('vinabb/web'))
			{
				$errors[] = $this->language->lang('FORM_INVALID');
			}

			// Get from the form
			$lang_switch = $this->request->variable('vinabb_web_lang_switch', '');
			$maintenance_mode = $this->request->variable('vinabb_web_maintenance_mode', constants::MAINTENANCE_MODE_ADMIN);
			$maintenance_tpl = $this->request->variable('vinabb_web_maintenance_tpl', true);
			$maintenance_time = $this->request->variable('vinabb_web_maintenance_time', 0);
			$maintenance_text = $this->request->variable('vinabb_web_maintenance_text', '');

			if (empty($errors))
			{
				$this->config->set('vinabb_web_lang_switch', $lang_switch);
				$this->config->set('vinabb_web_maintenance_mode', $maintenance_mode);
				$this->config->set('vinabb_web_maintenance_tpl', $maintenance_tpl);
				$this->config->set('vinabb_web_maintenance_time', $maintenance_time);
				$this->config->set('vinabb_web_maintenance_text', $maintenance_text);

				$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_VINABB_SETTINGS');

				trigger_error($this->language->lang('VINABB_SETTINGS_UPDATED') . adm_back_link($this->u_action));
			}
			else
			{
				trigger_error(implode('<br>', $errors) . adm_back_link($this->u_action), E_USER_WARNING);				
			}
		}

		// Select an extra language to switch
		$sql = 'SELECT *
			FROM ' . LANG_TABLE . '
			ORDER BY lang_english_name';
		$result = $this->db->sql_query($sql);
		$rows = $this->db->sql_fetchrowset($result);
		$this->db->sql_freeresult($result);

		$selected_lang_switch = isset($lang_switch) ? $lang_switch : $this->config['vinabb_web_lang_switch'];
		$default_lang_name = $lang_switch_options = '';

		if (sizeof($rows))
		{
			if (sizeof($rows) > 1)
			{
				$lang_switch_options .= '<option value=""' . (($selected_lang_switch == '') ? ' selected' : '' ) . '>' . $this->language->lang('SELECT_LANGUAGE') . '</option>';
			}

			foreach ($rows as $row)
			{
				if ($row['lang_iso'] == $this->config['default_lang'])
				{
					$default_lang_name = ($row['lang_english_name'] == $row['lang_local_name']) ? $row['lang_english_name'] : $row['lang_english_name'] . ' (' . $row['lang_local_name'] . ')';
				}
				else
				{
					$lang_switch_options .= '<option value="' . $row['lang_iso'] . '"' . (($selected_lang_switch == $row['lang_iso']) ? ' selected' : '' ) . '>' . $row['lang_english_name'] . ' (' . $row['lang_local_name'] . ')</option>';
				}
			}
		}

		// Output
		$this->template->assign_vars(array(
			'STYLES_DEMO_URL'	=> generate_board_url() . (($this->config['enable_mod_rewrite']) ? '' : "/app.$phpEx") . '/demo/',

			'LOGO_TEXT'			=> (isset($logo_text) && !empty($logo_text)) ? $logo_text : $this->config['vinabb_stylesdemo_logo_text'],
			'AUTO_TOGGLE'		=> isset($auto_toggle) ? $auto_toggle : $this->config['vinabb_stylesdemo_auto_toggle'],
			'PHONE_WIDTH'		=> isset($phone_width) ? $phone_width : $this->config['vinabb_stylesdemo_phone_width'],
			'TABLET_WIDTH'		=> isset($tablet_width) ? $tablet_width : $this->config['vinabb_stylesdemo_tablet_width'],
			'MIN_PHONE_WIDTH'	=> constants::MIN_PHONE_WIDTH,
			'DEFAULT_LANG'		=> $default_lang_name,
			'LANG_ENABLE'		=> isset($lang_enable) ? $lang_enable : $this->config['vinabb_stylesdemo_lang_enable'],
			'ACP_ENABLE'		=> isset($acp_enable) ? $acp_enable : $this->config['vinabb_stylesdemo_acp_enable'],
			'JSON_ENABLE'		=> isset($json_enable) ? $json_enable : $this->config['vinabb_stylesdemo_json_enable'],
			'JSON_URL'			=> (isset($json_url) && !empty($json_url)) ? $json_url : $this->config['vinabb_stylesdemo_json_url'],

			'SCREENSHOT_TYPE'			=> isset($screenshot_type) ? $screenshot_type : $this->config['vinabb_stylesdemo_screenshot_type'],
			'SCREENSHOT_TYPE_LOCAL'		=> constants::SCREENSHOT_TYPE_LOCAL,
			'SCREENSHOT_TYPE_JSON'		=> constants::SCREENSHOT_TYPE_JSON,
			'SCREENSHOT_TYPE_PHANTOM'	=> constants::SCREENSHOT_TYPE_PHANTOM,
			'OS_NAME'					=> $this->get_php_os_name(),
			'GET_PHANTOM_FOR_OS'		=> $this->language->lang('GET_PHANTOM_' . ((PHP_INT_SIZE === 4 && $this->get_php_os_name(true) == 'LINUX') ? 'LINUX_32' : $this->get_php_os_name(true)), constants::PHANTOM_URL, $this->ext_root_path),
			'GET_PHANTOM_NO_OS'			=> $this->language->lang('GET_PHANTOM_NO_OS', constants::PHANTOM_URL, $this->ext_root_path),

			'LANG_SWITCH_OPTIONS'	=> $lang_switch_options,

			'U_ACTION'	=> $this->u_action,
		));
	}
}
