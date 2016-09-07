<?php
/**
 * This file is part of the VinaBB.vn package.
 *
 * @copyright (c) VinaBB <vinabb.vn>
 * @license GNU General Public License, version 2 (GPL-2.0)
 */

namespace vinabb\web\controller;

class board
{
	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\notification\manager */
	protected $notifications;

	/** @var \phpbb\event\dispatcher_interface */
	protected $dispatcher;

	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var \phpbb\group\helper $group_helper */
	protected $group_helper;

	/** @var string */
	protected $phpbb_root_path;

	/** @var string */
	protected $php_ext;

	/**
	* Constructor
	*
	* @param \phpbb\auth\auth $auth
	* @param \phpbb\db\driver\driver_interface $db
	* @param \phpbb\config\config $config
	* @param \phpbb\user $user
	* @param \phpbb\language\language $language
	* @param \phpbb\template\template $template
	* @param \phpbb\request\request $request
	* @param \phpbb\notification\manager $notifications
	* @param \phpbb\event\dispatcher_interface $dispatcher
	* @param \phpbb\controller\helper $helper
	* @param \phpbb\group\helper $group_helper
	* @param string $phpbb_root_path
	* @param string $php_ext
	*/
	public function __construct(
		\phpbb\auth\auth $auth,
		\phpbb\db\driver\driver_interface $db,
		\phpbb\config\config $config,
		\phpbb\user $user,
		\phpbb\language\language $language,
		\phpbb\template\template $template,
		\phpbb\request\request $request,
		\phpbb\notification\manager $notification,
		\phpbb\event\dispatcher_interface $dispatcher,
		\phpbb\controller\helper $helper,
		\phpbb\group\helper $group_helper,
		$phpbb_root_path,
		$php_ext
	)
	{
		$this->auth = $auth;
		$this->db = $db;
		$this->config = $config;
		$this->user = $user;
		$this->language = $language;
		$this->template = $template;
		$this->request = $request;
		$this->notification = $notification;
		$this->dispatcher = $dispatcher;
		$this->helper = $helper;
		$this->group_helper = $group_helper;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->php_ext = $php_ext;
	}

	/**
	* @return \Symfony\Component\HttpFoundation\Response
	*/
	public function index($board)
	{
		// 'board' or 'board/'
		$board = 'board';

		include "{$this->phpbb_root_path}includes/functions_display.{$this->php_ext}";

		// Mark notifications read
		if (($mark_notification = $this->request->variable('mark_notification', 0)))
		{
			if ($this->user->data['user_id'] == ANONYMOUS)
			{
				if ($this->request->is_ajax())
				{
					trigger_error('LOGIN_REQUIRED');
				}
				
				login_box('', $this->language->lang('LOGIN_REQUIRED'));
			}

			if (check_link_hash($this->request->variable('hash', ''), 'mark_notification_read'))
			{
				$notification = $this->notifications->load_notifications('notification.method.board', array(
					'notification_id'	=> $mark_notification,
				));

				if (isset($notification['notifications'][$mark_notification]))
				{
					$notification = $notification['notifications'][$mark_notification];

					$notification->mark_read();

					if ($this->request->is_ajax())
					{
						$json_response = new \phpbb\json_response();
						$json_response->send(array(
							'success'	=> true,
						));
					}

					if (($redirect = $this->request->variable('redirect', '')))
					{
						redirect(append_sid($this->phpbb_root_path . $redirect));
					}

					redirect($notification->get_redirect_url());
				}
			}
		}

		display_forums('', $this->config['load_moderators']);

		$order_legend = ($this->config['legend_sort_groupname']) ? 'group_name' : 'group_legend';

		// Grab group details for legend display
		if ($this->auth->acl_gets('a_group', 'a_groupadd', 'a_groupdel'))
		{
			$sql = 'SELECT group_id, group_name, group_colour, group_type, group_legend
				FROM ' . GROUPS_TABLE . '
				WHERE group_legend > 0
				ORDER BY ' . $order_legend . ' ASC';
		}
		else
		{
			$sql = 'SELECT g.group_id, g.group_name, g.group_colour, g.group_type, g.group_legend
				FROM ' . GROUPS_TABLE . ' g
				LEFT JOIN ' . USER_GROUP_TABLE . ' ug
					ON (
						g.group_id = ug.group_id
						AND ug.user_id = ' . $this->user->data['user_id'] . '
						AND ug.user_pending = 0
					)
				WHERE g.group_legend > 0
					AND (g.group_type <> ' . GROUP_HIDDEN . ' OR ug.user_id = ' . $this->user->data['user_id'] . ')
				ORDER BY g.' . $order_legend . ' ASC';
		}
		$result = $this->db->sql_query($sql);

		$legend = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$colour_text = ($row['group_colour']) ? ' style="color: #' . $row['group_colour'] . '"' : '';
			$group_name = $this->group_helper->get_name($row['group_name']);

			if ($row['group_name'] == 'BOTS' || ($this->user->data['user_id'] != ANONYMOUS && !$this->auth->acl_get('u_viewprofile')))
			{
				$legend[] = '<span' . $colour_text . '>' . $group_name . '</span>';
			}
			else
			{
				$legend[] = '<a' . $colour_text . ' href="' . append_sid("{$this->phpbb_root_path}memberlist.{$this->php_ext}", 'mode=group&g=' . $row['group_id']) . '">' . $group_name . '</a>';
			}
		}
		$this->db->sql_freeresult($result);

