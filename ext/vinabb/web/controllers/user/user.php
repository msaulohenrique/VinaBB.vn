<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\controllers\user;

class user
{
	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\event\dispatcher_interface */
	protected $dispatcher;

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

	/** @var \phpbb\group\helper */
	protected $group_helper;

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
	* @param \phpbb\language\language $language
	* @param \phpbb\request\request $request
	* @param \phpbb\template\template $template
	* @param \phpbb\user $user
	* @param \phpbb\controller\helper $helper
	* @param \phpbb\group\helper $group_helper
	* @param string $root_path
	* @param string $php_ext
	*/
	public function __construct(
		\phpbb\auth\auth $auth,
		\phpbb\config\config $config,
		\phpbb\db\driver\driver_interface $db,
		\phpbb\event\dispatcher_interface $dispatcher,
		\phpbb\language\language $language,
		\phpbb\request\request $request,
		\phpbb\template\template $template,
		\phpbb\user $user,
		\phpbb\controller\helper $helper,
		\phpbb\group\helper $group_helper,
		$root_path,
		$php_ext
	)
	{
		$this->auth = $auth;
		$this->config = $config;
		$this->db = $db;
		$this->dispatcher = $dispatcher;
		$this->language = $language;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->helper = $helper;
		$this->group_helper = $group_helper;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;

		// Common functions
		include "{$this->root_path}includes/functions_display.{$this->php_ext}";

		// Common language files
		$this->language->add_lang(array('memberlist', 'groups'));

		// Setting a variable to let the style designer know where he is...
		$this->template->assign_var('S_IN_MEMBERLIST', true);

		// User types
		$this->user_types = array(USER_NORMAL, USER_FOUNDER);

		if ($this->auth->acl_get('a_user'))
		{
			$this->user_types[] = USER_INACTIVE;
		}
	}

