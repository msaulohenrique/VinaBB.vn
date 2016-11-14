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

	/** @var array List of config items which has data changed, ready to write */
	protected $data = [];

	/** @var array List of methods need to be executed before saving config items */
	protected $tasks = [];

	/** @var array List of errors to be triggered, neither data updated or tasks executed */
	protected $errors = [];

	/** @var array */
	protected $config_text_data = [];

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

		$this->config_text_data = $this->cache->get_config_text();
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
				$this->run_tasks();
				$this->set_group_settings();
				$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_VINABB_SETTINGS');

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
			'PAGE_TITLE_EXPLAIN'	=> $this->language->lang('ACP_VINABB_SETTINGS_EXPLAIN'),
			'FORM_NAME'				=> 'acp_vinabb_settings',

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
			'ACP_CAT_MAINTENANCE'	=> [
				'maintenance_mode'		=> ['type' => 'radio', 'type_data' => $this->maintenance_mode_type_data(), 'default' => 0, 'check' => 'method', 'check_data' => 'maintenance_mode_founder', 'task' => 'maintenance_mode_founder_task'],
				'maintenance_tpl'		=> ['type' => 'radio', 'default' => true],
				'maintenance_time'		=> ['type' => 'int', 'explain' => true, 'append' => $this->language->lang('MINUTES'), 'default' => 0, 'task' => 'maintenance_time_task', 'unset' => true],
				'maintenance_text'		=> ['type' => 'text_uni', 'explain' => true, 'default' => ''],
				'maintenance_text_vi'	=> ['type' => 'text_uni', 'explain' => true, 'default' => '']
			],
			'DONATE'				=> [
				'donate_year'		=> ['type' => 'int', 'type_data' => ['min' => date('Y', time())], 'default' => 0],
				'donate_year_value'	=> ['type' => 'int', 'default' => 0],
				'donate_fund'		=> ['type' => 'int', 'default' => 0]
			]
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
				$this->run_tasks();
				$this->set_group_settings();
				$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_VINABB_SETTINGS_VERSION');

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
			'PAGE_TITLE_EXPLAIN'	=> $this->language->lang('ACP_VINABB_SETTINGS_VERSION_EXPLAIN'),
			'FORM_NAME'				=> 'acp_vinabb_settings_version',

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
			'LATEST_VERSIONS_INFO'	=> [
				'check_phpbb_url'				=> ['type' => 'url', 'default' => ''],
				'check_phpbb_download_url'		=> ['type' => 'url', 'explain' => true, 'default' => ''],
				'check_phpbb_download_dev_url'	=> ['type' => 'url', 'explain' => true, 'default' => ''],
				'check_phpbb_github_url'		=> ['type' => 'url', 'explain' => true, 'default' => ''],
				'check_phpbb_branch'			=> ['type' => 'string', 'default' => '', 'check' => 'regex', 'check_data' => '#^(\d+\.\d+)?$#', 'task' => 'reset_phpbb_version_task'],
				'check_phpbb_legacy_branch'		=> ['type' => 'string', 'default' => '', 'check' => 'regex', 'check_data' => '#^(\d+\.\d+)?$#', 'task' => 'reset_phpbb_legacy_version_task'],
				'check_phpbb_dev_branch'		=> ['type' => 'string', 'default' => '', 'check' => 'regex', 'check_data' => '#^(\d+\.\d+)?$#', 'task' => 'reset_phpbb_dev_version_task'],
				'check_php_url'					=> ['type' => 'url', 'default' => ''],
				'check_php_branch'				=> ['type' => 'string', 'default' => '', 'check' => 'regex', 'check_data' => '#^(\d+\.\d+)?$#', 'task' => 'reset_php_version_task'],
				'check_php_legacy_branch'		=> ['type' => 'string', 'default' => '', 'check' => 'regex', 'check_data' => '#^(\d+\.\d+)?$#', 'task' => 'reset_php_legacy_version_task']
			]
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
				$this->run_tasks();
				$this->set_group_settings();
				$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_VINABB_SETTINGS_SETUP');

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
			'PAGE_TITLE_EXPLAIN'	=> $this->language->lang('ACP_VINABB_SETTINGS_SETUP_EXPLAIN'),
			'FORM_NAME'				=> 'acp_vinabb_settings_setup',

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
			'ACP_LANGUAGE'			=> [
				'default_language'	=> ['type' => 'tpl', 'default' => $this->get_default_lang_name()],
				'lang_switch'		=> ['type' => 'select', 'explain' => true, 'default' => $this->build_lang_list($this->config['vinabb_web_lang_switch'])],
			],
			'FORUMS'				=> [
				'forum_id_vietnamese'				=> ['type' => 'select', 'default' => $this->build_forum_list($this->config['vinabb_web_forum_id_vietnamese'])],
				'forum_id_vietnamese_support'		=> ['type' => 'select', 'default' => $this->build_forum_list($this->config['vinabb_web_forum_id_vietnamese_support'])],
				'forum_id_vietnamese_ext'			=> ['type' => 'select', 'default' => $this->build_forum_list($this->config['vinabb_web_forum_id_vietnamese_ext'])],
				'forum_id_vietnamese_style'			=> ['type' => 'select', 'default' => $this->build_forum_list($this->config['vinabb_web_forum_id_vietnamese_style'])],
				'forum_id_vietnamese_tutorial'		=> ['type' => 'select', 'default' => $this->build_forum_list($this->config['vinabb_web_forum_id_vietnamese_tutorial'])],
				'forum_id_vietnamese_discussion'	=> ['type' => 'select', 'default' => $this->build_forum_list($this->config['vinabb_web_forum_id_vietnamese_discussion'])],
				'forum_id_english'					=> ['type' => 'select', 'default' => $this->build_forum_list($this->config['vinabb_web_forum_id_english'])],
				'forum_id_english_support'			=> ['type' => 'select', 'default' => $this->build_forum_list($this->config['vinabb_web_forum_id_english_support'])],
				'forum_id_english_tutorial'			=> ['type' => 'select', 'default' => $this->build_forum_list($this->config['vinabb_web_forum_id_english_tutorial'])],
				'forum_id_english_discussion'		=> ['type' => 'select', 'default' => $this->build_forum_list($this->config['vinabb_web_forum_id_english_discussion'])]
			],
			'FOOTER_MANAGER_ROLE'	=> [
				'manager_name'		=> ['type' => 'string_uni', 'default' => ''],
				'manager_name_vi'	=> ['type' => 'string_uni', 'default' => ''],
				'manager_username'	=> ['type' => 'string_uni', 'type_data' => ['max' => $this->config['max_name_chars']], 'default' => ''],
				'manager_user_id'	=> ['type' => 'int', 'default' => 0]
			],
			'MAP'					=> [
				'map_api'			=> ['type' => 'string', 'default' => ''],
				'map_lat'			=> ['type' => 'int', 'type_data' => ['step' => 0.000001], 'default' => 0.0],
				'map_lng'			=> ['type' => 'int', 'type_data' => ['step' => 0.000001], 'default' => 0.0],
				'map_address'		=> ['type' => 'string_uni', 'default' => ''],
				'map_address_vi'	=> ['type' => 'string_uni', 'default' => ''],
				'map_phone'			=> ['type' => 'string', 'default' => ''],
				'map_phone_name'	=> ['type' => 'string_uni', 'default' => '']
			],
			'ANALYTICS'				=> [
				'google_analytics_id'	=> ['type' => 'string', 'default' => '']
			],
			'DONATE'				=> [
				'donate_currency'	=> ['type' => 'string', 'type_data' => ['max' => 3], 'default' => ''],
				'donate_owner'		=> ['type' => 'string_uni', 'default' => ''],
				'donate_owner_vi'	=> ['type' => 'string_uni', 'default' => ''],
				'donate_email'		=> ['type' => 'string', 'default' => ''],
				'donate_bank'		=> ['type' => 'string_uni', 'default' => ''],
				'donate_bank_vi'	=> ['type' => 'string_uni', 'default' => ''],
				'donate_bank_acc'	=> ['type' => 'string', 'default' => ''],
				'donate_bank_swift'	=> ['type' => 'string', 'type_data' => ['max' => 11], 'default' => ''],
				'donate_paypal'		=> ['type' => 'url', 'default' => '']
			],
			'SOCIAL_LINKS'			=> [
				'facebook_url'		=> ['type' => 'url', 'default' => ''],
				'twitter_url'		=> ['type' => 'url', 'default' => ''],
				'google_plus_url'	=> ['type' => 'url', 'default' => ''],
				'github_url'		=> ['type' => 'url', 'default' => '']
			]
		];
	}

	/**
	* Helper to output setting items to template variables
	*
	* @param string $group_name Group name of settings
	*/
	protected function output_group_settings($group_name = 'main')
	{
		foreach ($this->{'list_' . $group_name . '_settings'}() as $group_name => $group_data)
		{
			// Group output
			$this->template->assign_block_vars('groups', [
				'LEGEND'	=> $this->language->lang($this->language->is_set($group_name) ? $group_name : 'SETTINGS')
			]);

			foreach ($group_data as $name => $data)
			{
				// Row output
				$this->template->assign_block_vars('groups.rows', [
					'FIELD'		=> $name,
					'TITLE'		=> (substr($name, -3) == '_' . constants::LANG_VIETNAMESE) ? $this->language->lang(strtoupper(substr($name, 0, -3))) . ' (' . $this->language->lang('VIETNAMESE') . ')' : $this->language->lang(strtoupper($name)),
					'EXPLAIN'	=> (isset($data['explain']) && $data['explain'] === true) ? ((substr($name, -3) == '_' . constants::LANG_VIETNAMESE) ? $this->language->lang(strtoupper(substr($name, 0, -3)) . '_EXPLAIN') : $this->language->lang(strtoupper($name) . '_EXPLAIN')) : '',
					'HTML'		=> $this->return_input_html($name, $data),
					'PREPEND'	=> (isset($data['prepend']) && $data['prepend'] != '') ? $data['prepend'] : '',
					'APPEND'	=> (isset($data['append']) && $data['append'] != '') ? $data['append'] : '',
					'EXTRA'		=> (isset($data['extra']) && $data['extra'] != '') ? $data['extra'] : ''
				]);
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
		foreach ($this->{'list_' . $group_name . '_settings'}() as $group_name => $group_data)
		{
			foreach ($group_data as $name => $data)
			{
				if ($data['type'] != 'tpl')
				{
					// Get form input
					${$name} = $this->request->variable($name, $data['default'], (substr($data['type'], -4) == '_uni'));

					// config or config_text?
					$key = (substr($data['type'], 0, 4) == 'text') ? 'config_text_data' : 'config';
					$check = true;

					if (isset($data['check']))
					{
						switch ($data['check'])
						{
							case 'empty':
								if (${$name} == '')
								{
									$this->errors[] = $this->language->lang('ERROR_' . strtoupper($name) . '_EMPTY');
									$check = false;
								}
							break;

							case 'regex':
								if (isset($data['check_data']) && $data['check_data'] != '' && !preg_match($data['check_data'], ${$name}))
								{
									$this->errors[] = $this->language->lang('ERROR_' . strtoupper($name) . '_REGEX');
									$check = false;
								}
							break;

							case 'method':
								if (isset($data['check_data']) && $data['check_data'] != '' && method_exists($this, $data['check_data']) && !$this->{$data['check_data']}(${$name}))
								{
									$this->errors[] = $this->language->lang('ERROR_' . strtoupper($data['check_data']));
									$check = false;
								}
							break;
						}
					}

					// Valid data, add to array if has data changed
					if ($check && ${$name} != $this->$key['vinabb_web_' . $name])
					{
						// This is not a real config item?
						if (!isset($data['unset']) || (isset($data['unset']) && $data['unset'] === false))
						{
							$this->data[$key]['vinabb_web_' . $name] = ${$name};
						}

						// This config item comes with a task?
						if (isset($data['task']) && $data['task'] != '')
						{
							$this->tasks[$data['task']] = ${$name};
						}
					}
				}
			}
		}
	}

	/**
	* Generate HTML from our defined data types for each config row
	*
	* Input types:
	*	tpl: Return a template variable as string {ABC}
	*	int: Integer number: <input type="number"
	*	url: URL <input type="url"
	*	email: email address <input type="email"
	*	string: Text <input type="text"
	*	string_uni: Unicode text <input type="text"
	*	text: Block text <textarea (Stored in the table _config_text)
	*	text_uni: Unicode block text <textarea (Stored in the table _config_text)
	*	radio: Radio button
	*	select: Dropdown selection
	*
	* @param string $name	Config name, used for field name: <input name="..."
	* @param array $data	Config item data
	* @return string HTML code
	*/
	protected function return_input_html($name, $data)
	{
		$html = '';

		switch ($data['type'])
		{
			case 'tpl':
				$html = $data['default'];
			break;

			case 'int':
				$min = (isset($data['type_data']['min']) && is_numeric($data['type_data']['min'])) ? $data['type_data']['min'] : 0;
				$min_html = ($min != '') ? ' min="' . $min . '"' : '';
				$max = (isset($data['type_data']['max']) && is_numeric($data['type_data']['max'])) ? $data['type_data']['max'] : '';
				$max_html = ($max != '') ? ' max=" ' . $max .'"' : '';
				$step = (isset($data['type_data']['step']) && is_numeric($data['type_data']['step'])) ? $data['type_data']['step'] : '';
				$step_html = ($step != '') ? ' step="' . $step . '"' : '';
				$html = '<input type="number" name="' . $name . '" id="' . $name . '"' . $min_html . $max_html . $step_html . ' value="' . $this->config['vinabb_web_' . $name] . '">';
			break;

			case 'url':
			case 'email':
			case 'string':
			case 'string_uni':
				$type = str_replace(['string', 'string_uni'], 'text', $data['type']);
				$maxlength = (isset($data['type_data']['max']) && is_numeric($data['type_data']['max'])) ? $data['type_data']['max'] : constants::MAX_CONFIG_NAME;
				$maxlength_html = ($maxlength != '') ? ' maxlength=" ' . $maxlength .'"' : '';
				$html = '<input class="medium" type="' . $type . '" name="' . $name . '" id="' . $name . '"' . $maxlength_html . ' value="' . $this->config['vinabb_web_' . $name] . '">';
			break;

			case 'text':
			case 'text_uni':
				$rows = (isset($data['type_data']['rows']) && is_numeric($data['type_data']['rows'])) ? $data['type_data']['rows'] : 5;
				$rows_html = ($rows != '') ? ' rows="' . $rows . '"' : '';
				$maxlength = (isset($data['type_data']['max']) && is_numeric($data['type_data']['max'])) ? $data['type_data']['max'] : '';
				$maxlength_html = ($maxlength != '') ? ' maxlength=" ' . $maxlength .'"' : '';
				$html = '<textarea name="' . $name . '" id="' . $name . '"' . $rows_html . $maxlength_html . '>' . $this->config_text_data['vinabb_web_' . $name] . '</textarea>';
			break;

			case 'radio':
				$value = $this->config['vinabb_web_' . $name];

				// Radio with multiple options
				if (isset($data['type_data']) && sizeof($data['type_data']))
				{
					$id_html = ' id="' . $name . '"';

					foreach ($data['type_data'] as $radio_value => $label)
					{
						$checked_html = ($value == $radio_value) ? ' checked' : '';
						$html .= '<label><input type="radio" class="radio" name="' . $name . '"' . $id_html . ' value="' . $radio_value . '"' . $checked_html. '> ' . $label . '</label>';

						// Only assign id="" for the first item
						$id_html = '';
					}
				}
				// Normal radio with yes/no options
				else
				{
					$yes_checked_html = ($value) ? ' checked' : '';
					$no_checked_html = (!$value) ? ' checked' : '';
					$html .= '<label><input type="radio" class="radio" name="' . $name . '" id="' . $name . '" value="1"' . $yes_checked_html . '> ' . $this->language->lang('YES') . '</label>';
					$html .= '<label><input type="radio" class="radio" name="' . $name . '" value="0"' . $no_checked_html. '> ' . $this->language->lang('NO') . '</label>';
				}
			break;

			case 'select':
				$html = '<select name="' . $name . '" id="' . $name . '">' . $data['default'] . '</select>';
			break;
		}

		return $html;
	}

	/**
	* Helper to run taks before set_group_settings()
	*/
	protected function run_tasks()
	{
		if (sizeof($this->tasks))
		{
			foreach ($this->tasks as $method_name => $value)
			{
				if (method_exists($this, $method_name))
				{
					$this->$method_name($value);
				}
			}
		}
	}

	/**
	* Helper to save setting items
	*/
	protected function set_group_settings()
	{
		if (isset($this->data['config']))
		{
			foreach ($this->data['config'] as $config_name => $config_value)
			{
				$this->config->set($config_name, $config_value);
			}
		}

		if (isset($this->data['config_text_data']))
		{
			$this->config_text->set_array($this->data['config_text_data']);
			$this->cache->clear_config_text();
		}
	}

	/**
	* Radio options for the config item 'maintenance_mode'
	*
	* @return array
	*/
	protected function maintenance_mode_type_data()
	{
		return [
			constants::MAINTENANCE_MODE_NONE	=> $this->language->lang('MAINTENANCE_MODE_NONE'),
			constants::MAINTENANCE_MODE_FOUNDER	=> $this->language->lang('MAINTENANCE_MODE_FOUNDER'),
			constants::MAINTENANCE_MODE_ADMIN	=> $this->language->lang('MAINTENANCE_MODE_ADMIN'),
			constants::MAINTENANCE_MODE_MOD		=> $this->language->lang('MAINTENANCE_MODE_MOD'),
			constants::MAINTENANCE_MODE_USER	=> $this->language->lang('MAINTENANCE_MODE_USER')
		];
	}

	/**
	* Check only founders can set the founder-level maintenance mode
	*
	* @param int $value Input value
	* @return bool
	*/
	protected function maintenance_mode_founder($value)
	{
		return !($value == constants::MAINTENANCE_MODE_FOUNDER && $this->user->data['user_type'] != USER_FOUNDER);
	}

	/**
	* Kill out all normal administrators from the ACP
	* keep only founder-level sessions
	*
	* @param int $value Input value
	*/
	protected function maintenance_mode_founder_task($value)
	{
		if ($value == constants::MAINTENANCE_MODE_FOUNDER)
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

	/**
	* Convert the scheduled maintenance time from 'number of minutes' into 'UNIX timestamp'
	*
	* @param int $value Input value
	*/
	protected function maintenance_time_task($value)
	{
		$value = (int) $value;
		$time = time() + ($value * 60);
		$maintenance_time_reset = $this->request->variable('maintenance_time_reset', false);

		if ($value || $maintenance_time_reset)
		{
			$this->config->set('vinabb_web_maintenance_time', $time);
		}
	}

	/**
	* Reset the stored newest phpBB version if the branch has changed
	*
	* @param string $value Input value
	*/
	protected function reset_phpbb_version_task($value)
	{
		if ($value != '' && $this->config['vinabb_web_check_phpbb_branch'] != '')
		{
			$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_VINABB_CHANGE_CHECK_PHPBB_BRANCH', time(), [$this->config['vinabb_web_check_phpbb_branch'], $value]);
		}

		$this->config->set('vinabb_web_check_gc', 0, true);
		$this->config->set('vinabb_web_check_phpbb_version', '');
	}

	/**
	* Reset the stored newest phpBB legacy version if the branch has changed
	*
	* @param string $value Input value
	*/
	protected function reset_phpbb_legacy_version_task($value)
	{
		if ($value != '' && $this->config['vinabb_web_check_phpbb_legacy_branch'] != '')
		{
			$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_VINABB_CHANGE_CHECK_PHPBB_LEGACY_BRANCH', time(), [$this->config['vinabb_web_check_phpbb_legacy_branch'], $value]);
		}

		$this->config->set('vinabb_web_check_gc', 0, true);
		$this->config->set('vinabb_web_check_phpbb_legacy_version', '');
	}

	/**
	* Reset the stored newest phpBB development version if the branch has changed
	*
	* @param string $value Input value
	*/
	protected function reset_phpbb_dev_version_task($value)
	{
		if ($value != '' && $this->config['vinabb_web_check_phpbb_dev_branch'] != '')
		{
			$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_VINABB_CHANGE_CHECK_PHPBB_DEV_BRANCH', time(), [$this->config['vinabb_web_check_phpbb_dev_branch'], $value]);
		}

		$this->config->set('vinabb_web_check_gc', 0, true);
		$this->config->set('vinabb_web_check_phpbb_dev_version', '');
	}

	/**
	* Reset the stored newest PHP version if the branch has changed
	*
	* @param string $value Input value
	*/
	protected function reset_php_version_task($value)
	{
		if ($value != '' && $this->config['vinabb_web_check_php_branch'] != '')
		{
			$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_VINABB_CHANGE_CHECK_PHP_BRANCH', time(), [$this->config['vinabb_web_check_php_branch'], $value]);
		}

		$this->config->set('vinabb_web_check_gc', 0, true);
		$this->config->set('vinabb_web_check_php_version', '');
	}

	/**
	* Reset the stored newest PHP legacy version if the branch has changed
	*
	* @param string $value Input value
	*/
	protected function reset_php_legacy_version_task($value)
	{
		if ($value != '' && $this->config['vinabb_web_check_php_legacy_branch'] != '')
		{
			$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_VINABB_CHANGE_CHECK_PHP_LEGACY_BRANCH', time(), [$this->config['vinabb_web_check_php_legacy_branch'], $value]);
		}

		$this->config->set('vinabb_web_check_gc', 0, true);
		$this->config->set('vinabb_web_check_php_legacy_version', '');
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
