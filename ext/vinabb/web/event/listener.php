<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use s9e\TextFormatter\Bundles\MediaPack;
use vinabb\web\includes\constants;

class listener implements EventSubscriberInterface
{
	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\db\driver\driver_interface */
    protected $db;

	/** @var \phpbb\cache\service */
	protected $cache;

	/** @var \phpbb\config\config */
    protected $config;

	/** @var \phpbb\template\template */
    protected $template;

	/** @var \phpbb\user */
    protected $user;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\request\request */
    protected $request;

	/** @var \phpbb\extension\manager */
	protected $ext_manager;

	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var \phpbb\path_helper */
	protected $path_helper;

	/** @var \vinabb\web\controller\helper */
	protected $ext_helper;

	/** @var string */
	protected $phpbb_root_path;

	/** @var string */
	protected $phpbb_admin_path;

	/** @var string */
	protected $php_ext;

	/**
	* Constructor
	*
	* @param \phpbb\auth\auth $auth
	* @param \phpbb\db\driver\driver_interface $db
	* @param \phpbb\cache\service $cache
	* @param \phpbb\config\config $config
	* @param \phpbb\template\template $template
	* @param \phpbb\user $user
	* @param \phpbb\language\language $language
	* @param \phpbb\request\request $request
	* @param \phpbb\extension\manager $ext_manager
	* @param \phpbb\controller\helper $helper
	* @param \phpbb\path_helper $path_helper
	* @param \vinabb\web\controller\helper $ext_helper
	* @param string $phpbb_root_path
	* @param string $php_ext
	*/
	public function __construct(\phpbb\auth\auth $auth,
								\phpbb\db\driver\driver_interface $db,
								\phpbb\cache\service $cache,
								\phpbb\config\config $config,
								\phpbb\template\template $template,
								\phpbb\user $user,
								\phpbb\language\language $language,
								\phpbb\request\request $request,
								\phpbb\extension\manager $ext_manager,
								\phpbb\controller\helper $helper,
								\phpbb\path_helper $path_helper,
								\vinabb\web\controller\helper $ext_helper,
								$phpbb_root_path,
								$phpbb_admin_path,
								$php_ext)
	{
		$this->auth = $auth;
		$this->db = $db;
		$this->cache = $cache;
		$this->config = $config;
		$this->template = $template;
		$this->user = $user;
		$this->language = $language;
		$this->request = $request;
		$this->ext_manager = $ext_manager;
		$this->helper = $helper;
		$this->path_helper = $path_helper;
		$this->ext_helper = $ext_helper;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->phpbb_admin_path = $phpbb_admin_path;
		$this->php_ext = $php_ext;

		$this->config_text = $this->cache->get_config_text_data();
		$this->ext_root_path = $this->ext_manager->get_extension_path('vinabb/web', true);
		$this->ext_web_path = $this->path_helper->update_web_root_path($this->ext_root_path);
	}

	static public function getSubscribedEvents()
	{
		return array(
			'core.user_setup'			=> 'user_setup',
			'core.page_header_after'	=> 'page_header_after',
			'core.adm_page_header'		=> 'adm_page_header',

			'core.append_sid'							=> 'append_sid',
			'core.add_log'								=> 'add_log',
			'core.user_add_modify_data'					=> 'user_add_modify_data',
			'core.update_username'						=> 'update_username',
			'core.submit_post_modify_sql_data'			=> 'submit_post_modify_sql_data',
			'core.generate_smilies_after'				=> 'generate_smilies_after',
			'core.login_box_redirect'					=> 'login_box_redirect',
			'core.make_jumpbox_modify_tpl_ary'			=> 'make_jumpbox_modify_tpl_ary',
			'core.memberlist_memberrow_before'			=> 'memberlist_memberrow_before',
			'core.memberlist_prepare_profile_data'		=> 'memberlist_prepare_profile_data',
			'core.memberlist_team_modify_template_vars'	=> 'memberlist_team_modify_template_vars',
			'core.modify_format_display_text_after'		=> 'modify_format_display_text_after',
			'core.modify_submit_post_data'				=> 'modify_submit_post_data',
			'core.modify_text_for_display_after'		=> 'modify_text_for_display_after',
			'core.modify_text_for_edit_before'			=> 'modify_text_for_edit_before',
			'core.modify_text_for_storage_after'		=> 'modify_text_for_storage_after',
			'core.submit_pm_before'						=> 'submit_pm_before',
			'core.ucp_pm_view_messsage'					=> 'ucp_pm_view_messsage',
			'core.viewtopic_modify_post_row'			=> 'viewtopic_modify_post_row',
			'core.obtain_users_online_string_sql'		=> 'obtain_users_online_string_sql',

			'core.acp_manage_forums_update_data_before'	=> 'acp_manage_forums_update_data_before',

			'vinabb.web.text_formatter_s9e_configure_after'		=> 'text_formatter_s9e_configure_after',
			'vinabb.web.text_formatter_s9e_configure_before'	=> 'text_formatter_s9e_configure_before',
		);
	}