	public function memberlist()
	{
		// The basic memberlist
		$page_title = $this->user->lang['MEMBERLIST'];
		$template_html = 'memberlist_body.html';

		/* @var $pagination \phpbb\pagination */
		$pagination = $phpbb_container->get('pagination');

		// Sorting
		$sort_key_text = array('a' => $this->user->lang['SORT_USERNAME'], 'c' => $this->user->lang['SORT_JOINED'], 'd' => $this->user->lang['SORT_POST_COUNT']);
		$sort_key_sql = array('a' => 'u.username_clean', 'c' => 'u.user_regdate', 'd' => 'u.user_posts');

		if ($this->config['jab_enable'])
		{
			$sort_key_text['k'] = $this->user->lang['JABBER'];
			$sort_key_sql['k'] = 'u.user_jabber';
		}

		if ($this->auth->acl_get('a_user'))
		{
			$sort_key_text['e'] = $this->user->lang['SORT_EMAIL'];
			$sort_key_sql['e'] = 'u.user_email';
		}

		if ($this->auth->acl_get('u_viewonline'))
		{
			$sort_key_text['l'] = $this->user->lang['SORT_LAST_ACTIVE'];
			$sort_key_sql['l'] = 'u.user_lastvisit';
		}

		$sort_key_text['m'] = $this->user->lang['SORT_RANK'];
		$sort_key_sql['m'] = 'u.user_rank';

		$sort_dir_text = array('a' => $this->user->lang['ASCENDING'], 'd' => $this->user->lang['DESCENDING']);

		$s_sort_key = '';
		foreach ($sort_key_text as $key => $value)
		{
			$selected = ($sort_key == $key) ? ' selected="selected"' : '';
			$s_sort_key .= '<option value="' . $key . '"' . $selected . '>' . $value . '</option>';
		}

		$s_sort_dir = '';
		foreach ($sort_dir_text as $key => $value)
		{
			$selected = ($sort_dir == $key) ? ' selected="selected"' : '';
			$s_sort_dir .= '<option value="' . $key . '"' . $selected . '>' . $value . '</option>';
		}

		// Additional sorting options for user search ... if search is enabled, if not
		// then only admins can make use of this (for ACP functionality)
		$sql_select = $sql_where_data = $sql_from = $sql_where = $order_by = '';


		$form			= $this->request->variable('form', '');
		$field			= $this->request->variable('field', '');
		$select_single 	= $this->request->variable('select_single', false);

		// Search URL parameters, if any of these are in the URL we do a search
		$search_params = array('username', 'email', 'jabber', 'search_group_id', 'joined_select', 'active_select', 'count_select', 'joined', 'active', 'count', 'ip');

		// We validate form and field here, only id/class allowed
		$form = (!preg_match('/^[a-z0-9_-]+$/i', $form)) ? '' : $form;
		$field = (!preg_match('/^[a-z0-9_-]+$/i', $field)) ? '' : $field;
		if ((($mode == '' || $mode == 'searchuser') || sizeof(array_intersect($this->request->variable_names(\phpbb\request\request_interface::GET), $search_params)) > 0) && ($this->config['load_search'] || $this->auth->acl_get('a_')))
		{
			$username	= $this->request->variable('username', '', true);
			$email		= strtolower($this->request->variable('email', ''));
			$jabber		= $this->request->variable('jabber', '');
			$search_group_id	= $this->request->variable('search_group_id', 0);

			// when using these, make sure that we actually have values defined in $find_key_match
			$joined_select	= $this->request->variable('joined_select', 'lt');
			$active_select	= $this->request->variable('active_select', 'lt');
			$count_select	= $this->request->variable('count_select', 'eq');

			$joined			= explode('-', $this->request->variable('joined', ''));
			$active			= explode('-', $this->request->variable('active', ''));
			$count			= ($this->request->variable('count', '') !== '') ? $this->request->variable('count', 0) : '';
			$ipdomain		= $this->request->variable('ip', '');

			$find_key_match = array('lt' => '<', 'gt' => '>', 'eq' => '=');

			$find_count = array('lt' => $this->user->lang['LESS_THAN'], 'eq' => $this->user->lang['EQUAL_TO'], 'gt' => $this->user->lang['MORE_THAN']);
			$s_find_count = '';
			foreach ($find_count as $key => $value)
			{
				$selected = ($count_select == $key) ? ' selected="selected"' : '';
				$s_find_count .= '<option value="' . $key . '"' . $selected . '>' . $value . '</option>';
			}

			$find_time = array('lt' => $this->user->lang['BEFORE'], 'gt' => $this->user->lang['AFTER']);
			$s_find_join_time = '';
			foreach ($find_time as $key => $value)
			{
				$selected = ($joined_select == $key) ? ' selected="selected"' : '';
				$s_find_join_time .= '<option value="' . $key . '"' . $selected . '>' . $value . '</option>';
			}

			$s_find_active_time = '';
			foreach ($find_time as $key => $value)
			{
				$selected = ($active_select == $key) ? ' selected="selected"' : '';
				$s_find_active_time .= '<option value="' . $key . '"' . $selected . '>' . $value . '</option>';
			}

			$sql_where .= ($username) ? ' AND u.username_clean ' . $this->db->sql_like_expression(str_replace('*', $this->db->get_any_char(), utf8_clean_string($username))) : '';
			$sql_where .= ($this->auth->acl_get('a_user') && $email) ? ' AND u.user_email ' . $this->db->sql_like_expression(str_replace('*', $this->db->get_any_char(), $email)) . ' ' : '';
			$sql_where .= ($jabber) ? ' AND u.user_jabber ' . $this->db->sql_like_expression(str_replace('*', $this->db->get_any_char(), $jabber)) . ' ' : '';
			$sql_where .= (is_numeric($count) && isset($find_key_match[$count_select])) ? ' AND u.user_posts ' . $find_key_match[$count_select] . ' ' . (int) $count . ' ' : '';

			if (isset($find_key_match[$joined_select]) && sizeof($joined) == 3)
			{
				$joined_time = gmmktime(0, 0, 0, (int) $joined[1], (int) $joined[2], (int) $joined[0]);

				if ($joined_time !== false)
				{
					$sql_where .= " AND u.user_regdate " . $find_key_match[$joined_select] . ' ' . $joined_time;
				}
			}

			if (isset($find_key_match[$active_select]) && sizeof($active) == 3 && $this->auth->acl_get('u_viewonline'))
			{
				$active_time = gmmktime(0, 0, 0, (int) $active[1], (int) $active[2], (int) $active[0]);

				if ($active_time !== false)
				{
					$sql_where .= " AND u.user_lastvisit " . $find_key_match[$active_select] . ' ' . $active_time;
				}
			}

			$sql_where .= ($search_group_id) ? " AND u.user_id = ug.user_id AND ug.group_id = $search_group_id AND ug.user_pending = 0 " : '';

			if ($search_group_id)
			{
				$sql_from = ', ' . USER_GROUP_TABLE . ' ug ';
			}

			if ($ipdomain && $this->auth->acl_getf_global('m_info'))
			{
				if (strspn($ipdomain, 'abcdefghijklmnopqrstuvwxyz'))
				{
					$hostnames = gethostbynamel($ipdomain);

					if ($hostnames !== false)
					{
						$ips = "'" . implode('\', \'', array_map(array($db, 'sql_escape'), preg_replace('#([0-9]{1,3}\.[0-9]{1,3}[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3})#', "\\1", gethostbynamel($ipdomain)))) . "'";
					}
					else
					{
						$ips = false;
					}
				}
				else
				{
					$ips = "'" . str_replace('*', '%', $this->db->sql_escape($ipdomain)) . "'";
				}

				if ($ips === false)
				{
					// A minor fudge but it does the job :D
					$sql_where .= " AND u.user_id = 0";
				}
				else
				{
					$ip_forums = array_keys($this->auth->acl_getf('m_info', true));

					$sql = 'SELECT DISTINCT poster_id
						FROM ' . POSTS_TABLE . '
						WHERE poster_ip ' . ((strpos($ips, '%') !== false) ? 'LIKE' : 'IN') . " ($ips)
							AND " . $this->db->sql_in_set('forum_id', $ip_forums);

					/**
					 * Modify sql query for members search by ip address / hostname
					 *
					 * @event core.memberlist_modify_ip_search_sql_query
					 * @var	string	ipdomain	The host name
					 * @var	string	ips			IP address list for the given host name
					 * @var	string	sql			The SQL query for searching members by IP address
					 * @since 3.1.7-RC1
					 */
					$vars = array(
						'ipdomain',
						'ips',
						'sql',
					);
					extract($this->dispatcher->trigger_event('core.memberlist_modify_ip_search_sql_query', compact($vars)));

					$result = $this->db->sql_query($sql);

					if ($row = $this->db->sql_fetchrow($result))
					{
						$ip_sql = array();
						do
						{
							$ip_sql[] = $row['poster_id'];
						}
						while ($row = $this->db->sql_fetchrow($result));

						$sql_where .= ' AND ' . $this->db->sql_in_set('u.user_id', $ip_sql);
					}
					else
					{
						// A minor fudge but it does the job :D
						$sql_where .= " AND u.user_id = 0";
					}
					unset($ip_forums);

					$this->db->sql_freeresult($result);
				}
			}
		}

		$first_char = $this->request->variable('first_char', '');

		if ($first_char == 'other')
		{
			for ($i = 97; $i < 123; $i++)
			{
				$sql_where .= ' AND u.username_clean NOT ' . $this->db->sql_like_expression(chr($i) . $this->db->get_any_char());
			}
		}
		else if ($first_char)
		{
			$sql_where .= ' AND u.username_clean ' . $this->db->sql_like_expression(substr($first_char, 0, 1) . $this->db->get_any_char());
		}

		// Are we looking at a usergroup? If so, fetch additional info
		// and further restrict the user info query
		if ($mode == 'group')
		{
			// We JOIN here to save a query for determining membership for hidden groups. ;)
			$sql = 'SELECT g.*, ug.user_id, ug.group_leader
				FROM ' . GROUPS_TABLE . ' g
				LEFT JOIN ' . USER_GROUP_TABLE . ' ug ON (ug.user_pending = 0 AND ug.user_id = ' . $this->user->data['user_id'] . " AND ug.group_id = $group_id)
				WHERE g.group_id = $group_id";
			$result = $this->db->sql_query($sql);
			$group_row = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);

			if (!$group_row)
			{
				trigger_error('NO_GROUP');
			}

			switch ($group_row['group_type'])
			{
				case GROUP_OPEN:
					$group_row['l_group_type'] = 'OPEN';
					break;

				case GROUP_CLOSED:
					$group_row['l_group_type'] = 'CLOSED';
					break;

				case GROUP_HIDDEN:
					$group_row['l_group_type'] = 'HIDDEN';

					// Check for membership or special permissions
					if (!$this->auth->acl_gets('a_group', 'a_groupadd', 'a_groupdel') && $group_row['user_id'] != $this->user->data['user_id'])
					{
						trigger_error('NO_GROUP');
					}
					break;

				case GROUP_SPECIAL:
					$group_row['l_group_type'] = 'SPECIAL';
					break;

				case GROUP_FREE:
					$group_row['l_group_type'] = 'FREE';
					break;
			}

			$avatar_img = phpbb_get_group_avatar($group_row);

			// ... same for group rank
			$user_rank_data = array(
				'title'		=> null,
				'img'		=> null,
				'img_src'	=> null,
			);
			if ($group_row['group_rank'])
			{
				$user_rank_data = phpbb_get_user_rank($group_row, false);

				if ($user_rank_data['img'])
				{
					$user_rank_data['img'] .= '<br />';
				}
			}
			// include modules for manage groups link display or not
			// need to ensure the module is active
			$can_manage_group = false;
			if ($this->user->data['is_registered'] && $group_row['group_leader'])
			{
				if (!class_exists('p_master'))
				{
					include($this->root_path . 'includes/functions_module.' . $phpEx);
				}
				$module = new p_master;
				$module->list_modules('ucp');

				if ($module->is_active('ucp_groups', 'manage'))
				{
					$can_manage_group = true;
				}
				unset($module);
			}

			$this->template->assign_vars(array(
					'GROUP_DESC'	=> generate_text_for_display($group_row['group_desc'], $group_row['group_desc_uid'], $group_row['group_desc_bitfield'], $group_row['group_desc_options']),
					'GROUP_NAME'	=> $this->group_helper->get_name($group_row['group_name']),
					'GROUP_COLOR'	=> $group_row['group_colour'],
					'GROUP_TYPE'	=> $this->user->lang['GROUP_IS_' . $group_row['l_group_type']],
					'GROUP_RANK'	=> $user_rank_data['title'],

					'AVATAR_IMG'	=> $avatar_img,
					'RANK_IMG'		=> $user_rank_data['img'],
					'RANK_IMG_SRC'	=> $user_rank_data['img_src'],

					'U_PM'			=> ($this->auth->acl_get('u_sendpm') && $this->auth->acl_get('u_masspm_group') && $group_row['group_receive_pm'] && $this->config['allow_privmsg'] && $this->config['allow_mass_pm']) ? append_sid("{$this->root_path}ucp.$phpEx", 'i=pm&amp;mode=compose&amp;g=' . $group_id) : '',
					'U_MANAGE'		=> ($can_manage_group) ? append_sid("{$this->root_path}ucp.$phpEx", 'i=ucp_groups&amp;mode=manage') : false,)
			);

			$sql_select = ', ug.group_leader';
			$sql_from = ', ' . USER_GROUP_TABLE . ' ug ';
			$order_by = 'ug.group_leader DESC, ';

			$sql_where .= " AND ug.user_pending = 0 AND u.user_id = ug.user_id AND ug.group_id = $group_id";
			$sql_where_data = " AND u.user_id = ug.user_id AND ug.group_id = $group_id";
		}

		// Sorting and order
		if (!isset($sort_key_sql[$sort_key]))
		{
			$sort_key = $default_key;
		}

		$order_by .= $sort_key_sql[$sort_key] . ' ' . (($sort_dir == 'a') ? 'ASC' : 'DESC');

		// Unfortunately we must do this here for sorting by rank, else the sort order is applied wrongly
		if ($sort_key == 'm')
		{
			$order_by .= ', u.user_posts DESC';
		}

		/**
		 * Modify sql query data for members search
		 *
		 * @event core.memberlist_modify_sql_query_data
		 * @var	string	order_by		SQL ORDER BY clause condition
		 * @var	string	sort_dir		The sorting direction
		 * @var	string	sort_key		The sorting key
		 * @var	array	sort_key_sql	Arraty with the sorting conditions data
		 * @var	string	sql_from		SQL FROM clause condition
		 * @var	string	sql_select		SQL SELECT fields list
		 * @var	string	sql_where		SQL WHERE clause condition
		 * @var	string	sql_where_data	SQL WHERE clause additional conditions data
		 * @since 3.1.7-RC1
		 */
		$vars = array(
			'order_by',
			'sort_dir',
			'sort_key',
			'sort_key_sql',
			'sql_from',
			'sql_select',
			'sql_where',
			'sql_where_data',
		);
		extract($this->dispatcher->trigger_event('core.memberlist_modify_sql_query_data', compact($vars)));

		// Count the users ...
		$sql = 'SELECT COUNT(u.user_id) AS total_users
			FROM ' . USERS_TABLE . " u$sql_from
			WHERE " . $this->db->sql_in_set('u.user_type', $user_types) . "
			$sql_where";
		$result = $this->db->sql_query($sql);
		$total_users = (int) $this->db->sql_fetchfield('total_users');
		$this->db->sql_freeresult($result);

		// Build a relevant pagination_url
		$params = $sort_params = array();

		// We do not use $this->request->variable() here directly to save some calls (not all variables are set)
		$check_params = array(
			'g'				=> array('g', 0),
			'sk'			=> array('sk', $default_key),
			'sd'			=> array('sd', 'a'),
			'form'			=> array('form', ''),
			'field'			=> array('field', ''),
			'select_single'	=> array('select_single', $select_single),
			'username'		=> array('username', '', true),
			'email'			=> array('email', ''),
			'jabber'		=> array('jabber', ''),
			'search_group_id'	=> array('search_group_id', 0),
			'joined_select'	=> array('joined_select', 'lt'),
			'active_select'	=> array('active_select', 'lt'),
			'count_select'	=> array('count_select', 'eq'),
			'joined'		=> array('joined', ''),
			'active'		=> array('active', ''),
			'count'			=> ($this->request->variable('count', '') !== '') ? array('count', 0) : array('count', ''),
			'ip'			=> array('ip', ''),
			'first_char'	=> array('first_char', ''),
		);

		$u_first_char_params = array();
		foreach ($check_params as $key => $call)
		{
			if (!isset($_REQUEST[$key]))
			{
				continue;
			}

			$param = call_user_func_array(array($request, 'variable'), $call);
			// Encode strings, convert everything else to int in order to prevent empty parameters.
			$param = urlencode($key) . '=' . ((is_string($param)) ? urlencode($param) : (int) $param);
			$params[] = $param;

			if ($key != 'first_char')
			{
				$u_first_char_params[] = $param;
			}
			if ($key != 'sk' && $key != 'sd')
			{
				$sort_params[] = $param;
			}
		}

		$u_hide_find_member = append_sid("{$this->root_path}memberlist.$phpEx", "start=$start" . (!empty($params) ? '&amp;' . implode('&amp;', $params) : ''));

		if ($mode)
		{
			$params[] = "mode=$mode";
			$u_first_char_params[] = "mode=$mode";
		}
		$sort_params[] = "mode=$mode";

		$pagination_url = append_sid("{$this->root_path}memberlist.$phpEx", implode('&amp;', $params));
		$sort_url = append_sid("{$this->root_path}memberlist.$phpEx", implode('&amp;', $sort_params));

		unset($search_params, $sort_params);

		$u_first_char_params = implode('&amp;', $u_first_char_params);
		$u_first_char_params .= ($u_first_char_params) ? '&amp;' : '';

		$first_characters = array();
		$first_characters[''] = $this->user->lang['ALL'];
		for ($i = 97; $i < 123; $i++)
		{
			$first_characters[chr($i)] = chr($i - 32);
		}
		$first_characters['other'] = $this->user->lang['OTHER'];

		foreach ($first_characters as $char => $desc)
		{
			$this->template->assign_block_vars('first_char', array(
				'DESC'			=> $desc,
				'VALUE'			=> $char,
				'S_SELECTED'	=> ($first_char == $char) ? true : false,
				'U_SORT'		=> append_sid("{$this->root_path}memberlist.$phpEx", $u_first_char_params . 'first_char=' . $char) . '#memberlist',
			));
		}

		// Some search user specific data
		if (($mode == '' || $mode == 'searchuser') && ($this->config['load_search'] || $this->auth->acl_get('a_')))
		{
			$group_selected = $this->request->variable('search_group_id', 0);
			$s_group_select = '<option value="0"' . ((!$group_selected) ? ' selected="selected"' : '') . '>&nbsp;</option>';
			$group_ids = array();

			if ($this->auth->acl_gets('a_group', 'a_groupadd', 'a_groupdel'))
			{
				$sql = 'SELECT group_id, group_name, group_type
					FROM ' . GROUPS_TABLE;

				if (!$this->config['coppa_enable'])
				{
					$sql .= " WHERE group_name <> 'REGISTERED_COPPA'";
				}

				$sql .= ' ORDER BY group_name ASC';
			}
			else
			{
				$sql = 'SELECT g.group_id, g.group_name, g.group_type
					FROM ' . GROUPS_TABLE . ' g
					LEFT JOIN ' . USER_GROUP_TABLE . ' ug
						ON (
							g.group_id = ug.group_id
							AND ug.user_id = ' . $this->user->data['user_id'] . '
							AND ug.user_pending = 0
						)
					WHERE (g.group_type <> ' . GROUP_HIDDEN . ' OR ug.user_id = ' . $this->user->data['user_id'] . ')';

				if (!$this->config['coppa_enable'])
				{
					$sql .= " AND g.group_name <> 'REGISTERED_COPPA'";
				}

				$sql .= ' ORDER BY g.group_name ASC';
			}
			$result = $this->db->sql_query($sql);

			while ($row = $this->db->sql_fetchrow($result))
			{
				$group_ids[] = $row['group_id'];
				$s_group_select .= '<option value="' . $row['group_id'] . '"' . (($group_selected == $row['group_id']) ? ' selected="selected"' : '') . '>' . $this->group_helper->get_name($row['group_name']) . '</option>';
			}
			$this->db->sql_freeresult($result);

			if ($group_selected !== 0 && !in_array($group_selected, $group_ids))
			{
				trigger_error('NO_GROUP');
			}

			$this->template->assign_vars(array(
					'USERNAME'	=> $username,
					'EMAIL'		=> $email,
					'JABBER'	=> $jabber,
					'JOINED'	=> implode('-', $joined),
					'ACTIVE'	=> implode('-', $active),
					'COUNT'		=> $count,
					'IP'		=> $ipdomain,

					'S_IP_SEARCH_ALLOWED'	=> ($this->auth->acl_getf_global('m_info')) ? true : false,
					'S_EMAIL_SEARCH_ALLOWED'=> ($this->auth->acl_get('a_user')) ? true : false,
					'S_JABBER_ENABLED'		=> $this->config['jab_enable'],
					'S_IN_SEARCH_POPUP'		=> ($form && $field) ? true : false,
					'S_SEARCH_USER'			=> ($mode == 'searchuser' || ($mode == '' && $submit)),
					'S_FORM_NAME'			=> $form,
					'S_FIELD_NAME'			=> $field,
					'S_SELECT_SINGLE'		=> $select_single,
					'S_COUNT_OPTIONS'		=> $s_find_count,
					'S_SORT_OPTIONS'		=> $s_sort_key,
					'S_JOINED_TIME_OPTIONS'	=> $s_find_join_time,
					'S_ACTIVE_TIME_OPTIONS'	=> $s_find_active_time,
					'S_GROUP_SELECT'		=> $s_group_select,
					'S_USER_SEARCH_ACTION'	=> append_sid("{$this->root_path}memberlist.$phpEx", "mode=searchuser&amp;form=$form&amp;field=$field"))
			);
		}

		$start = $pagination->validate_start($start, $this->config['topics_per_page'], $total_users);

		// Get us some users :D
		$sql = "SELECT u.user_id
			FROM " . USERS_TABLE . " u
				$sql_from
			WHERE " . $this->db->sql_in_set('u.user_type', $user_types) . "
				$sql_where
			ORDER BY $order_by";
		$result = $this->db->sql_query_limit($sql, $this->config['topics_per_page'], $start);

		$user_list = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$user_list[] = (int) $row['user_id'];
		}
		$this->db->sql_freeresult($result);

