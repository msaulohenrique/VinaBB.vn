<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\controllers;

/**
* Controller for the User Control Panel
*/
class ucp implements ucp_interface
{
	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\config\config */
	protected $config;

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

	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var string */
	protected $root_path;

	/** @var string */
	protected $php_ext;

	/** @var \vinabb\web\includes\p_master $module */
	protected $module;

	/** @var string $id */
	protected $id;

	/** @var string $mode */
	protected $mode;

	/**
	* Constructor
	*
	* @param \phpbb\auth\auth $auth
	* @param \phpbb\config\config $config
	* @param \phpbb\db\driver\driver_interface $db
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
		$this->language = $language;
		$this->log = $log;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->helper = $helper;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;
	}

	/**
	* UCP module
	*
	* @param string	$id		Module basename
	* @param string	$mode	Module mode
	* @return mixed|bool
	*/
	public function main($id, $mode)
	{
		// Common functions
		require "{$this->root_path}includes/functions_user.{$this->php_ext}";
		require "{$this->root_path}includes/functions_module.{$this->php_ext}";

		if (in_array($mode, ['login', 'login_link', 'logout', 'confirm', 'sendpassword', 'activate']))
		{
			define('IN_LOGIN', true);
		}

		$this->language->add_lang('ucp');

		// Setting a variable to let the style designer know where he is...
		$this->template->assign_var('S_IN_UCP', true);

		$this->module = new \vinabb\web\includes\p_master();
		$this->id = $id;
		$this->mode = $mode;

		if (in_array($mode, ['activate', 'resend_act', 'sendpassword', 'register', 'confirm', 'login', 'login_link', 'logout', 'terms', 'privacy', 'delete_cookies', 'switch_perm', 'restore_perm']))
		{
			$this->{'ucp_' . $mode}();

			// We use this approach because it does not impose large code changes
			return true;
		}

		// Only registered users can go beyond this point
		$this->require_login();

		// Instantiate module system and generate list of available modules
		$this->module->list_modules('ucp');

		// Check if the zebra module is set
		if ($this->module->is_active('zebra', 'friends'))
		{
			$this->display_online_friends();
		}

		// Do not display subscribed topics/forums if not allowed
		if (!$this->config['allow_topic_notify'] && !$this->config['allow_forum_notify'])
		{
			$this->module->set_display('main', 'subscribed', false);
		}

		// Select the active module
		$this->module->set_active($id, $mode);

		// Load and execute the relevant module
		$this->module->load_active();

		// Assign data to the template engine for the list of modules
		$this->module->assign_tpl_vars("{$this->root_path}ucp.{$this->php_ext}");

		// Generate the page, do not display/query online list
		$this->module->display($this->module->get_page_title());
	}

	/**
	* Sub-method for the main() with mode = activate
	*/
	protected function ucp_activate()
	{
		$this->module->load('ucp', 'activate');
		$this->module->display($this->language->lang('UCP_ACTIVATE'));

		redirect(append_sid("{$this->root_path}index.{$this->php_ext}"));
	}

	/**
	* Sub-method for the main() with mode = resend_act
	*/
	protected function ucp_resend_act()
	{
		$this->module->load('ucp', 'resend');
		$this->module->display($this->language->lang('UCP_RESEND'));
	}

	/**
	* Sub-method for the main() with mode = sendpassword
	*/
	protected function ucp_sendpassword()
	{
		$this->module->load('ucp', 'remind');
		$this->module->display($this->language->lang('UCP_REMIND'));
	}

	/**
	* Sub-method for the main() with mode = register
	*/
	protected function ucp_register()
	{
		if ($this->user->data['is_registered'] || $this->request->is_set('not_agreed'))
		{
			redirect(append_sid("{$this->root_path}index.{$this->php_ext}"));
		}

		$this->module->load('ucp', 'register');
		$this->module->display($this->language->lang('REGISTER'));
	}

	/**
	* Sub-method for the main() with mode = confirm
	*/
	protected function ucp_confirm()
	{
		$this->module->load('ucp', 'confirm');
	}

	/**
	* Sub-method for the main() with mode = login
	*/
	protected function ucp_login()
	{
		if ($this->user->data['is_registered'])
		{
			redirect(append_sid("{$this->root_path}index.{$this->php_ext}"));
		}

		login_box($this->request->variable('redirect', "index.{$this->php_ext}"));
	}

	/**
	* Sub-method for the main() with mode = login_link
	*/
	protected function ucp_login_link()
	{
		if ($this->user->data['is_registered'])
		{
			redirect(append_sid("{$this->root_path}index.{$this->php_ext}"));
		}

		$this->module->load('ucp', 'login_link');
		$this->module->display($this->language->lang('UCP_LOGIN_LINK'));
	}

	/**
	* Logout the session
	*/
	public function logout()
	{
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
	}

	/**
	* Sub-method for the main() with mode = logout
	*/
	protected function ucp_logout()
	{
		$this->logout();
	}

	/**
	* Display agreement page
	*
	* @param string	$title		Page title
	* @param string	$message	Page content
	* @return \Symfony\Component\HttpFoundation\Response
	*/
	public function display_agreement($title = 'TERMS_USE', $message = 'TERMS_OF_USE_CONTENT')
	{
		if (!$this->language->is_set($message))
		{
			if ($this->user->data['is_registered'])
			{
				redirect(append_sid("{$this->root_path}index.{$this->php_ext}"));
			}

			login_box();
		}

		$this->template->assign_vars([
			'S_AGREEMENT'		=> true,
			'AGREEMENT_TITLE'	=> $this->language->lang($title),
			'AGREEMENT_TEXT'	=> $this->language->lang($message, $this->config['sitename'], generate_board_url()),
			'U_BACK'			=> $this->helper->route('vinabb_web_ucp_route', ['mode' => 'login'])
		]);

		return $this->helper->render('ucp_agreement.html', $this->language->lang($title));
	}

