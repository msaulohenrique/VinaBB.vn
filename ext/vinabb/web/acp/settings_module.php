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
		global $phpbb_container;

		$this->auth = $phpbb_container->get('auth');
		$this->cache = $phpbb_container->get('cache');
		$this->config = $phpbb_container->get('config');
		$this->config_text = $phpbb_container->get('config_text');
		$this->db = $phpbb_container->get('dbal.conn');
		$this->ext_manager = $phpbb_container->get('ext.manager');
		$this->language = $phpbb_container->get('language');
		$this->log = $phpbb_container->get('log');
		$this->request = $phpbb_container->get('request');
		$this->template = $phpbb_container->get('template');
		$this->user = $phpbb_container->get('user');

		$this->tpl_name = 'acp_settings';
		$this->page_title = $this->language->lang('ACP_VINABB_SETTINGS');
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
			$lang_enable = $this->request->variable('lang_enable', false);
			$lang_switch = $this->request->variable('lang_switch', '');

			$maintenance_mode = $this->request->variable('maintenance_mode', constants::MAINTENANCE_MODE_ADMIN);
			$maintenance_tpl = $this->request->variable('maintenance_tpl', true);
			$maintenance_time = $this->request->variable('maintenance_time', 0);
			$maintenance_time_reset = $this->request->variable('maintenance_time_reset', false);
			$maintenance_text = $this->request->variable('maintenance_text', '', true);
			$maintenance_text_vi = $this->request->variable('maintenance_text_vi', '', true);

			$forum_id_vietnamese = $this->request->variable('forum_id_vietnamese', 0);
			$forum_id_vietnamese_support = $this->request->variable('forum_id_vietnamese_support', 0);
			$forum_id_vietnamese_ext = $this->request->variable('forum_id_vietnamese_ext', 0);
			$forum_id_vietnamese_style = $this->request->variable('forum_id_vietnamese_style', 0);
			$forum_id_vietnamese_tutorial = $this->request->variable('forum_id_vietnamese_tutorial', 0);
			$forum_id_vietnamese_discussion = $this->request->variable('forum_id_vietnamese_discussion', 0);
			$forum_id_english = $this->request->variable('forum_id_english', 0);
			$forum_id_english_support = $this->request->variable('forum_id_english_support', 0);
			$forum_id_english_tutorial = $this->request->variable('forum_id_english_tutorial', 0);
			$forum_id_english_discussion = $this->request->variable('forum_id_english_discussion', 0);

			$manager_name = $this->request->variable('manager_name', '', true);
			$manager_username = $this->request->variable('manager_username', '', true);
			$manager_user_id = $this->request->variable('manager_user_id', 0);

			$donate_year = $this->request->variable('donate_year', 0);
			$donate_year_value = $this->request->variable('donate_year_value', 0);
			$donate_fund = $this->request->variable('donate_fund', 0);
			$donate_currency = strtoupper($this->request->variable('donate_currency', ''));
			$donate_owner = $this->request->variable('donate_owner', '', true);
			$donate_owner_vi = $this->request->variable('donate_owner_vi', '', true);
			$donate_email = $this->request->variable('donate_email', '');
			$donate_bank = $this->request->variable('donate_bank', '', true);
			$donate_bank_vi = $this->request->variable('donate_bank_vi', '', true);
			$donate_bank_acc = $this->request->variable('donate_bank_acc', '');
			$donate_bank_swift = strtoupper($this->request->variable('donate_bank_swift', ''));
			$donate_paypal = $this->request->variable('donate_paypal', '');

			$map_api = $this->request->variable('map_api', '');
			$map_lat = $this->request->variable('map_lat', 0.0);
			$map_lng = $this->request->variable('map_lng', 0.0);
			$map_address = $this->request->variable('map_address', '', true);
			$map_address_vi = $this->request->variable('map_address_vi', '', true);
			$map_phone = $this->request->variable('map_phone', '');
			$map_phone_name = $this->request->variable('map_phone_name', '', true);

			$facebook_url = $this->request->variable('facebook_url', '');
			$twitter_url = $this->request->variable('twitter_url', '');
			$google_plus_url = $this->request->variable('google_plus_url', '');
			$github_url = $this->request->variable('github_url', '');

			$check_phpbb_url = $this->request->variable('check_phpbb_url', '');
			$check_phpbb_download_url = $this->request->variable('check_phpbb_download_url', '');
			$check_phpbb_download_dev_url = $this->request->variable('check_phpbb_download_dev_url', '');
			$check_phpbb_github_url = $this->request->variable('check_phpbb_github_url', '');
			$check_phpbb_branch = $this->request->variable('check_phpbb_branch', '');
			$check_phpbb_legacy_branch = $this->request->variable('check_phpbb_legacy_branch', '');
			$check_phpbb_dev_branch = $this->request->variable('check_phpbb_dev_branch', '');
			$check_php_url = $this->request->variable('check_php_url', '');
			$check_php_branch = $this->request->variable('check_php_branch', '');
			$check_php_legacy_branch = $this->request->variable('check_php_legacy_branch', '');

			// Check switch lang
			if ($lang_enable && (empty($lang_switch) || $lang_switch == $this->config['default_lang']))
			{
				$lang_enable = false;
				$lang_switch = '';
			}

			// Check maintenance mode
			if ($maintenance_mode == constants::MAINTENANCE_MODE_FOUNDER && $this->user->data['user_type'] != USER_FOUNDER)
			{
				$errors[] = $this->language->lang('ERROR_MAINTENANCE_MODE_FOUNDER');
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

			// Check latest phpBB branch version
			if (!preg_match('#^(\d+\.\d+)?$#', $check_phpbb_branch))
			{
				$errors[] = $this->language->lang('ERROR_PHPBB_BRANCH_INVALID');
			}

			// Check legacy phpBB branch version
			if (!preg_match('#^(\d+\.\d+)?$#', $check_phpbb_legacy_branch))
			{
				$errors[] = $this->language->lang('ERROR_PHPBB_LEGACY_BRANCH_INVALID');
			}

			// Check development phpBB branch version
			if (!preg_match('#^(\d+\.\d+)?$#', $check_phpbb_dev_branch))
			{
				$errors[] = $this->language->lang('ERROR_PHPBB_DEV_BRANCH_INVALID');
			}

			// Check latest PHP branch version
			if (!preg_match('#^(\d+\.\d+)?$#', $check_php_branch))
			{
				$errors[] = $this->language->lang('ERROR_PHP_BRANCH_INVALID');
			}

			// Check legacy PHP branch version
			if (!preg_match('#^(\d+\.\d+)?$#', $check_php_legacy_branch))
			{
				$errors[] = $this->language->lang('ERROR_PHP_LEGACY_BRANCH_INVALID');
			}

			if (empty($errors))
			{
				// Kill out all normal administrators from the ACP
				if ($maintenance_mode == constants::MAINTENANCE_MODE_FOUNDER)
				{
					$founder_user_ids = array();

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

				// Maintenance with scheduled time
				if ($maintenance_time || $maintenance_time_reset)
				{
					$this->config->set('vinabb_web_maintenance_time', $maintenance_time);
				}

				// Need to reset last check time for new versions?
				if ($check_phpbb_branch != $this->config['vinabb_web_check_phpbb_branch'])
				{
					$this->config->set('vinabb_web_check_gc', 0, true);
					$this->config->set('vinabb_web_check_phpbb_version', '');
				}

				if ($check_phpbb_legacy_branch != $this->config['vinabb_web_check_phpbb_legacy_branch'])
				{
					$this->config->set('vinabb_web_check_gc', 0, true);
					$this->config->set('vinabb_web_check_phpbb_legacy_version', '');
				}

				if ($check_phpbb_dev_branch != $this->config['vinabb_web_check_phpbb_dev_branch'])
				{
					$this->config->set('vinabb_web_check_gc', 0, true);
					$this->config->set('vinabb_web_check_phpbb_dev_version', '');
				}

				if ($check_php_branch != $this->config['vinabb_web_check_php_branch'])
				{
					$this->config->set('vinabb_web_check_gc', 0, true);
					$this->config->set('vinabb_web_check_php_version', '');
				}

				if ($check_php_legacy_branch != $this->config['vinabb_web_check_php_legacy_branch'])
				{
					$this->config->set('vinabb_web_check_gc', 0, true);
					$this->config->set('vinabb_web_check_php_legacy_version', '');
				}

				// Save settings
				$this->config->set('vinabb_web_lang_enable', $lang_enable);
				$this->config->set('vinabb_web_lang_switch', $lang_switch);
				$this->config->set('vinabb_web_maintenance_mode', $maintenance_mode);
				$this->config->set('vinabb_web_maintenance_tpl', $maintenance_tpl);
				$this->config_text->set_array(array(
					'vinabb_web_maintenance_text'		=> $maintenance_text,
					'vinabb_web_maintenance_text_vi'	=> $maintenance_text_vi
				));

				$this->config->set('vinabb_web_forum_id_vietnamese', $forum_id_vietnamese);
				$this->config->set('vinabb_web_forum_id_vietnamese_support', $forum_id_vietnamese_support);
				$this->config->set('vinabb_web_forum_id_vietnamese_ext', $forum_id_vietnamese_ext);
				$this->config->set('vinabb_web_forum_id_vietnamese_style', $forum_id_vietnamese_style);
				$this->config->set('vinabb_web_forum_id_vietnamese_tutorial', $forum_id_vietnamese_tutorial);
				$this->config->set('vinabb_web_forum_id_vietnamese_discussion', $forum_id_vietnamese_discussion);
				$this->config->set('vinabb_web_forum_id_english', $forum_id_english);
				$this->config->set('vinabb_web_forum_id_english_support', $forum_id_english_support);
				$this->config->set('vinabb_web_forum_id_english_tutorial', $forum_id_english_tutorial);
				$this->config->set('vinabb_web_forum_id_english_discussion', $forum_id_english_discussion);

				$this->config->set('vinabb_web_manager_name', $manager_name);
				$this->config->set('vinabb_web_manager_username', $manager_username);
				$this->config->set('vinabb_web_manager_user_id', $manager_user_id);

				$this->config->set('vinabb_web_donate_year', $donate_year);
				$this->config->set('vinabb_web_donate_year_value', $donate_year_value);
				$this->config->set('vinabb_web_donate_fund', $donate_fund);
				$this->config->set('vinabb_web_donate_currency', $donate_currency);
				$this->config->set('vinabb_web_donate_owner', $donate_owner);
				$this->config->set('vinabb_web_donate_owner_vi', $donate_owner_vi);
				$this->config->set('vinabb_web_donate_email', $donate_email);
				$this->config->set('vinabb_web_donate_bank', $donate_bank);
				$this->config->set('vinabb_web_donate_bank_vi', $donate_bank_vi);
				$this->config->set('vinabb_web_donate_bank_acc', $donate_bank_acc);
				$this->config->set('vinabb_web_donate_bank_swift', $donate_bank_swift);
				$this->config->set('vinabb_web_donate_paypal', $donate_paypal);

				$this->config->set('vinabb_web_map_api', $map_api);
				$this->config->set('vinabb_web_map_lat', $map_lat);
				$this->config->set('vinabb_web_map_lng', $map_lng);
				$this->config->set('vinabb_web_map_address', $map_address);
				$this->config->set('vinabb_web_map_address_vi', $map_address_vi);
				$this->config->set('vinabb_web_map_phone', $map_phone);
				$this->config->set('vinabb_web_map_phone_name', $map_phone_name);

				$this->config->set('vinabb_web_facebook_url', $facebook_url);
				$this->config->set('vinabb_web_twitter_url', $twitter_url);
				$this->config->set('vinabb_web_google_plus_url', $google_plus_url);
				$this->config->set('vinabb_web_github_url', $github_url);

				$this->config->set('vinabb_web_check_phpbb_url', $check_phpbb_url);
				$this->config->set('vinabb_web_check_phpbb_download_url', $check_phpbb_download_url);
				$this->config->set('vinabb_web_check_phpbb_download_dev_url', $check_phpbb_download_dev_url);
				$this->config->set('vinabb_web_check_phpbb_github_url', $check_phpbb_github_url);
				$this->config->set('vinabb_web_check_phpbb_branch', $check_phpbb_branch);
				$this->config->set('vinabb_web_check_phpbb_legacy_branch', $check_phpbb_legacy_branch);
				$this->config->set('vinabb_web_check_phpbb_dev_branch', $check_phpbb_dev_branch);
				$this->config->set('vinabb_web_check_php_url', $check_php_url);
				$this->config->set('vinabb_web_check_php_branch', $check_php_branch);
				$this->config->set('vinabb_web_check_php_legacy_branch', $check_php_legacy_branch);

				$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_VINABB_SETTINGS');

				$this->cache->clear_config_text_data();

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

		// Get data from the config_text table
		$data = $this->config_text->get_array(array(
			'vinabb_web_maintenance_text',
			'vinabb_web_maintenance_text_vi'
		));

		// Select forums
		$sql = 'SELECT forum_id, forum_name
			FROM ' . FORUMS_TABLE . '
			WHERE forum_type = ' . FORUM_CAT . '
			ORDER BY left_id';
		$result = $this->db->sql_query($sql);
		$rows = $this->db->sql_fetchrowset($result);
		$this->db->sql_freeresult($result);

		$forum_vietnamese_options = '<option value=""' . ((!$this->config['vinabb_web_forum_id_vietnamese']) ? ' selected' : '' ) . '>' . $this->language->lang('SELECT_FORUM') . '</option>';
		$forum_vietnamese_support_options = '<option value=""' . ((!$this->config['vinabb_web_forum_id_vietnamese_support']) ? ' selected' : '' ) . '>' . $this->language->lang('SELECT_FORUM') . '</option>';
		$forum_vietnamese_ext_options = '<option value=""' . ((!$this->config['vinabb_web_forum_id_vietnamese_ext']) ? ' selected' : '' ) . '>' . $this->language->lang('SELECT_FORUM') . '</option>';
		$forum_vietnamese_style_options = '<option value=""' . ((!$this->config['vinabb_web_forum_id_vietnamese_style']) ? ' selected' : '' ) . '>' . $this->language->lang('SELECT_FORUM') . '</option>';
		$forum_vietnamese_tutorial_options = '<option value=""' . ((!$this->config['vinabb_web_forum_id_vietnamese_tutorial']) ? ' selected' : '' ) . '>' . $this->language->lang('SELECT_FORUM') . '</option>';
		$forum_vietnamese_discussion_options = '<option value=""' . ((!$this->config['vinabb_web_forum_id_vietnamese_discussion']) ? ' selected' : '' ) . '>' . $this->language->lang('SELECT_FORUM') . '</option>';
		$forum_english_options = '<option value=""' . ((!$this->config['vinabb_web_forum_id_english']) ? ' selected' : '' ) . '>' . $this->language->lang('SELECT_FORUM') . '</option>';
		$forum_english_support_options = '<option value=""' . ((!$this->config['vinabb_web_forum_id_english_support']) ? ' selected' : '' ) . '>' . $this->language->lang('SELECT_FORUM') . '</option>';
		$forum_english_tutorial_options = '<option value=""' . ((!$this->config['vinabb_web_forum_id_english_tutorial']) ? ' selected' : '' ) . '>' . $this->language->lang('SELECT_FORUM') . '</option>';
		$forum_english_discussion_options = '<option value=""' . ((!$this->config['vinabb_web_forum_id_english_discussion']) ? ' selected' : '' ) . '>' . $this->language->lang('SELECT_FORUM') . '</option>';

		foreach ($rows as $row)
		{
			$forum_vietnamese_options .= '<option value="' . $row['forum_id'] . '"' . (($this->config['vinabb_web_forum_id_vietnamese'] == $row['forum_id']) ? ' selected' : '' ) . '>' . $row['forum_name'] . '</option>';
			$forum_vietnamese_support_options .= '<option value="' . $row['forum_id'] . '"' . (($this->config['vinabb_web_forum_id_vietnamese_support'] == $row['forum_id']) ? ' selected' : '' ) . '>' . $row['forum_name'] . '</option>';
			$forum_vietnamese_ext_options .= '<option value="' . $row['forum_id'] . '"' . (($this->config['vinabb_web_forum_id_vietnamese_ext'] == $row['forum_id']) ? ' selected' : '' ) . '>' . $row['forum_name'] . '</option>';
			$forum_vietnamese_style_options .= '<option value="' . $row['forum_id'] . '"' . (($this->config['vinabb_web_forum_id_vietnamese_style'] == $row['forum_id']) ? ' selected' : '' ) . '>' . $row['forum_name'] . '</option>';
			$forum_vietnamese_tutorial_options .= '<option value="' . $row['forum_id'] . '"' . (($this->config['vinabb_web_forum_id_vietnamese_tutorial'] == $row['forum_id']) ? ' selected' : '' ) . '>' . $row['forum_name'] . '</option>';
			$forum_vietnamese_discussion_options .= '<option value="' . $row['forum_id'] . '"' . (($this->config['vinabb_web_forum_id_vietnamese_discussion'] == $row['forum_id']) ? ' selected' : '' ) . '>' . $row['forum_name'] . '</option>';
			$forum_english_options .= '<option value="' . $row['forum_id'] . '"' . (($this->config['vinabb_web_forum_id_english'] == $row['forum_id']) ? ' selected' : '' ) . '>' . $row['forum_name'] . '</option>';
			$forum_english_support_options .= '<option value="' . $row['forum_id'] . '"' . (($this->config['vinabb_web_forum_id_english_support'] == $row['forum_id']) ? ' selected' : '' ) . '>' . $row['forum_name'] . '</option>';
			$forum_english_tutorial_options .= '<option value="' . $row['forum_id'] . '"' . (($this->config['vinabb_web_forum_id_english_tutorial'] == $row['forum_id']) ? ' selected' : '' ) . '>' . $row['forum_name'] . '</option>';
			$forum_english_discussion_options .= '<option value="' . $row['forum_id'] . '"' . (($this->config['vinabb_web_forum_id_english_discussion'] == $row['forum_id']) ? ' selected' : '' ) . '>' . $row['forum_name'] . '</option>';
		}

		// Output
		$this->template->assign_vars(array(
			'LANG_ENABLE'			=> isset($lang_enable) ? $lang_enable : $this->config['vinabb_web_lang_enable'],
			'DEFAULT_LANG'			=> $default_lang_name,
			'LANG_SWITCH_OPTIONS'	=> $lang_switch_options,

			'MAINTENANCE_MODE'			=> isset($maintenance_mode) ? $maintenance_mode : $this->config['vinabb_web_maintenance_mode'],
			'MAINTENANCE_TPL'			=> isset($maintenance_tpl) ? $maintenance_tpl : $this->config['vinabb_web_maintenance_tpl'],
			'MAINTENANCE_TIME'			=> isset($maintenance_time) ? $maintenance_time : 0,
			'MAINTENANCE_TIME_REMAIN'	=> ($this->config['vinabb_web_maintenance_time'] > time()) ? $this->user->format_date($this->config['vinabb_web_maintenance_time']) : '',
			'MAINTENANCE_TEXT'			=> (isset($maintenance_text) && !empty($maintenance_text)) ? $maintenance_text : $data['vinabb_web_maintenance_text'],
			'MAINTENANCE_TEXT_VI'		=> (isset($maintenance_text_vi) && !empty($maintenance_text_vi)) ? $maintenance_text_vi : $data['vinabb_web_maintenance_text_vi'],
			'MAINTENANCE_MODE_NONE'		=> constants::MAINTENANCE_MODE_NONE,
			'MAINTENANCE_MODE_FOUNDER'	=> constants::MAINTENANCE_MODE_FOUNDER,
			'MAINTENANCE_MODE_ADMIN'	=> constants::MAINTENANCE_MODE_ADMIN,
			'MAINTENANCE_MODE_MOD'		=> constants::MAINTENANCE_MODE_MOD,
			'MAINTENANCE_MODE_USER'		=> constants::MAINTENANCE_MODE_USER,

			'FORUM_VIETNAMESE_OPTIONS'				=> $forum_vietnamese_options,
			'FORUM_VIETNAMESE_SUPPORT_OPTIONS'		=> $forum_vietnamese_support_options,
			'FORUM_VIETNAMESE_EXT_OPTIONS'			=> $forum_vietnamese_ext_options,
			'FORUM_VIETNAMESE_STYLE_OPTIONS'		=> $forum_vietnamese_style_options,
			'FORUM_VIETNAMESE_TUTORIAL_OPTIONS'		=> $forum_vietnamese_tutorial_options,
			'FORUM_VIETNAMESE_DISCUSSION_OPTIONS'	=> $forum_vietnamese_discussion_options,
			'FORUM_ENGLISH_OPTIONS'					=> $forum_english_options,
			'FORUM_ENGLISH_SUPPORT_OPTIONS'			=> $forum_english_support_options,
			'FORUM_ENGLISH_TUTORIAL_OPTIONS'		=> $forum_english_tutorial_options,
			'FORUM_ENGLISH_DISCUSSION_OPTIONS'		=> $forum_english_discussion_options,

			'MANAGER_NAME'		=> isset($manager_name) ? $manager_name : $this->config['vinabb_web_manager_name'],
			'MANAGER_USERNAME'	=> isset($manager_username) ? $manager_username : $this->config['vinabb_web_manager_username'],
			'MANAGER_USER_ID'	=> isset($manager_user_id) ? $manager_user_id : $this->config['vinabb_web_manager_user_id'],

			'CURRENT_YEAR'		=> date('Y', time()),
			'DONATE_YEAR'		=> isset($donate_year) ? $donate_year : $this->config['vinabb_web_donate_year'],
			'DONATE_YEAR_VALUE'	=> isset($donate_year_value) ? $donate_year_value : $this->config['vinabb_web_donate_year_value'],
			'DONATE_FUND'		=> isset($donate_fund) ? $donate_fund : $this->config['vinabb_web_donate_fund'],
			'DONATE_CURRENCY'	=> isset($donate_currency) ? $donate_currency : $this->config['vinabb_web_donate_currency'],
			'DONATE_OWNER'		=> isset($donate_owner) ? $donate_owner : $this->config['vinabb_web_donate_owner'],
			'DONATE_OWNER_VI'	=> isset($donate_owner_vi) ? $donate_owner_vi : $this->config['vinabb_web_donate_owner_vi'],
			'DONATE_EMAIL'		=> isset($donate_email) ? $donate_email : $this->config['vinabb_web_donate_email'],
			'DONATE_BANK'		=> isset($donate_bank) ? $donate_bank : $this->config['vinabb_web_donate_bank'],
			'DONATE_BANK_VI'	=> isset($donate_bank_vi) ? $donate_bank_vi : $this->config['vinabb_web_donate_bank_vi'],
			'DONATE_BANK_ACC'	=> isset($donate_bank_acc) ? $donate_bank_acc : $this->config['vinabb_web_donate_bank_acc'],
			'DONATE_BANK_SWIFT'	=> isset($donate_bank_swift) ? $donate_bank_swift : $this->config['vinabb_web_donate_bank_swift'],
			'DONATE_PAYPAL'		=> isset($donate_paypal) ? $donate_paypal : $this->config['vinabb_web_donate_paypal'],

			'MAP_API'			=> isset($map_api) ? $map_api : $this->config['vinabb_web_map_api'],
			'MAP_LAT'			=> isset($map_lat) ? $map_lat : $this->config['vinabb_web_map_lat'],
			'MAP_LNG'			=> isset($map_lng) ? $map_lng : $this->config['vinabb_web_map_lng'],
			'MAP_ADDRESS'		=> isset($map_address) ? $map_address : $this->config['vinabb_web_map_address'],
			'MAP_ADDRESS_VI'	=> isset($map_address_vi) ? $map_address_vi : $this->config['vinabb_web_map_address_vi'],
			'MAP_PHONE'			=> isset($map_phone) ? $map_phone : $this->config['vinabb_web_map_phone'],
			'MAP_PHONE_NAME'	=> isset($map_phone_name) ? $map_phone_name : $this->config['vinabb_web_map_phone_name'],

			'FACEBOOK_URL'		=> isset($facebook_url) ? $facebook_url : $this->config['vinabb_web_facebook_url'],
			'TWITTER_URL'		=> isset($twitter_url) ? $twitter_url : $this->config['vinabb_web_twitter_url'],
			'GOOGLE_PLUS_URL'	=> isset($google_plus_url) ? $google_plus_url : $this->config['vinabb_web_google_plus_url'],
			'GITHUB_URL'		=> isset($github_url) ? $github_url : $this->config['vinabb_web_github_url'],

			'CHECK_PHPBB_URL'				=> isset($check_phpbb_url) ? $check_phpbb_url : $this->config['vinabb_web_check_phpbb_url'],
			'CHECK_PHPBB_DOWNLOAD_URL'		=> isset($check_phpbb_download_url) ? $check_phpbb_download_url : $this->config['vinabb_web_check_phpbb_download_url'],
			'CHECK_PHPBB_DOWNLOAD_DEV_URL'	=> isset($check_phpbb_download_dev_url) ? $check_phpbb_download_dev_url : $this->config['vinabb_web_check_phpbb_download_dev_url'],
			'CHECK_PHPBB_GITHUB_URL'		=> isset($check_phpbb_github_url) ? $check_phpbb_github_url : $this->config['vinabb_web_check_phpbb_github_url'],
			'CHECK_PHPBB_BRANCH'			=> isset($check_phpbb_branch) ? $check_phpbb_branch : $this->config['vinabb_web_check_phpbb_branch'],
			'CHECK_PHPBB_LEGACY_BRANCH'		=> isset($check_phpbb_legacy_branch) ? $check_phpbb_legacy_branch : $this->config['vinabb_web_check_phpbb_legacy_branch'],
			'CHECK_PHPBB_DEV_BRANCH'		=> isset($check_phpbb_dev_branch) ? $check_phpbb_dev_branch : $this->config['vinabb_web_check_phpbb_dev_branch'],
			'CHECK_PHP_URL'					=> isset($check_php_url) ? $check_php_url : $this->config['vinabb_web_check_php_url'],
			'CHECK_PHP_BRANCH'				=> isset($check_php_branch) ? $check_php_branch : $this->config['vinabb_web_check_php_branch'],
			'CHECK_PHP_LEGACY_BRANCH'		=> isset($check_php_legacy_branch) ? $check_php_legacy_branch : $this->config['vinabb_web_check_php_legacy_branch'],

			'U_ACTION'	=> $this->u_action,
		));
	}
}
