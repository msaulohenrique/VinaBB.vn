<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\controllers;

use Symfony\Component\DependencyInjection\ContainerInterface;

class mcp
{
	/** @var \phpbb\auth\auth $auth */
	protected $auth;

	/** @var ContainerInterface $container */
	protected $container;

	/** @var \phpbb\language\language $language */
	protected $language;

	/** @var \phpbb\request\request $request */
	protected $request;

	/** @var \phpbb\template\template $template */
	protected $template;

	/** @var \phpbb\user $user */
	protected $user;

	/** @var \phpbb\controller\helper $helper */
	protected $helper;

	/** @var string $root_path */
	protected $root_path;

	/** @var string $php_ext */
	protected $php_ext;

	/** @var \vinabb\web\includes\p_master $module */
	protected $module;

	/** @var string $id */
	protected $id;

	/** @var string $mode */
	protected $mode;

	/** @var bool $quickmod */
	protected $quickmod;

	/** @var string $action */
	protected $action;

	/** @var int $forum_id */
	protected $forum_id;

	/** @var int $topic_id */
	protected $topic_id;

	/** @var int $post_id */
	protected $post_id;

	/** @var int $user_id */
	protected $user_id;

	/** @var string $username */
	protected $username;

	/**
	* Constructor
	*
	* @param \phpbb\auth\auth			$auth		Authentication object
	* @param ContainerInterface			$container	Container object
	* @param \phpbb\language\language	$language	Language object
	* @param \phpbb\request\request		$request	Request object
	* @param \phpbb\template\template	$template	Template object
	* @param \phpbb\user				$user		User object
	* @param \phpbb\controller\helper	$helper		Controller helper
	* @param string						$root_path	phpBB root path
	* @param string						$php_ext	PHP file extension
	*/
	public function __construct(
		\phpbb\auth\auth $auth,
		ContainerInterface $container,
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
		$this->container = $container;
		$this->language = $language;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->helper = $helper;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;
	}