	/**
	* Sub-method for the main() with mode = terms
	*/
	protected function ucp_terms()
	{
		display_agreement('TERMS_USE', 'TERMS_OF_USE_CONTENT');
	}

	/**
	* Sub-method for the main() with mode = privacy
	*/
	protected function ucp_privacy()
	{
		display_agreement('PRIVACY', 'PRIVACY_POLICY');
	}

	/**
	* Delete Cookies with dynamic names (DO NOT delete poll cookies)
	*/
	public function delete_cookies()
	{
		if (confirm_box(true))
		{
			$set_time = time() - 31536000;

			foreach ($this->request->variable_names(\phpbb\request\request_interface::COOKIE) as $cookie_name)
			{
				// Only delete board cookies, no other ones...
				if (strpos($cookie_name, $this->config['cookie_name'] . '_') !== false)
				{
					continue;
				}

				$cookie_name = str_replace($this->config['cookie_name'] . '_', '', $cookie_name);

				// Polls are stored as {cookie_name}_poll_{topic_id}, cookie_name_ got removed, therefore checking for poll_
				if (strpos($cookie_name, 'poll_') !== false)
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
	}

	/**
	* Sub-method for the main() with mode = delete_cookies
	*/
	protected function ucp_delete_cookies()
	{
		$this->delete_cookies();
	}

	/**
	* Switch permissions to another user
	*/
	public function switch_perm()
	{
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

		$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_ACL_TRANSFER_PERMISSIONS', false, [$user_row['username']]);

		$message = sprintf($this->language->lang('PERMISSIONS_TRANSFERRED'), $user_row['username']) . '<br><br>' . $this->language->lang('RETURN_INDEX', '<a href="' . append_sid("{$this->root_path}index.{$this->php_ext}") . '">', '</a>');
		trigger_error($message);
	}

	/**
	* Sub-method for the main() with mode = switch_perm
	*/
	protected function ucp_switch_perm()
	{
		$this->switch_perm();
	}

	/**
	* Restore original user permissions
	*/
	public function restore_perm()
	{
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

		$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_ACL_RESTORE_PERMISSIONS', false, [$username]);

		$message = $this->language->lang('PERMISSIONS_RESTORED') . '<br><br>' . $this->language->lang('RETURN_INDEX', '<a href="' . append_sid("{$this->root_path}index.{$this->php_ext}") . '">', '</a>');
		trigger_error($message);
	}

	/**
	* Sub-method for the main() with mode = restore_perm
	*/
	protected function ucp_restore_perm()
	{
		$this->restore_perm();
	}

	/**
	* Requires guests to login
	*/
	protected function require_login()
	{
		if (!$this->user->data['is_registered'])
		{
			if ($this->user->data['is_bot'])
			{
				redirect(append_sid("{$this->root_path}index.{$this->php_ext}"));
			}

			if ($this->id == 'pm' && $this->mode == 'view' && $this->request->is_set('p'))
			{
				$redirect_url = $this->helper->route('vinabb_web_ucp_route', ['id' => 'pm', 'p' => $this->request->variable('p', 0)]);
				login_box($redirect_url, $this->language->lang('LOGIN_EXPLAIN_UCP'));
			}

			login_box('', $this->language->lang('LOGIN_EXPLAIN_UCP'));
		}
	}

	/**
	* Get the list of online friends
	*/
	public function display_online_friends()
	{
		// Output listing of friends online
		$update_time = $this->config['load_online_time'] * 60;

		$sql_ary = [
			'SELECT'	=> 'u.user_id, u.username, u.username_clean, u.user_colour, MAX(s.session_time) AS online_time, MIN(s.session_viewonline) AS viewonline',
			'FROM'		=> [
				USERS_TABLE	=> 'u',
				ZEBRA_TABLE	=> 'z'
			],
			'LEFT_JOIN'	=> [
				[
					'FROM'	=> [SESSIONS_TABLE => 's'],
					'ON'	=> 's.session_user_id = z.zebra_id'
				]
			],
			'WHERE'		=> 'z.user_id = ' . $this->user->data['user_id'] . '
				AND z.friend = 1
				AND u.user_id = z.zebra_id',
			'GROUP_BY'	=> 'z.zebra_id, u.user_id, u.username_clean, u.user_colour, u.username',
			'ORDER_BY'	=> 'u.username_clean'
		];

		$sql = $this->db->sql_build_query('SELECT_DISTINCT', $sql_ary);
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$which = (time() - $update_time < $row['online_time'] && ($row['viewonline'] || $this->auth->acl_get('u_viewonline'))) ? 'online' : 'offline';

			$this->template->assign_block_vars("friends_{$which}", [
				'USER_ID'	=> $row['user_id'],

				'U_PROFILE'		=> get_username_string('profile', $row['user_id'], $row['username'], $row['user_colour']),
				'USER_COLOUR'	=> get_username_string('colour', $row['user_id'], $row['username'], $row['user_colour']),
				'USERNAME'		=> get_username_string('username', $row['user_id'], $row['username'], $row['user_colour']),
				'USERNAME_FULL'	=> get_username_string('full', $row['user_id'], $row['username'], $row['user_colour'])
			]);
		}
		$this->db->sql_freeresult($result);
	}
}
