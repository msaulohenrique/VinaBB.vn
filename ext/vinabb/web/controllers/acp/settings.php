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

			if (!sizeof($this->errors))
			{
				$this->set_main_settings();
				$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_VINABB_SETTINGS');
				$this->cache->clear_config_text();

				trigger_error($this->language->lang('MESSAGE_VINABB_SETTINGS_UPDATE') . adm_back_link($this->u_action));
			}
		}

		// Output
		$this->output_group_settings('list_main_settings');

		$this->template->assign_vars([
			'ERROR_MSG'	=> sizeof($this->errors) ? implode('<br>', $this->errors) : '',
			'U_ACTION'	=> $this->u_action
		]);
	}

	/**
	* Save main settings
	*/
	public function set_main_settings()
	{
		$this->set_group_settings('list_main_settings');
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
			if (!check_form_key('preventing'))
			{
				$this->errors[] = $this->language->lang('FORM_INVALID');
			}

			if (!sizeof($this->errors))
			{
				$this->set_version_settings();
				$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_VINABB_SETTINGS_VERSION');
				$this->cache->clear_config_text();

				trigger_error($this->language->lang('MESSAGE_VINABB_SETTINGS_VERSION_UPDATE') . adm_back_link($this->u_action));
			}
		}

		// Output
		$this->output_group_settings('list_version_settings');

		$this->template->assign_vars([
			'ERROR_MSG'	=> sizeof($this->errors) ? implode('<br>', $this->errors) : '',
			'U_ACTION'	=> $this->u_action
		]);
	}

	/**
	* Save main settings
	*/
	public function set_version_settings()
	{
		$this->set_group_settings('list_version_settings');
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
			'check_phpbb_branch'			=> ['type' => 'string', 'default' => '', 'check' => ''],
			'check_phpbb_legacy_branch'		=> ['type' => 'string', 'default' => '', 'check' => ''],
			'check_phpbb_dev_branch'		=> ['type' => 'string', 'default' => '', 'check' => ''],
			'check_php_url'					=> ['type' => 'string', 'default' => '', 'check' => ''],
			'check_php_branch'				=> ['type' => 'string', 'default' => '', 'check' => ''],
			'check_php_legacy_branch'		=> ['type' => 'string', 'default' => '', 'check' => '']
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

			if (!sizeof($this->errors))
			{
				$this->set_setup_settings();
				$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_VINABB_SETTINGS_SETUP');
				$this->cache->clear_config_text();

				trigger_error($this->language->lang('MESSAGE_VINABB_SETTINGS_SETUP_UPDATE') . adm_back_link($this->u_action));
			}
		}

		// Output
		$this->output_group_settings('list_setup_settings');

		$this->template->assign_vars([
			'ERROR_MSG'	=> sizeof($this->errors) ? implode('<br>', $this->errors) : '',
			'U_ACTION'	=> $this->u_action
		]);
	}

	/**
	* Save main settings
	*/
	public function set_setup_settings()
	{
		$this->set_group_settings('list_setup_settings');
	}

	/**
	* List of setup setting items
	*
	* @return array
	*/
	public function list_setup_settings()
	{
		return [
			'lang_enable'	=> ['type' => 'bool', 'default' => false, 'check' => ''],
			'lang_switch'	=> ['type' => 'string', 'default' => '', 'check' => ''],

			'forum_id_vietnamese'				=> ['type' => 'int', 'default' => 0, 'check' => ''],
			'forum_id_vietnamese_support'		=> ['type' => 'int', 'default' => 0, 'check' => ''],
			'forum_id_vietnamese_ext'			=> ['type' => 'int', 'default' => 0, 'check' => ''],
			'forum_id_vietnamese_style'			=> ['type' => 'int', 'default' => 0, 'check' => ''],
			'forum_id_vietnamese_tutorial'		=> ['type' => 'int', 'default' => 0, 'check' => ''],
			'forum_id_vietnamese_discussion'	=> ['type' => 'int', 'default' => 0, 'check' => ''],
			'forum_id_english'					=> ['type' => 'int', 'default' => 0, 'check' => ''],
			'forum_id_english_support'			=> ['type' => 'int', 'default' => 0, 'check' => ''],
			'forum_id_english_tutorial'			=> ['type' => 'int', 'default' => 0, 'check' => ''],
			'forum_id_english_discussion'		=> ['type' => 'int', 'default' => 0, 'check' => ''],

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
	* @param string $list_method_name List method name for each group of settings
	*/
	protected function output_group_settings($list_method_name = 'list_main_settings')
	{
		foreach ($this->$list_method_name() as $name => $data)
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
	* Helper to list setting items
	*
	* @param string $list_method_name List method name for each group of settings
	*/
	protected function set_group_settings($list_method_name = 'list_main_settings')
	{
		foreach ($this->$list_method_name() as $name => $data)
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
}
