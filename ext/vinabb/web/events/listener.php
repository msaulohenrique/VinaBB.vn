<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\events;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use vinabb\web\includes\constants;

/**
* PHP events
*/
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

	/** @var \phpbb\extension\manager */
	protected $ext_manager;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var \vinabb\web\events\helper_interface */
	protected $event_helper;

	/** @var \vinabb\web\controllers\helper_interface */
	protected $ext_helper;

	/** @var \phpbb\path_helper */
	protected $path_helper;

	/** @var string */
	protected $root_path;

	/** @var string */
	protected $admin_path;

	/** @var string */
	protected $php_ext;

	/** @var string */
	protected $ext_root_path;

	/** @var string */
	protected $ext_web_path;

	/**
	* Constructor
	*
	* @param \phpbb\auth\auth $auth
	* @param \phpbb\db\driver\driver_interface $db
	* @param \phpbb\cache\service $cache
	* @param \phpbb\config\config $config
	* @param \phpbb\extension\manager $ext_manager
	* @param \phpbb\language\language $language
	* @param \phpbb\request\request $request
	* @param \phpbb\template\template $template
	* @param \phpbb\user $user
	* @param \phpbb\controller\helper $helper
	* @param \vinabb\web\events\helper_interface $event_helper
	* @param \vinabb\web\controllers\helper_interface $ext_helper
	* @param \phpbb\path_helper $path_helper
	* @param string $root_path
	* @param string $admin_path
	* @param string $php_ext
	*/
	public function __construct(
		\phpbb\auth\auth $auth,
		\phpbb\db\driver\driver_interface $db,
		\phpbb\cache\service $cache,
		\phpbb\config\config $config,
		\phpbb\extension\manager $ext_manager,
		\phpbb\language\language $language,
		\phpbb\request\request $request,
		\phpbb\template\template $template,
		\phpbb\user $user,
		\phpbb\controller\helper $helper,
		\vinabb\web\events\helper_interface $event_helper,
		\vinabb\web\controllers\helper_interface $ext_helper,
		\phpbb\path_helper $path_helper,
		$root_path,
		$admin_path,
		$php_ext
	)
	{
		$this->auth = $auth;
		$this->db = $db;
		$this->cache = $cache;
		$this->config = $config;
		$this->ext_manager = $ext_manager;
		$this->language = $language;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->helper = $helper;
		$this->event_helper = $event_helper;
		$this->ext_helper = $ext_helper;
		$this->path_helper = $path_helper;
		$this->root_path = $root_path;
		$this->admin_path = $admin_path;
		$this->php_ext = $php_ext;

		$this->ext_root_path = $this->ext_manager->get_extension_path('vinabb/web', true);
		$this->ext_web_path = $this->path_helper->update_web_root_path($this->ext_root_path);
	}

	/**
	* List of phpBB's PHP events to be used
	*
	* @return array
	*/
	static public function getSubscribedEvents()
	{
		return [
			'core.user_setup'			=> 'user_setup',
			'core.page_header_after'	=> 'page_header_after',
			'core.adm_page_header'		=> 'adm_page_header',

			'core.append_sid'							=> 'append_sid',
			'core.add_log'								=> 'add_log',
			'core.get_avatar_after'						=> 'get_avatar_after',
			'core.user_add_modify_data'					=> 'user_add_modify_data',
			'core.update_username'						=> 'update_username',
			'core.submit_post_modify_sql_data'			=> 'submit_post_modify_sql_data',
			'core.generate_smilies_after'				=> 'generate_smilies_after',
			'core.login_box_redirect'					=> 'login_box_redirect',
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
			'core.text_formatter_s9e_configure_after'	=> 'text_formatter_s9e_configure_after',
			'core.text_formatter_s9e_configure_before'	=> 'text_formatter_s9e_configure_before',

			'core.acp_manage_forums_update_data_before'	=> 'acp_manage_forums_update_data_before'
		];
	}

	/**
	* core.user_setup
	*
	* @param array $event Data from the PHP event
	*/
	public function user_setup($event)
	{
		// Add our common language variables
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = [
			'ext_name' => 'vinabb/web',
			'lang_set' => 'common'
		];
		$event['lang_set_ext'] = $lang_set_ext;
	}

	/**
	* core.page_header_after
	*
	* @param array $event Data from the PHP event
	*/
	public function page_header_after($event)
	{
		// Display the forum list on every page
		$this->event_helper->list_forums();

		// Display phpBB Resource's category list on every page
		$this->event_helper->list_bb_cats();

		// Language switcher for guests
		if ($this->user->data['user_id'] == ANONYMOUS && $this->config['vinabb_web_lang_switch'] != '')
		{
			$this->event_helper->add_lang_switcher();
		}

		// Add template variables
		$this->event_helper->auth_to_template();
		$this->event_helper->config_to_template();
		$this->event_helper->add_new_routes();

		$this->template->assign_vars([
			'S_VIETNAMESE'	=> $this->user->lang_name == constants::LANG_VIETNAMESE,

			'T_JS_LANG_PATH'	=> "{$this->ext_web_path}language/{$this->user->lang_name}/js",

			'U_MCP'				=> ($this->auth->acl_get('m_') || $this->auth->acl_getf_global('m_')) ? $this->helper->route('vinabb_web_mcp_route', [], true, $this->user->session_id) : '',
			'U_CONTACT_PM'		=> ($this->config['allow_privmsg'] && $this->auth->acl_get('u_sendpm') && $this->config['vinabb_web_manager_user_id']) ? $this->helper->route('vinabb_web_ucp_route', ['id' => 'pm', 'mode' => 'compose', 'u' => $this->config['vinabb_web_manager_user_id']]) : '',
			'U_LOGIN_ACTION'	=> $this->helper->route('vinabb_web_ucp_route', ['id' => 'front', 'mode' => 'login']),
			'U_SEND_PASSWORD'	=> ($this->config['email_enable']) ? $this->helper->route('vinabb_web_ucp_route', ['id' => 'front', 'mode' => 'sendpassword']) : ''
		]);

		// Maintenance mode
		$this->event_helper->maintenance_mode();
	}

	/**
	* core.adm_page_header
	*
	* @param array $event Data from the PHP event
	*/
	public function adm_page_header($event)
	{
		// Add template variables
		$this->template->assign_vars([
			'S_FOUNDER'	=> $this->user->data['user_type'] == USER_FOUNDER
		]);
	}

	/**
	* core.append_sid
	*
	* @param array $event Data from the PHP event
	*/
	public function append_sid($event)
	{
		// Add checking our extension, unless it causes errors when disabling the extension
		if (!$event['is_route'] && $this->ext_manager->is_enabled('vinabb/web'))
		{
			// Get parameters
			$params_ary = [];

			if ($event['params'] !== false || strpos($event['url'], "ucp.{$this->php_ext}") !== false || strpos($event['url'], "mcp.{$this->php_ext}") !== false)
			{
				$event_params = ($event['params'] !== false) ? $event['params'] : substr(strrchr($event['url'], '?'), 1);

				if (!empty($event_params))
				{
					$params = explode('&', str_replace(['&amp;', '?'], ['&', ''], $event_params));

					foreach ($params as $param)
					{
						list($param_key, $param_value) = explode('=', $param);
						$params_ary[$param_key] = $param_value;
					}
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

					if ($params_ary['forum_id'])
					{
						$params_ary['seo'] = $forum_data[$params_ary['forum_id']]['name_seo'] . constants::REWRITE_URL_SEO;
					}
				}

				$route_name = 'vinabb_web_board_forum_route';
			}
			else if (strpos($event['url'], "viewonline.{$this->php_ext}") !== false)
			{
				$route_name = 'vinabb_web_user_online_route';
			}
			else if (strpos($event['url'], "mcp.{$this->php_ext}") !== false)
			{
				if (isset($params_ary['i']))
				{
					$params_ary['id'] = (substr($params_ary['i'], 0, 4) == 'mcp_') ? substr($params_ary['i'], 4) : $params_ary['i'];
					unset($params_ary['i']);
				}

				$route_name = 'vinabb_web_mcp_route';
			}
			else if (strpos($event['url'], "ucp.{$this->php_ext}") !== false)
			{
				if (isset($params_ary['i']))
				{
					$params_ary['id'] = (substr($params_ary['i'], 0, 4) == 'ucp_') ? substr($params_ary['i'], 4) : $params_ary['i'];
					unset($params_ary['i']);
				}
				else if (isset($params_ary['mode']) && in_array($params_ary['mode'], ['activate', 'resend_act', 'sendpassword', 'register', 'confirm', 'login', 'login_link', 'logout', 'terms', 'privacy', 'delete_cookies', 'switch_perm', 'restore_perm']))
				{
					$params_ary['id'] = 'front';
				}

				$route_name = 'vinabb_web_ucp_route';
			}
			else if (strpos($event['url'], "memberlist.{$this->php_ext}") !== false)
			{
				if (isset($params_ary['mode']))
				{
					switch ($params_ary['mode'])
					{
						case 'contactadmin':
							$route_name = 'vinabb_web_user_contact_admin_route';
						break;

						case 'email':
							if (isset($params_ary['t']))
							{
								$params_ary['type'] = 'topic';
								$params_ary['id'] = $params_ary['t'];
								unset($params_ary['t']);
							}
							else if (isset($params_ary['u']))
							{
								$params_ary['type'] = 'user';
								$params_ary['id'] = $params_ary['u'];
								unset($params_ary['u']);
							}

							$route_name = 'vinabb_web_user_email_route';
						break;

						case 'contact':
							if (isset($params_ary['u']))
							{
								$params_ary['user_id'] = $params_ary['u'];
								unset($params_ary['u']);
							}

							$route_name = 'vinabb_web_user_contact_route';
						break;

						case 'team':
							$route_name = 'vinabb_web_user_team_route';
						break;
					}

					unset($params_ary['mode']);
				}
				else
				{
					$route_name = 'vinabb_web_user_list_route';
				}
			}

			// Replace by routes
			if (!empty($route_name))
			{
				$event['append_sid_overwrite'] = $this->helper->route($route_name, $params_ary, false, $event['session_id']);
			}
		}
	}

	/**
	* core.add_log
	*
	* @param array $event Data from the PHP event
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
	* core.get_avatar_after
	*
	* @param array $event Data from the PHP event
	*/
	public function get_avatar_after($event)
	{
		$avatar_data = $event['avatar_data'];

		$this->template->assign_vars([
			'CURRENT_USER_AVATAR_URL'		=>	$avatar_data['src'],
			'CURRENT_USER_AVATAR_WIDTH'		=>	$avatar_data['width'],
			'CURRENT_USER_AVATAR_HEIGHT'	=>	$avatar_data['height']
		]);
	}

	/**
	* core.user_add_modify_data
	*
	* @param array $event Data from the PHP event
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
	* @param array $event Data from the PHP event
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
	* @param array $event Data from the PHP event
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
	* @param array $event Data from the PHP event
	*/
	public function generate_smilies_after($event)
	{
		// Do not display the "More smilies" link
		$event['display_link'] = false;
	}

	/**
	* core.login_box_redirect
	*
	* @param array $event Data from the PHP event
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
	* core.memberlist_memberrow_before
	*
	* @param array $event Data from the PHP event
	*/
	public function memberlist_memberrow_before($event)
	{
		// Enable contact fields on the member list
		$event['use_contact_fields'] = true;
	}

	/**
	* core.memberlist_prepare_profile_data
	*
	* @param array $event Data from the PHP event
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
		$template_data['U_PM_ALT'] = ($this->config['allow_privmsg'] && $this->auth->acl_get('u_sendpm')) ? $this->helper->route('vinabb_web_ucp_route', ['id' => 'pm', 'mode' => 'compose', 'u' => $data['user_id']]) : '';
		$event['template_data'] = $template_data;
	}

	/**
	* core.memberlist_team_modify_template_vars
	*
	* @param array $event Data from the PHP event
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
	* @param array $event Data from the PHP event
	*/
	public function modify_format_display_text_after($event)
	{
		$event['text'] = $this->event_helper->render($this->event_helper->parse($event['text']));
	}

	/**
	* core.modify_submit_post_data
	*
	* @param array $event Data from the PHP event
	*/
	public function modify_submit_post_data($event)
	{
		$data = $event['data'];
		$data['message'] = $this->event_helper->parse($data['message']);
		$event['data'] = $data;
	}

	/**
	* core.modify_text_for_display_after
	*
	* @param array $event Data from the PHP event
	*/
	public function modify_text_for_display_after($event)
	{
		$event['text'] = $this->event_helper->render($event['text']);

		// Load highlight.js
		$this->template->assign_vars([
			'S_LOAD_HIGHLIGHT'	=> true
		]);
	}

	/**
	* core.modify_text_for_edit_before
	*
	* @param array $event Data from the PHP event
	*/
	public function modify_text_for_edit_before($event)
	{
		$event['text'] = $this->event_helper->unparse($event['text']);
	}

	/**
	* core.modify_text_for_storage_after
	*
	* @param array $event Data from the PHP event
	*/
	public function modify_text_for_storage_after($event)
	{
		$event['text'] = $this->event_helper->parse($event['text']);
	}

	/**
	* core.submit_pm_before
	*
	* @param array $event Data from the PHP event
	*/
	public function submit_pm_before($event)
	{
		$data = $event['data'];
		$data['message'] = $this->event_helper->parse($data['message']);
		$event['data'] = $data;
	}

	/**
	* core.ucp_pm_view_messsage
	*
	* @param array $event Data from the PHP event
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
	* @param array $event Data from the PHP event
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
	* @param array $event Data from the PHP event
	*/
	public function obtain_users_online_string_sql($event)
	{
		// Get total online users (only number)
		$online_users = $event['online_users'];

		$this->template->assign_vars([
			'TOTAL_ONLINE_USERS'	=> $online_users['total_online']
		]);
	}

	/**
	* core.text_formatter_s9e_configure_after
	*
	* @param array $event Data from the PHP event
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
	* core.text_formatter_s9e_configure_before
	*
	* @param array $event Data from the PHP event
	*/
	public function text_formatter_s9e_configure_before($event)
	{
		$configurator = $event['configurator'];

		foreach ($configurator->MediaEmbed->defaultSites->getIds() as $site_id)
		{
			if (in_array($site_id, ['facebook', 'twitter', 'googleplus', 'youtube', 'flickr', 'instagram', 'gist']))
			{
				$configurator->MediaEmbed->add($site_id);
			}
		}

		// Add our site
		$configurator->MediaEmbed->add('vinabb', [
			'host'		=> 'vinabb.vn',
			'extract'	=> [
				"!vinabb\\.vn/viewtopic\\.php\\?f=(?'f'[0-9]+)\\&t=(?'t'[0-9]+)!",
				"!vinabb\\.vn/viewtopic\\.php\\?t=(?'t'[0-9]+)!",
			],
			'iframe'	=> [
				'width'		=> 560,
				'height'	=> 260,
				'src'		=> 'http://localhost/vinabb/embed/topic/{@t}',
			]
		]);
	}

	/**
	* core.acp_manage_forums_update_data_before
	*
	* @param array $event Data from the PHP event
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
}
