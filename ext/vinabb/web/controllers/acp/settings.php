<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\controllers\acp;

use vinabb\web\includes\constants;

/**
* Controller for the settings_module
*/
class settings
{
	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\cache\service */
	protected $cache;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\config\db_text */
	protected $config_text;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\log\log */
	protected $log;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var string */
	protected $u_action;

	/** @var array */
	protected $errors = [];

	/** @var array */
	protected $forum_data = [];

	/**
	* Constructor
	*
	* @param \phpbb\auth\auth $auth
	* @param \phpbb\cache\service				$cache			Cache service
	* @param \phpbb\config\config				$config			Config object
	* @param \phpbb\config\db_text				$config_text	Config text object
	* @param \phpbb\db\driver\driver_interface	$db				Database object
	* @param \phpbb\language\language			$language		Language object
	* @param \phpbb\log\log						$log			Log object
	* @param \phpbb\request\request				$request		Request object
	* @param \phpbb\template\template			$template		Template object
	* @param \phpbb\user						$user			User object
	*/
	public function __construct(
		\phpbb\auth\auth $auth,
		\phpbb\cache\service $cache,
		\phpbb\config\config $config,
		\phpbb\config\db_text $config_text,
		\phpbb\db\driver\driver_interface $db,
		\phpbb\language\language $language,
		\phpbb\log\log $log,
		\phpbb\request\request $request,
		\phpbb\template\template $template,
		\phpbb\user $user
	)
	{
		$this->auth = $auth;
		$this->cache = $cache;
		$this->config = $config;
		$this->config_text = $config_text;
		$this->db = $db;
		$this->language = $language;
		$this->log = $log;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;

		$this->forum_data = $this->cache->get_forum_data();
	}

	/**
	* Set form action URL
	*
	* @param string $u_action Form action
	*/
	public function set_form_action($u_action)
	{
		$this->u_action = $u_action;
	}

	/**
	* Display main settings
	*/
	public function display_main_settings()
	{
		// Create a form key for preventing CSRF attacks
		add_form_key('acp_settings');

		// Submit
		if ($this->request->is_set_post('submit'))
		{
			// Test if the submitted form is valid
			if (!check_form_key('acp_settings'))
			{
				$this->errors[] = $this->language->lang('FORM_INVALID');
			}

			// Checking setting values
			$this->check_group_settings('main');

			if (empty($this->errors))
			{
				$this->set_group_settings('main');
				$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_VINABB_SETTINGS');
				$this->cache->clear_config_text();

				trigger_error($this->language->lang('MESSAGE_MAIN_SETTINGS_UPDATE') . adm_back_link($this->u_action));
			}
			else
			{
				trigger_error(implode('<br>', $this->errors) . adm_back_link($this->u_action), E_USER_WARNING);
			}
		}

		// Output
		$this->output_group_settings('main');

		$this->template->assign_vars([
			'U_ACTION'	=> $this->u_action
		]);
	}

	/**
	* List of main setting items
	*
	* @return array
	*/
	public function list_main_settings()
	{
		return [
			'maintenance_mode'			=> ['type' => 'int', 'default' => 0, 'check' => ''],
			'maintenance_tpl'			=> ['type' => 'bool', 'default' => true, 'check' => ''],
			'maintenance_time'			=> ['type' => 'int', 'default' => 0, 'check' => ''],
			'maintenance_time_reset'	=> ['type' => 'bool', 'default' => false, 'check' => ''],
			'maintenance_text'			=> ['type' => 'string_uni', 'default' => '', 'check' => ''],
			'maintenance_text_vi'		=> ['type' => 'string_uni', 'default' => '', 'check' => ''],
			'maintenance_mode_none'		=> ['type' => 'tpl', 'default' => constants::MAINTENANCE_MODE_NONE, 'check' => ''],
			'maintenance_mode_founder'	=> ['type' => 'tpl', 'default' => constants::MAINTENANCE_MODE_FOUNDER, 'check' => ''],
			'maintenance_mode_admin'	=> ['type' => 'tpl', 'default' => constants::MAINTENANCE_MODE_ADMIN, 'check' => ''],
			'maintenance_mode_mod'		=> ['type' => 'tpl', 'default' => constants::MAINTENANCE_MODE_MOD, 'check' => ''],
			'maintenance_mode_user'		=> ['type' => 'tpl', 'default' => constants::MAINTENANCE_MODE_USER, 'check' => ''],

			'donate_year'		=> ['type' => 'int', 'default' => 0, 'check' => ''],
			'donate_year_value'	=> ['type' => 'int', 'default' => 0, 'check' => ''],
			'donate_fund'		=> ['type' => 'int', 'default' => 0, 'check' => ''],
			'donate_currency'	=> ['type' => 'string', 'default' => '', 'check' => ''],
			'donate_owner'		=> ['type' => 'string_uni', 'default' => '', 'check' => ''],
			'donate_owner_vi'	=> ['type' => 'string_uni', 'default' => '', 'check' => ''],
			'donate_email'		=> ['type' => 'string', 'default' => '', 'check' => ''],
			'donate_bank'		=> ['type' => 'string_uni', 'default' => '', 'check' => ''],
			'donate_bank_vi'	=> ['type' => 'string_uni', 'default' => '', 'check' => ''],
			'donate_bank_acc'	=> ['type' => 'string', 'default' => '', 'check' => ''],
			'donate_bank_swift'	=> ['type' => 'string', 'default' => '', 'check' => ''],
			'donate_paypal'		=> ['type' => 'string', 'default' => '', 'check' => ''],
			'current_year'		=> ['type' => 'tpl', 'default' => date('Y', time()), 'check' => '']
		];
	}

