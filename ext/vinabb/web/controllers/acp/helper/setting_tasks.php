<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\controllers\acp\helper;

use vinabb\web\includes\constants;

/**
* Task helper for the settings_module
*/
class setting_tasks implements setting_tasks_interface
{
	/** @var \vinabb\web\controllers\cache\service_interface $cache */
	protected $cache;

	/** @var \phpbb\config\config $config */
	protected $config;

	/** @var \phpbb\db\driver\driver_interface $db */
	protected $db;

	/** @var \phpbb\language\language $language */
	protected $language;

	/** @var \phpbb\log\log $log */
	protected $log;

	/** @var \phpbb\request\request $request */
	protected $request;

	/** @var \phpbb\user $user */
	protected $user;

	/** @var array $lang_data */
	protected $lang_data;

	/**
	* Constructor
	*
	* @param \vinabb\web\controllers\cache\service_interface	$cache		Cache service
	* @param \phpbb\config\config								$config		Config object
	* @param \phpbb\db\driver\driver_interface					$db			Database object
	* @param \phpbb\language\language							$language	Language object
	* @param \phpbb\log\log										$log		Log object
	* @param \phpbb\request\request								$request	Request object
	* @param \phpbb\user										$user		User object
	*/
	public function __construct(
		\vinabb\web\controllers\cache\service_interface $cache,
		\phpbb\config\config $config,
		\phpbb\db\driver\driver_interface $db,
		\phpbb\language\language $language,
		\phpbb\log\log $log,
		\phpbb\request\request $request,
		\phpbb\user $user
	)
	{
		$this->cache = $cache;
		$this->config = $config;
		$this->db = $db;
		$this->language = $language;
		$this->log = $log;
		$this->request = $request;
		$this->user = $user;

		$this->lang_data = $this->cache->get_lang_data();
	}

	/**
	* Radio options for the config item 'maintenance_mode'
	*
	* @return array
	*/
	public function maintenance_mode_type_data()
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
	public function maintenance_mode_founder($value)
	{
		return !($value == constants::MAINTENANCE_MODE_FOUNDER && $this->user->data['user_type'] != USER_FOUNDER);
	}

	/**
	* Kill out all normal administrators from the ACP
	* keep only founder-level sessions
	*
	* @param int $value Input value
	*/
	public function maintenance_mode_founder_task($value)
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
	public function maintenance_time_task($value)
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
	public function reset_phpbb_version_task($value)
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
	public function reset_phpbb_legacy_version_task($value)
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
	public function reset_phpbb_dev_version_task($value)
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
	public function reset_php_version_task($value)
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
	public function reset_php_legacy_version_task($value)
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
	public function get_default_lang_name()
	{
		return $this->lang_data[$this->config['default_lang']]['english_name'] . ' (' . $this->lang_data[$this->config['default_lang']]['local_name'] . ')';
	}

	/**
	* Select an extra language to switch
	*
	* @param string $selected_lang 2-letter language ISO code
	* @return string HTML code
	*/
	public function build_lang_list($selected_lang)
	{
		$lang_switch_options = '<option value=""' . (($selected_lang == '') ? ' selected' : '') . '>' . $this->language->lang('SELECT_LANGUAGE') . '</option>';

		foreach ($this->lang_data as $lang_iso => $data)
		{
			if ($lang_iso != $this->config['default_lang'])
			{
				$lang_switch_options .= '<option value="' . $lang_iso . '"' . (($selected_lang == $lang_iso) ? ' selected' : '') . '>' . $data['english_name'] . ' (' . $data['local_name'] . ')</option>';
			}
		}

		return $lang_switch_options;
	}

	/**
	* Select categories (not postable forums)
	*
	* @param int $selected_forum Selected forum ID
	* @return string HTML code
	*/
	public function build_forum_list($selected_forum)
	{
		$forum_options = '<option value=""' . (($selected_forum == 0) ? ' selected' : '') . '>' . $this->language->lang('SELECT_FORUM') . '</option>';

		foreach ($this->cache->get_forum_data() as $forum_id => $data)
		{
			if ($data['type'] == FORUM_CAT)
			{
				$forum_options .= '<option value="' . $forum_id . '"' . (($selected_forum == $forum_id) ? ' selected' : '') . '>' . $data['name'] . '</option>';
			}
		}

		return $forum_options;
	}
}
