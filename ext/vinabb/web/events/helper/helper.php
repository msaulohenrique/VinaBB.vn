<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\events\helper;

use s9e\TextFormatter\Bundles\MediaPack;
use vinabb\web\includes\constants;

class helper implements helper_interface
{
	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \vinabb\web\controllers\cache\service_interface */
	protected $cache;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var \vinabb\web\controllers\helper_interface */
	protected $ext_helper;

	/** @var string */
	protected $root_path;

	/** @var string */
	protected $php_ext;

	/** @var array */
	protected $config_text;

	/**
	* Constructor
	*
	* @param \phpbb\auth\auth $auth
	* @param \vinabb\web\controllers\cache\service_interface $cache
	* @param \phpbb\config\config $config
	* @param \phpbb\language\language $language
	* @param \phpbb\template\template $template
	* @param \phpbb\user $user
	* @param \phpbb\controller\helper $helper
	* @param \vinabb\web\controllers\helper_interface $ext_helper
	* @param string $root_path
	* @param string $php_ext
	*/
	public function __construct(
		\phpbb\auth\auth $auth,
		\vinabb\web\controllers\cache\service_interface $cache,
		\phpbb\config\config $config,
		\phpbb\language\language $language,
		\phpbb\template\template $template,
		\phpbb\user $user,
		\phpbb\controller\helper $helper,
		\vinabb\web\controllers\helper_interface $ext_helper,
		$root_path,
		$php_ext
	)
	{
		$this->auth = $auth;
		$this->cache = $cache;
		$this->config = $config;
		$this->language = $language;
		$this->template = $template;
		$this->user = $user;
		$this->helper = $helper;
		$this->ext_helper = $ext_helper;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;

		$this->config_text = $this->cache->get_config_text();
	}

	/**
	* Display forum list on header
	*/
	public function list_forums()
	{
		foreach ($this->cache->get_forum_data() as $forum_id => $forum_data)
		{
			// Non-postable forum with no subforums, don't display
			if ($forum_data['type'] == FORUM_CAT && ($forum_data['left_id'] + 1 == $forum_data['right_id']))
			{
				continue;
			}

			// If the user does not have permissions to list this forum skip
			if (!$this->auth->acl_get('f_list', $forum_id))
			{
				continue;
			}

			$this->template->assign_block_vars('header_forums', [
				'PARENT_ID'	=> $forum_data['parent_id'],
				'FORUM_ID'	=> $forum_id,
				'NAME'		=> $forum_data['name'],
				'DESC'		=> $forum_data['desc'],
				'URL'		=> $this->helper->route('vinabb_web_board_forum_route', ['forum_id' => $forum_id, 'seo' => $forum_data['name_seo'] . constants::REWRITE_URL_SEO]),

				'S_HAS_SUBFORUMS'	=> $forum_data['left_id'] + 1 != $forum_data['right_id'],
				'S_IS_CAT'			=> $forum_data['type'] == FORUM_CAT,
				'S_IS_LINK'			=> $forum_data['type'] == FORUM_LINK,
				'S_IS_POST'			=> $forum_data['type'] == FORUM_POST
			]);
		}
	}

	/**
	* Generate the category list of all phpBB resourse types
	*/
	public function list_bb_cats()
	{
		$bb_types = ['ext', 'style', 'acp_style', 'lang', 'tool'];

		foreach ($bb_types as $bb_type)
		{
			foreach ($this->cache->get_bb_cats($this->ext_helper->get_bb_type_constants($bb_type)) as $cat_id => $cat_data)
			{
				$this->template->assign_block_vars($bb_type . '_cats', [
					'NAME'		=> $cat_data[($this->user->lang_name == constants::LANG_VIETNAMESE) ? 'name_vi' : 'name'],
					'VARNAME'	=> $cat_data['varname'],
					'DESC'		=> $cat_data[($this->user->lang_name == constants::LANG_VIETNAMESE) ? 'desc_vi' : 'desc'],
					'ICON'		=> $cat_data['icon'],
					'URL'		=> $this->helper->route('vinabb_web_bb_cat_route', ['type' => $this->ext_helper->get_bb_type_varnames($bb_type), 'cat' => $cat_data['varname']])
				]);
			}
		}
	}

