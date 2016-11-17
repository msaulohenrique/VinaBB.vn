<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\controllers\user;

use vinabb\web\includes\constants;

class online implements online_interface
{
	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\cache\service */
	protected $cache;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\event\dispatcher_interface */
	protected $dispatcher;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\pagination */
	protected $pagination;

	/** @var \phpbb\request\request */
	protected $request;

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
	protected $admin_path;

	/** @var string */
	protected $php_ext;

	/**
	* Constructor
	*
	* @param \phpbb\auth\auth $auth
	* @param \phpbb\cache\service $cache
	* @param \phpbb\config\config $config
	* @param \phpbb\db\driver\driver_interface $db
	* @param \phpbb\event\dispatcher_interface $dispatcher
	* @param \phpbb\language\language $language
	* @param \phpbb\pagination $pagination
	* @param \phpbb\request\request $request
	* @param \phpbb\template\template $template
	* @param \phpbb\user $user
	* @param \phpbb\controller\helper $helper
	* @param \vinabb\web\controllers\helper_interface $ext_helper
	* @param string $root_path
	* @param string $admin_path
	* @param string $php_ext
	*/
	public function __construct(
		\phpbb\auth\auth $auth,
		\phpbb\cache\service $cache,
		\phpbb\config\config $config,
		\phpbb\db\driver\driver_interface $db,
		\phpbb\event\dispatcher_interface $dispatcher,
		\phpbb\language\language $language,
		\phpbb\pagination $pagination,
		\phpbb\request\request $request,
		\phpbb\template\template $template,
		\phpbb\user $user,
		\phpbb\controller\helper $helper,
		\vinabb\web\controllers\helper_interface $ext_helper,
		$root_path,
		$admin_path,
		$php_ext
	)
	{
		$this->auth = $auth;
		$this->cache = $cache;
		$this->config = $config;
		$this->db = $db;
		$this->dispatcher = $dispatcher;
		$this->language = $language;
		$this->pagination = $pagination;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->helper = $helper;
		$this->ext_helper = $ext_helper;
		$this->root_path = $root_path;
		$this->admin_path = $admin_path;
		$this->php_ext = $php_ext;
	}