	/**
	* Display main settings
	*/
	public function display_version_settings()
	{
		// Create a form key for preventing CSRF attacks
		add_form_key('acp_settings_version');

		// Submit
		if ($this->request->is_set_post('submit'))
		{
			// Test if the submitted form is valid
			if (!check_form_key('acp_settings_version'))
			{
				$this->errors[] = $this->language->lang('FORM_INVALID');
			}

			// Checking setting values
			$this->check_group_settings('version');

			if (empty($this->errors))
			{
				$this->set_group_settings('version');
				$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_VINABB_SETTINGS_VERSION');
				$this->cache->clear_config_text();

				trigger_error($this->language->lang('MESSAGE_VERSION_SETTINGS_UPDATE') . adm_back_link($this->u_action));
			}
			else
			{
				trigger_error(implode('<br>', $this->errors) . adm_back_link($this->u_action), E_USER_WARNING);
			}
		}

		// Output
		$this->output_group_settings('version');

		$this->template->assign_vars([
			'U_ACTION'	=> $this->u_action
		]);
	}

	/**
	* List of version setting items
	*
	* @return array
	*/
	public function list_version_settings()
	{
		return [
			'check_phpbb_url'				=> ['type' => 'string', 'default' => '', 'check' => ''],
			'check_phpbb_download_url'		=> ['type' => 'string', 'default' => '', 'check' => ''],
			'check_phpbb_download_dev_url'	=> ['type' => 'string', 'default' => '', 'check' => ''],
			'check_phpbb_github_url'		=> ['type' => 'string', 'default' => '', 'check' => ''],
			'check_phpbb_branch'			=> ['type' => 'string', 'default' => '', 'check' => 'regex', 'check_data' => '#^(\d+\.\d+)?$#'],
			'check_phpbb_legacy_branch'		=> ['type' => 'string', 'default' => '', 'check' => 'regex', 'check_data' => '#^(\d+\.\d+)?$#'],
			'check_phpbb_dev_branch'		=> ['type' => 'string', 'default' => '', 'check' => 'regex', 'check_data' => '#^(\d+\.\d+)?$#'],
			'check_php_url'					=> ['type' => 'string', 'default' => '', 'check' => ''],
			'check_php_branch'				=> ['type' => 'string', 'default' => '', 'check' => 'regex', 'check_data' => '#^(\d+\.\d+)?$#'],
			'check_php_legacy_branch'		=> ['type' => 'string', 'default' => '', 'check' => 'regex', 'check_data' => '#^(\d+\.\d+)?$#']
		];
	}

	/**
	* Display main settings
	*/
	public function display_setup_settings()
	{
		// Create a form key for preventing CSRF attacks
		add_form_key('acp_settings_setup');

		// Submit
		if ($this->request->is_set_post('submit'))
		{
			// Test if the submitted form is valid
			if (!check_form_key('acp_settings_setup'))
			{
				$this->errors[] = $this->language->lang('FORM_INVALID');
			}

			// Checking setting values
			$this->check_group_settings('setup');

			if (empty($this->errors))
			{
				$this->set_group_settings('setup');
				$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_VINABB_SETTINGS_SETUP');
				$this->cache->clear_config_text();

				trigger_error($this->language->lang('MESSAGE_SETUP_SETTINGS_UPDATE') . adm_back_link($this->u_action));
			}
			else
			{
				trigger_error(implode('<br>', $this->errors) . adm_back_link($this->u_action), E_USER_WARNING);
			}
		}

		// Output
		$this->output_group_settings('setup');

		$this->template->assign_vars([
			'U_ACTION'	=> $this->u_action
		]);
	}