	/**
	* Load a MCP module
	*
	* @param string	$id		Module basename
	* @param string	$mode	Module mode
	*/
	public function main($id, $mode)
	{
		// If do not define global, the module mcp_main will not be accessed
		global $module, $action;

		// Common functions
		require "{$this->root_path}includes/functions_admin.{$this->php_ext}";
		require "{$this->root_path}includes/functions_mcp.{$this->php_ext}";
		require "{$this->root_path}includes/functions_module.{$this->php_ext}";

		// Language
		$this->language->add_lang('mcp');

		$this->module = new \vinabb\web\includes\p_master();
		$module = $this->module;
		$this->id = $id;
		$this->mode = $mode;

		// Only Moderators can go beyond this point
		$this->require_login();

		// Get URL parameters
		$this->request_data();
		$action = $this->action;

		// Adjust the module basename if mode = 'topic_logs'
		$this->is_topic_logs();

		// If the user doesn't have any moderator powers (globally or locally) he can't access the mcp
		$this->check_mcp_auth();

		// If the user cannot read the forum he tries to access then we won't allow mcp access either
		$this->check_mcp_forum_auth();

		// Instantiate module system and generate list of available modules
		$this->module->list_modules('mcp');

		// Do the quickmod action
		if ($this->quickmod)
		{
			$this->mode = 'quickmod';
			$quickmod_data = [
				'lock'			=> 'quickmod',
				'unlock'		=> 'quickmod',
				'lock_post'		=> 'quickmod',
				'unlock_post'	=> 'quickmod',
				'make_sticky'	=> 'quickmod',
				'make_announce'	=> 'quickmod',
				'make_global'	=> 'quickmod',
				'make_normal'	=> 'quickmod',
				'fork'			=> 'quickmod',
				'move'			=> 'quickmod',
				'delete_post'	=> 'quickmod',
				'delete_topic'	=> 'quickmod',
				'restore_topic'	=> 'quickmod',
				'topic_logs'	=> 'quickmod_topic_logs',
				'merge_topic'	=> 'quickmod_merge_topic',
				'split'			=> 'quickmod_split_merge',
				'merge'			=> 'quickmod_split_merge'
			];

			if (isset($quickmod_data[$this->action]))
			{
				$this->{$quickmod_data[$this->action]}();
			}
			else
			{
				trigger_error($this->language->lang('QUICKMOD_ACTION_NOT_ALLOWED', $this->action), E_USER_ERROR);
			}
		}
		// Or select the active module
		else
		{
			$this->module->set_active($this->id, $this->mode);
		}

		// Hide some of the options if we don't have the relevant information to use them
		$this->hide_mcp_modules();

		// Load and execute the relevant module
		$this->module->load_active();

		// Assign data to the template engine for the list of modules
		$this->module->assign_tpl_vars("{$this->root_path}mcp.{$this->php_ext}");

		// Generate urls for letting the moderation control panel being accessed in different modes
		$this->output_template();

		// Generate the page, do not display/query online list
		$this->module->display($this->module->get_page_title());
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

			login_box('', $this->language->lang('LOGIN_EXPLAIN_MCP'));
		}
	}

	/**
	* Request data
	*
	* @throws \phpbb\exception\http_exception
	*/
	protected function request_data()
	{
		$this->quickmod = (bool) $this->request->is_set('quickmod');
		$this->action = $this->request->variable('action', '');

		// Forum action
		$forum_action = $this->request->variable('forum_action', '');

		if ($forum_action !== '' && $this->request->variable('sort', false, false, \phpbb\request\request_interface::POST))
		{
			$this->action = $forum_action;
		}

		// Multiple actions
		$action_ary = $this->request->variable('action', ['' => 0]);

		if (sizeof($action_ary))
		{
			list($this->action,) = each($action_ary);
		}

		unset($action_ary);

		// URL parameters
		$this->forum_id = $this->request->variable('f', 0);
		$this->topic_id = $this->request->variable('t', 0);
		$this->post_id = $this->request->variable('p', 0);
		$this->user_id = $this->request->variable('u', 0);
		$this->username = $this->request->variable('username', '', true);

		if ($this->post_id)
		{
			try
			{
				/** @var \vinabb\web\entities\post_interface $entity */
				$entity = $this->container->get('vinabb.web.entities.post')->load($this->post_id);
			}
			catch (\vinabb\web\exceptions\base $e)
			{
				throw new \phpbb\exception\http_exception(404, 'NO_POST');
			}

			$this->forum_id = $entity->get_forum_id();
			$this->topic_id = $entity->get_topic_id();
		}
		else if ($this->topic_id)
		{
			try
			{
				/** @var \vinabb\web\entities\topic_interface $entity */
				$entity = $this->container->get('vinabb.web.entities.topic')->load($this->topic_id);
			}
			catch (\vinabb\web\exceptions\base $e)
			{
				throw new \phpbb\exception\http_exception(404, 'NO_TOPIC');
			}

			$this->forum_id = $entity->get_forum_id();
		}
	}

	/**
	* Switch to the module mcp_logs
	*/
	protected function is_topic_logs()
	{
		if ($this->mode == 'topic_logs')
		{
			$this->id = 'logs';
			$this->quickmod = false;
		}
	}

	/**
	* Checking extra moderator permissions
	*/
	protected function check_mcp_auth()
	{
		if (!$this->auth->acl_getf_global('m_'))
		{
			// Except he is using one of the quickmod tools for users
			$user_quickmod_actions = [
				'lock'			=> 'f_user_lock',
				'make_sticky'	=> 'f_sticky',
				'make_announce'	=> 'f_announce',
				'make_global'	=> 'f_announce_global',
				'make_normal'	=> ['f_announce', 'f_announce_global', 'f_sticky']
			];

			$allow_user = false;

			if ($this->quickmod && isset($user_quickmod_actions[$this->action]) && $this->user->data['is_registered'] && $this->auth->acl_gets($user_quickmod_actions[$this->action], $this->forum_id))
			{
				$topic_info = phpbb_get_topic_data([$this->topic_id]);

				if ($topic_info[$this->topic_id]['topic_poster'] == $this->user->data['user_id'])
				{
					$allow_user = true;
				}
			}

			if (!$allow_user)
			{
				send_status_line(403, 'Forbidden');
				trigger_error('NOT_AUTHORISED');
			}
		}
	}

	/**
	* Checking extra moderator permissions for the forum
	*/
	protected function check_mcp_forum_auth()
	{
		if ($this->forum_id && !$this->auth->acl_get('f_read', $this->forum_id))
		{
			send_status_line(403, 'Forbidden');
			trigger_error('NOT_AUTHORISED');
		}

		if ($this->forum_id)
		{
			$this->module->acl_forum_id = $this->forum_id;
		}
	}

	/**
	* Sub-method for the main() with quickmod=1;action=lock|unlock|lock_post|unlock_post|make_sticky|make_announce|make_global|make_normal|fork|move|delete_post|delete_topic|restore_topic
	*/
	protected function quickmod()
	{
		$this->module->load('mcp', 'main', 'quickmod');
	}

	/**
	* Sub-method for the main() with quickmod=1;action=topic_logs
	*/
	protected function quickmod_topic_logs()
	{
		// Reset start parameter if we jumped from the quickmod dropdown
		if ($this->request->variable('start', 0))
		{
			$this->request->overwrite('start', 0);
		}

		$this->module->set_active('logs', 'topic_logs');
	}

	/**
	* Sub-method for the main() with quickmod=1;action=merge_topic
	*/
	protected function quickmod_merge_topic()
	{
		$this->module->set_active('main', 'forum_view');
	}

	/**
	* Sub-method for the main() with quickmod=1;action=split|merge
	*/
	protected function quickmod_split_merge()
	{
		$this->module->set_active('main', 'topic_view');
	}

	/**
	* Hide MCP modules
	*/
	protected function hide_mcp_modules()
	{
		if (in_array($this->mode, ['', 'unapproved_topics', 'unapproved_posts', 'deleted_topics' , 'deleted_posts']))
		{
			$this->module->set_display('queue', 'approve_details', false);
		}

		if (in_array($this->mode, ['', 'reports', 'reports_closed', 'pm_reports' , 'pm_reports_closed', 'pm_report_details']))
		{
			$this->module->set_display('reports', 'report_details', false);
		}

		if (in_array($this->mode, ['', 'reports', 'reports_closed', 'pm_reports' , 'pm_reports_closed', 'report_details']))
		{
			$this->module->set_display('pm_reports', 'pm_report_details', false);
		}

		if (!$this->forum_id)
		{
			$this->module->set_display('main', 'forum_view', false);
			$this->module->set_display('logs', 'forum_logs', false);
		}

		if (!$this->topic_id)
		{
			$this->module->set_display('main', 'topic_view', false);
			$this->module->set_display('logs', 'topic_logs', false);
		}

		if (!$this->post_id)
		{
			$this->module->set_display('main', 'post_details', false);
			$this->module->set_display('warn', 'warn_post', false);
		}

		if (!$this->user_id && $this->username == '')
		{
			$this->module->set_display('notes', 'user_notes', false);
			$this->module->set_display('warn', 'warn_user', false);
		}
	}

	/**
	* Generate template variables
	*/
	protected function output_template()
	{
		$this->template->assign_vars([
			'U_MCP_FORUM'	=> ($this->forum_id) ? $this->helper->route('vinabb_web_mcp_route', ['id' => 'main', 'mode' => 'forum_view', 'f' => $this->forum_id]) : '',
			'U_MCP_TOPIC'	=> ($this->forum_id && $this->topic_id) ? $this->helper->route('vinabb_web_mcp_route', ['id' => 'main', 'mode' => 'topic_view', 't' => $this->topic_id]) : '',
			'U_MCP_POST'	=> ($this->forum_id && $this->topic_id && $this->post_id) ? $this->helper->route('vinabb_web_mcp_route', ['id' => 'main', 'mode' => 'post_details', 't' => $this->topic_id, 'p' => $this->post_id]) : '',

			'S_IN_MCP'	=> true
		]);
	}
}