		// Load custom profile fields
		if ($this->config['load_cpf_memberlist'])
		{
			/* @var $cp \phpbb\profilefields\manager */
			$cp = $phpbb_container->get('profilefields.manager');

			$cp_row = $cp->generate_profile_fields_template_headlines('field_show_on_ml');
			foreach ($cp_row as $profile_field)
			{
				$this->template->assign_block_vars('custom_fields', $profile_field);
			}
		}

		$leaders_set = false;
		// So, did we get any users?
		if (sizeof($user_list))
		{
			// Session time?! Session time...
			$sql = 'SELECT session_user_id, MAX(session_time) AS session_time
				FROM ' . SESSIONS_TABLE . '
				WHERE session_time >= ' . (time() - $this->config['session_length']) . '
					AND ' . $this->db->sql_in_set('session_user_id', $user_list) . '
				GROUP BY session_user_id';
			$result = $this->db->sql_query($sql);

			$session_times = array();
			while ($row = $this->db->sql_fetchrow($result))
			{
				$session_times[$row['session_user_id']] = $row['session_time'];
			}
			$this->db->sql_freeresult($result);

			// Do the SQL thang
			if ($mode == 'group')
			{
				$sql = "SELECT u.*
						$sql_select
					FROM " . USERS_TABLE . " u
						$sql_from
					WHERE " . $this->db->sql_in_set('u.user_id', $user_list) . "
						$sql_where_data";
			}
			else
			{
				$sql = 'SELECT *
					FROM ' . USERS_TABLE . '
					WHERE ' . $this->db->sql_in_set('user_id', $user_list);
			}
			$result = $this->db->sql_query($sql);

			$id_cache = array();
			while ($row = $this->db->sql_fetchrow($result))
			{
				$row['session_time'] = (!empty($session_times[$row['user_id']])) ? $session_times[$row['user_id']] : 0;
				$row['last_visit'] = (!empty($row['session_time'])) ? $row['session_time'] : $row['user_lastvisit'];

				$id_cache[$row['user_id']] = $row;
			}
			$this->db->sql_freeresult($result);

			// Load custom profile fields
			if ($this->config['load_cpf_memberlist'])
			{
				// Grab all profile fields from users in id cache for later use - similar to the poster cache
				$profile_fields_cache = $cp->grab_profile_fields_data($user_list);

				// Filter the fields we don't want to show
				foreach ($profile_fields_cache as $user_id => $user_profile_fields)
				{
					foreach ($user_profile_fields as $field_ident => $profile_field)
					{
						if (!$profile_field['data']['field_show_on_ml'])
						{
							unset($profile_fields_cache[$user_id][$field_ident]);
						}
					}
				}
			}

			// If we sort by last active date we need to adjust the id cache due to user_lastvisit not being the last active date...
			if ($sort_key == 'l')
			{
				usort($user_list,  'phpbb_sort_last_active');
			}

			// do we need to display contact fields as such
			$use_contact_fields = false;

			/**
			 * Modify list of users before member row is created
			 *
			 * @event core.memberlist_memberrow_before
			 * @var array	user_list			Array containing list of users
			 * @var bool	use_contact_fields	Should we display contact fields as such?
			 * @since 3.1.7-RC1
			 */
			$vars = array('user_list', 'use_contact_fields');
			extract($this->dispatcher->trigger_event('core.memberlist_memberrow_before', compact($vars)));

			for ($i = 0, $end = sizeof($user_list); $i < $end; ++$i)
			{
				$user_id = $user_list[$i];
				$row = $id_cache[$user_id];
				$is_leader = (isset($row['group_leader']) && $row['group_leader']) ? true : false;
				$leaders_set = ($leaders_set || $is_leader);

				$cp_row = array();
				if ($this->config['load_cpf_memberlist'])
				{
					$cp_row = (isset($profile_fields_cache[$user_id])) ? $cp->generate_profile_fields_template_data($profile_fields_cache[$user_id], $use_contact_fields) : array();
				}

				$memberrow = array_merge(phpbb_show_profile($row, false, false, false), array(
					'ROW_NUMBER'		=> $i + ($start + 1),

					'S_CUSTOM_PROFILE'	=> (isset($cp_row['row']) && sizeof($cp_row['row'])) ? true : false,
					'S_GROUP_LEADER'	=> $is_leader,
					'S_INACTIVE'		=> $row['user_type'] == USER_INACTIVE,

					'U_VIEW_PROFILE'	=> get_username_string('profile', $user_id, $row['username']),
				));

				if (isset($cp_row['row']) && sizeof($cp_row['row']))
				{
					$memberrow = array_merge($memberrow, $cp_row['row']);
				}

				$this->template->assign_block_vars('memberrow', $memberrow);

				if (isset($cp_row['blockrow']) && sizeof($cp_row['blockrow']))
				{
					foreach ($cp_row['blockrow'] as $field_data)
					{
						$this->template->assign_block_vars('memberrow.custom_fields', $field_data);
					}
				}

				unset($id_cache[$user_id]);
			}
		}