	/**
	* List of setup setting items
	*
	* @return array
	*/
	public function list_setup_settings()
	{
		return [
			'lang_enable'			=> ['type' => 'bool', 'default' => false, 'check' => ''],
			'lang_switch'			=> ['type' => 'string', 'default' => '', 'check' => ''],
			'default_lang'			=> ['type' => 'tpl', 'default' => $this->get_default_lang_name(), 'check' => ''],
			'lang_switch_options'	=> ['type' => 'tpl', 'default' => $this->build_lang_list($this->config['vinabb_web_lang_switch']), 'check' => ''],

			'forum_id_vietnamese'					=> ['type' => 'int', 'default' => 0, 'check' => ''],
			'forum_id_vietnamese_support'			=> ['type' => 'int', 'default' => 0, 'check' => ''],
			'forum_id_vietnamese_ext'				=> ['type' => 'int', 'default' => 0, 'check' => ''],
			'forum_id_vietnamese_style'				=> ['type' => 'int', 'default' => 0, 'check' => ''],
			'forum_id_vietnamese_tutorial'			=> ['type' => 'int', 'default' => 0, 'check' => ''],
			'forum_id_vietnamese_discussion'		=> ['type' => 'int', 'default' => 0, 'check' => ''],
			'forum_id_english'						=> ['type' => 'int', 'default' => 0, 'check' => ''],
			'forum_id_english_support'				=> ['type' => 'int', 'default' => 0, 'check' => ''],
			'forum_id_english_tutorial'				=> ['type' => 'int', 'default' => 0, 'check' => ''],
			'forum_id_english_discussion'			=> ['type' => 'int', 'default' => 0, 'check' => ''],
			'forum_vietnamese_options'				=> ['type' => 'tpl', 'default' => $this->build_forum_list($this->config['vinabb_web_forum_id_vietnamese']), 'check' => ''],
			'forum_vietnamese_support_options'		=> ['type' => 'tpl', 'default' => $this->build_forum_list($this->config['vinabb_web_forum_id_vietnamese_support']), 'check' => ''],
			'forum_vietnamese_ext_options'			=> ['type' => 'tpl', 'default' => $this->build_forum_list($this->config['vinabb_web_forum_id_vietnamese_ext']), 'check' => ''],
			'forum_vietnamese_style_options'		=> ['type' => 'tpl', 'default' => $this->build_forum_list($this->config['vinabb_web_forum_id_vietnamese_style']), 'check' => ''],
			'forum_vietnamese_tutorial_options'		=> ['type' => 'tpl', 'default' => $this->build_forum_list($this->config['vinabb_web_forum_id_vietnamese_tutorial']), 'check' => ''],
			'forum_vietnamese_discussion_options'	=> ['type' => 'tpl', 'default' => $this->build_forum_list($this->config['vinabb_web_forum_id_vietnamese_discussion']), 'check' => ''],
			'forum_english_options'					=> ['type' => 'tpl', 'default' => $this->build_forum_list($this->config['vinabb_web_forum_id_english']), 'check' => ''],
			'forum_english_support_options'			=> ['type' => 'tpl', 'default' => $this->build_forum_list($this->config['vinabb_web_forum_id_english_support']), 'check' => ''],
			'forum_english_tutorial_options'		=> ['type' => 'tpl', 'default' => $this->build_forum_list($this->config['vinabb_web_forum_id_english_tutorial']), 'check' => ''],
			'forum_english_discussion_options'		=> ['type' => 'tpl', 'default' => $this->build_forum_list($this->config['vinabb_web_forum_id_english_discussion']), 'check' => ''],

			'manager_name'		=> ['type' => 'string_uni', 'default' => '', 'check' => ''],
			'manager_name_vi'	=> ['type' => 'string_uni', 'default' => '', 'check' => ''],
			'manager_username'	=> ['type' => 'string_uni', 'default' => '', 'check' => ''],
			'manager_user_id'	=> ['type' => 'int', 'default' => 0, 'check' => ''],

			'map_api'			=> ['type' => 'string', 'default' => '', 'check' => ''],
			'map_lat'			=> ['type' => 'int', 'default' => 0, 'check' => ''],
			'map_lng'			=> ['type' => 'int', 'default' => 0, 'check' => ''],
			'map_address'		=> ['type' => 'string_uni', 'default' => '', 'check' => ''],
			'map_address_vi'	=> ['type' => 'string_uni', 'default' => '', 'check' => ''],
			'map_phone'			=> ['type' => 'string', 'default' => '', 'check' => ''],
			'map_phone_name'	=> ['type' => 'string_uni', 'default' => '', 'check' => ''],

			'facebook_url'		=> ['type' => 'string', 'default' => '', 'check' => ''],
			'twitter_url'		=> ['type' => 'string', 'default' => '', 'check' => ''],
			'google_plus_url'	=> ['type' => 'string', 'default' => '', 'check' => ''],
			'github_url'		=> ['type' => 'string', 'default' => '', 'check' => '']
		];
	}

