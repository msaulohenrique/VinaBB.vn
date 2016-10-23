<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\controller;

class ucp
{
	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\event\dispatcher_interface */
	protected $dispatcher;

	/** @var \phpbb\extension\manager */
	protected $ext_manager;

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

	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var string */
	protected $root_path;

	/** @var string */
	protected $php_ext;

	/**
	* Constructor
	*
	* @param \phpbb\auth\auth $auth
	* @param \phpbb\config\config $config
	* @param \phpbb\db\driver\driver_interface $db
	* @param \phpbb\event\dispatcher_interface $dispatcher
	* @param \phpbb\extension\manager $ext_manager
	* @param \phpbb\language\language $language
	* @param \phpbb\log\log $log
	* @param \phpbb\request\request $request
	* @param \phpbb\template\template $template
	* @param \phpbb\user $user
	* @param \phpbb\controller\helper $helper
	* @param string $root_path
	* @param string $php_ext
	*/
	public function __construct(
		\phpbb\auth\auth $auth,
		\phpbb\config\config $config,
		\phpbb\db\driver\driver_interface $db,
		\phpbb\event\dispatcher_interface $dispatcher,
		\phpbb\extension\manager $ext_manager,
		\phpbb\language\language $language,
		\phpbb\log\log $log,
		\phpbb\request\request $request,
		\phpbb\template\template $template,
		\phpbb\user $user,
		\phpbb\controller\helper $helper,
		$root_path,
		$php_ext
	)
	{
		$this->auth = $auth;
		$this->config = $config;
		$this->db = $db;
		$this->dispatcher = $dispatcher;
		$this->ext_manager = $ext_manager;
		$this->language = $language;
		$this->log = $log;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->helper = $helper;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;

		$this->ext_root_path = $this->ext_manager->get_extension_path('vinabb/web', true);
	}