	/**
	* 'Who is online' page
	*
	* @param $mode View mode
	* @return \Symfony\Component\HttpFoundation\Response
	*/
	public function main($mode)
	{
		$this->language->add_lang('memberlist');

		// Get and set some variables
		$session_id = $this->request->variable('s', '');
		$start = $this->request->variable('start', 0);
		$sort_key = $this->request->variable('sk', 'b');
		$sort_dir = $this->request->variable('sd', 'd');
		$show_guests = ($this->config['load_online_guests']) ? $this->request->variable('sg', 0) : 0;

		// Can this user view profiles/memberlist?
		if (!$this->auth->acl_gets('u_viewprofile', 'a_user', 'a_useradd', 'a_userdel'))
		{
			if ($this->user->data['user_id'] != ANONYMOUS)
			{
				trigger_error('NO_VIEW_USERS');
			}

			login_box('', $this->language->lang('LOGIN_EXPLAIN_VIEWONLINE'));
		}

		$sort_key_text = [
			'a' => $this->language->lang('SORT_USERNAME'),
			'b' => $this->language->lang('SORT_JOINED'),
			'c' => $this->language->lang('SORT_LOCATION')
		];

		$sort_key_sql = [
			'a' => 'u.username_clean',
			'b' => 's.session_time',
			'c' => 's.session_page'
		];

		// Sorting and order
		if (!isset($sort_key_text[$sort_key]))
		{
			$sort_key = 'b';
		}

		$order_by = $sort_key_sql[$sort_key] . ' ' . (($sort_dir == 'a') ? 'ASC' : 'DESC');

		$this->user->update_session_infos();

		// Forum info
		$forum_data = $this->cache->get_forum_data();

		/**
		* [REMOVED]
		* Modify the forum data SQL query for getting additional fields if needed
		*
		* @event core.viewonline_modify_forum_data_sql
		* @var	array	sql_ary			The SQL array
		* @since 3.1.5-RC1
		*/

		// Get number of online guests (if we do not display them)
		$guest_counter = 0;

		if (!$show_guests)
		{
			$guest_counter = $this->get_guest_counter();
		}

		// Get user list
		$sql_ary = array(
			'SELECT'	=> 'u.user_id, u.username, u.username_clean, u.user_type, u.user_colour, s.session_id, s.session_time, s.session_page, s.session_ip, s.session_browser, s.session_viewonline, s.session_forum_id',
			'FROM'		=> array(
				USERS_TABLE		=> 'u',
				SESSIONS_TABLE	=> 's',
			),
			'WHERE'		=> 'u.user_id = s.session_user_id
				AND s.session_time >= ' . (time() - ($this->config['load_online_time'] * 60)) .
				((!$show_guests) ? ' AND s.session_user_id <> ' . ANONYMOUS : ''),
			'ORDER_BY'	=> $order_by,
		);

		/**
		* Modify the SQL query for getting the user data to display viewonline list
		*
		* @event core.viewonline_modify_sql
		* @var	array	sql_ary			The SQL array
		* @var	bool	show_guests		Do we display guests in the list
		* @var	int		guest_counter	Number of guests displayed
		* @var	array	forum_data		Array with forum data
		* @since 3.1.0-a1
		* @change 3.1.0-a2 Added vars guest_counter and forum_data
		*/
		$vars = array('sql_ary', 'show_guests', 'guest_counter', 'forum_data');
		extract($this->dispatcher->trigger_event('core.viewonline_modify_sql', compact($vars)));

		$result = $this->db->sql_query($this->db->sql_build_query('SELECT', $sql_ary));

		$prev_id = $prev_ip = array();
		$logged_visible_online = $logged_hidden_online = $counter = 0;

		while ($row = $this->db->sql_fetchrow($result))
		{
			if ($row['user_id'] != ANONYMOUS && !isset($prev_id[$row['user_id']]))
			{
				$s_user_hidden = false;
				$user_colour = ($row['user_colour']) ? ' style="color: #' . $row['user_colour'] . '" class="username-coloured"' : '';

				$username_full = ($row['user_type'] != USER_IGNORE) ? get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']) : '<span' . $user_colour . '>' . $row['username'] . '</span>';

				if (!$row['session_viewonline'])
				{
					$view_online = ($this->auth->acl_get('u_viewonline') || $row['user_id'] === $this->user->data['user_id']) ? true : false;
					$logged_hidden_online++;

					$username_full = '<em>' . $username_full . '</em>';
					$s_user_hidden = true;
				}
				else
				{
					$view_online = true;
					$logged_visible_online++;
				}

				$prev_id[$row['user_id']] = 1;

				if ($view_online)
				{
					$counter++;
				}

				if (!$view_online || $counter > $start + $this->config['topics_per_page'] || $counter <= $start)
				{
					continue;
				}
			}
			else if ($show_guests && $row['user_id'] == ANONYMOUS && !isset($prev_ip[$row['session_ip']]))
			{
				$prev_ip[$row['session_ip']] = 1;
				$guest_counter++;
				$counter++;

				if ($counter > $start + $this->config['topics_per_page'] || $counter <= $start)
				{
					continue;
				}

				$s_user_hidden = false;
				$username_full = get_username_string('full', $row['user_id'], $this->user->lang['GUEST']);
			}
			else
			{
				continue;
			}

			$on_page_data = array(
				"index.{$this->php_ext}"	=> array(
					'lang'	=> $this->language->lang('INDEX'),
					'url'	=> append_sid("{$this->root_path}index.{$this->php_ext}"),
				),
				"{$this->admin_path}index.{$this->php_ext}"	=> array(
					'lang'	=> $this->language->lang('ACP'),
					'url'	=> '',
				),
				"app.{$this->php_ext}/search"	=> array(
					'lang'	=> $this->language->lang('SEARCHING_FORUMS'),
					'url'	=> $this->helper->route('vinabb_web_board_search_route'),
				),
				"app.{$this->php_ext}/online"	=> array(
					'lang'	=> $this->language->lang('VIEWING_ONLINE'),
					'url'	=> $this->helper->route('vinabb_web_user_online_route'),
				),
				"app.{$this->php_ext}/user/list"	=> array(
					'lang'	=> $this->language->lang('VIEWING_MEMBERS'),
					'url'	=> $this->helper->route('vinabb_web_user_list_route'),
				),
				"app.{$this->php_ext}/user/profile"	=> array(
					'lang'	=> $this->language->lang('VIEWING_MEMBER_PROFILE'),
					'url'	=> $this->helper->route('vinabb_web_user_list_route'),
				),
				"app.{$this->php_ext}/user/contact/admin"	=> array(
					'lang'	=> $this->language->lang('VIEWING_CONTACT_ADMIN'),
					'url'	=> $this->helper->route('vinabb_web_user_contact_admin_route'),
				),
				"app.{$this->php_ext}/mcp"	=> array(
					'lang'	=> $this->language->lang('VIEWING_MCP'),
					'url'	=> '',
				),
				"app.{$this->php_ext}/ucp/front/register"	=> array(
					'lang'	=> $this->language->lang('VIEWING_REGISTER'),
					'url'	=> '',
				),
				"app.{$this->php_ext}/ucp/pm/compose"	=> array(
					'lang'	=> $this->language->lang('POSTING_PRIVATE_MESSAGE'),
					'url'	=> '',
				),
				"app.{$this->php_ext}/ucp/pm"	=> array(
					'lang'	=> $this->language->lang('VIEWING_PRIVATE_MESSAGES'),
					'url'	=> '',
				),
				"app.{$this->php_ext}/ucp/profile"	=> array(
					'lang'	=> $this->language->lang('CHANGING_PROFILE'),
					'url'	=> '',
				),
				"app.{$this->php_ext}/ucp/prefs"	=> array(
					'lang'	=> $this->language->lang('CHANGING_PREFERENCES'),
					'url'	=> '',
				),
				"app.{$this->php_ext}/ucp"	=> array(
					'lang'	=> $this->language->lang('VIEWING_UCP'),
					'url'	=> '',
				),
				"app.{$this->php_ext}/attachment"		=> array(
					'lang'	=> $this->language->lang('DOWNLOADING_FILE'),
					'url'	=> '',
				),
				"app.{$this->php_ext}/post/"		=> array(
					'lang'	=> $this->language->lang('REPORTING_POST'),
					'url'	=> '',
				),
				"app.{$this->php_ext}/help/"		=> array(
					'lang'	=> $this->language->lang('VIEWING_FAQ'),
					'url'	=> $this->helper->route('phpbb_help_faq_controller'),
				),
			);

			// What are they viewing?
			$location = $location_url = '';

			// First, try to detect from the basic list
			foreach ($on_page_data as $page_str => $page_data)
			{
				if (strpos($row['session_page'], $page_str) !== false)
				{
					$location = $page_data['lang'];
					$location_url = (isset($page_data['url']) && !empty($page_data['url'])) ? $page_data['url'] : append_sid("{$this->root_path}index.{$this->php_ext}");
				}
			}

			// Then, with more URL parameters than...
			if (empty($location_url))
			{
				if (strpos($row['session_page'], "app.{$this->php_ext}/board/forum") !== false || strpos($row['session_page'], "app.{$this->php_ext}/board/topic") !== false || strpos($row['session_page'], "app.{$this->php_ext}/posting") !== false)
				{
					$forum_id = $row['session_forum_id'];

					if ($forum_id && $this->auth->acl_get('f_list', $forum_id))
					{
						$location_url = $this->helper->route('vinabb_web_board_forum_route', array('forum_id' => $forum_id, 'seo' => $forum_data[$forum_id]['forum_name_seo'] . constants::REWRITE_URL_SEO));

						if ($forum_data[$forum_id]['forum_type'] == FORUM_LINK)
						{
							$location = $this->language->lang('READING_LINK', $forum_data[$forum_id]['forum_name']);
						}
						else if (strpos($row['session_page'], "app.{$this->php_ext}/board/forum") !== false)
						{
							$location = $this->language->lang('READING_FORUM', $forum_data[$forum_id]['forum_name']);
						}
						else if (strpos($row['session_page'], "app.{$this->php_ext}/board/topic") !== false)
						{
							$location = $this->language->lang('READING_TOPIC', $forum_data[$forum_id]['forum_name']);
						}
						else if (strpos($row['session_page'], "app.{$this->php_ext}/posting") !== false)
						{
							if (strpos($row['session_page'], "app.{$this->php_ext}/posting/reply") !== false || strpos($row['session_page'], "app.{$this->php_ext}/posting/quote") !== false)
							{
								$location = $this->language->lang('REPLYING_MESSAGE', $forum_data[$forum_id]['forum_name']);
							}
							else
							{
								$location = $this->language->lang('POSTING_MESSAGE', $forum_data[$forum_id]['forum_name']);
							}
						}

						if (empty($location))
						{
							$location = $this->language->lang('BOARD');
						}
					}
					else
					{
						$location = $this->language->lang('INDEX');
						$location_url = append_sid("{$this->root_path}index.{$this->php_ext}");
					}
				}
				else
				{
					$location = $this->language->lang('INDEX');
					$location_url = append_sid("{$this->root_path}index.{$this->php_ext}");
				}
			}

			/**
			* Overwrite the location's name and URL, which are displayed in the list
			*
			* @event core.viewonline_overwrite_location
			* @var	array	on_page			File name and query string
			* @var	array	row				Array with the users sql row
			* @var	string	location		Page name to displayed in the list
			* @var	string	location_url	Page url to displayed in the list
			* @var	array	forum_data		Array with forum data
			* @since 3.1.0-a1
			* @change 3.1.0-a2 Added var forum_data
			*/
			$vars = ['on_page', 'row', 'location', 'location_url', 'forum_data'];
			extract($this->dispatcher->trigger_event('core.viewonline_overwrite_location', compact($vars)));

			$template_row = [
				'USERNAME' 			=> $row['username'],
				'USERNAME_COLOUR'	=> $row['user_colour'],
				'USERNAME_FULL'		=> $username_full,
				'LASTUPDATE'		=> $this->user->format_date($row['session_time']),
				'FORUM_LOCATION'	=> $location,
				'USER_IP'			=> ($this->auth->acl_get('a_')) ? (($mode == 'lookup' && $session_id == $row['session_id']) ? gethostbyaddr($row['session_ip']) : $row['session_ip']) : '',
				'USER_BROWSER'		=> ($this->auth->acl_get('a_user')) ? $row['session_browser'] : '',

				'U_USER_PROFILE'	=> ($row['user_type'] != USER_IGNORE) ? get_username_string('profile', $row['user_id'], '') : '',
				'U_USER_IP'			=> ($mode != 'lookup' || $row['session_id'] != $session_id) ? $this->helper->route('vinabb_web_user_online_route', ['mode' => 'lookup', 's' => $row['session_id'], 'sg' => $show_guests, 'start' => $start, 'sk' => $sort_key, 'sd' => $sort_dir]) : $this->helper->route('vinabb_web_user_online_route', ['mode' => 'lookup', 'sg' => $show_guests, 'start' => $start, 'sk' => $sort_key, 'sd' => $sort_dir]),
				'U_WHOIS'			=> $this->helper->route('vinabb_web_user_online_whois_route', ['session_id' => $row['session_id']]),
				'U_FORUM_LOCATION'	=> $location_url,

				'S_USER_HIDDEN'		=> $s_user_hidden,
				'S_GUEST'			=> ($row['user_id'] == ANONYMOUS),
				'S_USER_TYPE'		=> $row['user_type']
			];

			/**
			* Modify viewonline template data before it is displayed in the list
			*
			* @event core.viewonline_modify_user_row
			* @var	array	on_page			File name and query string
			* @var	array	row				Array with the users sql row
			* @var	array	forum_data		Array with forum data
			* @var	array	template_row	Array with template variables for the user row
			* @since 3.1.0-RC4
			*/
			$vars = ['on_page', 'row', 'forum_data', 'template_row'];
			extract($this->dispatcher->trigger_event('core.viewonline_modify_user_row', compact($vars)));

			$this->template->assign_block_vars('user_row', $template_row);
		}
		$this->db->sql_freeresult($result);
		unset($prev_id, $prev_ip);

		// Refreshing the page every 60 seconds...
		meta_refresh(60, $this->helper->route('vinabb_web_user_online_route', array('sg' => $show_guests, 'sk' => $sort_key, 'sd' => $sort_dir, 'start' => $start)));

		$start = $this->pagination->validate_start($start, $this->config['topics_per_page'], $counter);
		$base_url = $this->helper->route('vinabb_web_user_online_route', array('sg' => $show_guests, 'sk' => $sort_key, 'sd' => $sort_dir));
		$this->pagination->generate_template_pagination($base_url, 'pagination', 'start', $counter, $this->config['topics_per_page'], $start);

		// Send data to template
		$this->template->assign_vars(array(
			'TOTAL_REGISTERED_USERS_ONLINE'	=> $this->language->lang('REG_USERS_ONLINE', (int) $logged_visible_online, $this->language->lang('HIDDEN_USERS_ONLINE', (int) $logged_hidden_online)),
			'TOTAL_GUEST_USERS_ONLINE'		=> $this->language->lang('GUEST_USERS_ONLINE', (int) $guest_counter),
			'LEGEND'						=> $this->ext_helper->get_group_legend(),

			'U_SORT_USERNAME'	=> $this->helper->route('vinabb_web_user_online_route', array('sk' => 'a', 'sd' => (($sort_key == 'a' && $sort_dir == 'a') ? 'd' : 'a'), 'sg' => ((int) $show_guests))),
			'U_SORT_UPDATED'	=> $this->helper->route('vinabb_web_user_online_route', array('sk' => 'b', 'sd' => (($sort_key == 'b' && $sort_dir == 'a') ? 'd' : 'a'), 'sg' => ((int) $show_guests))),
			'U_SORT_LOCATION'	=> $this->helper->route('vinabb_web_user_online_route', array('sk' => 'c', 'sd' => (($sort_key == 'c' && $sort_dir == 'a') ? 'd' : 'a'), 'sg' => ((int) $show_guests))),

			'U_SWITCH_GUEST_DISPLAY'	=> $this->helper->route('vinabb_web_user_online_route', array('sg' => ((int) !$show_guests))),
			'L_SWITCH_GUEST_DISPLAY'	=> ($show_guests) ? $this->language->lang('HIDE_GUESTS') : $this->language->lang('DISPLAY_GUESTS'),
			'S_SWITCH_GUEST_DISPLAY'	=> ($this->config['load_online_guests']) ? true : false,
			'S_VIEWONLINE'				=> true,
		));

		// We do not need to load the who is online box here. ;)
		$this->config['load_online'] = false;

		return $this->helper->render('viewonline_body.html', $this->language->lang('WHO_IS_ONLINE'));
	}