	/**
	* core.user_setup
	*
	* @param $event
	*/
	public function user_setup($event)
	{
		// Add our common language variables
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = array(
			'ext_name' => 'vinabb/web',
			'lang_set' => 'common',
		);
		$event['lang_set_ext'] = $lang_set_ext;
	}

	/**
	* core.page_header_after
	*
	* @param $event
	*/
	public function page_header_after($event)
	{
		// Maintenance mode
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
				$message = ($this->user->lang_name == 'vi') ? $this->config_text['vinabb_web_maintenance_text_vi'] : $this->config_text['vinabb_web_maintenance_text'];
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

			// Display the maintenance text
			$msg_title = $this->language->lang('MAINTENANCE_TITLE');
			trigger_error($message, ($this->config['vinabb_web_maintenance_tpl']) ? E_USER_WARNING : E_USER_ERROR);
		}

		// Display the forum list on every page
		// Write a new function as soon...

		// Get language data from cache
		$lang_data = $this->cache->get_lang_data();

		// Language switcher for guests
		$lang_switch = ($this->user->lang_name == $this->config['default_lang']) ? $this->config['vinabb_web_lang_switch'] : $this->config['default_lang'];
		$lang_switch_title = ($this->user->lang_name == $this->config['default_lang']) ? $this->language->lang('LANG_SWITCH', $lang_data[$this->config['default_lang']]['local_name'], $lang_data[$this->config['vinabb_web_lang_switch']]['local_name']) : $this->language->lang('LANG_SWITCH', $lang_data[$this->config['vinabb_web_lang_switch']]['local_name'], $lang_data[$this->config['default_lang']]['local_name']);