	public function main($id, $mode)
	{
		// Allow to use '/' at the end
		if (substr($mode, -1) == '/')
		{
			$mode = substr($mode, 0, -1);
		}

		// Common functions
		require "{$this->root_path}includes/functions_user.{$this->php_ext}";
		require "{$this->ext_root_path}includes/functions_module.{$this->php_ext}";

		if (in_array($mode, array('login', 'login_link', 'logout', 'confirm', 'sendpassword', 'activate')))
		{
			define('IN_LOGIN', true);
		}

		$this->language->add_lang('ucp');

		// Setting a variable to let the style designer know where he is...
		$this->template->assign_var('S_IN_UCP', true);

		$module = new \vinabb\web\includes\p_master();
		$default = false;

		// Basic "global" modes
		switch ($mode)
		{
			case 'activate':
				$module->load('ucp', 'activate');
				$module->display($this->language->lang('UCP_ACTIVATE'));

				redirect(append_sid("{$this->root_path}index.{$this->php_ext}"));
			break;

			case 'resend_act':
				$module->load('ucp', 'resend');
				$module->display($this->language->lang('UCP_RESEND'));
			break;

			case 'sendpassword':
				$module->load('ucp', 'remind');
				$module->display($this->language->lang('UCP_REMIND'));
			break;

			case 'register':
				if ($this->user->data['is_registered'] || $this->request->is_set('not_agreed'))
				{
					redirect(append_sid("{$this->root_path}index.{$this->php_ext}"));
				}

				$module->load('ucp', 'register');
				$module->display($this->language->lang('REGISTER'));
			break;

			case 'confirm':
				$module->load('ucp', 'confirm');
			break;

			case 'login':
				if ($this->user->data['is_registered'])
				{
					redirect(append_sid("{$this->root_path}index.{$this->php_ext}"));
				}

				login_box($this->request->variable('redirect', "index.{$this->php_ext}"));
			break;

			case 'login_link':
				if ($this->user->data['is_registered'])
				{
					redirect(append_sid("{$this->root_path}index.{$this->php_ext}"));
				}

				$module->load('ucp', 'login_link');
				$module->display($this->language->lang('UCP_LOGIN_LINK'));
			break;

			case 'logout':
				if ($this->user->data['user_id'] != ANONYMOUS && $this->request->is_set('sid') && $this->request->variable('sid', '') === $this->user->session_id)
				{
					$this->user->session_kill();
				}
				else if ($this->user->data['user_id'] != ANONYMOUS)
				{
					meta_refresh(3, append_sid("{$this->root_path}index.{$this->php_ext}"));

					$message = $this->language->lang('LOGOUT_FAILED') . '<br><br>' . $this->language->lang('RETURN_INDEX', '<a href="' . append_sid("{$this->root_path}index.{$this->php_ext}") . '">', '</a>');
					trigger_error($message);
				}

				redirect(append_sid("{$this->root_path}index.{$this->php_ext}"));
			break;

			case 'terms':
			case 'privacy':
				$message = ($mode == 'terms') ? 'TERMS_OF_USE_CONTENT' : 'PRIVACY_POLICY';
				$title = ($mode == 'terms') ? 'TERMS_USE' : 'PRIVACY';

				if (!$this->language->is_set($message))
				{
					if ($this->user->data['is_registered'])
					{
						redirect(append_sid("{$this->root_path}index.{$this->php_ext}"));
					}

					login_box();
				}

				$this->template->assign_vars(array(
					'S_AGREEMENT'		=> true,
					'AGREEMENT_TITLE'	=> $this->language->lang($title),
					'AGREEMENT_TEXT'	=> $this->language->lang($message, $this->config['sitename'], generate_board_url()),
					'U_BACK'			=> $this->helper->route('vinabb_web_ucp_route', array('mode' => 'login')),
					'L_BACK'			=> $this->language->lang('BACK_TO_LOGIN'),
				));

				return $this->helper->render('ucp_agreement.html', $this->language->lang($title));
			break;

			case 'delete_cookies':
				// Delete Cookies with dynamic names (do NOT delete poll cookies)
				if (confirm_box(true))
				{
					$set_time = time() - 31536000;

					foreach ($this->request->variable_names(\phpbb\request\request_interface::COOKIE) as $cookie_name)
					{
						$cookie_data = $this->request->variable($cookie_name, '', true, \phpbb\request\request_interface::COOKIE);

						// Only delete board cookies, no other ones...
						if (strpos($cookie_name, $this->config['cookie_name'] . '_') !== 0)
						{
							continue;
						}

						$cookie_name = str_replace($this->config['cookie_name'] . '_', '', $cookie_name);

						/**
						* Event to save custom cookies from deletion
						*
						* @event core.ucp_delete_cookies
						* @var	string	cookie_name		Cookie name to checking
						* @var	bool	retain_cookie	Do we retain our cookie or not, true if retain
						* @since 3.1.3-RC1
						*/
						$retain_cookie = false;
						$vars = array('cookie_name', 'retain_cookie');
						extract($this->dispatcher->trigger_event('core.ucp_delete_cookies', compact($vars)));

						if ($retain_cookie)
						{
							continue;
						}

						// Polls are stored as {cookie_name}_poll_{topic_id}, cookie_name_ got removed, therefore checking for poll_
						if (strpos($cookie_name, 'poll_') !== 0)
						{
							$this->user->set_cookie($cookie_name, '', $set_time);
						}
					}

					$this->user->set_cookie('track', '', $set_time);
					$this->user->set_cookie('u', '', $set_time);
					$this->user->set_cookie('k', '', $set_time);
					$this->user->set_cookie('sid', '', $set_time);

					// We destroy the session here, the user will be logged out nevertheless
					$this->user->session_kill();
					$this->user->session_begin();

					meta_refresh(3, append_sid("{$this->root_path}index.{$this->php_ext}"));

					$message = $this->language->lang('COOKIES_DELETED') . '<br><br>' . $this->language->lang('RETURN_INDEX', '<a href="' . append_sid("{$this->root_path}index.{$this->php_ext}") . '">', '</a>');
					trigger_error($message);
				}
				else
				{
					confirm_box(false, 'DELETE_COOKIES', '');
				}

				redirect(append_sid("{$this->root_path}index.{$this->php_ext}"));
			break;

			case 'switch_perm':
				$user_id = $this->request->variable('u', 0);

				$sql = 'SELECT *
					FROM ' . USERS_TABLE . '
					WHERE user_id = ' . (int) $user_id;
				$result = $this->db->sql_query($sql);
				$user_row = $this->db->sql_fetchrow($result);
				$this->db->sql_freeresult($result);

				if (!$this->auth->acl_get('a_switchperm') || !$user_row || $user_id == $this->user->data['user_id'] || !check_link_hash($this->request->variable('hash', ''), 'switchperm'))
				{
					redirect(append_sid("{$this->root_path}index.{$this->php_ext}"));
				}

				include "{$this->root_path}includes/acp/auth.{$this->php_ext}";

				$auth_admin = new auth_admin();
				if (!$auth_admin->ghost_permissions($user_id, $this->user->data['user_id']))
				{
					redirect(append_sid("{$this->root_path}index.{$this->php_ext}"));
				}

				$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_ACL_TRANSFER_PERMISSIONS', false, array($user_row['username']));

				$message = sprintf($this->language->lang('PERMISSIONS_TRANSFERRED'), $user_row['username']) . '<br><br>' . $this->language->lang('RETURN_INDEX', '<a href="' . append_sid("{$this->root_path}index.{$this->php_ext}") . '">', '</a>');
				trigger_error($message);
			break;

			case 'restore_perm':
				if (!$this->user->data['user_perm_from'] || !$this->auth->acl_get('a_switchperm'))
				{
					redirect(append_sid("{$this->root_path}index.{$this->php_ext}"));
				}

				$this->auth->acl_cache($this->user->data);

				$sql = 'SELECT username
					FROM ' . USERS_TABLE . '
					WHERE user_id = ' . $this->user->data['user_perm_from'];
				$result = $this->db->sql_query($sql);
				$username = $this->db->sql_fetchfield('username');
				$this->db->sql_freeresult($result);

				$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_ACL_RESTORE_PERMISSIONS', false, array($username));

				$message = $this->language->lang('PERMISSIONS_RESTORED') . '<br><br>' . $this->language->lang('RETURN_INDEX', '<a href="' . append_sid("{$this->root_path}index.{$this->php_ext}") . '">', '</a>');
				trigger_error($message);
			break;

			default:
				$default = true;
			break;
		}

		// We use this approach because it does not impose large code changes
		if (!$default)
		{
			return true;
		}

		// Only registered users can go beyond this point
		if (!$this->user->data['is_registered'])
		{
			if ($this->user->data['is_bot'])
			{
				redirect(append_sid("{$this->root_path}index.{$this->php_ext}"));
			}

			if ($id == 'pm' && $mode == 'view' && $this->request->is_set('p', \phpbb\request\request_interface::GET))
			{
				$redirect_url = $this->helper->route('vinabb_web_ucp_route', array('id' => 'pm', 'p' => $this->request->variable('p', 0)));
				login_box($redirect_url, $this->language->lang('LOGIN_EXPLAIN_UCP'));
			}

			login_box('', $this->language->lang('LOGIN_EXPLAIN_UCP'));
		}

		// Instantiate module system and generate list of available modules
		$module->list_modules('ucp');

		// Check if the zebra module is set
		if ($module->is_active('zebra', 'friends'))
		{
			// Output listing of friends online
			$update_time = $this->config['load_online_time'] * 60;

			$sql_ary = array(
				'SELECT'	=> 'u.user_id, u.username, u.username_clean, u.user_colour, MAX(s.session_time) AS online_time, MIN(s.session_viewonline) AS viewonline',
				'FROM'		=> array(
					USERS_TABLE		=> 'u',
					ZEBRA_TABLE		=> 'z',
				),
				'LEFT_JOIN'	=> array(
					array(
						'FROM'	=> array(SESSIONS_TABLE => 's'),
						'ON'	=> 's.session_user_id = z.zebra_id',
					),
				),
				'WHERE'		=> 'z.user_id = ' . $this->user->data['user_id'] . '
					AND z.friend = 1
					AND u.user_id = z.zebra_id',
				'GROUP_BY'	=> 'z.zebra_id, u.user_id, u.username_clean, u.user_colour, u.username',
				'ORDER_BY'	=> 'u.username_clean',
			);

			$sql = $this->db->sql_build_query('SELECT_DISTINCT', $sql_ary);
			$result = $this->db->sql_query($sql);

			while ($row = $this->db->sql_fetchrow($result))
			{
				$which = (time() - $update_time < $row['online_time'] && ($row['viewonline'] || $this->auth->acl_get('u_viewonline'))) ? 'online' : 'offline';

				$this->template->assign_block_vars("friends_{$which}", array(
						'USER_ID'	=> $row['user_id'],

						'U_PROFILE'		=> get_username_string('profile', $row['user_id'], $row['username'], $row['user_colour']),
						'USER_COLOUR'	=> get_username_string('colour', $row['user_id'], $row['username'], $row['user_colour']),
						'USERNAME'		=> get_username_string('username', $row['user_id'], $row['username'], $row['user_colour']),
						'USERNAME_FULL'	=> get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']))
				);
			}
			$this->db->sql_freeresult($result);
		}

		// Do not display subscribed topics/forums if not allowed
		if (!$this->config['allow_topic_notify'] && !$this->config['allow_forum_notify'])
		{
			$module->set_display('main', 'subscribed', false);
		}

		/**
		* Use this event to enable and disable additional UCP modules
		*
		* @event core.ucp_display_module_before
		* @var	p_master	module	Object holding all modules and their status
		* @var	mixed		id		Active module category (can be the int or string)
		* @var	string		mode	Active module
		* @since 3.1.0-a1
		*/
		$vars = array('module', 'id', 'mode');
		extract($this->dispatcher->trigger_event('core.ucp_display_module_before', compact($vars)));

		// Select the active module
		$module->set_active($id, $mode);

		// Load and execute the relevant module
		$module->load_active();

		// Assign data to the template engine for the list of modules
		$module->assign_tpl_vars("{$this->root_path}ucp.{$this->php_ext}");

		// Generate the page, do not display/query online list
		$module->display($module->get_page_title());
	}
}