	/**
	* Helper to output setting items to template variables
	*
	* @param string $group_name Group name of settings
	*/
	protected function output_group_settings($group_name = 'main')
	{
		foreach ($this->{'list_' . $group_name . '_settings'}() as $name => $data)
		{
			if ($data['type'] == 'tpl')
			{
				$this->template->assign_var(strtoupper($name), $data['default']);
			}
			else
			{
				$this->template->assign_var(strtoupper($name), $this->config['vinabb_web_' . $name]);
			}
		}
	}

	/**
	* Helper to get and check setting items
	*
	* @param string $group_name Group name of settings
	*/
	protected function check_group_settings($group_name = 'main')
	{
		foreach ($this->{'list_' . $group_name . '_settings'}() as $name => $data)
		{
			if ($data['type'] != 'tpl')
			{
				${$name} = $this->request->variable($name, $data['default'], (substr($data['type'], -4) == '_uni'));

				switch ($data['check'])
				{
					case 'empty':
						if (empty(${$name}))
						{
							$this->errors[] = $this->language->lang('ERROR_' . strtoupper($name) . '_EMPTY');
						}
					break;

					case 'regex':
						if (isset($data['check_data']) && !empty($data['check_data']) && !preg_match($data['check_data'], ${$name}))
						{
							$this->errors[] = $this->language->lang('ERROR_' . strtoupper($name) . '_INVALID');
						}
					break;
				}
			}
		}
	}

	/**
	* Helper to save setting items
	*
	* @param string $group_name Group name of settings
	*/
	protected function set_group_settings($group_name = 'main')
	{
		foreach ($this->{'list_' . $group_name . '_settings'}() as $name => $data)
		{
			if ($data['type'] != 'tpl')
			{
				// Get form input
				${$name} = $this->request->variable($name, $data['default'], (substr($data['type'], -4) == '_uni'));

				// Save if the data has changed
				if (${$name} != $this->config['vinabb_web_' . $name])
				{
					$this->config->set('vinabb_web_' . $name, ${$name});
				}
			}
		}
	}

	/**
	* Kill out all normal administrators from the ACP
	* keep only founder-level sessions
	*/
	protected function kill_admin_sessions()
	{
		$founder_user_ids = [];

		$sql = 'SELECT user_id
			FROM ' . USERS_TABLE . '
			WHERE user_type = ' . USER_FOUNDER;
		$result = $this->db->sql_query($sql);
		$rows = $this->db->sql_fetchrowset($result);
		$this->db->sql_freeresult($result);

		foreach ($rows as $row)
		{
			$founder_user_ids[] = $row['user_id'];
		}

		if (sizeof($founder_user_ids))
		{
			$sql = 'UPDATE ' . SESSIONS_TABLE . '
				SET session_admin = 0
				WHERE session_admin = 1
					AND ' . $this->db->sql_in_set('session_user_id', $founder_user_ids, true);
			$this->db->sql_query($sql);
		}
	}

	/**
	* Get default language name
	*
	* @return string
	*/
	protected function get_default_lang_name()
	{
		$data = $this->cache->get_lang_data();

		return $data[$this->config['default_lang']]['english_name'] . ' (' . $data[$this->config['default_lang']]['local_name'] . ')';
	}

	/**
	* Select an extra language to switch
	*
	* @param string $selected_lang 2-letter language ISO code
	* @return string HTML code
	*/
	protected function build_lang_list($selected_lang)
	{
		$sql = 'SELECT *
			FROM ' . LANG_TABLE . "
			WHERE lang_iso <> '" . $this->db->sql_escape($this->config['default_lang']) . "'
			ORDER BY lang_english_name";
		$result = $this->db->sql_query($sql);
		$rows = $this->db->sql_fetchrowset($result);
		$this->db->sql_freeresult($result);

		$lang_switch_options = '<option value=""' . (($selected_lang == '') ? ' selected' : '') . '>' . $this->language->lang('SELECT_LANGUAGE') . '</option>';

		foreach ($rows as $row)
		{
			$lang_switch_options .= '<option value="' . $row['lang_iso'] . '"' . (($selected_lang == $row['lang_iso']) ? ' selected' : '') . '>' . $row['lang_english_name'] . ' (' . $row['lang_local_name'] . ')</option>';
		}

		return $lang_switch_options;
	}

	/**
	* Select categories (not postable forums)
	*
	* @param int $selected_forum Selected forum ID
	* @return string HTML code
	*/
	protected function build_forum_list($selected_forum)
	{
		$forum_options = '<option value=""' . (($selected_forum == 0) ? ' selected' : '') . '>' . $this->language->lang('SELECT_FORUM') . '</option>';

		foreach ($this->forum_data as $forum_id => $data)
		{
			if ($data['type'] == FORUM_CAT)
			{
				$forum_options .= '<option value="' . $forum_id . '"' . (($selected_forum == $forum_id) ? ' selected' : '') . '>' . $data['name'] . '</option>';
			}
		}

		return $forum_options;
	}
}