	/**
	* Get number of online guests
	*
	* @return int
	*/
	protected function get_guest_counter()
	{
		switch ($this->db->get_sql_layer())
		{
			case 'sqlite':
			case 'sqlite3':
				$sql = 'SELECT COUNT(session_ip) as num_guests
					FROM (
						SELECT DISTINCT session_ip
							FROM ' . SESSIONS_TABLE . '
							WHERE session_user_id = ' . ANONYMOUS . '
								AND session_time >= ' . (time() - ($this->config['load_online_time'] * 60)) .
					')';
			break;

			default:
				$sql = 'SELECT COUNT(DISTINCT session_ip) as num_guests
					FROM ' . SESSIONS_TABLE . '
					WHERE session_user_id = ' . ANONYMOUS . '
						AND session_time >= ' . (time() - ($this->config['load_online_time'] * 60));
			break;
		}

		$result = $this->db->sql_query($sql);
		$guest_counter = (int) $this->db->sql_fetchfield('num_guests');
		$this->db->sql_freeresult($result);

		return $guest_counter;
	}

	/**
	* Whois requested
	*
	* @param string $session_id Session ID
	* @return \Symfony\Component\HttpFoundation\Response
	*/
	public function whois($session_id)
	{
		if ($this->auth->acl_get('a_'))
		{
			include "{$this->root_path}includes/functions_user.{$this->php_ext}";

			$sql = 'SELECT u.user_id, u.username, u.user_type, s.session_ip
				FROM ' . USERS_TABLE . ' u, ' . SESSIONS_TABLE . " s
				WHERE s.session_id = '" . $this->db->sql_escape($session_id) . "'
			AND	u.user_id = s.session_user_id";
			$result = $this->db->sql_query($sql);

			if ($row = $this->db->sql_fetchrow($result))
			{
				$this->template->assign_var('WHOIS', user_ipwhois($row['session_ip']));
			}
			$this->db->sql_freeresult($result);

			return $this->helper->render('viewonline_whois.html', $this->language->lang('WHO_IS_ONLINE'));
		}
		else
		{
			send_status_line(401, 'Unauthorized');
			trigger_error('NOT_AUTHORISED');
		}
	}
}
