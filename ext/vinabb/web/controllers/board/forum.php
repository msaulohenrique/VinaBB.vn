<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\controllers\board;

use vinabb\web\includes\constants;

/**
* Controller for the forum page
*/
class forum implements forum_interface
{
	/** @var \phpbb\auth\auth $auth */
	protected $auth;

	/** @var \phpbb\cache\service $cache */
	protected $cache;

	/** @var \phpbb\config\config $config */
	protected $config;

	/** @var \phpbb\content_visibility $content_visibility */
	protected $content_visibility;

	/** @var \phpbb\cron\manager $cron */
	protected $cron;

	/** @var \phpbb\db\driver\driver_interface $db */
	protected $db;

	/** @var \phpbb\language\language $language */
	protected $language;

	/** @var \vinabb\web\controllers\pagination $pagination */
	protected $pagination;

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

	/** @var array $forum_data */
	protected $forum_data;

	/** @var int $start */
	protected $start;

	/**
	* Constructor
	*
	* @param \phpbb\auth\auth							$auth				Authentication object
	* @param \phpbb\cache\service						$cache				Cache service
	* @param \phpbb\config\config						$config				Config object
	* @param \phpbb\content_visibility					$content_visibility	Content visibility
	* @param \phpbb\cron\manager						$cron				Cron manager
	* @param \phpbb\db\driver\driver_interface			$db					Database object
	* @param \phpbb\language\language					$language			Language object
	* @param \vinabb\web\controllers\pagination			$pagination			Pagination object
	* @param \phpbb\request\request						$request			Request object
	* @param \phpbb\template\template					$template			Template object
	* @param \phpbb\user								$user				User object
	* @param \phpbb\controller\helper					$helper				Controller helper
	* @param string										$root_path			phpBB root path
	* @param string										$php_ext			PHP file extension
	*/
	public function __construct(
		\phpbb\auth\auth $auth,
		\phpbb\cache\service $cache,
		\phpbb\config\config $config,
		\phpbb\content_visibility $content_visibility,
		\phpbb\cron\manager $cron,
		\phpbb\db\driver\driver_interface $db,
		\phpbb\language\language $language,
		\vinabb\web\controllers\pagination $pagination,
		\phpbb\request\request $request,
		\phpbb\template\template $template,
		\phpbb\user $user,
		\phpbb\controller\helper $helper,
		$root_path,
		$php_ext
	)
	{
		$this->auth = $auth;
		$this->cache = $cache;
		$this->config = $config;
		$this->content_visibility = $content_visibility;
		$this->cron = $cron;
		$this->db = $db;
		$this->language = $language;
		$this->pagination = $pagination;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->helper = $helper;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;
	}