	/**
	* Language switcher for guests
	*/
	public function add_lang_switcher()
	{
		// Get language data from cache
		$lang_data = $this->cache->get_lang_data();

		// Language titles
		if ($this->user->lang_name == $this->config['default_lang'])
		{
			$lang_current = $this->config['default_lang'];
			$lang_switch = $this->config['vinabb_web_lang_switch'];
		}
		else
		{
			$lang_current = $this->config['vinabb_web_lang_switch'];
			$lang_switch = $this->config['default_lang'];
		}

		$this->template->assign_vars([
			'LANG_SWITCH_CURRENT'	=> $this->user->lang_name,
			'LANG_SWITCH_DEFAULT'	=> $this->config['default_lang'],
			'LANG_SWITCH_TITLE'		=> $this->language->lang('LANG_SWITCH', $lang_data[$lang_current]['local_name'], $lang_data[$lang_switch]['local_name']),

			'U_LANG'	=> append_sid("{$this->root_path}index.{$this->php_ext}", "language={$lang_switch}")
		]);
	}

	/**
	* Add checking permissions to template variables
	*/
	public function auth_to_template()
	{
		$this->template->assign_vars([
			'S_GUEST'		=> $this->user->data['user_id'] == ANONYMOUS,// not S_USER_LOGGED_IN
			// S_IS_BOT
			// S_USER_NEW
			// S_REGISTERED_USER
			'S_MOD'			=> $this->auth->acl_get('m_'),
			'S_GLOBAL_MOD'	=> $this->auth->acl_getf_global('m_'),
			'S_ADMIN'		=> $this->auth->acl_get('a_'),
			'S_FOUNDER'		=> $this->user->data['user_type'] == USER_FOUNDER
		]);
	}

	/**
	* Get value from config items and export to template variables
	*/
	public function config_to_template()
	{
		$this->template->assign_vars([
			'CONFIG_TOTAL_USERS'			=> $this->config['num_users'],
			'CONFIG_TOTAL_FORUMS'			=> $this->config['num_forums'],
			'CONFIG_TOTAL_TOPICS'			=> $this->config['num_topics'],
			'CONFIG_TOTAL_POSTS'			=> $this->config['num_posts'],
			'CONFIG_TOTAL_BB_EXTS'			=> $this->config['vinabb_web_total_bb_exts'],
			'CONFIG_TOTAL_BB_STYLES'		=> $this->config['vinabb_web_total_bb_styles'],
			'CONFIG_TOTAL_BB_ACP_STYLES'	=> $this->config['vinabb_web_total_bb_acp_styles'],
			'CONFIG_TOTAL_BB_LANGS'			=> $this->config['vinabb_web_total_bb_langs'],
			'CONFIG_TOTAL_BB_TOOLS'			=> $this->config['vinabb_web_total_bb_tools'],
			'CONFIG_TOTAL_BB_AUTHORS'		=> $this->config['vinabb_web_total_bb_authors'],
			'CONFIG_TOTAL_BB_SUBSCRIBERS'	=> $this->config['vinabb_web_total_bb_subscribers'],

			'FORUM_ID_VIETNAMESE'				=> $this->config['vinabb_web_forum_id_vietnamese'],
			'FORUM_ID_VIETNAMESE_SUPPORT'		=> $this->config['vinabb_web_forum_id_vietnamese_support'],
			'FORUM_ID_VIETNAMESE_EXT'			=> $this->config['vinabb_web_forum_id_vietnamese_ext'],
			'FORUM_ID_VIETNAMESE_STYLE'			=> $this->config['vinabb_web_forum_id_vietnamese_style'],
			'FORUM_ID_VIETNAMESE_TUTORIAL'		=> $this->config['vinabb_web_forum_id_vietnamese_tutorial'],
			'FORUM_ID_VIETNAMESE_DISCUSSION'	=> $this->config['vinabb_web_forum_id_vietnamese_discussion'],
			'FORUM_ID_ENGLISH'					=> $this->config['vinabb_web_forum_id_english'],
			'FORUM_ID_ENGLISH_SUPPORT'			=> $this->config['vinabb_web_forum_id_english_support'],
			'FORUM_ID_ENGLISH_TUTORIAL'			=> $this->config['vinabb_web_forum_id_english_tutorial'],
			'FORUM_ID_ENGLISH_DISCUSSION'		=> $this->config['vinabb_web_forum_id_english_discussion'],

			'MANAGER_NAME'		=> ($this->user->lang_name == constants::LANG_VIETNAMESE && !empty($this->config['vinabb_web_manager_name_vi'])) ? $this->config['vinabb_web_manager_name_vi'] : $this->config['vinabb_web_manager_name'],
			'MANAGER_USERNAME'	=> $this->config['vinabb_web_manager_username'],

			'MAP_API'			=> $this->config['vinabb_web_map_api'],
			'MAP_LAT'			=> $this->config['vinabb_web_map_lat'],
			'MAP_LNG'			=> $this->config['vinabb_web_map_lng'],
			'MAP_ADDRESS'		=> ($this->user->lang_name == constants::LANG_VIETNAMESE) ? $this->config['vinabb_web_map_address_vi'] : $this->config['vinabb_web_map_address'],
			'MAP_PHONE'			=> $this->config['vinabb_web_map_phone'],
			'MAP_PHONE_NAME'	=> $this->config['vinabb_web_map_phone_name'],

			'GOOGLE_ANALYTICS_ID' => $this->config['vinabb_web_google_analytics_id'],

			'FACEBOOK_URL'		=> htmlspecialchars_decode($this->config['vinabb_web_facebook_url']),
			'TWITTER_URL'		=> htmlspecialchars_decode($this->config['vinabb_web_twitter_url']),
			'GOOGLE_PLUS_URL'	=> htmlspecialchars_decode($this->config['vinabb_web_google_plus_url']),
			'GITHUB_URL'		=> htmlspecialchars_decode($this->config['vinabb_web_github_url'])
		]);
	}

