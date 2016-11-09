<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\controllers;

class mcp
{
	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\event\dispatcher_interface */
	protected $dispatcher;

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

	/** @var string */
	protected $root_path;

	/** @var string */
	protected $php_ext;

	/** @var string */
	protected $ext_root_path;

	/**
	* Constructor
	*
	* @param \phpbb\auth\auth $auth
	* @param \phpbb\db\driver\driver_interface $db
	* @param \phpbb\event\dispatcher_interface $dispatcher
	* @param \phpbb\extension\manager $ext_manager
	* @param \phpbb\language\language $language
	* @param \phpbb\request\request $request
	* @param \phpbb\template\template $template
	* @param \phpbb\user $user
	* @param \phpbb\controller\helper $helper
	* @param string $root_path
	* @param string $php_ext
	*/
	public function __construct(
		\phpbb\auth\auth $auth,
		\phpbb\db\driver\driver_interface $db,
		\phpbb\event\dispatcher_interface $dispatcher,
		\phpbb\extension\manager $ext_manager,
		\phpbb\language\language $language,
		\phpbb\request\request $request,
		\phpbb\template\template $template,
		\phpbb\user $user,
		\phpbb\controller\helper $helper,
		$root_path,
		$php_ext
	)
	{
		$this->auth = $auth;
		$this->db = $db;
		$this->dispatcher = $dispatcher;
		$this->ext_manager = $ext_manager;
		$this->language = $language;
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
		require "{$this->root_path}includes/functions_admin.{$this->php_ext}";
		require "{$this->root_path}includes/functions_mcp.{$this->php_ext}";
		require "{$this->ext_root_path}includes/functions_module.{$this->php_ext}";

		$this->language->add_lang('mcp');

		// Setting a variable to let the style designer know where he is...
		$this->template->assign_var('S_IN_MCP', true);

		$module = new \vinabb\web\includes\p_master();

		// Only Moderators can go beyond this point
		if (!$this->user->data['is_registered'])
		{
			if ($this->user->data['is_bot'])
			{
				redirect(append_sid("{$this->root_path}index.{$this->php_ext}"));
			}

			login_box('', $this->language->lang('LOGIN_EXPLAIN_MCP'));
		}

		$quickmod = (isset($_REQUEST['quickmod'])) ? true : false;
		$action = $this->request->variable('action', '');
		$action_ary = $this->request->variable('action', array('' => 0));
		$forum_action = $this->request->variable('forum_action', '');

		if ($forum_action !== '' && $this->request->variable('sort', false, false, \phpbb\request\request_interface::POST))
		{
			$action = $forum_action;
		}

		if (sizeof($action_ary))
		{
			list($action,) = each($action_ary);
		}
		unset($action_ary);

		if ($mode == 'topic_logs')
		{
			$id = 'logs';
			$quickmod = false;
		}

		$post_id = $this->request->variable('p', 0);
		$topic_id = $this->request->variable('t', 0);
		$forum_id = $this->request->variable('f', 0);
		$user_id = $this->request->variable('u', 0);
		$username = $this->request->variable('username', '', true);

		if ($post_id)
		{
			// We determine the topic and forum id here, to make sure the moderator really has moderative rights on this post
			$sql = 'SELECT topic_id, forum_id
				FROM ' . POSTS_TABLE . "
				WHERE post_id = $post_id";
			$result = $this->db->sql_query($sql);
			$row = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);

			$topic_id = (int) $row['topic_id'];
			$forum_id = (int) $row['forum_id'];
		}
		else if ($topic_id)
		{
			$sql = 'SELECT forum_id
				FROM ' . TOPICS_TABLE . "
				WHERE topic_id = $topic_id";
			$result = $this->db->sql_query($sql);
			$row = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);

