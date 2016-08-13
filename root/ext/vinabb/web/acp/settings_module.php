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
			$lang_enable = $this->request->variable('lang_switch', '');
			$lang_switch = $this->request->variable('lang_switch', '');
			$maintenance_mode = $this->request->variable('maintenance_mode', constants::MAINTENANCE_MODE_ADMIN);
			$maintenance_tpl = $this->request->variable('maintenance_tpl', true);
			$maintenance_time = $this->request->variable('maintenance_time', 0);
			$maintenance_text = $this->request->variable('maintenance_text', '');

			// Check switch lang
			if ($lang_enable && (empty($lang_switch) || $lang_switch == $this->config['default_lang']))
			{
				$lang_enable = false;
				$lang_switch = '';
			}

			// Check and convert $maintenance_time
			if ($maintenance_time && is_numeric($maintenance_time))
			{
				$maintenance_time = time() + ($maintenance_time * 60);
			}
			else
			{
				$maintenance_time = 0;
			}

			if (empty($errors))
			{
				$this->config->set('vinabb_web_lang_switch', $lang_switch);
				$this->config->set('vinabb_web_maintenance_mode', $maintenance_mode);
				$this->config->set('vinabb_web_maintenance_tpl', $maintenance_tpl);
				$this->config->set('vinabb_web_maintenance_text', $maintenance_text);

				if ($maintenance_time)
				{
					$this->config->set('vinabb_web_maintenance_time', $maintenance_time);
				}

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
			'DEFAULT_LANG'				=> $default_lang_name,
			'MAINTENANCE_MODE'			=> isset($maintenance_mode) ? $maintenance_mode : $this->config['vinabb_web_maintenance_mode'],
			'MAINTENANCE_TPL'			=> isset($maintenance_tpl) ? $maintenance_tpl : $this->config['vinabb_web_maintenance_tpl'],
			'MAINTENANCE_TIME'			=> isset($maintenance_time) ? $maintenance_time : 0,
			'MAINTENANCE_TIME_REMAIN'	=> ($this->config['vinabb_web_maintenance_time'] > time()) ? $this->user->format_date($this->config['vinabb_web_maintenance_time']) : '',
			'MAINTENANCE_TEXT'			=> (isset($maintenance_text) && !empty($maintenance_text)) ? $maintenance_text : $this->config['vinabb_web_maintenance_text'],

			'MAINTENANCE_MODE_NONE'		=> constants::MAINTENANCE_MODE_NONE,
			'MAINTENANCE_MODE_SERVER'	=> constants::MAINTENANCE_MODE_SERVER,
			'MAINTENANCE_MODE_FOUNDER'	=> constants::MAINTENANCE_MODE_FOUNDER,
			'MAINTENANCE_MODE_ADMIN'	=> constants::MAINTENANCE_MODE_ADMIN,
			'MAINTENANCE_MODE_MOD'		=> constants::MAINTENANCE_MODE_MOD,
			'MAINTENANCE_MODE_USER'		=> constants::MAINTENANCE_MODE_USER,

			'LANG_SWITCH_OPTIONS'	=> $lang_switch_options,

			'U_ACTION'	=> $this->u_action,
		));
	}
}