	/**
	* Add our new links to the header
	*/
	public function add_new_routes()
	{
		$this->template->assign_vars([
			'U_BOARD'			=> $this->helper->route('vinabb_web_board_route'),
			'U_PORTAL'			=> $this->helper->route('vinabb_web_portal_route'),
			'U_BB'				=> $this->helper->route('vinabb_web_bb_route'),
			'U_BB_EXTS'			=> $this->helper->route('vinabb_web_bb_type_route', ['type' => constants::BB_TYPE_VARNAME_EXT]),
			'U_BB_STYLES'		=> $this->helper->route('vinabb_web_bb_type_route', ['type' => constants::BB_TYPE_VARNAME_STYLE]),
			'U_BB_ACP_STYLES'	=> $this->helper->route('vinabb_web_bb_type_route', ['type' => constants::BB_TYPE_VARNAME_ACP_STYLE]),
			'U_BB_LANGS'		=> $this->helper->route('vinabb_web_bb_type_route', ['type' => constants::BB_TYPE_VARNAME_LANG]),
			'U_BB_TOOLS'		=> $this->helper->route('vinabb_web_bb_type_route', ['type' => constants::BB_TYPE_VARNAME_TOOL]),
			'U_FAQ_BBCODE'		=> $this->helper->route('phpbb_help_bbcode_controller'),
		]);
	}