		// Add template variables
		$this->template->assign_vars(array(
			'CONFIG_TOTAL_USERS'	=> $this->config['num_users'],
			'CONFIG_TOTAL_FORUMS'	=> $this->config['num_forums'],
			'CONFIG_TOTAL_TOPICS'	=> $this->config['num_topics'],
			'CONFIG_TOTAL_POSTS'	=> $this->config['num_posts'],

			'LANG_SWITCH_CURRENT'	=> $this->user->lang_name,
			'LANG_SWITCH_DEFAULT'	=> $this->config['default_lang'],
			'LANG_SWITCH_NEW'		=> $lang_data[$lang_switch]['local_name'],
			'LANG_SWITCH_TITLE'		=> $lang_switch_title,

			'FORUM_ID_VIETNAMESE'				=> constants::FORUM_ID_VIETNAMESE,
			'FORUM_ID_VIETNAMESE_SUPPORT'		=> constants::FORUM_ID_VIETNAMESE_SUPPORT,
			'FORUM_ID_VIETNAMESE_EXT'			=> constants::FORUM_ID_VIETNAMESE_EXT,
			'FORUM_ID_VIETNAMESE_STYLE'			=> constants::FORUM_ID_VIETNAMESE_STYLE,
			'FORUM_ID_VIETNAMESE_TUTORIAL'		=> constants::FORUM_ID_VIETNAMESE_TUTORIAL,
			'FORUM_ID_VIETNAMESE_DISCUSSION'	=> constants::FORUM_ID_VIETNAMESE_DISCUSSION,
			'FORUM_ID_ENGLISH'					=> constants::FORUM_ID_ENGLISH,
			'FORUM_ID_ENGLISH_SUPPORT'			=> constants::FORUM_ID_ENGLISH_SUPPORT,
			'FORUM_ID_ENGLISH_TUTORIAL'			=> constants::FORUM_ID_ENGLISH_TUTORIAL,
			'FORUM_ID_ENGLISH_DISCUSSION'		=> constants::FORUM_ID_ENGLISH_DISCUSSION,

			'MAP_API'			=> $this->config['vinabb_web_map_api'],
			'MAP_LAT'			=> $this->config['vinabb_web_map_lat'],
			'MAP_LNG'			=> $this->config['vinabb_web_map_lng'],
			'MAP_ADDRESS'		=> $this->config['vinabb_web_map_address'],
			'MAP_ADDRESS_VI'	=> $this->config['vinabb_web_map_address_vi'],
			'MAP_PHONE'			=> $this->config['vinabb_web_map_phone'],
			'MAP_PHONE_NAME'	=> $this->config['vinabb_web_map_phone_name'],

			'FACEBOOK_URL'		=> $this->config['vinabb_web_facebook_url'],
			'TWITTER_URL'		=> $this->config['vinabb_web_twitter_url'],
			'GOOGLE_PLUS_URL'	=> $this->config['vinabb_web_google_plus_url'],
			'GITHUB_URL'		=> $this->config['vinabb_web_github_url'],

			'EXT_ASSETS_PATH'	=> "{$this->ext_web_path}assets",

			'S_FOUNDER'		=> $this->user->data['user_type'] == USER_FOUNDER,
			'S_VIETNAMESE'	=> $this->user->lang_name == constants::LANG_VIETNAMESE,

			'U_BOARD'			=> $this->helper->route('vinabb_web_board_route', array('board' => 'board')),
			'U_MCP'				=> ($this->auth->acl_get('m_') || $this->auth->acl_getf_global('m_')) ? append_sid("{$this->phpbb_root_path}mcp.{$this->php_ext}", 'i=main&mode=front', true, $this->user->session_id) : '',
			'U_LANG'			=> ($this->user->data['user_id'] == ANONYMOUS && $this->config['vinabb_web_lang_enable']) ? append_sid("{$this->phpbb_root_path}index.{$this->php_ext}", "language=$lang_switch") : '',
			'U_LOGIN_ACTION'	=> $this->helper->route('vinabb_web_ucp_route', array('id' => 'front', 'mode' => 'login')),
			'U_SEND_PASSWORD'	=> ($this->config['email_enable']) ? append_sid("{$this->phpbb_root_path}ucp.{$this->php_ext}", 'mode=sendpassword') : '',
		));
	}

	/**
	* core.adm_page_header
	*
	* @param $event
	*/
	public function adm_page_header($event)
	{
		// Add template variables
		$this->template->assign_vars(array(
			'S_FOUNDER'	=> $this->user->data['user_type'] == USER_FOUNDER
		));
	}

	/**
	* core.append_sid
	*
	* @param $event
	*/
	public function append_sid($event)
	{
		// Add checking our extension, unless it causes errors when disabling the extension
		if (!$event['is_route'] && $this->ext_manager->is_enabled('vinabb/web'))
		{
			// Do not use &amp;
			$event['is_amp'] = false;

			// Get parameters
			$params_ary = array();

			if ($event['params'] !== false)
			{
				$params = explode('&', str_replace(array('&amp;', '?'), array('&', ''), $event['params']));

				foreach ($params as $param)
				{
					list($param_key, $param_value) = explode('=', $param);
					$params_ary[$param_key] = $param_value;
				}
			}

			// Detect URLs
			$route_name = '';

			if (strpos($event['url'], "viewforum.{$this->php_ext}") !== false)
			{
				// Get forum SEO names from cache
				$forum_data = $this->cache->get_forum_data();

				if (!sizeof($params_ary))
				{
					$params_ary['f'] = '';
				}

				if (isset($params_ary['f']))
				{
					$params_ary['forum_id'] = $params_ary['f'];
					unset($params_ary['f']);

					$params_ary['seo'] = $forum_data[$params_ary['forum_id']]['name_seo'] . constants::REWRITE_URL_SEO;
				}

				$route_name = 'vinabb_web_board_forum_route';
			}
			else if (strpos($event['url'], "viewonline.{$this->php_ext}") !== false)
			{
				$route_name = 'vinabb_web_online_route';
			}
			else if (strpos($event['url'], "ucp.{$this->php_ext}") !== false)
			{
				if (isset($params_ary['i']))
				{
					$params_ary['id'] = (substr($params_ary['i'], 0, 4) == 'ucp_') ? substr($params_ary['i'], 4) : $params_ary['i'];
					unset($params_ary['i']);
				}
				else if (isset($params_ary['mode']) && in_array($params_ary['mode'], array('activate', 'resend_act', 'sendpassword', 'register', 'confirm', 'login', 'login_link', 'logout', 'terms', 'privacy', 'delete_cookies', 'switch_perm', 'restore_perm')))
				{
					$params_ary['id'] = 'front';
				}

				$route_name = 'vinabb_web_ucp_route';
			}
			else if (strpos($event['url'], "x.{$this->php_ext}") !== false)
			{
				//echo $event['params'] . "<br>";
			}

			// Replace by routes
			if (!empty($route_name))
			{
				$event['append_sid_overwrite'] = $this->helper->route($route_name, $params_ary);
			}
		}
	}

	/**
	* core.add_log
	*
	* @param $event
	*/
	public function add_log($event)
	{
		if (substr($event['log_operation'], 0, 14) == 'LOG_FORUM_DEL_')
		{
			// Update forum counter
			$sql = 'SELECT COUNT(forum_id) AS num_forums
				FROM ' . FORUMS_TABLE;
			$result = $this->db->sql_query($sql);
			$num_forums = $this->db->sql_fetchfield('num_forums');
			$this->db->sql_freeresult($result);

			$this->config->set('num_forums', $num_forums, true);

			// Clear forum cache
			$this->cache->clear_forum_data();
		}
		else if ($event['log_operation'] == 'LOG_FORUM_ADD' || $event['log_operation'] == 'LOG_FORUM_EDIT')
		{
			// Update forum counter
			if ($event['log_operation'] == 'LOG_FORUM_ADD')
			{
				$this->config->increment('num_forums', 1, true);
			}

			// Clear forum cache
			$this->cache->clear_forum_data();
		}
		// Clear language data cache
		else if ($event['log_operation'] == 'LOG_LANGUAGE_PACK_INSTALLED' || $event['log_operation'] == 'LOG_LANGUAGE_PACK_DELETED')
		{
			$this->cache->clear_lang_data();
		}
	}

	/**
	* core.user_add_modify_data
	*
	* @param $event
	*/
	public function user_add_modify_data($event)
	{
		// Add SEO username for newly registered users
		$sql_ary = $event['sql_ary'];
		$sql_ary['username_seo'] = $this->ext_helper->clean_url($sql_ary['username']);
		$event['sql_ary'] = $sql_ary;
	}

	/**
	* core.update_username
	*
	* @param $event
	*/
	public function update_username($event)
	{
		// Update SEO username again when changed
		$username_seo = $this->ext_helper->clean_url($event['new_name']);

		$sql = 'UPDATE ' . USERS_TABLE . "
			SET username_seo = '$username_seo'
			WHERE username = '" . $this->db->sql_escape($event['new_name']) . "'";
		$this->db->sql_query($sql);
	}

	/**
	* core.submit_post_modify_sql_data
	*
	* @param $event
	*/
	public function submit_post_modify_sql_data($event)
	{
		// Adjust topic SEO title based on topic title
		if ($event['post_mode'] == 'post' || $event['post_mode'] == 'edit_topic' || $event['post_mode'] == 'edit_first_post')
		{
			$sql_data = $event['sql_data'];
			$sql_data[TOPICS_TABLE]['sql']['topic_title_seo'] = $this->ext_helper->clean_url($sql_data[TOPICS_TABLE]['sql']['topic_title']);
			$event['sql_data'] = $sql_data;
		}
	}

	/**
	* core.generate_smilies_after
	*
	* @param $event
	*/
	public function generate_smilies_after($event)
	{
		// Do not display the "More smilies" link
		$event['display_link'] = false;
	}

	/**
	* core.login_box_redirect
	*
	* @param $event
	*/
	public function login_box_redirect($event)
	{
		// Prevent standard administrators to login successfully if the maintenance mode is enabled with founder level
		if ($this->config['vinabb_web_maintenance_mode'] == constants::MAINTENANCE_MODE_FOUNDER && $this->user->data['user_type'] != USER_FOUNDER)
		{
			$this->user->unset_admin();
		}
	}

	/**
	* core.make_jumpbox_modify_tpl_ary
	*
	* @param $event
	*/
	public function make_jumpbox_modify_tpl_ary($event)
	{
		// Add PARENT_ID and HAS_SUBFORUM
		$row = $event['row'];
		$tpl_ary = $event['tpl_ary'];
		$i = isset($tpl_ary[1]) ? 1 : 0;
		$tpl_ary[$i]['PARENT_ID'] = $row['parent_id'];
		$tpl_ary[$i]['HAS_SUBFORUM'] = ($row['left_id'] != $row['right_id'] - 1) ? true : false;
		$event['tpl_ary'] = $tpl_ary;
	}

	/**
	* core.memberlist_memberrow_before
	*
	* @param $event
	*/
	public function memberlist_memberrow_before($event)
	{
		// Enable contact fields on the member list
		$event['use_contact_fields'] = true;
	}

	/**
	* core.memberlist_prepare_profile_data
	*
	* @param $event
	*/
	public function memberlist_prepare_profile_data($event)
	{
		// Add USER_ID and U_PM_ALT without checking $can_receive_pm
		// Also translate the rank title RANK_TITLE with the original value RANK_TITLE_RAW
		$data = $event['data'];
		$template_data = $event['template_data'];
		$template_data['USER_ID'] = $data['user_id'];
		$template_data['RANK_TITLE_RAW'] = $template_data['RANK_TITLE'];
		$template_data['RANK_TITLE'] = ($this->language->is_set(['RANK_TITLES', strtoupper($template_data['RANK_TITLE'])])) ? $this->language->lang(['RANK_TITLES', strtoupper($template_data['RANK_TITLE'])]) : $template_data['RANK_TITLE'];
		$template_data['U_PM_ALT'] = ($this->config['allow_privmsg'] && $this->auth->acl_get('u_sendpm')) ? append_sid("{$this->phpbb_root_path}ucp.{$this->php_ext}", 'i=pm&amp;mode=compose&amp;u=' . $data['user_id']) : '';
		$event['template_data'] = $template_data;
	}

	/**
	* core.memberlist_team_modify_template_vars
	*
	* @param $event
	*/
	public function memberlist_team_modify_template_vars($event)
	{
		// Translate the rank title RANK_TITLE with the original value RANK_TITLE_RAW
		$template_vars = $event['template_vars'];
		$template_vars['RANK_TITLE_RAW'] = $template_vars['RANK_TITLE'];
		$template_vars['RANK_TITLE'] = ($this->language->is_set(['RANK_TITLES', strtoupper($template_vars['RANK_TITLE'])])) ? $this->language->lang(['RANK_TITLES', strtoupper($template_vars['RANK_TITLE'])]) : $template_vars['RANK_TITLE'];
		$event['template_vars'] = $template_vars;
	}

	/**
	* core.modify_format_display_text_after
	*
	* @param $event
	*/
	public function modify_format_display_text_after($event)
	{
		$event['text'] = $this->render($this->parse($event['text']));
	}

	/**
	* core.modify_submit_post_data
	*
	* @param $event
	*/
	public function modify_submit_post_data($event)
	{
		$data = $event['data'];
		$data['message'] = $this->parse($data['message']);
		$event['data'] = $data;
	}

	/**
	* core.modify_text_for_display_after
	*
	* @param $event
	*/
	public function modify_text_for_display_after($event)
	{
		$event['text'] = $this->render($event['text']);

		// Load highlight.js
		$this->template->assign_vars(array(
			'S_LOAD_HIGHLIGHT'	=> true,
		));
	}

	/**
	* core.modify_text_for_edit_before
	*
	* @param $event
	*/
	public function modify_text_for_edit_before($event)
	{
		$event['text'] = $this->unparse($event['text']);
	}

	/**
	* core.modify_text_for_storage_after
	*
	* @param $event
	*/
	public function modify_text_for_storage_after($event)
	{
		$event['text'] = $this->parse($event['text']);
	}

	/**
	* core.submit_pm_before
	*
	* @param $event
	*/
	public function submit_pm_before($event)
	{
		$data = $event['data'];
		$data['message'] = $this->parse($data['message']);
		$event['data'] = $data;
	}

	/**
	* core.ucp_pm_view_messsage
	*
	* @param $event
	*/
	public function ucp_pm_view_messsage($event)
	{
		// Translate the rank title RANK_TITLE with the original value RANK_TITLE_RAW
		$msg_data = $event['msg_data'];
		$msg_data['RANK_TITLE_RAW'] = $msg_data['RANK_TITLE'];
		$msg_data['RANK_TITLE'] = ($this->language->is_set(['RANK_TITLES', strtoupper($msg_data['RANK_TITLE'])])) ? $this->language->lang(['RANK_TITLES', strtoupper($msg_data['RANK_TITLE'])]) : $msg_data['RANK_TITLE'];
		$event['msg_data'] = $msg_data;
	}

	/**
	* core.viewtopic_modify_post_row
	*
	* @param $event
	*/
	public function viewtopic_modify_post_row($event)
	{
		// Translate the rank title RANK_TITLE with the original value RANK_TITLE_RAW
		$post_row = $event['post_row'];
		$post_row['RANK_TITLE_RAW'] = $post_row['RANK_TITLE'];
		$post_row['RANK_TITLE'] = ($this->language->is_set(['RANK_TITLES', strtoupper($post_row['RANK_TITLE'])])) ? $this->language->lang(['RANK_TITLES', strtoupper($post_row['RANK_TITLE'])]) : $post_row['RANK_TITLE'];
		$event['post_row'] = $post_row;
	}

	/**
	* core.obtain_users_online_string_sql
	*
	* @param $event
	*/
	public function obtain_users_online_string_sql($event)
	{
		// Get total online users (only number)
		$online_users = $event['online_users'];

		$this->template->assign_vars(array(
			'TOTAL_ONLINE_USERS'	=> $online_users['total_online']
		));
	}

	/**
	* core.acp_manage_forums_update_data_before
	*
	* @param $event
	*/
	public function acp_manage_forums_update_data_before($event)
	{
		// Adjust the column 'forum_name_seo' based on 'forum_name'
		$forum_data_sql = $event['forum_data_sql'];
		$forum_data_sql['forum_name_seo'] = $this->ext_helper->clean_url($forum_data_sql['forum_name']);

		// If there have more than 2 same forum SEO names, add parent forum SEO name as prefix
		if ($forum_data_sql['parent_id'])
		{
			$forum_data = $this->cache->get_forum_data();

			$sql = 'SELECT forum_id, parent_id, forum_name_seo
				FROM ' . FORUMS_TABLE . '
				WHERE forum_id <> ' . $forum_data_sql['forum_id'] . "
					AND forum_name = '" . $this->db->sql_escape($forum_data_sql['forum_name']) . "'";
			$result = $this->db->sql_query($sql);
			$rows = $this->db->sql_fetchrowset($result);
			$this->db->sql_freeresult($result);

			if (sizeof($rows))
			{
				foreach ($rows as $row)
				{
					$sql = 'UPDATE ' . FORUMS_TABLE . "
						SET forum_name_seo = '" . $forum_data[$row['parent_id']]['name_seo'] . constants::REWRITE_URL_FORUM_CAT . $row['forum_name_seo'] . "'
						WHERE forum_id = " . $row['forum_id'];
					$this->sql_query($sql);
				}

				$forum_data_sql['forum_name_seo'] = $forum_data[$forum_data_sql['parent_id']]['name_seo'] . constants::REWRITE_URL_FORUM_CAT . $forum_data_sql['forum_name_seo'];
			}
		}

		$event['forum_data_sql'] = $forum_data_sql;
	}

	/**
	* vinabb.web.text_formatter_s9e_configure_after
	*
	* @param $event
	*/
	public function text_formatter_s9e_configure_after($event)
	{
		/**
		* Use backticks to post inline code: `$phpBB`
		*
		* https://github.com/s9e/phpbb-ext-incode
		* @copyright Copyright (c) 2015 The s9e Authors
		*/
		$configurator = $event['configurator'];
		$action = $configurator->tags->onDuplicate('ignore');

		$configurator->Preg->replace(
			'/`(.*?)`/',
			'<code class="inline">$1</code>',
			'C'
		);

		$configurator->tags->onDuplicate($action);
	}

	/**
	* vinabb.web.text_formatter_s9e_configure_before
	*
	* @param $event
	*/
	public function text_formatter_s9e_configure_before($event)
	{
		$configurator = $event['configurator'];
		//$configurator->MediaEmbed->createIndividualBBCodes = true;

		foreach ($configurator->MediaEmbed->defaultSites->getIds() as $site_id)
		{
			if (in_array($site_id, array('facebook', 'twitter', 'googleplus', 'youtube', 'flickr', 'instagram', 'gist')))
			{
				$configurator->MediaEmbed->add($site_id);
			}
		}

		// Add our site
		/*$vinabb_urls = array(
			'forum'	=> array(
				'host'		=> 'vinabb.vn',
				'extract'	=> "!vinabb\\.vn/viewforum\\.php\\?f=(?'f'[0-9]+)!",
				'iframe'	=> array(
					'width'		=> 560,
					'height'	=> 260,
					'src'		=> 'http://localhost/vinabb/embed/forum/{@f}',
				),
			),
			'topic'	=> array(
				'host'		=> 'vinabb.vn',
				'extract'	=> array(
					"!vinabb\\.vn/viewtopic\\.php\\?f=(?'f'[0-9]+)\\&t=(?'t'[0-9]+)!",
					"!vinabb\\.vn/viewtopic\\.php\\?t=(?'t'[0-9]+)!",
				),
				'iframe'	=> array(
					'width'		=> 560,
					'height'	=> 260,
					'src'		=> 'http://localhost/vinabb/embed/topic/{@t}',
				),
			),
			'post'	=> array(
				'host'		=> 'vinabb.vn',
				'extract'	=> array(
					"!vinabb\\.vn/viewtopic\\.php\\?f=(?'f'[0-9]+)\\&p=(?'p'[0-9]+)!",
					"!vinabb\\.vn/viewtopic\\.php\\?p=(?'p'[0-9]+)!",
				),
				'iframe'	=> array(
					'width'		=> 560,
					'height'	=> 260,
					'src'		=> 'http://localhost/vinabb/embed/post/{@p}',
				),
			),
		);

		foreach ($vinabb_urls as $url_type => $url_data)
		{
			$configurator->MediaEmbed->add('vinabb' . $url_type, $url_data);
		}*/


		$configurator->MediaEmbed->add('vinabb', array(
				'host'		=> 'vinabb.vn',
				'extract'	=> array(
					"!vinabb\\.vn/viewtopic\\.php\\?f=(?'f'[0-9]+)\\&t=(?'t'[0-9]+)!",
					"!vinabb\\.vn/viewtopic\\.php\\?t=(?'t'[0-9]+)!",
				),
				'iframe'	=> array(
					'width'		=> 560,
					'height'	=> 260,
					'src'		=> 'http://localhost/vinabb/embed/topic/{@t}',
				),
			)
		);
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
	private function render($text)
	{
		if (strpos($text, '<!-- s9e:mediaembed') === false)
		{
			return $text;
		}

		return preg_replace_callback(
			'(<!-- s9e:mediaembed:([^ ]+) --><!-- m -->.*?<!-- m -->)',
			function ($m)
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
	private function parse($text)
	{
		if (strpos($text, '<!-- m -->') === false)
		{
			return $text;
		}

		return preg_replace_callback(
			'(<!-- m -->.*?href="([^"]+).*?<!-- m -->)',
			function ($m)
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
	private function unparse($text)
	{
		if (strpos($text, '<!-- s9e:mediaembed') === false)
		{
			return $text;
		}

		return preg_replace('(<!-- s9e:mediaembed:([^ ]+) -->)', '', $text);
	}
}
