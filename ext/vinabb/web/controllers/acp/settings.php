<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\controllers\acp;

/**
* Controller for the settings_module
*/
class settings extends \vinabb\web\controllers\acp\helper\setting_helper implements settings_interface
{
	/** @var \phpbb\auth\auth $auth */
	protected $auth;

	/** @var \vinabb\web\controllers\cache\service_interface $cache */
	protected $cache;

	/** @var \phpbb\config\config $config */
	protected $config;

	/** @var \phpbb\config\db_text $config_text */
	protected $config_text;

	/** @var \phpbb\language\language $language */
	protected $language;

	/** @var \phpbb\log\log $log */
	protected $log;

	/** @var \phpbb\request\request $request */
	protected $request;

	/** @var \phpbb\template\template $template */
	protected $template;

	/** @var \phpbb\user $user */
	protected $user;

	/** @var \vinabb\web\controllers\acp\helper\setting_tasks_interface $task_helper */
	protected $task_helper;

	/** @var string $u_action */
	protected $u_action;

	/** @var array $errors List of errors to be triggered, neither data updated or tasks executed */
	protected $errors;

	/**
	* Constructor
	*
	* @param \phpbb\auth\auth											$auth			Authentication object
	* @param \vinabb\web\controllers\cache\service_interface			$cache			Cache service
	* @param \phpbb\config\config										$config			Config object
	* @param \phpbb\config\db_text										$config_text	Config text object
	* @param \phpbb\language\language									$language		Language object
	* @param \phpbb\log\log												$log			Log object
	* @param \phpbb\request\request										$request		Request object
	* @param \phpbb\template\template									$template		Template object
	* @param \phpbb\user												$user			User object
	* @param \vinabb\web\controllers\acp\helper\setting_tasks_interface	$task_helper	Task helper
	*/
	public function __construct(
		\phpbb\auth\auth $auth,
		\vinabb\web\controllers\cache\service_interface $cache,
		\phpbb\config\config $config,
		\phpbb\config\db_text $config_text,
		\phpbb\language\language $language,
		\phpbb\log\log $log,
		\phpbb\request\request $request,
		\phpbb\template\template $template,
		\phpbb\user $user,
		\vinabb\web\controllers\acp\helper\setting_tasks_interface $task_helper
	)
	{
		$this->auth = $auth;
		$this->cache = $cache;
		$this->config = $config;
		$this->config_text = $config_text;
		$this->language = $language;
		$this->log = $log;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->task_helper = $task_helper;
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
		add_form_key('acp_vinabb_settings');

		// Submit
		if ($this->request->is_set_post('submit'))
		{
			// Test if the submitted form is valid
			if (!check_form_key('acp_vinabb_settings'))
			{
				$this->errors[] = $this->language->lang('FORM_INVALID');
			}

			// Checking setting values
			$this->check_group_settings('main');

			if (!sizeof($this->errors))
			{
				$this->run_tasks();
				$this->set_group_settings();
				$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_VINABB_SETTINGS');

				trigger_error($this->language->lang('MESSAGE_MAIN_SETTINGS_UPDATE') . adm_back_link($this->u_action));
			}
		}

		// Output
		$this->output_group_settings('main');

		$this->template->assign_vars([
			'PAGE_TITLE_EXPLAIN'	=> $this->language->lang('ACP_VINABB_SETTINGS_EXPLAIN'),
			'FORM_NAME'				=> 'acp_vinabb_settings',
			'ERRORS'				=> sizeof($this->errors) ? implode('<br>', $this->errors) : '',

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
				'maintenance_mode'		=> ['type' => 'radio', 'type_data' => $this->task_helper->maintenance_mode_type_data(), 'default' => 0, 'check' => 'method', 'check_data' => 'maintenance_mode_founder', 'task' => 'maintenance_mode_founder_task'],
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
		add_form_key('acp_vinabb_settings_version');

		// Submit
		if ($this->request->is_set_post('submit'))
		{
			// Test if the submitted form is valid
			if (!check_form_key('acp_vinabb_settings_version'))
			{
				$this->errors[] = $this->language->lang('FORM_INVALID');
			}

			// Checking setting values
			$this->check_group_settings('version');

			if (!sizeof($this->errors))
			{
				$this->run_tasks();
				$this->set_group_settings();
				$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_VINABB_SETTINGS_VERSION');

				trigger_error($this->language->lang('MESSAGE_VERSION_SETTINGS_UPDATE') . adm_back_link($this->u_action));
			}
		}

		// Output
		$this->output_group_settings('version');

		$this->template->assign_vars([
			'PAGE_TITLE_EXPLAIN'	=> $this->language->lang('ACP_VINABB_SETTINGS_VERSION_EXPLAIN'),
			'FORM_NAME'				=> 'acp_vinabb_settings_version',
			'ERRORS'				=> sizeof($this->errors) ? implode('<br>', $this->errors) : '',

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
		add_form_key('acp_vinabb_settings_setup');

		// Submit
		if ($this->request->is_set_post('submit'))
		{
			// Test if the submitted form is valid
			if (!check_form_key('acp_vinabb_settings_setup'))
			{
				$this->errors[] = $this->language->lang('FORM_INVALID');
			}

			// Checking setting values
			$this->check_group_settings('setup');

			if (!sizeof($this->errors))
			{
				$this->run_tasks();
				$this->set_group_settings();
				$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_VINABB_SETTINGS_SETUP');

				trigger_error($this->language->lang('MESSAGE_SETUP_SETTINGS_UPDATE') . adm_back_link($this->u_action));
			}
		}

		// Output
		$this->output_group_settings('setup');

		$this->template->assign_vars([
			'PAGE_TITLE_EXPLAIN'	=> $this->language->lang('ACP_VINABB_SETTINGS_SETUP_EXPLAIN'),
			'FORM_NAME'				=> 'acp_vinabb_settings_setup',
			'ERRORS'				=> sizeof($this->errors) ? implode('<br>', $this->errors) : '',

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
				'default_language'	=> ['type' => 'tpl', 'default' => $this->task_helper->get_default_lang_name()],
				'lang_switch'		=> ['type' => 'select', 'explain' => true, 'default' => $this->task_helper->build_lang_list($this->config['vinabb_web_lang_switch'])],
			],
			'FORUMS'				=> [
				'forum_id_vietnamese'				=> ['type' => 'select', 'default' => $this->task_helper->build_forum_list($this->config['vinabb_web_forum_id_vietnamese'])],
				'forum_id_vietnamese_support'		=> ['type' => 'select', 'default' => $this->task_helper->build_forum_list($this->config['vinabb_web_forum_id_vietnamese_support'])],
				'forum_id_vietnamese_ext'			=> ['type' => 'select', 'default' => $this->task_helper->build_forum_list($this->config['vinabb_web_forum_id_vietnamese_ext'])],
				'forum_id_vietnamese_style'			=> ['type' => 'select', 'default' => $this->task_helper->build_forum_list($this->config['vinabb_web_forum_id_vietnamese_style'])],
				'forum_id_vietnamese_tutorial'		=> ['type' => 'select', 'default' => $this->task_helper->build_forum_list($this->config['vinabb_web_forum_id_vietnamese_tutorial'])],
				'forum_id_vietnamese_discussion'	=> ['type' => 'select', 'default' => $this->task_helper->build_forum_list($this->config['vinabb_web_forum_id_vietnamese_discussion'])],
				'forum_id_english'					=> ['type' => 'select', 'default' => $this->task_helper->build_forum_list($this->config['vinabb_web_forum_id_english'])],
				'forum_id_english_support'			=> ['type' => 'select', 'default' => $this->task_helper->build_forum_list($this->config['vinabb_web_forum_id_english_support'])],
				'forum_id_english_tutorial'			=> ['type' => 'select', 'default' => $this->task_helper->build_forum_list($this->config['vinabb_web_forum_id_english_tutorial'])],
				'forum_id_english_discussion'		=> ['type' => 'select', 'default' => $this->task_helper->build_forum_list($this->config['vinabb_web_forum_id_english_discussion'])]
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
}