			$forum_id = (int) $row['forum_id'];
		}

		// If the user doesn't have any moderator powers (globally or locally) he can't access the mcp
		if (!$this->auth->acl_getf_global('m_'))
		{
			// Except he is using one of the quickmod tools for users
			$user_quickmod_actions = array(
				'lock'			=> 'f_user_lock',
				'make_sticky'	=> 'f_sticky',
				'make_announce'	=> 'f_announce',
				'make_global'	=> 'f_announce_global',
				'make_normal'	=> array('f_announce', 'f_announce_global', 'f_sticky')
			);

			$allow_user = false;

			if ($quickmod && isset($user_quickmod_actions[$action]) && $this->user->data['is_registered'] && $this->auth->acl_gets($user_quickmod_actions[$action], $forum_id))
			{
				$topic_info = phpbb_get_topic_data(array($topic_id));

				if ($topic_info[$topic_id]['topic_poster'] == $this->user->data['user_id'])
				{
					$allow_user = true;
				}
			}

			if (!$allow_user)
			{
				trigger_error('NOT_AUTHORISED');
			}
		}

		// If the user cannot read the forum he tries to access then we won't allow mcp access either
		if ($forum_id && !$this->auth->acl_get('f_read', $forum_id))
		{
			trigger_error('NOT_AUTHORISED');
		}

		/**
		* Allow applying additional permissions to MCP access besides f_read
		*
		* @event core.mcp_global_f_read_auth_after
		* @var	string		action			The action the user tried to execute
		* @var	int			forum_id		The forum the user tried to access
		* @var	string		mode			The MCP module the user is trying to access
		* @var	p_master	module			Module system class
		* @var	bool		quickmod		True if the user is accessing using quickmod tools
		* @var	int			topic_id		The topic the user tried to access
		* @since 3.1.3-RC1
		*/
		$vars = array(
			'action',
			'forum_id',
			'mode',
			'module',
			'quickmod',
			'topic_id',
		);
		extract($this->dispatcher->trigger_event('core.mcp_global_f_read_auth_after', compact($vars)));

		if ($forum_id)
		{
			$module->acl_forum_id = $forum_id;
		}

		// Instantiate module system and generate list of available modules
		$module->list_modules('mcp');

		if ($quickmod)
		{
			$mode = 'quickmod';

			switch ($action)
			{
				case 'lock':
				case 'unlock':
				case 'lock_post':
				case 'unlock_post':
				case 'make_sticky':
				case 'make_announce':
				case 'make_global':
				case 'make_normal':
				case 'fork':
				case 'move':
				case 'delete_post':
				case 'delete_topic':
				case 'restore_topic':
					$module->load('mcp', 'main', 'quickmod');
				return;

				case 'topic_logs':
					// Reset start parameter if we jumped from the quickmod dropdown
					if ($this->request->variable('start', 0))
					{
						$this->request->overwrite('start', 0);
					}

					$module->set_active('logs', 'topic_logs');
				break;

				case 'merge_topic':
					$module->set_active('main', 'forum_view');
				break;

				case 'split':
				case 'merge':
					$module->set_active('main', 'topic_view');
				break;

				default:
					// If needed, the flag can be set to true within event listener
					// to indicate that the action was handled properly
					// and to pass by the trigger_error() call below
					$is_valid_action = false;

					/**
					* This event allows you to add custom quickmod options
					*
					* @event core.modify_quickmod_options
					* @var	object	module			Instance of module system class
					* @var	string	action			Quickmod option
					* @var	bool	is_valid_action	Flag indicating if the action was handled properly
					* @since 3.1.0-a4
					*/
					$vars = array('module', 'action', 'is_valid_action');
					extract($this->dispatcher->trigger_event('core.modify_quickmod_options', compact($vars)));

					if (!$is_valid_action)
					{
						trigger_error($this->language->lang('QUICKMOD_ACTION_NOT_ALLOWED', $action), E_USER_ERROR);
					}
				break;
			}
		}
		else
		{
			// Select the active module
			$module->set_active($id, $mode);
		}

		// Hide some of the options if we don't have the relevant information to use them
		if (!$post_id)
		{
			$module->set_display('main', 'post_details', false);
			$module->set_display('warn', 'warn_post', false);
		}

		if ($mode == '' || $mode == 'unapproved_topics' || $mode == 'unapproved_posts' || $mode == 'deleted_topics' || $mode == 'deleted_posts')
		{
			$module->set_display('queue', 'approve_details', false);
		}

		if ($mode == '' || $mode == 'reports' || $mode == 'reports_closed' || $mode == 'pm_reports' || $mode == 'pm_reports_closed' || $mode == 'pm_report_details')
		{
			$module->set_display('reports', 'report_details', false);
		}

		if ($mode == '' || $mode == 'reports' || $mode == 'reports_closed' || $mode == 'pm_reports' || $mode == 'pm_reports_closed' || $mode == 'report_details')
		{
			$module->set_display('pm_reports', 'pm_report_details', false);
		}

		if (!$topic_id)
		{
			$module->set_display('main', 'topic_view', false);
			$module->set_display('logs', 'topic_logs', false);
		}

		if (!$forum_id)
		{
			$module->set_display('main', 'forum_view', false);
			$module->set_display('logs', 'forum_logs', false);
		}

		if (!$user_id && $username == '')
		{
			$module->set_display('notes', 'user_notes', false);
			$module->set_display('warn', 'warn_user', false);
		}

		/**
		* This event allows you to set display option for custom MCP modules
		*
		* @event core.modify_mcp_modules_display_option
		* @var	p_master	module			Module system class
		* @var	string		mode			MCP mode
		* @var	int			user_id			User id
		* @var	int			forum_id		Forum id
		* @var	int			topic_id		Topic id
		* @var	int			post_id			Post id
		* @var	string		username		User name
		* @var	int			id				Parent module id
		* @since 3.1.0-b2
		*/
		$vars = array(
			'module',
			'mode',
			'user_id',
			'forum_id',
			'topic_id',
			'post_id',
			'username',
			'id',
		);
		extract($this->dispatcher->trigger_event('core.modify_mcp_modules_display_option', compact($vars)));

		// Load and execute the relevant module
		$module->load_active();

		// Assign data to the template engine for the list of modules
		$module->assign_tpl_vars("{$this->root_path}mcp.{$this->php_ext}");

		// Generate urls for letting the moderation control panel being accessed in different modes
		$this->template->assign_vars(array(
			'U_MCP'			=> $this->helper->route('vinabb_web_mcp_route', array('id' => 'main')),
			'U_MCP_FORUM'	=> ($forum_id) ? $this->helper->route('vinabb_web_mcp_route', array('id' => 'main', 'mode' => 'forum_view', 'f' => $forum_id)) : '',
			'U_MCP_TOPIC'	=> ($forum_id && $topic_id) ? $this->helper->route('vinabb_web_mcp_route', array('id' => 'main', 'mode' => 'topic_view', 't' => $topic_id)) : '',
			'U_MCP_POST'	=> ($forum_id && $topic_id && $post_id) ? $this->helper->route('vinabb_web_mcp_route', array('id' => 'main', 'mode' => 'post_details', 't' => $topic_id, 'p' => $post_id)) : '',
		));

		// Generate the page, do not display/query online list
		$module->display($module->get_page_title());
	}
}