	/**
	* Maintenance mode by user levels
	*/
	public function maintenance_mode()
	{
		global $msg_title;

		if (!defined('IN_LOGIN') && (
				($this->config['vinabb_web_maintenance_mode'] == constants::MAINTENANCE_MODE_FOUNDER && $this->user->data['user_type'] != USER_FOUNDER)
				|| ($this->config['vinabb_web_maintenance_mode'] == constants::MAINTENANCE_MODE_ADMIN && !$this->auth->acl_gets('a_'))
				|| ($this->config['vinabb_web_maintenance_mode'] == constants::MAINTENANCE_MODE_MOD && !$this->auth->acl_gets('a_', 'm_') && !$this->auth->acl_getf_global('m_'))
				|| ($this->config['vinabb_web_maintenance_mode'] == constants::MAINTENANCE_MODE_USER && ($this->user->data['user_id'] == ANONYMOUS || $this->user->data['is_bot']))
			))
		{
			// Get current time
			$now = time();
			$in_maintenance_time = ($this->config['vinabb_web_maintenance_time'] > $now) ? true : false;

			// Get maintenance text with/without the end time
			if (empty($this->config_text['vinabb_web_maintenance_text']) || empty($this->config_text['vinabb_web_maintenance_text_vi']))
			{
				if ($in_maintenance_time)
				{
					// Short maintenance time: 12 hours
					if (($this->config['vinabb_web_maintenance_time'] - $now) > (12 * 60 * 60))
					{
						$message = $this->language->lang('MAINTENANCE_TEXT_TIME_LONG', $this->user->format_date($this->config['vinabb_web_maintenance_time'], 'd/m/Y H:i'));
					}
					else
					{
						$message = $this->language->lang('MAINTENANCE_TEXT_TIME_SHORT', $this->user->format_date($this->config['vinabb_web_maintenance_time'], 'H:i'));
					}
				}
				else
				{
					$message = $this->language->lang('MAINTENANCE_TEXT');
				}

				$message .= '<br>';
			}
			else
			{
				$message = ($this->user->lang_name == constants::LANG_VIETNAMESE) ? $this->config_text['vinabb_web_maintenance_text_vi'] : $this->config_text['vinabb_web_maintenance_text'];
				$message = str_replace("\n", '<br>', $message);

				if ($in_maintenance_time)
				{
					$message .= '<br ><br >' . $this->language->lang('MAINTENANCE_TEXT_TIME_END', $this->user->format_date($this->config['vinabb_web_maintenance_time'], 'd/m/Y H:i'));
				}
			}

			// Get timezone data
			$dt = $this->user->create_datetime();
			$timezone_offset = $this->language->lang(['timezones', 'UTC_OFFSET'], phpbb_format_timezone_offset($dt->getOffset()));
			$timezone_name = $this->user->timezone->getName();

			if ($this->language->is_set(['timezones', $timezone_name]))
			{
				$timezone_name = $this->language->lang(['timezones', $timezone_name]);
			}

			if ($in_maintenance_time)
			{
				$message .= '<br>' . $this->language->lang('MAINTENANCE_TEXT_TIMEZONE', $timezone_offset, $timezone_name);
			}

			// Use simple header
			$this->template->assign_vars([
				'S_SIMPLE_HEADER'	=> true,
				'S_ERROR'			=> true
			]);

			// Display the maintenance text
			$msg_title = $this->language->lang('MAINTENANCE_TITLE');
			trigger_error($message, ($this->config['vinabb_web_maintenance_tpl']) ? E_USER_WARNING : E_USER_ERROR);
		}
	}

	/**
	* Render MediaEmbed markup tags when displaying text
	*
	* https://github.com/s9e/phpbb-ext-mediaembed
	* @copyright Copyright (c) 2014-2016 The s9e Authors
	*
	* @param $text
	* @return mixed
	*/
	public function render($text)
	{
		if (strpos($text, '<!-- s9e:mediaembed') === false)
		{
			return $text;
		}

		return preg_replace_callback(
			'(<!-- s9e:mediaembed:([^ ]+) --><!-- m -->.*?<!-- m -->)',
			function($m)
			{
				return MediaPack::render(base64_decode($m[1]));
			},
			$text
		);
	}

	/**
	* Insert MediaEmbed markup tags when saving text
	*
	* https://github.com/s9e/phpbb-ext-mediaembed
	* @copyright Copyright (c) 2014-2016 The s9e Authors
	*
	* @param $text
	* @return mixed
	*/
	public function parse($text)
	{
		if (strpos($text, '<!-- m -->') === false)
		{
			return $text;
		}

		return preg_replace_callback(
			'(<!-- m -->.*?href="([^"]+).*?<!-- m -->)',
			function($m)
			{
				$xml = MediaPack::parse(htmlspecialchars_decode($m[1]));

				return ($xml[1] === 'r') ? '<!-- s9e:mediaembed:' . base64_encode($xml) . ' -->' . $m[0] : $m[0];
			},
			$text
		);
	}

	/**
	* Remove MediaEmbed markup tags when editing text
	*
	* https://github.com/s9e/phpbb-ext-mediaembed
	* @copyright Copyright (c) 2014-2016 The s9e Authors
	*
	* @param $text
	* @return mixed
	*/
	public function unparse($text)
	{
		if (strpos($text, '<!-- s9e:mediaembed') === false)
		{
			return $text;
		}

		return preg_replace('(<!-- s9e:mediaembed:([^ ]+) -->)', '', $text);
	}
}