	/**
	* Main method
	*
	* @param int 	$forum_id	Forum ID
	* @param string $page		Page number
	*/
	public function main($forum_id, $page)
	{
		global $_SID, $_EXTRA_URL;

		// Common functions
		include "{$this->root_path}includes/functions_display.{$this->php_ext}";

		$page = max(1, floor(str_replace(constants::REWRITE_URL_PAGE, '', $page)));
		$this->start = floor(($page - 1) * $this->config['topics_per_page']);

		// Start initial var setup
		$default_sort_days = (!empty($this->user->data['user_topic_show_days'])) ? $this->user->data['user_topic_show_days'] : 0;
		$default_sort_key = (!empty($this->user->data['user_topic_sortby_type'])) ? $this->user->data['user_topic_sortby_type'] : 't';
		$default_sort_dir = (!empty($this->user->data['user_topic_sortby_dir'])) ? $this->user->data['user_topic_sortby_dir'] : 'd';

		$sort_days = $this->request->variable('st', $default_sort_days);
		$sort_key = $this->request->variable('sk', $default_sort_key);
		$sort_dir = $this->request->variable('sd', $default_sort_dir);

		$this->get_forum_data($forum_id);

		// Configure style, language, etc.
		$this->user->setup('viewforum', $this->forum_data['forum_style']);

		$this->require_login();

		// Is this forum a link? ... User got here either because the
		// number of clicks is being tracked or they guessed the id
		$this->update_click_counter();

		// Build navigation links
		generate_forum_nav($this->forum_data);

		// Forum Rules
		generate_forum_rules($this->forum_data);

		// Do we have subforums?
		$active_forum_ary = $moderators = [];

		if ($this->forum_data['left_id'] != $this->forum_data['right_id'] - 1)
		{
			list($active_forum_ary, $moderators) = display_forums($this->forum_data, $this->config['load_moderators'], $this->config['load_moderators']);
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
		$topics_count = $this->content_visibility->get_count('forum_topics', $this->forum_data, $forum_id);
		$this->start = $this->pagination->validate_start($this->start, $this->config['topics_per_page'], $topics_count);

		page_header($this->forum_data['forum_name'] . ($this->start ? ' - ' . $this->language->lang('PAGE_TITLE_NUMBER', $this->pagination->get_on_page($this->config['topics_per_page'], $this->start)) : ''), true, $forum_id);

		$this->template->set_filenames([
			'body'	=> 'viewforum_body.html'
		]);

		$this->template->assign_vars([
			'S_VIEWFORUM'	=> true,
			'U_VIEW_FORUM'	=> $this->helper->route('vinabb_web_board_forum_route', ($this->start == 0) ? ['forum_id' => $forum_id] : ['forum_id' => $forum_id, 'seo' => $this->forum_data['forum_name_seo'] . constants::REWRITE_URL_SEO, 'page' => constants::REWRITE_URL_PAGE . $this->pagination->get_on_page($this->config['topics_per_page'], $this->start)])
		]);

		// Not postable forum or showing active topics?
		if (!($this->forum_data['forum_type'] == FORUM_POST || (($this->forum_data['forum_flags'] & FORUM_FLAG_ACTIVE_TOPICS) && $this->forum_data['forum_type'] == FORUM_CAT)))
		{
			page_footer();
		}

		// Ok, if someone has only list-access, we only display the forum list.
		// We also make this circumstance available to the template in case we want to display a notice. ;)
		if (!$this->auth->acl_get('f_read', $forum_id))
		{
			$this->template->assign_var('S_NO_READ_ACCESS', true);

			page_footer();
		}

		// Handle marking topics
		$this->mark_topics();

		// Is a forum specific topic count required?
		if ($this->forum_data['forum_topics_per_page'])
		{
			$this->config['topics_per_page'] = $this->forum_data['forum_topics_per_page'];
		}

		// Do the forum Prune thang - cron type job...
		$this->run_cron_tasks();

		// Forum subscription
		$this->subscribe_forum();

		// Forum posting permission list
		gen_forum_auth_level('forum', $forum_id, $this->forum_data['forum_status']);

		// Topic ordering options
		$limit_days = [
			0	=> $this->language->lang('ALL_TOPICS'),
			1	=> $this->language->lang('1_DAY'),
			7	=> $this->language->lang('7_DAYS'),
			14	=> $this->language->lang('2_WEEKS'),
			30	=> $this->language->lang('1_MONTH'),
			90	=> $this->language->lang('3_MONTHS'),
			180	=> $this->language->lang('6_MONTHS'),
			365	=> $this->language->lang('1_YEAR')
		];

		$sort_by_text = [
			'a'	=> $this->language->lang('AUTHOR'),
			't'	=> $this->language->lang('POST_TIME'),
			'r'	=> $this->language->lang('REPLIES'),
			's'	=> $this->language->lang('SUBJECT'),
			'v'	=> $this->language->lang('VIEWS')
		];

		$sort_by_sql = [
			'a'	=> 't.topic_first_poster_name',
			't'	=> ['t.topic_last_post_time', 't.topic_last_post_id'],
			'r'	=> (($this->auth->acl_get('m_approve', $forum_id)) ? 't.topic_posts_approved + t.topic_posts_unapproved + t.topic_posts_softdeleted' : 't.topic_posts_approved'),
			's'	=> $this->db->sql_lower_text('t.topic_title'),
			'v'	=> 't.topic_views'
		];

		$s_limit_days = $s_sort_key = $s_sort_dir = $u_sort_param = '';

		gen_sort_selects($limit_days, $sort_by_text, $sort_days, $sort_key, $sort_dir, $s_limit_days, $s_sort_key, $s_sort_dir, $u_sort_param, $default_sort_days, $default_sort_key, $default_sort_dir);

		// Convert $u_sort_param from string to array
		$u_sort_param_ary = [];

		if ($u_sort_param != '')
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

			$sql_array = [
				'SELECT'	=> 'COUNT(t.topic_id) AS num_topics',
				'FROM'		=> [TOPICS_TABLE => 't'],
				'WHERE'		=> 't.forum_id = ' . $forum_id . '
					AND (t.topic_last_post_time >= ' . $min_post_time . '
						OR t.topic_type = ' . POST_ANNOUNCE . '
						OR t.topic_type = ' . POST_GLOBAL . ')
					AND ' . $this->content_visibility->get_visibility_sql('topic', $forum_id, 't.')
			];
			$result = $this->db->sql_query($this->db->sql_build_query('SELECT', $sql_array));
			$topics_count = (int) $this->db->sql_fetchfield('num_topics');
			$this->db->sql_freeresult($result);

			if ($this->request->is_set_post('sort'))
			{
				$this->start = 0;
			}

			$sql_limit_time = "AND t.topic_last_post_time >= $min_post_time";

			// Make sure we have information about day selection ready
			$this->template->assign_var('S_SORT_DAYS', true);
		}
		else
		{
			$sql_limit_time = '';
		}

		// Display active topics?
		$s_display_active = ($this->forum_data['forum_type'] == FORUM_CAT && ($this->forum_data['forum_flags'] & FORUM_FLAG_ACTIVE_TOPICS)) ? true : false;

		// Search hidden fields
		$s_search_hidden_fields = ['fid' => [$forum_id]];

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
		$forum_url_params = ['forum_id' => $forum_id];

		if ($this->start)
		{
			$forum_url_params['page'] = constants::REWRITE_URL_PAGE . $this->pagination->get_on_page($this->config['topics_per_page'], $this->start);
		}

		$forum_url_params['seo'] = $this->forum_data['forum_name_seo'] . constants::REWRITE_URL_SEO;
		$forum_url_sort_params = $forum_url_params;

		if (sizeof($u_sort_param_ary))
		{
			$forum_url_sort_params = array_merge($forum_url_sort_params, $u_sort_param_ary);
		}

		// Output
		$this->template->assign_vars([
			'MODERATORS'	=> (!empty($moderators[$forum_id])) ? implode($this->language->lang('COMMA_SEPARATOR'), $moderators[$forum_id]) : '',

			'L_NO_TOPICS' 			=> ($this->forum_data['forum_status'] == ITEM_LOCKED) ? $this->language->lang('POST_FORUM_LOCKED') : $this->language->lang('NO_TOPICS'),

			'S_DISPLAY_POST_INFO'	=> ($this->forum_data['forum_type'] == FORUM_POST && ($this->auth->acl_get('f_post', $forum_id) || $this->user->data['user_id'] == ANONYMOUS)) ? true : false,

			'S_IS_POSTABLE'					=> $this->forum_data['forum_type'] == FORUM_POST,
			'S_USER_CAN_POST'				=> $this->auth->acl_get('f_post', $forum_id),
			'S_DISPLAY_ACTIVE'				=> $s_display_active,
			'S_SELECT_SORT_DIR'				=> $s_sort_dir,
			'S_SELECT_SORT_KEY'				=> $s_sort_key,
			'S_SELECT_SORT_DAYS'			=> $s_limit_days,
			'S_TOPIC_ICONS'					=> ($s_display_active && sizeof($active_forum_ary)) ? max($active_forum_ary['enable_icons']) : (($this->forum_data['enable_icons']) ? true : false),
			'S_FORUM_ACTION'				=> $this->helper->route('vinabb_web_board_forum_route', $forum_url_params),
			'S_DISPLAY_SEARCHBOX'			=> ($this->auth->acl_get('u_search') && $this->auth->acl_get('f_search', $forum_id) && $this->config['load_search']),
			'S_SEARCHBOX_ACTION'			=> append_sid("{$this->root_path}search.{$this->php_ext}"),
			'S_SEARCH_LOCAL_HIDDEN_FIELDS'	=> build_hidden_fields($s_search_hidden_fields),
			'S_SINGLE_MODERATOR'			=> (!empty($moderators[$forum_id]) && sizeof($moderators[$forum_id]) > 1) ? false : true,
			'S_IS_LOCKED'					=> $this->forum_data['forum_status'] == ITEM_LOCKED,
			'S_VIEWFORUM'					=> true,

			'U_MCP'				=> ($this->auth->acl_get('m_', $forum_id)) ? $this->helper->route('vinabb_web_mcp_route', ['id' => 'main', 'mode' => 'forum_view', 'f' => $forum_id], true, $this->user->session_id) : '',
			'U_POST_NEW_TOPIC'	=> ($this->auth->acl_get('f_post', $forum_id) || $this->user->data['user_id'] == ANONYMOUS) ? append_sid("{$this->root_path}posting.{$this->php_ext}", 'mode=post&amp;f=' . $forum_id) : '',
			'U_VIEW_FORUM'		=> $this->helper->route('vinabb_web_board_forum_route', $forum_url_sort_params),
			'U_CANONICAL'		=> generate_board_url(true) . htmlspecialchars_decode($this->helper->route('vinabb_web_board_forum_route', $forum_url_params)),
			'U_MARK_TOPICS'		=> ($this->user->data['is_registered'] || $this->config['load_anon_lastread']) ? $this->helper->route('vinabb_web_board_forum_route', ['forum_id' => $forum_id, 'seo' => $this->forum_data['forum_name_seo'] . constants::REWRITE_URL_SEO, 'hash' => generate_link_hash('global'), 'mark' => 'topics', 'mark_time' => time()]) : '',
		]);

		// Grab icons
		$icons = $this->cache->obtain_icons();

		// Grab all topic data
		$rowset = $announcement_list = $topic_list = $global_announce_forums = [];

		$sql_array = [
			'SELECT'	=> 't.*',
			'FROM'		=> [TOPICS_TABLE => 't'],
			'LEFT_JOIN'	=> []
		];

		$sql_approved = ' AND ' . $this->content_visibility->get_visibility_sql('topic', $forum_id, 't.');

		if ($this->user->data['is_registered'])
		{
			if ($this->config['load_db_track'])
			{
				$sql_array['LEFT_JOIN'][] = ['FROM' => [TOPICS_POSTED_TABLE => 'tp'], 'ON' => 'tp.topic_id = t.topic_id AND tp.user_id = ' . $this->user->data['user_id']];
				$sql_array['SELECT'] .= ', tp.topic_posted';
			}

			if ($this->config['load_db_lastread'])
			{
				$sql_array['LEFT_JOIN'][] = ['FROM' => [TOPICS_TRACK_TABLE => 'tt'], 'ON' => 'tt.topic_id = t.topic_id AND tt.user_id = ' . $this->user->data['user_id']];
				$sql_array['SELECT'] .= ', tt.mark_time';

				if ($s_display_active && sizeof($active_forum_ary))
				{
					$sql_array['LEFT_JOIN'][] = ['FROM' => [FORUMS_TRACK_TABLE => 'ft'], 'ON' => 'ft.forum_id = t.forum_id AND ft.user_id = ' . $this->user->data['user_id']];
					$sql_array['SELECT'] .= ', ft.mark_time AS forum_mark_time';
				}
			}
		}

		if ($this->forum_data['forum_type'] == FORUM_POST)
		{
			// Get global announcement forums
			$g_forum_ary = $this->auth->acl_getf('f_read', true);
			$g_forum_ary = array_unique(array_keys($g_forum_ary));

			$sql_anounce_array['LEFT_JOIN'] = $sql_array['LEFT_JOIN'];
			$sql_anounce_array['LEFT_JOIN'][] = ['FROM' => [FORUMS_TABLE => 'f'], 'ON' => 'f.forum_id = t.forum_id'];
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

		$forum_tracking_info = [];

		if ($this->user->data['is_registered'] && $this->config['load_db_lastread'])
		{
			$forum_tracking_info[$forum_id] = $this->forum_data['mark_time'];

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

		if ($this->start > $topics_count / 2)
		{
			$store_reverse = true;

			// Select the sort order
			$direction = (($sort_dir == 'd') ? 'ASC' : 'DESC');

			$sql_limit = $this->pagination->reverse_limit($this->start, $sql_limit, $topics_count - sizeof($announcement_list));
			$sql_start = $this->pagination->reverse_start($this->start, $sql_limit, $topics_count - sizeof($announcement_list));
		}
		else
		{
			// Select the sort order
			$direction = (($sort_dir == 'd') ? 'DESC' : 'ASC');
			$sql_start = $this->start;
		}

		if (is_array($sort_by_sql[$sort_key]))
		{
			$sql_sort_order = implode(' ' . $direction . ', ', $sort_by_sql[$sort_key]) . ' ' . $direction;
		}
		else
		{
			$sql_sort_order = $sort_by_sql[$sort_key] . ' ' . $direction;
		}

		if ($this->forum_data['forum_type'] == FORUM_POST || !sizeof($active_forum_ary))
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
		$sql_ary = [
			'SELECT'	=> 't.topic_id',
			'FROM'		=> [TOPICS_TABLE => 't'],
			'WHERE'		=> "$sql_where
				AND " . $this->db->sql_in_set('t.topic_type', [POST_NORMAL, POST_STICKY]) . "
				$sql_approved
				$sql_limit_time",
			'ORDER_BY'	=> 't.topic_type ' . ((!$store_reverse) ? 'DESC' : 'ASC') . ', ' . $sql_sort_order,
		];
		$sql = $this->db->sql_build_query('SELECT', $sql_ary);
		$result = $this->db->sql_query_limit($sql, $sql_limit, $sql_start);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$topic_list[] = (int) $row['topic_id'];
		}
		$this->db->sql_freeresult($result);

		// For storing shadow topics
		$shadow_topic_list = [];

		if (sizeof($topic_list))
		{
			// SQL array for obtaining topics/stickies
			$sql_array = [
				'SELECT'	=> $sql_array['SELECT'],
				'FROM'		=> $sql_array['FROM'],
				'LEFT_JOIN'	=> $sql_array['LEFT_JOIN'],
				'WHERE'		=> $this->db->sql_in_set('t.topic_id', $topic_list)
			];

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
			$sql_array = [
				'SELECT'	=> 't.*',
				'FROM'		=> [TOPICS_TABLE => 't'],
				'WHERE'		=> $this->db->sql_in_set('t.topic_id', array_keys($shadow_topic_list))
			];
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
				$row = array_merge($row, [
					'topic_moved_id'	=> $rowset[$orig_topic_id]['topic_moved_id'],
					'topic_status'		=> $rowset[$orig_topic_id]['topic_status'],
					'topic_type'		=> $rowset[$orig_topic_id]['topic_type'],
					'topic_title'		=> $rowset[$orig_topic_id]['topic_title']
				]);

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
		unset($forum_url_sort_params['page']);
		$this->pagination->generate_template_pagination('vinabb_web_board_forum_route', $forum_url_sort_params, 'pagination', $total_topic_count, $this->config['topics_per_page'], $this->start);

		$this->template->assign_vars([
			'TOTAL_TOPICS'	=> ($s_display_active) ? false : $this->language->lang('VIEW_FORUM_TOPICS', (int) $total_topic_count)
		]);

		$topic_list = ($store_reverse) ? array_merge($announcement_list, array_reverse($topic_list)) : array_merge($announcement_list, $topic_list);
		$topic_tracking_info = $tracking_topics = [];

		// Okay, lets dump out the page ...
		if (sizeof($topic_list))
		{
			$mark_forum_read = true;
			$mark_time_forum = 0;

			// Generate topic forum list...
			$topic_forum_list = [];

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
					$mark_time_forum = (!empty($this->forum_data['mark_time'])) ? $this->forum_data['mark_time'] : $this->user->data['user_lastmark'];
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

				// Generate all the URIs...
				$view_topic_url = $this->helper->route('vinabb_web_board_topic_route', ['forum_id' => $forum_id, 'topic_id' => $topic_id, 'seo' => $row['topic_title_seo'] . constants::REWRITE_URL_SEO]);

				$topic_unapproved = (($row['topic_visibility'] == ITEM_UNAPPROVED || $row['topic_visibility'] == ITEM_REAPPROVE) && $this->auth->acl_get('m_approve', $row['forum_id']));
				$posts_unapproved = ($row['topic_visibility'] == ITEM_APPROVED && $row['topic_posts_unapproved'] && $this->auth->acl_get('m_approve', $row['forum_id']));
				$topic_deleted = $row['topic_visibility'] == ITEM_DELETED;

				$u_mcp_queue = ($topic_unapproved || $posts_unapproved) ? $this->helper->route('vinabb_web_mcp_route', ['id' => 'queue', 'mode' => (($topic_unapproved) ? 'approve_details' : 'unapproved_posts'), 't' => $topic_id], true, $this->user->session_id) : '';
				$u_mcp_queue = (!$u_mcp_queue && $topic_deleted) ? $this->helper->route('vinabb_web_mcp_route', ['id' => 'queue', 'mode' => 'deleted_topics', 't' => $topic_id], true, $this->user->session_id) : $u_mcp_queue;

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
					'FORUM_NAME'		=> (isset($row['forum_name'])) ? $row['forum_name'] : $this->forum_data['forum_name'],

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
					'S_HAS_POLL'			=> $row['poll_start'],
					'S_POST_ANNOUNCE'		=> $row['topic_type'] == POST_ANNOUNCE,
					'S_POST_GLOBAL'			=> $row['topic_type'] == POST_GLOBAL,
					'S_POST_STICKY'			=> $row['topic_type'] == POST_STICKY,
					'S_TOPIC_LOCKED'		=> $row['topic_status'] == ITEM_LOCKED,
					'S_TOPIC_MOVED'			=> $row['topic_status'] == ITEM_MOVED,

					'U_NEWEST_POST'			=> $this->helper->route('vinabb_web_board_topic_route', ['topic_id' => $topic_id, 'view' => 'unread', '#' => 'unread']),
					'U_LAST_POST'			=> $this->helper->route('vinabb_web_board_topic_route', ['topic_id' => $topic_id, '#' => 'p' . $row['topic_last_post_id']]),
					'U_LAST_POST_AUTHOR'	=> get_username_string('profile', $row['topic_last_poster_id'], $row['topic_last_poster_name'], $row['topic_last_poster_colour']),
					'U_TOPIC_AUTHOR'		=> get_username_string('profile', $row['topic_poster'], $row['topic_first_poster_name'], $row['topic_first_poster_colour']),
					'U_VIEW_TOPIC'			=> $view_topic_url,
					'U_VIEW_FORUM'			=> $this->helper->route('vinabb_web_board_forum_route', ['forum_id' => $row['forum_id'], 'seo' => $this->forum_data['forum_name_seo'] . constants::REWRITE_URL_SEO]),
					'U_MCP_REPORT'			=> $this->helper->route('vinabb_web_mcp_route', ['id' => 'reports', 'mode' => 'reports', 'f' => $row['forum_id'], 't' => $topic_id], true, $this->user->session_id),
					'U_MCP_QUEUE'			=> $u_mcp_queue,

					'S_TOPIC_TYPE_SWITCH'	=> ($s_type_switch == $s_type_switch_test) ? -1 : $s_type_switch_test,
				);

				$this->template->assign_block_vars('topicrow', $topic_row);

				$this->pagination->generate_template_pagination('vinabb_web_board_topic_route', ['topic_id' => $topic_id], 'topicrow.pagination', $replies + 1, $this->config['posts_per_page'], 1, true, true);

				$s_type_switch = ($row['topic_type'] == POST_ANNOUNCE || $row['topic_type'] == POST_GLOBAL) ? 1 : 0;

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
		if ($this->forum_data['forum_type'] == FORUM_POST && sizeof($topic_list) && $mark_forum_read)
		{
			update_forum_tracking_info($forum_id, $this->forum_data['forum_last_post_time'], false, $mark_time_forum);
		}

		page_footer();
	}

	/**
	* Get forum data
	*
	* @param int $forum_id Forum ID
	*/
	protected function get_forum_data($forum_id)
	{
		$sql_from = FORUMS_TABLE . ' f';
		$lastread_select = '';

		// Grab appropriate forum data
		if ($this->config['load_db_lastread'] && $this->user->data['is_registered'])
		{
			$sql_from .= ' LEFT JOIN ' . FORUMS_TRACK_TABLE . ' ft
				ON (ft.user_id = ' . $this->user->data['user_id'] . '
					AND ft.forum_id = f.forum_id)';
			$lastread_select .= ', ft.mark_time';
		}

		if ($this->user->data['is_registered'])
		{
			$sql_from .= ' LEFT JOIN ' . FORUMS_WATCH_TABLE . ' fw
				ON (fw.forum_id = f.forum_id
					AND fw.user_id = ' . $this->user->data['user_id'] . ')';
			$lastread_select .= ', fw.notify_status';
		}

		$sql = "SELECT f.* $lastread_select
			FROM $sql_from
			WHERE f.forum_id = $forum_id";
		$result = $this->db->sql_query($sql);
		$this->forum_data = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if ($this->forum_data === false)
		{
			trigger_error('NO_FORUM');
		}
	}

	/**
	* Checking actions on the forum
	*/
	protected function require_login()
	{
		// Redirect to login upon emailed notification links
		if ($this->request->is_set('e') && !$this->user->data['is_registered'])
		{
			login_box('', $this->language->lang('LOGIN_NOTIFY_FORUM'));
		}

		// Permissions check
		$this->require_login_auth();

		// Forum is passworded ... check whether access has been granted to this
		// user this session, if not show login box
		if ($this->forum_data['forum_password'])
		{
			login_forum_box($this->forum_data);
		}
	}

	/**
	* Sub-method for the require_login()
	*/
	protected function require_login_auth()
	{
		if (!$this->auth->acl_gets('f_list', 'f_read', $this->forum_data['forum_id']) || ($this->forum_data['forum_type'] == FORUM_LINK && $this->forum_data['forum_link'] && !$this->auth->acl_get('f_read', $this->forum_data['forum_id'])))
		{
			if ($this->user->data['user_id'] != ANONYMOUS)
			{
				send_status_line(403, 'Forbidden');
				trigger_error('SORRY_AUTH_READ');
			}

			login_box('', $this->language->lang('LOGIN_VIEWFORUM'));
		}
	}

	/**
	* Click tracking for forum links
	*/
	protected function update_click_counter()
	{
		if ($this->forum_data['forum_type'] == FORUM_LINK && $this->forum_data['forum_link'])
		{
			// Does it have click tracking enabled?
			if ($this->forum_data['forum_flags'] & FORUM_FLAG_LINK_TRACK)
			{
				$sql = 'UPDATE ' . FORUMS_TABLE . '
					SET forum_posts_approved = forum_posts_approved + 1
					WHERE forum_id = ' . $this->forum_data['forum_id'];
				$this->db->sql_query($sql);
			}

			// We redirect to the url. The third parameter indicates that external redirects are allowed.
			redirect($this->forum_data['forum_link'], false, true);

			return;
		}
	}

	/**
	* Marking topics as read
	*/
	protected function mark_topics()
	{
		$mark_read = $this->request->variable('mark', '');

		if ($mark_read == 'topics')
		{
			$token = $this->request->variable('hash', '');

			if (check_link_hash($token, 'global'))
			{
				markread('topics', [$this->forum_data['forum_id']], false, $this->request->variable('mark_time', 0));
			}

			$redirect_url = $this->helper->route('vinabb_web_board_forum_route', ['forum_id' => $this->forum_data['forum_id'], 'seo' => $this->forum_data['forum_name_seo'] . constants::REWRITE_URL_SEO]);
			meta_refresh(3, $redirect_url);

			if ($this->request->is_ajax())
			{
				// Tell the ajax script what language vars and URL need to be replaced
				$data = [
					'NO_UNREAD_POSTS'	=> $this->language->lang('NO_UNREAD_POSTS'),
					'UNREAD_POSTS'		=> $this->language->lang('UNREAD_POSTS'),
					'U_MARK_TOPICS'		=> ($this->user->data['is_registered'] || $this->config['load_anon_lastread']) ? htmlspecialchars_decode($this->helper->route('vinabb_web_board_forum_route', ['forum_id' => $this->forum_data['forum_id'], 'seo' => $this->forum_data['forum_name_seo'] . constants::REWRITE_URL_SEO, 'hash' => generate_link_hash('global'), 'mark' => 'topics', 'mark_time' => time()])) : '',
					'MESSAGE_TITLE'		=> $this->language->lang('INFORMATION'),
					'MESSAGE_TEXT'		=> $this->language->lang('TOPICS_MARKED')
				];

				$json_response = new \phpbb\json_response();
				$json_response->send($data);
			}

			trigger_error($this->language->lang('TOPICS_MARKED') . '<br><br>' . $this->language->lang('RETURN_FORUM', '<a href="' . $redirect_url . '">', '</a>'));
		}
	}

	/**
	* Run cron tasks manually
	*/
	protected function run_cron_tasks()
	{
		if (!$this->config['use_system_cron'])
		{
			$task = $this->cron->find_task('cron.task.core.prune_forum');
			$task->set_forum_data($this->forum_data);

			if ($task->is_ready())
			{
				$url = $task->get_url();
				$this->template->assign_var('RUN_CRON_TASK', '<img src="' . $url . '" width="1" height="1" alt="cron">');
			}
			else
			{
				// See if we should prune the shadow topics instead
				$task = $this->cron->find_task('cron.task.core.prune_shadow_topics');
				$task->set_forum_data($this->forum_data);

				if ($task->is_ready())
				{
					$url = $task->get_url();
					$this->template->assign_var('RUN_CRON_TASK', '<img src="' . $url . '" width="1" height="1" alt="cron">');
				}
			}
		}
	}

	/**
	* Subscribe new posts from the forum
	*/
	protected function subscribe_forum()
	{
		$s_watching_forum = [
			'link'			=> '',
			'link_toggle'	=> '',
			'title'			=> '',
			'title_toggle'	=> '',
			'is_watching'	=> false
		];

		if ($this->config['allow_forum_notify'] && $this->forum_data['forum_type'] == FORUM_POST && ($this->auth->acl_get('f_subscribe', $this->forum_data['forum_id']) || $this->user->data['user_id'] == ANONYMOUS))
		{
			$notify_status = (isset($this->forum_data['notify_status'])) ? $this->forum_data['notify_status'] : null;
			watch_topic_forum('forum', $s_watching_forum, $this->user->data['user_id'], $this->forum_data['forum_id'], 0, $notify_status, $this->start, $this->forum_data['forum_name']);
		}

		$this->template->assign_vars([
			'U_WATCH_FORUM_LINK'	=> $s_watching_forum['link'],
			'U_WATCH_FORUM_TOGGLE'	=> $s_watching_forum['link_toggle'],
			'S_WATCH_FORUM_TITLE'	=> $s_watching_forum['title'],
			'S_WATCH_FORUM_TOGGLE'	=> $s_watching_forum['title_toggle'],
			'S_WATCHING_FORUM'		=> $s_watching_forum['is_watching']
		]);
	}
}