		$pagination->generate_template_pagination($pagination_url, 'pagination', 'start', $total_users, $this->config['topics_per_page'], $start);

		// Generate page
		$this->template->assign_vars(array(
				'TOTAL_USERS'	=> $this->user->lang('LIST_USERS', (int) $total_users),

				'PROFILE_IMG'	=> $this->user->img('icon_user_profile', $this->user->lang['PROFILE']),
				'PM_IMG'		=> $this->user->img('icon_contact_pm', $this->user->lang['SEND_PRIVATE_MESSAGE']),
				'EMAIL_IMG'		=> $this->user->img('icon_contact_email', $this->user->lang['EMAIL']),
				'JABBER_IMG'	=> $this->user->img('icon_contact_jabber', $this->user->lang['JABBER']),
				'SEARCH_IMG'	=> $this->user->img('icon_user_search', $this->user->lang['SEARCH']),

				'U_FIND_MEMBER'			=> ($this->config['load_search'] || $this->auth->acl_get('a_')) ? append_sid("{$this->root_path}memberlist.$phpEx", 'mode=searchuser' . (($start) ? "&amp;start=$start" : '') . (!empty($params) ? '&amp;' . implode('&amp;', $params) : '')) : '',
				'U_HIDE_FIND_MEMBER'	=> ($mode == 'searchuser' || ($mode == '' && $submit)) ? $u_hide_find_member : '',
				'U_LIVE_SEARCH'			=> ($this->config['allow_live_searches']) ? append_sid("{$this->root_path}memberlist.$phpEx", 'mode=livesearch') : false,
				'U_SORT_USERNAME'		=> $sort_url . '&amp;sk=a&amp;sd=' . (($sort_key == 'a' && $sort_dir == 'a') ? 'd' : 'a'),
				'U_SORT_JOINED'			=> $sort_url . '&amp;sk=c&amp;sd=' . (($sort_key == 'c' && $sort_dir == 'd') ? 'a' : 'd'),
				'U_SORT_POSTS'			=> $sort_url . '&amp;sk=d&amp;sd=' . (($sort_key == 'd' && $sort_dir == 'd') ? 'a' : 'd'),
				'U_SORT_EMAIL'			=> $sort_url . '&amp;sk=e&amp;sd=' . (($sort_key == 'e' && $sort_dir == 'd') ? 'a' : 'd'),
				'U_SORT_ACTIVE'			=> ($this->auth->acl_get('u_viewonline')) ? $sort_url . '&amp;sk=l&amp;sd=' . (($sort_key == 'l' && $sort_dir == 'd') ? 'a' : 'd') : '',
				'U_SORT_RANK'			=> $sort_url . '&amp;sk=m&amp;sd=' . (($sort_key == 'm' && $sort_dir == 'd') ? 'a' : 'd'),
				'U_LIST_CHAR'			=> $sort_url . '&amp;sk=a&amp;sd=' . (($sort_key == 'l' && $sort_dir == 'd') ? 'a' : 'd'),

				'S_SHOW_GROUP'		=> ($mode == 'group') ? true : false,
				'S_VIEWONLINE'		=> $this->auth->acl_get('u_viewonline'),
				'S_LEADERS_SET'		=> $leaders_set,
				'S_MODE_SELECT'		=> $s_sort_key,
				'S_ORDER_SELECT'	=> $s_sort_dir,
				'S_MODE_ACTION'		=> $pagination_url)
		);
	}

	public function profile()
	{
		// Display a profile
		if ($user_id == ANONYMOUS && !$username)
		{
			trigger_error('NO_USER');
		}

		// Get user...
		$sql = 'SELECT *
			FROM ' . USERS_TABLE . '
			WHERE ' . (($username) ? "username_clean = '" . $this->db->sql_escape(utf8_clean_string($username)) . "'" : "user_id = $user_id");
		$result = $this->db->sql_query($sql);
		$member = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if (!$member)
		{
			trigger_error('NO_USER');
		}

		// a_user admins and founder are able to view inactive users and bots to be able to manage them more easily
		// Normal users are able to see at least users having only changed their profile settings but not yet reactivated.
		if (!$this->auth->acl_get('a_user') && $this->user->data['user_type'] != USER_FOUNDER)
		{
			if ($member['user_type'] == USER_IGNORE)
			{
				trigger_error('NO_USER');
			}
			else if ($member['user_type'] == USER_INACTIVE && $member['user_inactive_reason'] != INACTIVE_PROFILE)
			{
				trigger_error('NO_USER');
			}
		}

		$user_id = (int) $member['user_id'];

		// Get group memberships
		// Also get visiting user's groups to determine hidden group memberships if necessary.
		$auth_hidden_groups = ($user_id === (int) $this->user->data['user_id'] || $this->auth->acl_gets('a_group', 'a_groupadd', 'a_groupdel')) ? true : false;
		$sql_uid_ary = ($auth_hidden_groups) ? array($user_id) : array($user_id, (int) $this->user->data['user_id']);

		// Do the SQL thang
		$sql = 'SELECT g.group_id, g.group_name, g.group_type, ug.user_id
			FROM ' . GROUPS_TABLE . ' g, ' . USER_GROUP_TABLE . ' ug
			WHERE ' . $this->db->sql_in_set('ug.user_id', $sql_uid_ary) . '
				AND g.group_id = ug.group_id
				AND ug.user_pending = 0';
		$result = $this->db->sql_query($sql);

		// Divide data into profile data and current user data
		$profile_groups = $user_groups = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$row['user_id'] = (int) $row['user_id'];
			$row['group_id'] = (int) $row['group_id'];

			if ($row['user_id'] == $user_id)
			{
				$profile_groups[] = $row;
			}
			else
			{
				$user_groups[$row['group_id']] = $row['group_id'];
			}
		}
		$this->db->sql_freeresult($result);

		// Filter out hidden groups and sort groups by name
		$group_data = $group_sort = array();
		foreach ($profile_groups as $row)
		{
			if (!$auth_hidden_groups && $row['group_type'] == GROUP_HIDDEN && !isset($user_groups[$row['group_id']]))
			{
				// Skip over hidden groups the user cannot see
				continue;
			}

			$row['group_name'] = $this->group_helper->get_name($row['group_name']);

			$group_sort[$row['group_id']] = utf8_clean_string($row['group_name']);
			$group_data[$row['group_id']] = $row;
		}
		unset($profile_groups);
		unset($user_groups);
		asort($group_sort);

		$group_options = '';
		foreach ($group_sort as $group_id => $null)
		{
			$row = $group_data[$group_id];

			$group_options .= '<option value="' . $row['group_id'] . '"' . (($row['group_id'] == $member['group_id']) ? ' selected="selected"' : '') . '>' . $row['group_name'] . '</option>';
		}
		unset($group_data);
		unset($group_sort);

		// What colour is the zebra
		$sql = 'SELECT friend, foe
			FROM ' . ZEBRA_TABLE . "
			WHERE zebra_id = $user_id
				AND user_id = {$this->user->data['user_id']}";

		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$foe = ($row['foe']) ? true : false;
		$friend = ($row['friend']) ? true : false;
		$this->db->sql_freeresult($result);

		if ($this->config['load_onlinetrack'])
		{
			$sql = 'SELECT MAX(session_time) AS session_time, MIN(session_viewonline) AS session_viewonline
				FROM ' . SESSIONS_TABLE . "
				WHERE session_user_id = $user_id";
			$result = $this->db->sql_query($sql);
			$row = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);

			$member['session_time'] = (isset($row['session_time'])) ? $row['session_time'] : 0;
			$member['session_viewonline'] = (isset($row['session_viewonline'])) ? $row['session_viewonline'] :	0;
			unset($row);
		}

		if ($this->config['load_user_activity'])
		{
			display_user_activity($member);
		}

		// Do the relevant calculations
		$memberdays = max(1, round((time() - $member['user_regdate']) / 86400));
		$posts_per_day = $member['user_posts'] / $memberdays;
		$percentage = ($this->config['num_posts']) ? min(100, ($member['user_posts'] / $this->config['num_posts']) * 100) : 0;


		if ($member['user_sig'])
		{
			$parse_flags = ($member['user_sig_bbcode_bitfield'] ? OPTION_FLAG_BBCODE : 0) | OPTION_FLAG_SMILIES;
			$member['user_sig'] = generate_text_for_display($member['user_sig'], $member['user_sig_bbcode_uid'], $member['user_sig_bbcode_bitfield'], $parse_flags, true);
		}

		// We need to check if the modules 'zebra' ('friends' & 'foes' mode),  'notes' ('user_notes' mode) and  'warn' ('warn_user' mode) are accessible to decide if we can display appropriate links
		$zebra_enabled = $friends_enabled = $foes_enabled = $user_notes_enabled = $warn_user_enabled = false;

		// Only check if the user is logged in
		if ($this->user->data['is_registered'])
		{
			if (!class_exists('p_master'))
			{
				include($this->root_path . 'includes/functions_module.' . $phpEx);
			}
			$module = new p_master();

			$module->list_modules('ucp');
			$module->list_modules('mcp');

			$user_notes_enabled = ($module->loaded('mcp_notes', 'user_notes')) ? true : false;
			$warn_user_enabled = ($module->loaded('mcp_warn', 'warn_user')) ? true : false;
			$zebra_enabled = ($module->loaded('ucp_zebra')) ? true : false;
			$friends_enabled = ($module->loaded('ucp_zebra', 'friends')) ? true : false;
			$foes_enabled = ($module->loaded('ucp_zebra', 'foes')) ? true : false;

			unset($module);
		}

		// Custom Profile Fields
		$profile_fields = array();
		if ($this->config['load_cpf_viewprofile'])
		{
			/* @var $cp \phpbb\profilefields\manager */
			$cp = $phpbb_container->get('profilefields.manager');
			$profile_fields = $cp->grab_profile_fields_data($user_id);
			$profile_fields = (isset($profile_fields[$user_id])) ? $cp->generate_profile_fields_template_data($profile_fields[$user_id]) : array();
		}

		/**
		 * Modify user data before we display the profile
		 *
		 * @event core.memberlist_view_profile
		 * @var	array	member					Array with user's data
		 * @var	bool	user_notes_enabled		Is the mcp user notes module enabled?
		 * @var	bool	warn_user_enabled		Is the mcp warnings module enabled?
		 * @var	bool	zebra_enabled			Is the ucp zebra module enabled?
		 * @var	bool	friends_enabled			Is the ucp friends module enabled?
		 * @var	bool	foes_enabled			Is the ucp foes module enabled?
		 * @var	bool    friend					Is the user friend?
		 * @var	bool	foe						Is the user foe?
		 * @var	array	profile_fields			Array with user's profile field data
		 * @since 3.1.0-a1
		 * @changed 3.1.0-b2 Added friend and foe status
		 * @changed 3.1.0-b3 Added profile fields data
		 */
		$vars = array(
			'member',
			'user_notes_enabled',
			'warn_user_enabled',
			'zebra_enabled',
			'friends_enabled',
			'foes_enabled',
			'friend',
			'foe',
			'profile_fields',
		);
		extract($this->dispatcher->trigger_event('core.memberlist_view_profile', compact($vars)));

		$this->template->assign_vars(phpbb_show_profile($member, $user_notes_enabled, $warn_user_enabled));

		// If the user has m_approve permission or a_user permission, then list then display unapproved posts
		if ($this->auth->acl_getf_global('m_approve') || $this->auth->acl_get('a_user'))
		{
			$sql = 'SELECT COUNT(post_id) as posts_in_queue
				FROM ' . POSTS_TABLE . '
				WHERE poster_id = ' . $user_id . '
					AND ' . $this->db->sql_in_set('post_visibility', array(ITEM_UNAPPROVED, ITEM_REAPPROVE));
			$result = $this->db->sql_query($sql);
			$member['posts_in_queue'] = (int) $this->db->sql_fetchfield('posts_in_queue');
			$this->db->sql_freeresult($result);
		}
		else
		{
			$member['posts_in_queue'] = 0;
		}

		$this->template->assign_vars(array(
			'L_POSTS_IN_QUEUE'	=> $this->user->lang('NUM_POSTS_IN_QUEUE', $member['posts_in_queue']),

			'POSTS_DAY'			=> $this->user->lang('POST_DAY', $posts_per_day),
			'POSTS_PCT'			=> $this->user->lang('POST_PCT', $percentage),

			'SIGNATURE'		=> $member['user_sig'],
			'POSTS_IN_QUEUE'=> $member['posts_in_queue'],

			'PM_IMG'		=> $this->user->img('icon_contact_pm', $this->user->lang['SEND_PRIVATE_MESSAGE']),
			'L_SEND_EMAIL_USER'	=> $this->user->lang('SEND_EMAIL_USER', $member['username']),
			'EMAIL_IMG'		=> $this->user->img('icon_contact_email', $this->user->lang['EMAIL']),
			'JABBER_IMG'	=> $this->user->img('icon_contact_jabber', $this->user->lang['JABBER']),
			'SEARCH_IMG'	=> $this->user->img('icon_user_search', $this->user->lang['SEARCH']),

			'S_PROFILE_ACTION'	=> append_sid("{$this->root_path}memberlist.$phpEx", 'mode=group'),
			'S_GROUP_OPTIONS'	=> $group_options,
			'S_CUSTOM_FIELDS'	=> (isset($profile_fields['row']) && sizeof($profile_fields['row'])) ? true : false,

			'U_USER_ADMIN'			=> ($this->auth->acl_get('a_user')) ? append_sid("{$phpbb_admin_path}index.$phpEx", 'i=users&amp;mode=overview&amp;u=' . $user_id, true, $this->user->session_id) : '',
			'U_USER_BAN'			=> ($this->auth->acl_get('m_ban') && $user_id != $this->user->data['user_id']) ? append_sid("{$this->root_path}mcp.$phpEx", 'i=ban&amp;mode=user&amp;u=' . $user_id, true, $this->user->session_id) : '',
			'U_MCP_QUEUE'			=> ($this->auth->acl_getf_global('m_approve')) ? append_sid("{$this->root_path}mcp.$phpEx", 'i=queue', true, $this->user->session_id) : '',

			'U_SWITCH_PERMISSIONS'	=> ($this->auth->acl_get('a_switchperm') && $this->user->data['user_id'] != $user_id) ? append_sid("{$this->root_path}ucp.$phpEx", "mode=switch_perm&amp;u={$user_id}&amp;hash=" . generate_link_hash('switchperm')) : '',
			'U_EDIT_SELF'			=> ($user_id == $this->user->data['user_id'] && $this->auth->acl_get('u_chgprofileinfo')) ? append_sid("{$this->root_path}ucp.$phpEx", 'i=ucp_profile&amp;mode=profile_info') : '',

			'S_USER_NOTES'		=> ($user_notes_enabled) ? true : false,
			'S_WARN_USER'		=> ($warn_user_enabled) ? true : false,
			'S_ZEBRA'			=> ($this->user->data['user_id'] != $user_id && $this->user->data['is_registered'] && $zebra_enabled) ? true : false,
			'U_ADD_FRIEND'		=> (!$friend && !$foe && $friends_enabled) ? append_sid("{$this->root_path}ucp.$phpEx", 'i=zebra&amp;add=' . urlencode(htmlspecialchars_decode($member['username']))) : '',
			'U_ADD_FOE'			=> (!$friend && !$foe && $foes_enabled) ? append_sid("{$this->root_path}ucp.$phpEx", 'i=zebra&amp;mode=foes&amp;add=' . urlencode(htmlspecialchars_decode($member['username']))) : '',
			'U_REMOVE_FRIEND'	=> ($friend && $friends_enabled) ? append_sid("{$this->root_path}ucp.$phpEx", 'i=zebra&amp;remove=1&amp;usernames[]=' . $user_id) : '',
			'U_REMOVE_FOE'		=> ($foe && $foes_enabled) ? append_sid("{$this->root_path}ucp.$phpEx", 'i=zebra&amp;remove=1&amp;mode=foes&amp;usernames[]=' . $user_id) : '',

			'U_CANONICAL'	=> generate_board_url() . '/' . append_sid("memberlist.$phpEx", 'mode=viewprofile&amp;u=' . $user_id, true, ''),
		));

		if (!empty($profile_fields['row']))
		{
			$this->template->assign_vars($profile_fields['row']);
		}

		if (!empty($profile_fields['blockrow']))
		{
			foreach ($profile_fields['blockrow'] as $field_data)
			{
				$this->template->assign_block_vars('custom_fields', $field_data);
			}
		}

		// Inactive reason/account?
		if ($member['user_type'] == USER_INACTIVE)
		{
			$this->user->add_lang('acp/common');

			$inactive_reason = $this->user->lang['INACTIVE_REASON_UNKNOWN'];

			switch ($member['user_inactive_reason'])
			{
				case INACTIVE_REGISTER:
					$inactive_reason = $this->user->lang['INACTIVE_REASON_REGISTER'];
					break;

				case INACTIVE_PROFILE:
					$inactive_reason = $this->user->lang['INACTIVE_REASON_PROFILE'];
					break;

				case INACTIVE_MANUAL:
					$inactive_reason = $this->user->lang['INACTIVE_REASON_MANUAL'];
					break;

				case INACTIVE_REMIND:
					$inactive_reason = $this->user->lang['INACTIVE_REASON_REMIND'];
					break;
			}

			$this->template->assign_vars(array(
					'S_USER_INACTIVE'		=> true,
					'USER_INACTIVE_REASON'	=> $inactive_reason)
			);
		}

		return $this->helper->render('memberlist_view.html', $this->language->lang('VIEWING_PROFILE', $member['username']));
	}

	public function group()
	{

	}

	public function search()
	{

	}
}