		$legend = implode($this->language->lang('COMMA_SEPARATOR'), $legend);

		// Assign index specific vars
		$this->template->assign_vars(array(
				'TOTAL_POSTS'	=> $this->language->lang('TOTAL_POSTS_COUNT', (int) $this->config['num_posts']),
				'TOTAL_TOPICS'	=> $this->language->lang('TOTAL_TOPICS', (int) $this->config['num_topics']),
				'TOTAL_USERS'	=> $this->language->lang('TOTAL_USERS', (int) $this->config['num_users']),
				'NEWEST_USER'	=> $this->language->lang('NEWEST_USER', get_username_string('full', $this->config['newest_user_id'], $this->config['newest_username'], $this->config['newest_user_colour'])),

				'LEGEND'		=> $legend,
				'BIRTHDAY_LIST'	=> (empty($birthday_list)) ? '' : implode($this->language->lang('COMMA_SEPARATOR'), $birthday_list),

				'FORUM_IMG'					=> $this->user->img('forum_read', 'NO_UNREAD_POSTS'),
				'FORUM_UNREAD_IMG'			=> $this->user->img('forum_unread', 'UNREAD_POSTS'),
				'FORUM_LOCKED_IMG'			=> $this->user->img('forum_read_locked', 'NO_UNREAD_POSTS_LOCKED'),
				'FORUM_UNREAD_LOCKED_IMG'	=> $this->user->img('forum_unread_locked', 'UNREAD_POSTS_LOCKED'),

				'S_LOGIN_ACTION'	=> append_sid("{$this->phpbb_root_path}ucp.{$this->php_ext}", 'mode=login'),
				'U_SEND_PASSWORD'	=> ($this->config['email_enable']) ? append_sid("{$this->phpbb_root_path}ucp.{$this->php_ext}", 'mode=sendpassword') : '',
				'S_INDEX'			=> true,

				'U_MARK_FORUMS'		=> ($this->user->data['is_registered'] || $this->config['load_anon_lastread']) ? $this->helper->route('vinabb_web_board_route', array('board' => $board, 'hash' => generate_link_hash('global'), 'mark' => 'forums', 'mark_time' => time())) : '',
				'U_MCP'				=> ($this->auth->acl_get('m_') || $this->auth->acl_getf_global('m_')) ? append_sid("{$this->phpbb_root_path}mcp.{$this->php_ext}", 'i=main&mode=front', true, $this->user->session_id) : '')
		);

		$page_title = $this->language->lang('BOARD');

		/**
		* You can use this event to modify the page title and load data for the index
		*
		* @event vinabb.web.index_modify_page_title
		* @var	string	page_title		Title of the index page
		* @since 3.1.0-a1
		*/
		$vars = array('page_title');
		extract($this->dispatcher->trigger_event('vinabb.web.index_modify_page_title', compact($vars)));

		return $this->helper->render('index_body.html', $page_title);
	}
}
