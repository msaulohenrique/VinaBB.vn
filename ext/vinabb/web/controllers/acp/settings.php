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
	* Display main settings
	*/
	public function display_main_settings()
	{
		// Create a form key for preventing CSRF attacks
		add_form_key('acp_settings');

		$errors = [];

		// Submit
		if ($this->request->is_set_post('submit'))
		{
			// Test if the submitted form is valid
			if (!check_form_key('acp_settings'))
			{
				$errors[] = $this->language->lang('FORM_INVALID');
			}

			if (empty($errors))
			{
				$this->set_main_settings();
				$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_VINABB_SETTINGS');
				$this->cache->clear_config_text();

				trigger_error($this->language->lang('VINABB_SETTINGS_UPDATED') . adm_back_link($this->u_action));
			}
		}

		// Output
		$this->template->assign_vars([
			'ERROR_MSG'	=> sizeof($errors) ? implode('<br>', $errors) : '',
			'U_ACTION'	=> $this->u_action
		]);
	}

	/**
	* Save main settings
	*/
	public function set_main_settings()
	{
		$this->config->set('vinabb_web_', 0);
	}
}
