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
class common implements EventSubscriberInterface
{
	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \vinabb\web\controllers\cache\service_interface */
	protected $cache;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\extension\manager */
	protected $ext_manager;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var \vinabb\web\events\helper\helper_interface */
	protected $event_helper;

	/** @var \phpbb\path_helper */
	protected $path_helper;

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
	* @param \vinabb\web\controllers\cache\service_interface $cache
	* @param \phpbb\config\config $config
	* @param \phpbb\extension\manager $ext_manager
	* @param \phpbb\language\language $language
	* @param \phpbb\template\template $template
	* @param \phpbb\user $user
	* @param \phpbb\controller\helper $helper
	* @param \vinabb\web\events\helper\helper_interface $event_helper
	* @param \phpbb\path_helper $path_helper
	* @param string $php_ext
	*/
	public function __construct(
		\phpbb\auth\auth $auth,
		\vinabb\web\controllers\cache\service_interface $cache,
		\phpbb\config\config $config,
		\phpbb\extension\manager $ext_manager,
		\phpbb\language\language $language,
		\phpbb\template\template $template,
		\phpbb\user $user,
		\phpbb\controller\helper $helper,
		\vinabb\web\events\helper\helper_interface $event_helper,
		\phpbb\path_helper $path_helper,
		$php_ext
	)
	{
		$this->auth = $auth;
		$this->cache = $cache;
		$this->config = $config;
		$this->ext_manager = $ext_manager;
		$this->language = $language;
		$this->template = $template;
		$this->user = $user;
		$this->helper = $helper;
		$this->event_helper = $event_helper;
		$this->path_helper = $path_helper;
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
			'core.user_setup'						=> 'user_setup',
			'core.page_header_after'				=> 'page_header_after',
			'core.append_sid'						=> 'append_sid',
			'core.get_avatar_after'					=> 'get_avatar_after',
			'core.login_box_redirect'				=> 'login_box_redirect',
			'core.memberlist_prepare_profile_data'	=> 'memberlist_prepare_profile_data',
			'core.ucp_pm_view_messsage'				=> 'ucp_pm_view_messsage',
			'core.obtain_users_online_string_sql'	=> 'obtain_users_online_string_sql'
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
				$event_params = str_replace(['&amp;', '?'], ['&', ''], $event_params);

				// Some cases: abc.php?&x=1
				$event_params = (substr($event_params, 0, 1) == '&') ? substr($event_params, 1) : $event_params;

				if (!empty($event_params))
				{
					$params = explode('&', $event_params);

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
							$route_name = 'vinabb_web_user_contact_route';
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

							$route_name = 'vinabb_web_user_messenger_route';
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
	* core.get_avatar_after
	*
	* @param array $event Data from the PHP event
	*/
	public function get_avatar_after($event)
	{
		// Modify phpbb_get_avatar() to return only the attribute src=""
		$avatar_data = $event['avatar_data'];
		$event['html'] = $avatar_data['src'];
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
	* core.memberlist_prepare_profile_data
	*
	* @param array $event Data from the PHP event
	*/
	public function memberlist_prepare_profile_data($event)
	{
		$data = $event['data'];
		$template_data = $event['template_data'];

		// Add USER_ID
		$template_data['USER_ID'] = $data['user_id'];

		// Translate the rank title RANK_TITLE with the original value RANK_TITLE_RAW
		$template_data['RANK_TITLE_RAW'] = $template_data['RANK_TITLE'];
		$template_data['RANK_TITLE'] = ($this->language->is_set(['RANK_TITLES', strtoupper($template_data['RANK_TITLE'])])) ? $this->language->lang(['RANK_TITLES', strtoupper($template_data['RANK_TITLE'])]) : $template_data['RANK_TITLE'];

		// Override U_PM_ALT without checking $can_receive_pm
		$template_data['U_PM'] = ($this->config['allow_privmsg'] && $this->auth->acl_get('u_sendpm')) ? $this->helper->route('vinabb_web_ucp_route', ['id' => 'pm', 'mode' => 'compose', 'u' => $data['user_id']]) : '';

		// Override U_VIEW_PROFILE with our profile route
		$template_data['U_VIEW_PROFILE'] = $this->helper->route('vinabb_web_user_profile_route', ['username' => $data['username']]);

		// Return new data
		$event['template_data'] = $template_data;
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
}
