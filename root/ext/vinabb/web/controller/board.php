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

	/** @var \phpbb\content_visibility */
	protected $content_visibility;

	/** @var \phpbb\cache\service */
	protected $cache_service;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\pagination */
	protected $pagination;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\notification\manager */
	protected $notifications;

	/** @var \phpbb\cron\manager */
	protected $cron;

	/** @var \phpbb\event\dispatcher_interface */
	protected $dispatcher;

	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var \phpbb\group\helper */
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
	* @param \phpbb\content_visibility $content_visibility
	* @param \phpbb\cache\service $cache_service
	* @param \phpbb\config\config $config
	* @param \phpbb\user $user
	* @param \phpbb\language\language $language
	* @param \phpbb\template\template $template
	* @param \phpbb\pagination $pagination
	* @param \phpbb\request\request $request
	* @param \phpbb\notification\manager $notification
	* @param \phpbb\cron\manager $cron
	* @param \phpbb\event\dispatcher_interface $dispatcher
	* @param \phpbb\controller\helper $helper
	* @param \phpbb\group\helper $group_helper
	* @param string $phpbb_root_path
	* @param string $php_ext
	*/
	public function __construct(
		\phpbb\auth\auth $auth,
		\phpbb\db\driver\driver_interface $db,
		\phpbb\content_visibility $content_visibility,
		\phpbb\cache\service $cache_service,
		\phpbb\config\config $config,
		\phpbb\user $user,
		\phpbb\language\language $language,
		\phpbb\template\template $template,
		\phpbb\pagination $pagination,
		\phpbb\request\request $request,
		\phpbb\notification\manager $notification,
		\phpbb\cron\manager $cron,
		\phpbb\event\dispatcher_interface $dispatcher,
		\phpbb\controller\helper $helper,
		\phpbb\group\helper $group_helper,
		$phpbb_root_path,
		$php_ext
	)
	{
		$this->auth = $auth;
		$this->db = $db;
		$this->content_visibility = $content_visibility;
		$this->cache_service = $cache_service;
		$this->config = $config;
		$this->user = $user;
		$this->language = $language;
		$this->template = $template;
		$this->pagination = $pagination;
		$this->request = $request;
		$this->notification = $notification;
		$this->cron = $cron;
		$this->dispatcher = $dispatcher;
		$this->helper = $helper;
		$this->group_helper = $group_helper;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->php_ext = $php_ext;

		// Common functions
		include "{$this->phpbb_root_path}includes/functions_display.{$this->php_ext}";
	}

	/**
	* @return \Symfony\Component\HttpFoundation\Response
	*/
	public function index($board)
	{
		// 'board' or 'board/'
		$board = 'board';

		$this->language->add_lang('viewforum');

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
				$legend[] = '<a' . $colour_text . ' href="' . $this->helper->route('vinabb_web_user_group_route', array('id' => $row['group_id'])) . '">' . $group_name . '</a>';
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

				'S_LOGIN_ACTION'	=> $this->helper->route('vinabb_web_ucp_route', array('id' => 'front', 'mode' => 'login')),
				'U_SEND_PASSWORD'	=> ($this->config['email_enable']) ? $this->helper->route('vinabb_web_ucp_route', array('id' => 'front', 'mode' => 'sendpassword')) : '',
				'S_INDEX'			=> true,

				'U_MARK_FORUMS'		=> ($this->user->data['is_registered'] || $this->config['load_anon_lastread']) ? $this->helper->route('vinabb_web_board_route', array('board' => $board, 'hash' => generate_link_hash('global'), 'mark' => 'forums', 'mark_time' => time())) : '',
				'U_MCP'				=> ($this->auth->acl_get('m_') || $this->auth->acl_getf_global('m_')) ? $this->helper->route('vinabb_web_mcp_route', array('id' => 'main', 'mode' => 'front'), true, $this->user->session_id) : ''
		));

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

	public function forum($forum_id)
	{
		global $_SID, $_EXTRA_URL;

		// Start initial var setup
		$mark_read = $this->request->variable('mark', '');
		$start = $this->request->variable('start', 0);

		$default_sort_days = (!empty($this->user->data['user_topic_show_days'])) ? $this->user->data['user_topic_show_days'] : 0;
		$default_sort_key = (!empty($this->user->data['user_topic_sortby_type'])) ? $this->user->data['user_topic_sortby_type'] : 't';
		$default_sort_dir = (!empty($this->user->data['user_topic_sortby_dir'])) ? $this->user->data['user_topic_sortby_dir'] : 'd';

		$sort_days = $this->request->variable('st', $default_sort_days);
		$sort_key = $this->request->variable('sk', $default_sort_key);
		$sort_dir = $this->request->variable('sd', $default_sort_dir);

		// Check if the user has actually sent a forum ID with his/her request
		// If not give them a nice error page.
		if (!$forum_id)
		{
			trigger_error('NO_FORUM');
		}

		$sql_from = FORUMS_TABLE . ' f';
		$lastread_select = '';

		// Grab appropriate forum data
		if ($this->config['load_db_lastread'] && $this->user->data['is_registered'])
		{
			$sql_from .= ' LEFT JOIN ' . FORUMS_TRACK_TABLE . ' ft ON (ft.user_id = ' . $this->user->data['user_id'] . '
				AND ft.forum_id = f.forum_id)';
			$lastread_select .= ', ft.mark_time';
		}

		if ($this->user->data['is_registered'])
		{
			$sql_from .= ' LEFT JOIN ' . FORUMS_WATCH_TABLE . ' fw ON (fw.forum_id = f.forum_id AND fw.user_id = ' . $this->user->data['user_id'] . ')';
			$lastread_select .= ', fw.notify_status';
		}

		$sql = "SELECT f.* $lastread_select
			FROM $sql_from
			WHERE f.forum_id = $forum_id";
		$result = $this->db->sql_query($sql);
		$forum_data = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if (!$forum_data)
		{
			trigger_error('NO_FORUM');
		}


		// Configure style, language, etc.
		$this->user->setup('viewforum', $forum_data['forum_style']);

		// Redirect to login upon emailed notification links
		if (isset($_GET['e']) && !$this->user->data['is_registered'])
		{
			login_box('', $this->language->lang('LOGIN_NOTIFY_FORUM'));
		}

		// Permissions check
		if (!$this->auth->acl_gets('f_list', 'f_read', $forum_id) || ($forum_data['forum_type'] == FORUM_LINK && $forum_data['forum_link'] && !$this->auth->acl_get('f_read', $forum_id)))
		{
			if ($this->user->data['user_id'] != ANONYMOUS)
			{
				trigger_error('SORRY_AUTH_READ');
			}

			login_box('', $this->language->lang('LOGIN_VIEWFORUM'));
		}

		// Forum is passworded ... check whether access has been granted to this
		// user this session, if not show login box
		if ($forum_data['forum_password'])
		{
			login_forum_box($forum_data);
		}

		// Is this forum a link? ... User got here either because the
		// number of clicks is being tracked or they guessed the id
		if ($forum_data['forum_type'] == FORUM_LINK && $forum_data['forum_link'])
		{
			// Does it have click tracking enabled?
			if ($forum_data['forum_flags'] & FORUM_FLAG_LINK_TRACK)
			{
				$sql = 'UPDATE ' . FORUMS_TABLE . '
					SET forum_posts_approved = forum_posts_approved + 1
					WHERE forum_id = ' . $forum_id;
				$this->db->sql_query($sql);
			}

			// We redirect to the url. The third parameter indicates that external redirects are allowed.
			redirect($forum_data['forum_link'], false, true);
			return;
		}

		// Build navigation links
		generate_forum_nav($forum_data);

		// Forum Rules
		if ($this->auth->acl_get('f_read', $forum_id))
		{
			generate_forum_rules($forum_data);
		}

		// Do we have subforums?
		$active_forum_ary = $moderators = array();

		if ($forum_data['left_id'] != $forum_data['right_id'] - 1)
		{
			list($active_forum_ary, $moderators) = display_forums($forum_data, $this->config['load_moderators'], $this->config['load_moderators']);
		}
		else
		{
			$this->template->assign_var('S_HAS_SUBFORUM', false);

			if ($this->config['load_moderators'])
			{
				get_moderators($moderators, $forum_id);
			}
		}

		// Dump out the page header and load viewforum template
		$topics_count = $this->content_visibility->get_count('forum_topics', $forum_data, $forum_id);
		$start = $this->pagination->validate_start($start, $this->config['topics_per_page'], $topics_count);

		page_header($forum_data['forum_name'] . ($start ? ' - ' . $this->language->lang('PAGE_TITLE_NUMBER', $this->pagination->get_on_page($this->config['topics_per_page'], $start)) : ''), true, $forum_id);

		$this->template->set_filenames(array(
			'body'	=> 'viewforum_body.html'
		));

		$this->template->assign_vars(array(
			'U_VIEW_FORUM'	=> $this->helper->route('vinabb_web_board_forum_route', ($start == 0) ? array('forum_id' => $forum_id) : array('forum_id' => $forum_id, 'start' => $start))
		));

		// Not postable forum or showing active topics?
		if (!($forum_data['forum_type'] == FORUM_POST || (($forum_data['forum_flags'] & FORUM_FLAG_ACTIVE_TOPICS) && $forum_data['forum_type'] == FORUM_CAT)))
		{
			page_footer();
		}

		// Ok, if someone has only list-access, we only display the forum list.
		// We also make this circumstance available to the template in case we want to display a notice. ;)
		if (!$this->auth->acl_get('f_read', $forum_id))
		{
			$this->template->assign_vars(array(
				'S_NO_READ_ACCESS'		=> true
			));

			page_footer();
		}

		// Handle marking posts
		if ($mark_read == 'topics')
		{
			$token = $this->request->variable('hash', '');

			if (check_link_hash($token, 'global'))
			{
				markread('topics', array($forum_id), false, $this->request->variable('mark_time', 0));
			}

			$redirect_url = $this->helper->route('vinabb_web_board_forum_route', array('forum_id' => $forum_id));
			meta_refresh(3, $redirect_url);

			if ($this->request->is_ajax())
			{
				// Tell the ajax script what language vars and URL need to be replaced
				$data = array(
					'NO_UNREAD_POSTS'	=> $this->language->lang('NO_UNREAD_POSTS'),
					'UNREAD_POSTS'		=> $this->language->lang('UNREAD_POSTS'),
					'U_MARK_TOPICS'		=> ($this->user->data['is_registered'] || $this->config['load_anon_lastread']) ? htmlspecialchars_decode($this->helper->route('vinabb_web_board_forum_route', array('forum_id' => $forum_id, 'hash' => generate_link_hash('global'), 'mark' => 'topics', 'mark_time' => time()))) : '',
					'MESSAGE_TITLE'		=> $this->language->lang('INFORMATION'),
					'MESSAGE_TEXT'		=> $this->language->lang('TOPICS_MARKED')
				);
				$json_response = new \phpbb\json_response();
				$json_response->send($data);
			}

			trigger_error($this->language->lang('TOPICS_MARKED') . '<br><br>' . sprintf($this->language->lang('RETURN_FORUM'), '<a href="' . $redirect_url . '">', '</a>'));
		}

		// Is a forum specific topic count required?
		if ($forum_data['forum_topics_per_page'])
		{
			$this->config['topics_per_page'] = $forum_data['forum_topics_per_page'];
		}

		// Do the forum Prune thang - cron type job...
		if (!$this->config['use_system_cron'])
		{
			$task = $this->cron->find_task('cron.task.core.prune_forum');
			$task->set_forum_data($forum_data);

			if ($task->is_ready())
			{
				$url = $task->get_url();
				$this->template->assign_var('RUN_CRON_TASK', '<img src="' . $url . '" width="1" height="1" alt="cron">');
			}
			else
			{
				// See if we should prune the shadow topics instead
				$task = $this->cron->find_task('cron.task.core.prune_shadow_topics');
				$task->set_forum_data($forum_data);

				if ($task->is_ready())
				{
					$url = $task->get_url();
					$this->template->assign_var('RUN_CRON_TASK', '<img src="' . $url . '" width="1" height="1" alt="cron">');
				}
			}
		}

		// Forum rules and subscription info
		$s_watching_forum = array(
			'link'			=> '',
			'link_toggle'	=> '',
			'title'			=> '',
			'title_toggle'	=> '',
			'is_watching'	=> false,
		);

		if ($this->config['allow_forum_notify'] && $forum_data['forum_type'] == FORUM_POST && ($this->auth->acl_get('f_subscribe', $forum_id) || $this->user->data['user_id'] == ANONYMOUS))
		{
			$notify_status = (isset($forum_data['notify_status'])) ? $forum_data['notify_status'] : NULL;
			watch_topic_forum('forum', $s_watching_forum, $this->user->data['user_id'], $forum_id, 0, $notify_status, $start, $forum_data['forum_name']);
		}

		$s_forum_rules = '';
		gen_forum_auth_level('forum', $forum_id, $forum_data['forum_status']);

		// Topic ordering options
		$limit_days = array(
			0	=> $this->language->lang('ALL_TOPICS'),
			1	=> $this->language->lang('1_DAY'),
			7	=> $this->language->lang('7_DAYS'),
			14	=> $this->language->lang('2_WEEKS'),
			30	=> $this->language->lang('1_MONTH'),
			90	=> $this->language->lang('3_MONTHS'),
			180	=> $this->language->lang('6_MONTHS'),
			365	=> $this->language->lang('1_YEAR')
		);
		$sort_by_text = array(
			'a'	=> $this->language->lang('AUTHOR'),
			't'	=> $this->language->lang('POST_TIME'),
			'r'	=> $this->language->lang('REPLIES'),
			's'	=> $this->language->lang('SUBJECT'),
			'v'	=> $this->language->lang('VIEWS')
		);
		$sort_by_sql = array(
			'a'	=> 't.topic_first_poster_name',
			't'	=> array('t.topic_last_post_time', 't.topic_last_post_id'),
			'r'	=> (($this->auth->acl_get('m_approve', $forum_id)) ? 't.topic_posts_approved + t.topic_posts_unapproved + t.topic_posts_softdeleted' : 't.topic_posts_approved'),
			's'	=> $this->db->sql_lower_text('t.topic_title'),
			'v'	=> 't.topic_views'
		);
		$s_limit_days = $s_sort_key = $s_sort_dir = $u_sort_param = '';

		gen_sort_selects($limit_days, $sort_by_text, $sort_days, $sort_key, $sort_dir, $s_limit_days, $s_sort_key, $s_sort_dir, $u_sort_param, $default_sort_days, $default_sort_key, $default_sort_dir);

		// Convert $u_sort_param from string to array
		$u_sort_param_ary = array();
		if (!empty($u_sort_param))
		{
			$u_sort_param = htmlspecialchars_decode($u_sort_param);
			$u_sort_param_raw_ary = explode('&', $u_sort_param);

			foreach ($u_sort_param_raw_ary as $u_sort_param_raw)
			{
				list($u_sort_param_raw_key, $u_sort_param_raw_value) = explode('=', $u_sort_param_raw);
				$u_sort_param_ary[$u_sort_param_raw_key] = $u_sort_param_raw_value;
			}
		}

		// Limit topics to certain time frame, obtain correct topic count
		if ($sort_days)
		{
			$min_post_time = time() - ($sort_days * 86400);

			$sql_array = array(
				'SELECT'	=> 'COUNT(t.topic_id) AS num_topics',
				'FROM'		=> array(
					TOPICS_TABLE	=> 't',
				),
				'WHERE'		=> 't.forum_id = ' . $forum_id . '
					AND (t.topic_last_post_time >= ' . $min_post_time . '
						OR t.topic_type = ' . POST_ANNOUNCE . '
						OR t.topic_type = ' . POST_GLOBAL . ')
					AND ' . $this->content_visibility->get_visibility_sql('topic', $forum_id, 't.'),
			);

			/**
			* Modify the sort data SQL query for getting additional fields if needed
			*
			* @event vinabb.web.viewforum_modify_sort_data_sql
			* @var int		forum_id		The forum_id whose topics are being listed
			* @var int		start			Variable containing start for pagination
			* @var int		sort_days		The oldest topic displayable in elapsed days
			* @var string	sort_key		The sorting by. It is one of the first character of (in low case):
			*								Author, Post time, Replies, Subject, Views
			* @var string	sort_dir		Either "a" for ascending or "d" for descending
			* @var array	sql_array		The SQL array to get the data of all topics
			* @since 3.1.9-RC1
			*/
			$vars = array(
				'forum_id',
				'start',
				'sort_days',
				'sort_key',
				'sort_dir',
				'sql_array',
			);
			extract($this->dispatcher->trigger_event('vinabb.web.viewforum_modify_sort_data_sql', compact($vars)));

			$result = $this->db->sql_query($this->db->sql_build_query('SELECT', $sql_array));
			$topics_count = (int) $this->db->sql_fetchfield('num_topics');
			$this->db->sql_freeresult($result);

			if ($this->request->is_set_post('sort'))
			{
				$start = 0;
			}

			$sql_limit_time = "AND t.topic_last_post_time >= $min_post_time";

			// Make sure we have information about day selection ready
			$this->template->assign_var('S_SORT_DAYS', true);
		}
		else
		{
			$sql_limit_time = '';
		}

		// Basic pagewide vars
		$post_alt = ($forum_data['forum_status'] == ITEM_LOCKED) ? $this->language->lang('FORUM_LOCKED') : $this->language->lang('POST_NEW_TOPIC');

		// Display active topics?
		$s_display_active = ($forum_data['forum_type'] == FORUM_CAT && ($forum_data['forum_flags'] & FORUM_FLAG_ACTIVE_TOPICS)) ? true : false;

		$s_search_hidden_fields = array('fid' => array($forum_id));
		if (!empty($_SID))
		{
			$s_search_hidden_fields['sid'] = $_SID;
		}

		if (!empty($_EXTRA_URL))
		{
			foreach ($_EXTRA_URL as $url_param)
			{
				$url_param = explode('=', $url_param, 2);
				$s_search_hidden_fields[$url_param[0]] = $url_param[1];
			}
		}

		// Build forum URL with parameters
		$forum_url_params = array('forum_id' => $forum_id);

		if ($start)
		{
			$forum_url_params['start'] = $start;
		}

		$forum_url_sort_params = $forum_url_params;

		if (sizeof($u_sort_param_ary))
		{
			$forum_url_sort_params = array_merge($forum_url_sort_params, $u_sort_param_ary);
		}

		$this->template->assign_vars(array(
			'MODERATORS'	=> (!empty($moderators[$forum_id])) ? implode($this->language->lang('COMMA_SEPARATOR'), $moderators[$forum_id]) : '',

			'POST_IMG'					=> ($forum_data['forum_status'] == ITEM_LOCKED) ? $this->user->img('button_topic_locked', $post_alt) : $this->user->img('button_topic_new', $post_alt),
			'NEWEST_POST_IMG'			=> $this->user->img('icon_topic_newest', 'VIEW_NEWEST_POST'),
			'LAST_POST_IMG'				=> $this->user->img('icon_topic_latest', 'VIEW_LATEST_POST'),
			'FOLDER_IMG'				=> $this->user->img('topic_read', 'NO_UNREAD_POSTS'),
			'FOLDER_UNREAD_IMG'			=> $this->user->img('topic_unread', 'UNREAD_POSTS'),
			'FOLDER_HOT_IMG'			=> $this->user->img('topic_read_hot', 'NO_UNREAD_POSTS_HOT'),
			'FOLDER_HOT_UNREAD_IMG'		=> $this->user->img('topic_unread_hot', 'UNREAD_POSTS_HOT'),
			'FOLDER_LOCKED_IMG'			=> $this->user->img('topic_read_locked', 'NO_UNREAD_POSTS_LOCKED'),
			'FOLDER_LOCKED_UNREAD_IMG'	=> $this->user->img('topic_unread_locked', 'UNREAD_POSTS_LOCKED'),
			'FOLDER_STICKY_IMG'			=> $this->user->img('sticky_read', 'POST_STICKY'),
			'FOLDER_STICKY_UNREAD_IMG'	=> $this->user->img('sticky_unread', 'POST_STICKY'),
			'FOLDER_ANNOUNCE_IMG'		=> $this->user->img('announce_read', 'POST_ANNOUNCEMENT'),
			'FOLDER_ANNOUNCE_UNREAD_IMG'=> $this->user->img('announce_unread', 'POST_ANNOUNCEMENT'),
			'FOLDER_MOVED_IMG'			=> $this->user->img('topic_moved', 'TOPIC_MOVED'),
			'REPORTED_IMG'				=> $this->user->img('icon_topic_reported', 'TOPIC_REPORTED'),
			'UNAPPROVED_IMG'			=> $this->user->img('icon_topic_unapproved', 'TOPIC_UNAPPROVED'),
			'DELETED_IMG'				=> $this->user->img('icon_topic_deleted', 'TOPIC_DELETED'),
			'POLL_IMG'					=> $this->user->img('icon_topic_poll', 'TOPIC_POLL'),
			'GOTO_PAGE_IMG'				=> $this->user->img('icon_post_target', 'GOTO_PAGE'),

			'L_NO_TOPICS' 			=> ($forum_data['forum_status'] == ITEM_LOCKED) ? $this->language->lang('POST_FORUM_LOCKED') : $this->language->lang('NO_TOPICS'),

			'S_DISPLAY_POST_INFO'	=> ($forum_data['forum_type'] == FORUM_POST && ($this->auth->acl_get('f_post', $forum_id) || $this->user->data['user_id'] == ANONYMOUS)) ? true : false,

			'S_IS_POSTABLE'					=> ($forum_data['forum_type'] == FORUM_POST) ? true : false,
			'S_USER_CAN_POST'				=> ($this->auth->acl_get('f_post', $forum_id)) ? true : false,
			'S_DISPLAY_ACTIVE'				=> $s_display_active,
			'S_SELECT_SORT_DIR'				=> $s_sort_dir,
			'S_SELECT_SORT_KEY'				=> $s_sort_key,
			'S_SELECT_SORT_DAYS'			=> $s_limit_days,
			'S_TOPIC_ICONS'					=> ($s_display_active && sizeof($active_forum_ary)) ? max($active_forum_ary['enable_icons']) : (($forum_data['enable_icons']) ? true : false),
			'U_WATCH_FORUM_LINK'			=> $s_watching_forum['link'],
			'U_WATCH_FORUM_TOGGLE'			=> $s_watching_forum['link_toggle'],
			'S_WATCH_FORUM_TITLE'			=> $s_watching_forum['title'],
			'S_WATCH_FORUM_TOGGLE'			=> $s_watching_forum['title_toggle'],
			'S_WATCHING_FORUM'				=> $s_watching_forum['is_watching'],
			'S_FORUM_ACTION'				=> $this->helper->route('vinabb_web_board_forum_route', $forum_url_params),
			'S_DISPLAY_SEARCHBOX'			=> ($this->auth->acl_get('u_search') && $this->auth->acl_get('f_search', $forum_id) && $this->config['load_search']) ? true : false,
			'S_SEARCHBOX_ACTION'			=> $this->helper->route('vinabb_web_search_route'),
			'S_SEARCH_LOCAL_HIDDEN_FIELDS'	=> build_hidden_fields($s_search_hidden_fields),
			'S_SINGLE_MODERATOR'			=> (!empty($moderators[$forum_id]) && sizeof($moderators[$forum_id]) > 1) ? false : true,
			'S_IS_LOCKED'					=> ($forum_data['forum_status'] == ITEM_LOCKED) ? true : false,
			'S_VIEWFORUM'					=> true,

			'U_MCP'				=> ($this->auth->acl_get('m_', $forum_id)) ? $this->helper->route('vinabb_web_mcp_route', array('id' => 'main', 'mode' => 'forum_view', 'f' => $forum_id), true, $this->user->session_id) : '',
			'U_POST_NEW_TOPIC'	=> ($this->auth->acl_get('f_post', $forum_id) || $this->user->data['user_id'] == ANONYMOUS) ? $this->helper->route('vinabb_web_posting_route', array('mode' => 'post', 'forum_id' => $forum_id)) : '',
			'U_VIEW_FORUM'		=> $this->helper->route('vinabb_web_board_forum_route', $forum_url_sort_params),
			'U_CANONICAL'		=> generate_board_url(true) . htmlspecialchars_decode($this->helper->route('vinabb_web_board_forum_route', $forum_url_params)),
			'U_MARK_TOPICS'		=> ($this->user->data['is_registered'] || $this->config['load_anon_lastread']) ? $this->helper->route('vinabb_web_board_forum_route', array('forum_id' => $forum_id, 'hash' => generate_link_hash('global'), 'mark' => 'topics', 'mark_time' => time())) : '',
		));

		// Grab icons
		$icons = $this->cache_service->obtain_icons();

		// Grab all topic data
		$rowset = $announcement_list = $topic_list = $global_announce_forums = array();

		$sql_array = array(
			'SELECT'	=> 't.*',
			'FROM'		=> array(
				TOPICS_TABLE		=> 't'
			),
			'LEFT_JOIN'	=> array(),
		);

		/**
		* Event to modify the SQL query before the topic data is retrieved
		*
		* It may also be used to override the above assigned template vars
		*
		* @event vinabb.web.viewforum_get_topic_data
		* @var	array	forum_data			Array with forum data
		* @var	array	sql_array			The SQL array to get the data of all topics
		* @var	int		forum_id			The forum_id whose topics are being listed
		* @var	int		topics_count		The total number of topics for display
		* @var	int		sort_days			The oldest topic displayable in elapsed days
		* @var	string	sort_key			The sorting by. It is one of the first character of (in low case):
		*									Author, Post time, Replies, Subject, Views
		* @var	string	sort_dir			Either "a" for ascending or "d" for descending
		* @since 3.1.0-a1
		* @change 3.1.0-RC4 Added forum_data var
		* @change 3.1.4-RC1 Added forum_id, topics_count, sort_days, sort_key and sort_dir vars
		* @change 3.1.9-RC1 Fix types of properties
		*/
		$vars = array(
			'forum_data',
			'sql_array',
			'forum_id',
			'topics_count',
			'sort_days',
			'sort_key',
			'sort_dir',
		);
		extract($this->dispatcher->trigger_event('vinabb.web.viewforum_get_topic_data', compact($vars)));

		$sql_approved = ' AND ' . $this->content_visibility->get_visibility_sql('topic', $forum_id, 't.');

		if ($this->user->data['is_registered'])
		{
			if ($this->config['load_db_track'])
			{
				$sql_array['LEFT_JOIN'][] = array('FROM' => array(TOPICS_POSTED_TABLE => 'tp'), 'ON' => 'tp.topic_id = t.topic_id AND tp.user_id = ' . $this->user->data['user_id']);
				$sql_array['SELECT'] .= ', tp.topic_posted';
			}

			if ($this->config['load_db_lastread'])
			{
				$sql_array['LEFT_JOIN'][] = array('FROM' => array(TOPICS_TRACK_TABLE => 'tt'), 'ON' => 'tt.topic_id = t.topic_id AND tt.user_id = ' . $this->user->data['user_id']);
				$sql_array['SELECT'] .= ', tt.mark_time';

				if ($s_display_active && sizeof($active_forum_ary))
				{
					$sql_array['LEFT_JOIN'][] = array('FROM' => array(FORUMS_TRACK_TABLE => 'ft'), 'ON' => 'ft.forum_id = t.forum_id AND ft.user_id = ' . $this->user->data['user_id']);
					$sql_array['SELECT'] .= ', ft.mark_time AS forum_mark_time';
				}
			}
		}

		if ($forum_data['forum_type'] == FORUM_POST)
		{
			// Get global announcement forums
			$g_forum_ary = $this->auth->acl_getf('f_read', true);
			$g_forum_ary = array_unique(array_keys($g_forum_ary));

			$sql_anounce_array['LEFT_JOIN'] = $sql_array['LEFT_JOIN'];
			$sql_anounce_array['LEFT_JOIN'][] = array('FROM' => array(FORUMS_TABLE => 'f'), 'ON' => 'f.forum_id = t.forum_id');
			$sql_anounce_array['SELECT'] = $sql_array['SELECT'] . ', f.forum_name';

			// Obtain announcements ... removed sort ordering, sort by time in all cases
			$sql_ary = array(
				'SELECT'	=> $sql_anounce_array['SELECT'],
				'FROM'		=> $sql_array['FROM'],
				'LEFT_JOIN'	=> $sql_anounce_array['LEFT_JOIN'],

				'WHERE'		=> '(t.forum_id = ' . $forum_id . '
					AND t.topic_type = ' . POST_ANNOUNCE . ') OR
						(' . $this->db->sql_in_set('t.forum_id', $g_forum_ary) . '
					AND t.topic_type = ' . POST_GLOBAL . ')',

				'ORDER_BY'	=> 't.topic_time DESC',
			);
			$sql = $this->db->sql_build_query('SELECT', $sql_ary);
			$result = $this->db->sql_query($sql);

			while ($row = $this->db->sql_fetchrow($result))
			{
				if ($row['topic_visibility'] != ITEM_APPROVED && !$this->auth->acl_get('m_approve', $row['forum_id']))
				{
					// Do not display announcements that are waiting for approval or soft deleted.
					continue;
				}

				$rowset[$row['topic_id']] = $row;
				$announcement_list[] = $row['topic_id'];

				if ($forum_id != $row['forum_id'])
				{
					$topics_count++;
					$global_announce_forums[] = $row['forum_id'];
				}
			}
			$this->db->sql_freeresult($result);
		}

		$forum_tracking_info = array();

		if ($this->user->data['is_registered'] && $this->config['load_db_lastread'])
		{
			$forum_tracking_info[$forum_id] = $forum_data['mark_time'];

			if (!empty($global_announce_forums))
			{
				$sql = 'SELECT forum_id, mark_time
					FROM ' . FORUMS_TRACK_TABLE . '
					WHERE ' . $this->db->sql_in_set('forum_id', $global_announce_forums) . '
						AND user_id = ' . $this->user->data['user_id'];
				$result = $this->db->sql_query($sql);

				while ($row = $this->db->sql_fetchrow($result))
				{
					$forum_tracking_info[$row['forum_id']] = $row['mark_time'];
				}
				$this->db->sql_freeresult($result);
			}
		}

		// If the user is trying to reach late pages, start searching from the end
		$store_reverse = false;
		$sql_limit = $this->config['topics_per_page'];

		if ($start > $topics_count / 2)
		{
			$store_reverse = true;

			// Select the sort order
			$direction = (($sort_dir == 'd') ? 'ASC' : 'DESC');

			$sql_limit = $this->pagination->reverse_limit($start, $sql_limit, $topics_count - sizeof($announcement_list));
			$sql_start = $this->pagination->reverse_start($start, $sql_limit, $topics_count - sizeof($announcement_list));
		}
		else
		{
			// Select the sort order
			$direction = (($sort_dir == 'd') ? 'DESC' : 'ASC');
			$sql_start = $start;
		}

		if (is_array($sort_by_sql[$sort_key]))
		{
			$sql_sort_order = implode(' ' . $direction . ', ', $sort_by_sql[$sort_key]) . ' ' . $direction;
		}
		else
		{
			$sql_sort_order = $sort_by_sql[$sort_key] . ' ' . $direction;
		}

		if ($forum_data['forum_type'] == FORUM_POST || !sizeof($active_forum_ary))
		{
			$sql_where = 't.forum_id = ' . $forum_id;
		}
		else if (empty($active_forum_ary['exclude_forum_id']))
		{
			$sql_where = $this->db->sql_in_set('t.forum_id', $active_forum_ary['forum_id']);
		}
		else
		{
			$get_forum_ids = array_diff($active_forum_ary['forum_id'], $active_forum_ary['exclude_forum_id']);
			$sql_where = (sizeof($get_forum_ids)) ? $this->db->sql_in_set('t.forum_id', $get_forum_ids) : 't.forum_id = ' . $forum_id;
		}

		// Grab just the sorted topic ids
		$sql_ary = array(
			'SELECT'	=> 't.topic_id',
			'FROM'		=> array(
				TOPICS_TABLE => 't',
			),
			'WHERE'		=> "$sql_where
				AND t.topic_type IN (" . POST_NORMAL . ', ' . POST_STICKY . ")
				$sql_approved
				$sql_limit_time",
			'ORDER_BY'	=> 't.topic_type ' . ((!$store_reverse) ? 'DESC' : 'ASC') . ', ' . $sql_sort_order,
		);

		/**
		* Event to modify the SQL query before the topic ids data is retrieved
		*
		* @event vinabb.web.viewforum_get_topic_ids_data
		* @var	array	forum_data		Data about the forum
		* @var	array	sql_ary			SQL query array to get the topic ids data
		* @var	string	sql_approved	Topic visibility SQL string
		* @var	int		sql_limit		Number of records to select
		* @var	string	sql_limit_time	SQL string to limit topic_last_post_time data
		* @var	array	sql_sort_order	SQL sorting string
		* @var	int		sql_start		Offset point to start selection from
		* @var	string	sql_where		SQL WHERE clause string
		* @var	bool	store_reverse	Flag indicating if we select from the late pages
		*
		* @since 3.1.0-RC4
		*
		* @changed 3.1.3 Added forum_data
		*/
		$vars = array(
			'forum_data',
			'sql_ary',
			'sql_approved',
			'sql_limit',
			'sql_limit_time',
			'sql_sort_order',
			'sql_start',
			'sql_where',
			'store_reverse',
		);
		extract($this->dispatcher->trigger_event('vinabb.web.viewforum_get_topic_ids_data', compact($vars)));

		$sql = $this->db->sql_build_query('SELECT', $sql_ary);
		$result = $this->db->sql_query_limit($sql, $sql_limit, $sql_start);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$topic_list[] = (int) $row['topic_id'];
		}
		$this->db->sql_freeresult($result);

		// For storing shadow topics
		$shadow_topic_list = array();

		if (sizeof($topic_list))
		{
			// SQL array for obtaining topics/stickies
			$sql_array = array(
				'SELECT'	=> $sql_array['SELECT'],
				'FROM'		=> $sql_array['FROM'],
				'LEFT_JOIN'	=> $sql_array['LEFT_JOIN'],
				'WHERE'		=> $this->db->sql_in_set('t.topic_id', $topic_list)
			);

			// If store_reverse, then first obtain topics, then stickies, else the other way around...
			// Funnily enough you typically save one query if going from the last page to the middle (store_reverse) because
			// the number of stickies are not known
			$sql = $this->db->sql_build_query('SELECT', $sql_array);
			$result = $this->db->sql_query($sql);

			while ($row = $this->db->sql_fetchrow($result))
			{
				if ($row['topic_status'] == ITEM_MOVED)
				{
					$shadow_topic_list[$row['topic_moved_id']] = $row['topic_id'];
				}

				$rowset[$row['topic_id']] = $row;
			}
			$this->db->sql_freeresult($result);
		}

		// If we have some shadow topics, update the rowset to reflect their topic information
		if (sizeof($shadow_topic_list))
		{
			// SQL array for obtaining shadow topics
			$sql_array = array(
				'SELECT'	=> 't.*',
				'FROM'		=> array(
					TOPICS_TABLE	=> 't'
				),
				'WHERE'		=> $this->db->sql_in_set('t.topic_id', array_keys($shadow_topic_list)),
			);

			/**
			* Event to modify the SQL query before the shadowtopic data is retrieved
			*
			* @event vinabb.web.viewforum_get_shadowtopic_data
			* @var	array	sql_array		SQL array to get the data of any shadowtopics
			* @since 3.1.0-a1
			*/
			$vars = array('sql_array');
			extract($this->dispatcher->trigger_event('vinabb.web.viewforum_get_shadowtopic_data', compact($vars)));

			$sql = $this->db->sql_build_query('SELECT', $sql_array);
			$result = $this->db->sql_query($sql);

			while ($row = $this->db->sql_fetchrow($result))
			{
				$orig_topic_id = $shadow_topic_list[$row['topic_id']];

				// If the shadow topic is already listed within the rowset (happens for active topics for example), then do not include it...
				if (isset($rowset[$row['topic_id']]))
				{
					// We need to remove any trace regarding this topic. :)
					unset($rowset[$orig_topic_id]);
					unset($topic_list[array_search($orig_topic_id, $topic_list)]);

					$topics_count--;

					continue;
				}

				// Do not include those topics the user has no permission to access
				if (!$this->auth->acl_get('f_read', $row['forum_id']))
				{
					// We need to remove any trace regarding this topic. :)
					unset($rowset[$orig_topic_id]);
					unset($topic_list[array_search($orig_topic_id, $topic_list)]);

					$topics_count--;

					continue;
				}

				// We want to retain some values
				$row = array_merge($row, array(
					'topic_moved_id'	=> $rowset[$orig_topic_id]['topic_moved_id'],
					'topic_status'		=> $rowset[$orig_topic_id]['topic_status'],
					'topic_type'		=> $rowset[$orig_topic_id]['topic_type'],
					'topic_title'		=> $rowset[$orig_topic_id]['topic_title'],
				));

				// Shadow topics are never reported
				$row['topic_reported'] = 0;

				$rowset[$orig_topic_id] = $row;
			}
			$this->db->sql_freeresult($result);
		}
		unset($shadow_topic_list);

		// Ok, adjust topics count for active topics list
		if ($s_display_active)
		{
			$topics_count = 1;
		}

		// We need to remove the global announcements from the forums total topic count,
		// otherwise the number is different from the one on the forum list
		$total_topic_count = $topics_count - sizeof($announcement_list);

		// Remove start=...
		unset($forum_url_sort_params['start']);
		$this->pagination->generate_template_pagination($this->helper->route('vinabb_web_board_forum_route', $forum_url_sort_params), 'pagination', 'start', $total_topic_count, $this->config['topics_per_page'], $start);

		$this->template->assign_vars(array(
			'TOTAL_TOPICS'	=> ($s_display_active) ? false : $this->language->lang('VIEW_FORUM_TOPICS', (int) $total_topic_count),
		));

		$topic_list = ($store_reverse) ? array_merge($announcement_list, array_reverse($topic_list)) : array_merge($announcement_list, $topic_list);
		$topic_tracking_info = $tracking_topics = array();

		/**
		* Modify topics data before we display the viewforum page
		*
		* @event vinabb.web.viewforum_modify_topics_data
		* @var	array	topic_list			Array with current viewforum page topic ids
		* @var	array	rowset				Array with topics data (in topic_id => topic_data format)
		* @var	int		total_topic_count	Forum's total topic count
		* @since 3.1.0-b3
		*/
		$vars = array('topic_list', 'rowset', 'total_topic_count');
		extract($this->dispatcher->trigger_event('vinabb.web.viewforum_modify_topics_data', compact($vars)));

		// Okay, lets dump out the page ...
		if (sizeof($topic_list))
		{
			$mark_forum_read = true;
			$mark_time_forum = 0;

			// Generate topic forum list...
			$topic_forum_list = array();
			foreach ($rowset as $t_id => $row)
			{
				if (isset($forum_tracking_info[$row['forum_id']]))
				{
					$row['forum_mark_time'] = $forum_tracking_info[$row['forum_id']];
				}

				$topic_forum_list[$row['forum_id']]['forum_mark_time'] = ($this->config['load_db_lastread'] && $this->user->data['is_registered'] && isset($row['forum_mark_time'])) ? $row['forum_mark_time'] : 0;
				$topic_forum_list[$row['forum_id']]['topics'][] = (int) $t_id;
			}

			if ($this->config['load_db_lastread'] && $this->user->data['is_registered'])
			{
				foreach ($topic_forum_list as $f_id => $topic_row)
				{
					$topic_tracking_info += get_topic_tracking($f_id, $topic_row['topics'], $rowset, array($f_id => $topic_row['forum_mark_time']));
				}
			}
			else if ($this->config['load_anon_lastread'] || $this->user->data['is_registered'])
			{
				foreach ($topic_forum_list as $f_id => $topic_row)
				{
					$topic_tracking_info += get_complete_topic_tracking($f_id, $topic_row['topics']);
				}
			}

			unset($topic_forum_list);

			if (!$s_display_active)
			{
				if ($this->config['load_db_lastread'] && $this->user->data['is_registered'])
				{
					$mark_time_forum = (!empty($forum_data['mark_time'])) ? $forum_data['mark_time'] : $this->user->data['user_lastmark'];
				}
				else if ($this->config['load_anon_lastread'] || $this->user->data['is_registered'])
				{
					if (!$this->user->data['is_registered'])
					{
						$this->user->data['user_lastmark'] = (isset($tracking_topics['l'])) ? (int) (base_convert($tracking_topics['l'], 36, 10) + $this->config['board_startdate']) : 0;
					}

					$mark_time_forum = (isset($tracking_topics['f'][$forum_id])) ? (int) (base_convert($tracking_topics['f'][$forum_id], 36, 10) + $this->config['board_startdate']) : $this->user->data['user_lastmark'];
				}
			}

			$s_type_switch = 0;
			foreach ($topic_list as $topic_id)
			{
				$row = &$rowset[$topic_id];

				$topic_forum_id = ($row['forum_id']) ? (int) $row['forum_id'] : $forum_id;

				// This will allow the style designer to output a different header
				// or even separate the list of announcements from sticky and normal topics
				$s_type_switch_test = ($row['topic_type'] == POST_ANNOUNCE || $row['topic_type'] == POST_GLOBAL) ? 1 : 0;

				// Replies
				$replies = $this->content_visibility->get_count('topic_posts', $row, $topic_forum_id) - 1;

				if ($row['topic_status'] == ITEM_MOVED)
				{
					$topic_id = $row['topic_moved_id'];
					$unread_topic = false;
				}
				else
				{
					$unread_topic = (isset($topic_tracking_info[$topic_id]) && $row['topic_last_post_time'] > $topic_tracking_info[$topic_id]) ? true : false;
				}

				// Get folder img, topic status/type related information
				$folder_img = $folder_alt = $topic_type = '';
				topic_status($row, $replies, $unread_topic, $folder_img, $folder_alt, $topic_type);

				// Generate all the URIs ...
				$view_topic_url_params = 'f=' . $row['forum_id'] . '&t=' . $topic_id;
				$view_topic_url = $this->helper->route('vinabb_web_board_topic_route', array('topic_id' => $topic_id));

				$topic_unapproved = (($row['topic_visibility'] == ITEM_UNAPPROVED || $row['topic_visibility'] == ITEM_REAPPROVE) && $this->auth->acl_get('m_approve', $row['forum_id']));
				$posts_unapproved = ($row['topic_visibility'] == ITEM_APPROVED && $row['topic_posts_unapproved'] && $this->auth->acl_get('m_approve', $row['forum_id']));
				$topic_deleted = $row['topic_visibility'] == ITEM_DELETED;

				$u_mcp_queue = ($topic_unapproved || $posts_unapproved) ? $this->helper->route('vinabb_web_mcp_route', array('id' => 'queue', 'mode' => (($topic_unapproved) ? 'approve_details' : 'unapproved_posts'), 't' => $topic_id), true, $this->user->session_id) : '';
				$u_mcp_queue = (!$u_mcp_queue && $topic_deleted) ? $this->helper->route('vinabb_web_mcp_route', array('id' => 'queue', 'mode' => 'deleted_topics', 't' => $topic_id), true, $this->user->session_id) : $u_mcp_queue;

				// Send vars to template
				$topic_row = array(
					'FORUM_ID'					=> $row['forum_id'],
					'TOPIC_ID'					=> $topic_id,
					'TOPIC_AUTHOR'				=> get_username_string('username', $row['topic_poster'], $row['topic_first_poster_name'], $row['topic_first_poster_colour']),
					'TOPIC_AUTHOR_COLOUR'		=> get_username_string('colour', $row['topic_poster'], $row['topic_first_poster_name'], $row['topic_first_poster_colour']),
					'TOPIC_AUTHOR_FULL'			=> get_username_string('full', $row['topic_poster'], $row['topic_first_poster_name'], $row['topic_first_poster_colour']),
					'FIRST_POST_TIME'			=> $this->user->format_date($row['topic_time']),
					'LAST_POST_SUBJECT'			=> censor_text($row['topic_last_post_subject']),
					'LAST_POST_TIME'			=> $this->user->format_date($row['topic_last_post_time']),
					'LAST_VIEW_TIME'			=> $this->user->format_date($row['topic_last_view_time']),
					'LAST_POST_AUTHOR'			=> get_username_string('username', $row['topic_last_poster_id'], $row['topic_last_poster_name'], $row['topic_last_poster_colour']),
					'LAST_POST_AUTHOR_COLOUR'	=> get_username_string('colour', $row['topic_last_poster_id'], $row['topic_last_poster_name'], $row['topic_last_poster_colour']),
					'LAST_POST_AUTHOR_FULL'		=> get_username_string('full', $row['topic_last_poster_id'], $row['topic_last_poster_name'], $row['topic_last_poster_colour']),

					'REPLIES'			=> $replies,
					'VIEWS'				=> $row['topic_views'],
					'TOPIC_TITLE'		=> censor_text($row['topic_title']),
					'TOPIC_TYPE'		=> $topic_type,
					'FORUM_NAME'		=> (isset($row['forum_name'])) ? $row['forum_name'] : $forum_data['forum_name'],

					'TOPIC_IMG_STYLE'		=> $folder_img,
					'TOPIC_FOLDER_IMG'		=> $this->user->img($folder_img, $folder_alt),
					'TOPIC_FOLDER_IMG_ALT'	=> $this->language->lang($folder_alt),

					'TOPIC_ICON_IMG'		=> (!empty($icons[$row['icon_id']])) ? $icons[$row['icon_id']]['img'] : '',
					'TOPIC_ICON_IMG_WIDTH'	=> (!empty($icons[$row['icon_id']])) ? $icons[$row['icon_id']]['width'] : '',
					'TOPIC_ICON_IMG_HEIGHT'	=> (!empty($icons[$row['icon_id']])) ? $icons[$row['icon_id']]['height'] : '',
					'ATTACH_ICON_IMG'		=> ($this->auth->acl_get('u_download') && $this->auth->acl_get('f_download', $row['forum_id']) && $row['topic_attachment']) ? $this->user->img('icon_topic_attach', $this->language->lang('TOTAL_ATTACHMENTS')) : '',
					'UNAPPROVED_IMG'		=> ($topic_unapproved || $posts_unapproved) ? $this->user->img('icon_topic_unapproved', ($topic_unapproved) ? 'TOPIC_UNAPPROVED' : 'POSTS_UNAPPROVED') : '',

					'S_TOPIC_TYPE'			=> $row['topic_type'],
					'S_USER_POSTED'			=> (isset($row['topic_posted']) && $row['topic_posted']) ? true : false,
					'S_UNREAD_TOPIC'		=> $unread_topic,
					'S_TOPIC_REPORTED'		=> (!empty($row['topic_reported']) && $this->auth->acl_get('m_report', $row['forum_id'])) ? true : false,
					'S_TOPIC_UNAPPROVED'	=> $topic_unapproved,
					'S_POSTS_UNAPPROVED'	=> $posts_unapproved,
					'S_TOPIC_DELETED'		=> $topic_deleted,
					'S_HAS_POLL'			=> ($row['poll_start']) ? true : false,
					'S_POST_ANNOUNCE'		=> ($row['topic_type'] == POST_ANNOUNCE) ? true : false,
					'S_POST_GLOBAL'			=> ($row['topic_type'] == POST_GLOBAL) ? true : false,
					'S_POST_STICKY'			=> ($row['topic_type'] == POST_STICKY) ? true : false,
					'S_TOPIC_LOCKED'		=> ($row['topic_status'] == ITEM_LOCKED) ? true : false,
					'S_TOPIC_MOVED'			=> ($row['topic_status'] == ITEM_MOVED) ? true : false,

					'U_NEWEST_POST'			=> $this->helper->route('vinabb_web_board_topic_route', array('topic_id' => $topic_id, 'view' => 'unread', '#' => 'unread')),
					'U_LAST_POST'			=> $this->helper->route('vinabb_web_board_topic_route', array('topic_id' => $topic_id, '#' => 'p' . $row['topic_last_post_id'])),
					'U_LAST_POST_AUTHOR'	=> get_username_string('profile', $row['topic_last_poster_id'], $row['topic_last_poster_name'], $row['topic_last_poster_colour']),
					'U_TOPIC_AUTHOR'		=> get_username_string('profile', $row['topic_poster'], $row['topic_first_poster_name'], $row['topic_first_poster_colour']),
					'U_VIEW_TOPIC'			=> $view_topic_url,
					'U_VIEW_FORUM'			=> $this->helper->route('vinabb_web_board_forum_route', array('forum_id' => $row['forum_id'])),
					'U_MCP_REPORT'			=> $this->helper->route('vinabb_web_mcp_route', array('id' => 'reports', 'mode' => 'reports', 'f' => $row['forum_id'], 't' => $topic_id), true, $this->user->session_id),
					'U_MCP_QUEUE'			=> $u_mcp_queue,

					'S_TOPIC_TYPE_SWITCH'	=> ($s_type_switch == $s_type_switch_test) ? -1 : $s_type_switch_test,
				);

				/**
				* Modify the topic data before it is assigned to the template
				*
				* @event vinabb.web.viewforum_modify_topicrow
				* @var	array	row			Array with topic data
				* @var	array	topic_row	Template array with topic data
				* @since 3.1.0-a1
				*/
				$vars = array('row', 'topic_row');
				extract($this->dispatcher->trigger_event('vinabb.web.viewforum_modify_topicrow', compact($vars)));

				$this->template->assign_block_vars('topicrow', $topic_row);

				$this->pagination->generate_template_pagination($view_topic_url, 'topicrow.pagination', 'start', $replies + 1, $this->config['posts_per_page'], 1, true, true);

				$s_type_switch = ($row['topic_type'] == POST_ANNOUNCE || $row['topic_type'] == POST_GLOBAL) ? 1 : 0;

				/**
				* Event after the topic data has been assigned to the template
				*
				* @event vinabb.web.viewforum_topic_row_after
				* @var	array	row				Array with the topic data
				* @var	array	rowset			Array with topics data (in topic_id => topic_data format)
				* @var	bool	s_type_switch	Flag indicating if the topic type is [global] announcement
				* @var	int		topic_id		The topic ID
				* @var	array	topic_list		Array with current viewforum page topic ids
				* @var	array	topic_row		Template array with topic data
				* @since 3.1.3-RC1
				*/
				$vars = array(
					'row',
					'rowset',
					's_type_switch',
					'topic_id',
					'topic_list',
					'topic_row',
				);
				extract($this->dispatcher->trigger_event('vinabb.web.viewforum_topic_row_after', compact($vars)));

				if ($unread_topic)
				{
					$mark_forum_read = false;
				}

				unset($rowset[$topic_id]);
			}
		}

		// This is rather a fudge but it's the best I can think of without requiring information
		// on all topics (as we do in 2.0.x). It looks for unread or new topics, if it doesn't find
		// any it updates the forum last read cookie. This requires that the user visit the forum
		// after reading a topic
		if ($forum_data['forum_type'] == FORUM_POST && sizeof($topic_list) && $mark_forum_read)
		{
			update_forum_tracking_info($forum_id, $forum_data['forum_last_post_time'], false, $mark_time_forum);
		}

		page_footer();
	}
}
