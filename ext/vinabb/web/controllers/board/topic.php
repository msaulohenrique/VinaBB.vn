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
* Controller for the topic page
*/
class topic implements topic_interface
{
	/** @var \phpbb\auth\auth $auth */
	protected $auth;

	/** @var \phpbb\cache\service $cache */
	protected $cache;

	/** @var \phpbb\config\config $config */
	protected $config;

	/** @var \phpbb\content_visibility $content_visibility */
	protected $content_visibility;

	/** @var \phpbb\db\driver\driver_interface $db */
	protected $db;

	/** @var \phpbb\language\language $language */
	protected $language;

	/** @var \vinabb\web\controllers\pagination $pagination */
	protected $pagination;

	/** @var \phpbb\profilefields\manager $profile_fields */
	protected $profile_fields;

	/** @var \phpbb\request\request $request */
	protected $request;

	/** @var \phpbb\template\template $template */
	protected $template;

	/** @var \phpbb\user $user */
	protected $user;

	/** @var \phpbb\controller\helper $helper */
	protected $helper;

	/** @var \vinabb\web\controllers\helper_interface $ext_helper */
	protected $ext_helper;

	/** @var string $root_path */
	protected $root_path;

	/** @var string $php_ext */
	protected $php_ext;

	/**
	* Constructor
	*
	* @param \phpbb\auth\auth							$auth				Authentication object
	* @param \phpbb\cache\service						$cache				Cache service
	* @param \phpbb\config\config						$config				Config object
	* @param \phpbb\content_visibility					$content_visibility	Content visibility
	* @param \phpbb\db\driver\driver_interface			$db					Database object
	* @param \phpbb\language\language					$language			Language object
	* @param \vinabb\web\controllers\pagination			$pagination			Pagination object
	* @param \phpbb\profilefields\manager				$profile_fields		Profile field manager
	* @param \phpbb\request\request						$request			Request object
	* @param \phpbb\template\template					$template			Template object
	* @param \phpbb\user								$user				User object
	* @param \phpbb\controller\helper					$helper				Controller helper
	* @param \vinabb\web\controllers\helper_interface	$ext_helper			Extension helper
	* @param string										$root_path			phpBB root path
	* @param string										$php_ext			PHP file extension
	*/
	public function __construct(
		\phpbb\auth\auth $auth,
		\phpbb\cache\service $cache,
		\phpbb\config\config $config,
		\phpbb\content_visibility $content_visibility,
		\phpbb\db\driver\driver_interface $db,
		\phpbb\language\language $language,
		\vinabb\web\controllers\pagination $pagination,
		\phpbb\profilefields\manager $profile_fields,
		\phpbb\request\request $request,
		\phpbb\template\template $template,
		\phpbb\user $user,
		\phpbb\controller\helper $helper,
		\vinabb\web\controllers\helper_interface $ext_helper,
		$root_path,
		$php_ext
	)
	{
		$this->auth = $auth;
		$this->cache = $cache;
		$this->config = $config;
		$this->content_visibility = $content_visibility;
		$this->db = $db;
		$this->language = $language;
		$this->pagination = $pagination;
		$this->profile_fields = $profile_fields;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->helper = $helper;
		$this->ext_helper = $ext_helper;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;
	}

	/**
	* Main method
	*
	* @param int	$forum_id	Forum ID
	* @param int	$topic_id	Topic ID
	* @param string	$page		Page number
	*
	* @return \Symfony\Component\HttpFoundation\Response
	*/
	public function main($forum_id, $topic_id, $page)
	{
		global $_SID, $_EXTRA_URL;

		// Common functions
		include "{$this->root_path}includes/functions_display.{$this->php_ext}";
		include "{$this->root_path}includes/bbcode.{$this->php_ext}";
		include "{$this->root_path}includes/functions_user.{$this->php_ext}";

		$forum_id = ($forum_id == constants::REWRITE_URL_FORUM_ZERO) ? 0 : $forum_id;
		$page = max(1, floor(str_replace(constants::REWRITE_URL_PAGE, '', $page)));

		// Initial var setup
		$post_id = $this->request->variable('p', 0);
		$voted_id = $this->request->variable('vote_id', array('' => 0));
		$voted_id = (sizeof($voted_id) > 1) ? array_unique($voted_id) : $voted_id;

		$start = floor(($page - 1) * $this->config['posts_per_page']);
		$view = $this->request->variable('view', '');

		$default_sort_days = (!empty($this->user->data['user_post_show_days'])) ? $this->user->data['user_post_show_days'] : 0;
		$default_sort_key = (!empty($this->user->data['user_post_sortby_type'])) ? $this->user->data['user_post_sortby_type'] : 't';
		$default_sort_dir = (!empty($this->user->data['user_post_sortby_dir'])) ? $this->user->data['user_post_sortby_dir'] : 'a';

		$sort_days = $this->request->variable('st', $default_sort_days);
		$sort_key = $this->request->variable('sk', $default_sort_key);
		$sort_dir = $this->request->variable('sd', $default_sort_dir);

		$update = $this->request->variable('update', false);

		$s_can_vote = false;
		$hilit_words = $this->request->variable('hilit', '', true);

		// Do we have a topic or post id?
		if (!$topic_id && !$post_id)
		{
			trigger_error('NO_TOPIC');
		}

		// Find topic id if user requested a newer or older topic
		if ($view && !$post_id)
		{
			if (!$forum_id)
			{
				$sql = 'SELECT forum_id
					FROM ' . TOPICS_TABLE . "
					WHERE topic_id = $topic_id";
				$result = $this->db->sql_query($sql);
				$forum_id = (int) $this->db->sql_fetchfield('forum_id');
				$this->db->sql_freeresult($result);

				if (!$forum_id)
				{
					trigger_error('NO_TOPIC');
				}
			}

			if ($view == 'unread')
			{
				// Get topic tracking info
				$topic_tracking_info = get_complete_topic_tracking($forum_id, $topic_id);
				$topic_last_read = (isset($topic_tracking_info[$topic_id])) ? $topic_tracking_info[$topic_id] : 0;

				$sql = 'SELECT post_id, topic_id, forum_id
					FROM ' . POSTS_TABLE . "
					WHERE topic_id = $topic_id
						AND " . $this->content_visibility->get_visibility_sql('post', $forum_id) . "
						AND post_time > $topic_last_read
						AND forum_id = $forum_id
					ORDER BY post_time, post_id";
				$result = $this->db->sql_query_limit($sql, 1);
				$row = $this->db->sql_fetchrow($result);
				$this->db->sql_freeresult($result);

				if (!$row)
				{
					$sql = 'SELECT topic_last_post_id as post_id, topic_id, forum_id
						FROM ' . TOPICS_TABLE . '
						WHERE topic_id = ' . $topic_id;
					$result = $this->db->sql_query($sql);
					$row = $this->db->sql_fetchrow($result);
					$this->db->sql_freeresult($result);
				}

				if (!$row)
				{
					// Setup user environment so we can process lang string
					$this->language->add_lang('viewtopic');

					trigger_error('NO_TOPIC');
				}

				$post_id = $row['post_id'];
				$topic_id = $row['topic_id'];
			}
			else if ($view == 'next' || $view == 'previous')
			{
				$sql_condition = ($view == 'next') ? '>' : '<';
				$sql_ordering = ($view == 'next') ? 'ASC' : 'DESC';

				$sql = 'SELECT forum_id, topic_last_post_time
					FROM ' . TOPICS_TABLE . '
					WHERE topic_id = ' . $topic_id;
				$result = $this->db->sql_query($sql);
				$row = $this->db->sql_fetchrow($result);
				$this->db->sql_freeresult($result);

				if (!$row)
				{
					$this->language->add_lang('viewtopic');

					// OK, the topic doesn't exist. This error message is not helpful, but technically correct.
					trigger_error(($view == 'next') ? 'NO_NEWER_TOPICS' : 'NO_OLDER_TOPICS');
				}
				else
				{
					$sql = 'SELECT topic_id, forum_id
						FROM ' . TOPICS_TABLE . '
						WHERE forum_id = ' . $row['forum_id'] . "
							AND topic_moved_id = 0
							AND topic_last_post_time $sql_condition {$row['topic_last_post_time']}
							AND " . $this->content_visibility->get_visibility_sql('topic', $row['forum_id']) . "
						ORDER BY topic_last_post_time $sql_ordering, topic_last_post_id $sql_ordering";
					$result = $this->db->sql_query_limit($sql, 1);
					$row = $this->db->sql_fetchrow($result);
					$this->db->sql_freeresult($result);

					if (!$row)
					{
						$sql = 'SELECT forum_style
							FROM ' . FORUMS_TABLE . "
							WHERE forum_id = $forum_id";
						$result = $this->db->sql_query($sql);
						$forum_style = (int) $this->db->sql_fetchfield('forum_style');
						$this->db->sql_freeresult($result);

						$this->user->setup('viewtopic', $forum_style);
						trigger_error(($view == 'next') ? 'NO_NEWER_TOPICS' : 'NO_OLDER_TOPICS');
					}
					else
					{
						$topic_id = $row['topic_id'];
						$forum_id = $row['forum_id'];
					}
				}
			}

			if (isset($row) && $row['forum_id'])
			{
				$forum_id = $row['forum_id'];
			}
		}

		// This rather complex gaggle of code handles querying for topics but
		// also allows for direct linking to a post (and the calculation of which
		// page the post is on and the correct display of viewtopic)
		$sql_array = array(
			'SELECT'	=> 't.*, f.*',
			'FROM'		=> array(FORUMS_TABLE => 'f'),
		);

		// The FROM-Order is quite important here, else t.* columns can not be correctly bound.
		if ($post_id)
		{
			$sql_array['SELECT'] .= ', p.post_visibility, p.post_time, p.post_id';
			$sql_array['FROM'][POSTS_TABLE] = 'p';
		}

		// Topics table need to be the last in the chain
		$sql_array['FROM'][TOPICS_TABLE] = 't';

		if ($this->user->data['is_registered'])
		{
			$sql_array['SELECT'] .= ', tw.notify_status';
			$sql_array['LEFT_JOIN'] = array();

			$sql_array['LEFT_JOIN'][] = array(
				'FROM'	=> array(TOPICS_WATCH_TABLE => 'tw'),
				'ON'	=> 'tw.user_id = ' . $this->user->data['user_id'] . ' AND t.topic_id = tw.topic_id'
			);

			if ($this->config['allow_bookmarks'])
			{
				$sql_array['SELECT'] .= ', bm.topic_id as bookmarked';
				$sql_array['LEFT_JOIN'][] = array(
					'FROM'	=> array(BOOKMARKS_TABLE => 'bm'),
					'ON'	=> 'bm.user_id = ' . $this->user->data['user_id'] . ' AND t.topic_id = bm.topic_id'
				);
			}

			if ($this->config['load_db_lastread'])
			{
				$sql_array['SELECT'] .= ', tt.mark_time, ft.mark_time as forum_mark_time';

				$sql_array['LEFT_JOIN'][] = array(
					'FROM'	=> array(TOPICS_TRACK_TABLE => 'tt'),
					'ON'	=> 'tt.user_id = ' . $this->user->data['user_id'] . ' AND t.topic_id = tt.topic_id'
				);

				$sql_array['LEFT_JOIN'][] = array(
					'FROM'	=> array(FORUMS_TRACK_TABLE => 'ft'),
					'ON'	=> 'ft.user_id = ' . $this->user->data['user_id'] . ' AND t.forum_id = ft.forum_id'
				);
			}
		}

		if (!$post_id)
		{
			$sql_array['WHERE'] = "t.topic_id = $topic_id";
		}
		else
		{
			$sql_array['WHERE'] = "p.post_id = $post_id AND t.topic_id = p.topic_id";
		}

		$sql_array['WHERE'] .= ' AND f.forum_id = t.forum_id';

		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);
		$topic_data = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		// link to unapproved post or incorrect link
		if (!$topic_data)
		{
			// If post_id was submitted, we try at least to display the topic as a last resort...
			if ($post_id && $topic_id)
			{
				redirect($this->helper->route('vinabb_web_board_topic_route', array('forum_id' => (($forum_id) ? $forum_id : constants::REWRITE_URL_FORUM_ZERO), 'topic_id' => $topic_id)));
			}

			trigger_error('NO_TOPIC');
		}

		$forum_id = (int) $topic_data['forum_id'];

		// Now we know the forum_id and can check the permissions
		if ($topic_data['topic_visibility'] != ITEM_APPROVED && !$this->auth->acl_get('m_approve', $forum_id))
		{
			trigger_error('NO_TOPIC');
		}

		// This is for determining where we are (page)
		if ($post_id)
		{
			// Are we where we are supposed to be?
			if (($topic_data['post_visibility'] == ITEM_UNAPPROVED || $topic_data['post_visibility'] == ITEM_REAPPROVE) && !$this->auth->acl_get('m_approve', $topic_data['forum_id']))
			{
				// If post_id was submitted, we try at least to display the topic as a last resort...
				if ($topic_id)
				{
					redirect($this->helper->route('vinabb_web_board_topic_route', array('forum_id' => (($forum_id) ? $forum_id : constants::REWRITE_URL_FORUM_ZERO), 'topic_id' => $topic_id)));
				}

				trigger_error('NO_TOPIC');
			}
			if ($post_id == $topic_data['topic_first_post_id'] || $post_id == $topic_data['topic_last_post_id'])
			{
				$check_sort = ($post_id == $topic_data['topic_first_post_id']) ? 'd' : 'a';

				if ($sort_dir == $check_sort)
				{
					$topic_data['prev_posts'] = $this->content_visibility->get_count('topic_posts', $topic_data, $forum_id) - 1;
				}
				else
				{
					$topic_data['prev_posts'] = 0;
				}
			}
			else
			{
				$sql = 'SELECT COUNT(p.post_id) AS prev_posts
					FROM ' . POSTS_TABLE . " p
					WHERE p.topic_id = {$topic_data['topic_id']}
						AND " . $this->content_visibility->get_visibility_sql('post', $forum_id, 'p.');

				if ($sort_dir == 'd')
				{
					$sql .= " AND (p.post_time > {$topic_data['post_time']} OR (p.post_time = {$topic_data['post_time']} AND p.post_id >= {$topic_data['post_id']}))";
				}
				else
				{
					$sql .= " AND (p.post_time < {$topic_data['post_time']} OR (p.post_time = {$topic_data['post_time']} AND p.post_id <= {$topic_data['post_id']}))";
				}

				$result = $this->db->sql_query($sql);
				$row = $this->db->sql_fetchrow($result);
				$this->db->sql_freeresult($result);

				$topic_data['prev_posts'] = $row['prev_posts'] - 1;
			}
		}

		$topic_id = (int) $topic_data['topic_id'];
		$topic_replies = $this->content_visibility->get_count('topic_posts', $topic_data, $forum_id) - 1;

		// Check sticky/announcement time limit
		if (($topic_data['topic_type'] == POST_STICKY || $topic_data['topic_type'] == POST_ANNOUNCE) && $topic_data['topic_time_limit'] && ($topic_data['topic_time'] + $topic_data['topic_time_limit']) < time())
		{
			$sql = 'UPDATE ' . TOPICS_TABLE . '
				SET topic_type = ' . POST_NORMAL . ', topic_time_limit = 0
				WHERE topic_id = ' . $topic_id;
			$this->db->sql_query($sql);

			$topic_data['topic_type'] = POST_NORMAL;
			$topic_data['topic_time_limit'] = 0;
		}

		// Setup look and feel
		$this->user->setup('viewtopic', $topic_data['forum_style']);

		$overrides_f_read_check = false;
		$overrides_forum_password_check = false;
		$topic_tracking_info = isset($topic_tracking_info) ? $topic_tracking_info : null;

		// Start auth check
		if (!$overrides_f_read_check && !$this->auth->acl_get('f_read', $forum_id))
		{
			if ($this->user->data['user_id'] != ANONYMOUS)
			{
				send_status_line(403, 'Forbidden');
				trigger_error('SORRY_AUTH_READ');
			}

			login_box('', $this->language->lang('LOGIN_VIEWFORUM'));
		}

		// Forum is passworded ... check whether access has been granted to this
		// user this session, if not show login box
		if (!$overrides_forum_password_check && $topic_data['forum_password'])
		{
			login_forum_box($topic_data);
		}

		// Redirect to login upon emailed notification links if user is not logged in.
		if (isset($_GET['e']) && $this->user->data['user_id'] == ANONYMOUS)
		{
			login_box(build_url('e') . '#unread', $this->language->lang('LOGIN_NOTIFY_TOPIC'));
		}

		// What is start equal to?
		if ($post_id)
		{
			$start = floor(($topic_data['prev_posts']) / $this->config['posts_per_page']) * $this->config['posts_per_page'];
		}

		// Get topic tracking info
		if (!isset($topic_tracking_info))
		{
			$topic_tracking_info = array();

			// Get topic tracking info
			if ($this->config['load_db_lastread'] && $this->user->data['is_registered'])
			{
				$tmp_topic_data = array($topic_id => $topic_data);
				$topic_tracking_info = get_topic_tracking($forum_id, $topic_id, $tmp_topic_data, array($forum_id => $topic_data['forum_mark_time']));

				unset($tmp_topic_data);
			}
			else if ($this->config['load_anon_lastread'] || $this->user->data['is_registered'])
			{
				$topic_tracking_info = get_complete_topic_tracking($forum_id, $topic_id);
			}
		}

		// Post ordering options
		$limit_days = [
			0	=> $this->language->lang('ALL_POSTS'),
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
			's'	=> $this->language->lang('SUBJECT')
		];

		$sort_by_sql = [
			'a'	=> ['u.username_clean', 'p.post_id'],
			't'	=> ['p.post_time', 'p.post_id'],
			's'	=> ['p.post_subject', 'p.post_id']
		];

		$join_user_sql = ['a' => true, 't' => false, 's' => false];
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

		// Obtain correct post count and ordering SQL if user has
		// requested anything different
		if ($sort_days)
		{
			$min_post_time = time() - ($sort_days * 86400);

			$sql = 'SELECT COUNT(post_id) AS num_posts
				FROM ' . POSTS_TABLE . "
				WHERE topic_id = $topic_id
					AND post_time >= $min_post_time
					AND " . $this->content_visibility->get_visibility_sql('post', $forum_id);
			$result = $this->db->sql_query($sql);
			$total_posts = (int) $this->db->sql_fetchfield('num_posts');
			$this->db->sql_freeresult($result);

			$limit_posts_time = "AND p.post_time >= $min_post_time ";

			if ($this->request->is_set_post('sort'))
			{
				$start = 0;
			}
		}
		else
		{
			$total_posts = $topic_replies + 1;
			$limit_posts_time = '';
		}

		// Was a highlight request part of the URI?
		$highlight_match = $highlight = '';

		if ($hilit_words)
		{
			$highlight_match = phpbb_clean_search_string($hilit_words);
			$highlight = urlencode($highlight_match);
			$highlight_match = str_replace('\*', '\w+?', preg_quote($highlight_match, '#'));
			$highlight_match = preg_replace('#(?<=^|\s)\\\\w\*\?(?=\s|$)#', '\w+?', $highlight_match);
			$highlight_match = str_replace(' ', '|', $highlight_match);
		}

		// Make sure $start is set to the last page if it exceeds the amount
		$start = $this->pagination->validate_start($start, $this->config['posts_per_page'], $total_posts);

		// General Viewtopic URL for return links
		$viewtopic_url = append_sid("{$this->root_path}viewtopic.{$this->php_ext}", "f=$forum_id&amp;t=$topic_id" . (($start == 0) ? '' : "&amp;start=$start") . ((strlen($u_sort_param)) ? "&amp;$u_sort_param" : '') . (($highlight_match) ? "&amp;hilit=$highlight" : ''));

		// Are we watching this topic?
		$s_watching_topic = array(
			'link'			=> '',
			'link_toggle'	=> '',
			'title'			=> '',
			'title_toggle'	=> '',
			'is_watching'	=> false,
		);

		if ($this->config['allow_topic_notify'])
		{
			$notify_status = (isset($topic_data['notify_status'])) ? $topic_data['notify_status'] : null;
			watch_topic_forum('topic', $s_watching_topic, $this->user->data['user_id'], $forum_id, $topic_id, $notify_status, $start, $topic_data['topic_title']);

			// Reset forum notification if forum notify is set
			if ($this->config['allow_forum_notify'] && $this->auth->acl_get('f_subscribe', $forum_id))
			{
				$s_watching_forum = $s_watching_topic;
				watch_topic_forum('forum', $s_watching_forum, $this->user->data['user_id'], $forum_id, 0);
			}
		}

		// Bookmarks
		if ($this->config['allow_bookmarks'] && $this->user->data['is_registered'] && $this->request->variable('bookmark', 0))
		{
			$this->bookmark($topic_id, $viewtopic_url, $topic_data['bookmarked']);
		}

		// Grab icons
		$icons = $this->cache->obtain_icons();

		// Forum rules listing
		gen_forum_auth_level('topic', $forum_id, $topic_data['forum_status']);

		// Quick mod tools
		$allow_change_type = ($this->auth->acl_get('m_', $forum_id) || ($this->user->data['is_registered'] && $this->user->data['user_id'] == $topic_data['topic_poster'])) ? true : false;
		$s_quickmod_action = $this->helper->route('vinabb_web_mcp_route', ['id' => 'main', 'mode' => 'quickmod', 'quickmod' => 1, 'f' => $forum_id, 't' => $topic_id, 'start' => $start, 'redirect' => urlencode(str_replace('&amp;', '&', $viewtopic_url))], true, $this->user->session_id);

		$quickmod_array = [
			'lock'			=> ['LOCK_TOPIC', ($topic_data['topic_status'] == ITEM_UNLOCKED) && ($this->auth->acl_get('m_lock', $forum_id) || ($this->auth->acl_get('f_user_lock', $forum_id) && $this->user->data['is_registered'] && $this->user->data['user_id'] == $topic_data['topic_poster']))],
			'unlock'		=> ['UNLOCK_TOPIC', ($topic_data['topic_status'] != ITEM_UNLOCKED) && ($this->auth->acl_get('m_lock', $forum_id))],
			'delete_topic'	=> ['DELETE_TOPIC', ($this->auth->acl_get('m_delete', $forum_id) || (($topic_data['topic_visibility'] != ITEM_DELETED) && $this->auth->acl_get('m_softdelete', $forum_id)))],
			'restore_topic'	=> ['RESTORE_TOPIC', (($topic_data['topic_visibility'] == ITEM_DELETED) && $this->auth->acl_get('m_approve', $forum_id))],
			'move'			=> ['MOVE_TOPIC', $this->auth->acl_get('m_move', $forum_id) && $topic_data['topic_status'] != ITEM_MOVED],
			'split'			=> ['SPLIT_TOPIC', $this->auth->acl_get('m_split', $forum_id)],
			'merge'			=> ['MERGE_POSTS', $this->auth->acl_get('m_merge', $forum_id)],
			'merge_topic'	=> ['MERGE_TOPIC', $this->auth->acl_get('m_merge', $forum_id)],
			'fork'			=> ['FORK_TOPIC', $this->auth->acl_get('m_move', $forum_id)],
			'make_normal'	=> ['MAKE_NORMAL', ($allow_change_type && $this->auth->acl_gets('f_sticky', 'f_announce', 'f_announce_global', $forum_id) && $topic_data['topic_type'] != POST_NORMAL)],
			'make_sticky'	=> ['MAKE_STICKY', ($allow_change_type && $this->auth->acl_get('f_sticky', $forum_id) && $topic_data['topic_type'] != POST_STICKY)],
			'make_announce'	=> ['MAKE_ANNOUNCE', ($allow_change_type && $this->auth->acl_get('f_announce', $forum_id) && $topic_data['topic_type'] != POST_ANNOUNCE)],
			'make_global'	=> ['MAKE_GLOBAL', ($allow_change_type && $this->auth->acl_get('f_announce_global', $forum_id) && $topic_data['topic_type'] != POST_GLOBAL)],
			'topic_logs'	=> ['VIEW_TOPIC_LOGS', $this->auth->acl_get('m_', $forum_id)]
		];

		foreach ($quickmod_array as $option => $qm_ary)
		{
			if (!empty($qm_ary[1]))
			{
				phpbb_add_quickmod_option($s_quickmod_action, $option, $qm_ary[0]);
			}
		}

		// Navigation links
		generate_forum_nav($topic_data);

		// Forum Rules
		generate_forum_rules($topic_data);

		// Moderators
		$forum_moderators = [];

		if ($this->config['load_moderators'])
		{
			get_moderators($forum_moderators, $forum_id);
		}

		// This is only used for print view so ...
		$server_path = (!$view) ? $this->root_path : generate_board_url() . '/';

		// Replace naughty words in title
		$topic_data['topic_title'] = censor_text($topic_data['topic_title']);

		$s_search_hidden_fields = [
			't'		=> $topic_id,
			'sf'	=> 'msgonly'
		];

		if ($_SID)
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

		$pagination_params = [
			'forum_id'	=> $forum_id,
			'topic_id'	=> $topic_id,
			'seo'		=> $topic_data['topic_title_seo'] . constants::REWRITE_URL_SEO
		];

		if (sizeof($u_sort_param_ary))
		{
			$pagination_params = array_merge($pagination_params, $u_sort_param_ary);
		}

		// If we've got a hightlight set pass it on to pagination
		if ($highlight_match)
		{
			$pagination_params['hilit'] = $highlight;
		}

		$this->pagination->generate_template_pagination('vinabb_web_board_topic_route', $pagination_params, 'pagination', $total_posts, $this->config['posts_per_page'], $start);

		// Send vars to template
		$this->template->assign_vars(array(
				'FORUM_ID' 		=> $forum_id,
				'FORUM_NAME' 	=> $topic_data['forum_name'],
				'FORUM_DESC'	=> generate_text_for_display($topic_data['forum_desc'], $topic_data['forum_desc_uid'], $topic_data['forum_desc_bitfield'], $topic_data['forum_desc_options']),
				'TOPIC_ID' 		=> $topic_id,
				'TOPIC_TITLE' 	=> $topic_data['topic_title'],
				'TOPIC_POSTER'	=> $topic_data['topic_poster'],

				'TOPIC_AUTHOR_FULL'		=> get_username_string('full', $topic_data['topic_poster'], $topic_data['topic_first_poster_name'], $topic_data['topic_first_poster_colour']),
				'TOPIC_AUTHOR_COLOUR'	=> get_username_string('colour', $topic_data['topic_poster'], $topic_data['topic_first_poster_name'], $topic_data['topic_first_poster_colour']),
				'TOPIC_AUTHOR'			=> get_username_string('username', $topic_data['topic_poster'], $topic_data['topic_first_poster_name'], $topic_data['topic_first_poster_colour']),

				'TOTAL_POSTS'	=> $this->language->lang('VIEW_TOPIC_POSTS', (int) $total_posts),
				'U_MCP' 		=> ($this->auth->acl_get('m_', $forum_id)) ? append_sid("{$this->root_path}mcp.{$this->php_ext}", "i=main&amp;mode=topic_view&amp;f=$forum_id&amp;t=$topic_id" . (($start == 0) ? '' : "&amp;start=$start") . ((strlen($u_sort_param)) ? "&amp;$u_sort_param" : ''), true, $this->user->session_id) : '',
				'MODERATORS'	=> (isset($forum_moderators[$forum_id]) && sizeof($forum_moderators[$forum_id])) ? implode($this->language->lang('COMMA_SEPARATOR'), $forum_moderators[$forum_id]) : '',

				'S_IS_LOCKED'			=> ($topic_data['topic_status'] == ITEM_UNLOCKED && $topic_data['forum_status'] == ITEM_UNLOCKED) ? false : true,
				'S_SELECT_SORT_DIR' 	=> $s_sort_dir,
				'S_SELECT_SORT_KEY' 	=> $s_sort_key,
				'S_SELECT_SORT_DAYS' 	=> $s_limit_days,
				'S_SINGLE_MODERATOR'	=> (!empty($forum_moderators[$forum_id]) && sizeof($forum_moderators[$forum_id]) > 1) ? false : true,
				'S_TOPIC_ACTION' 		=> append_sid("{$this->root_path}viewtopic.{$this->php_ext}", "f=$forum_id&amp;t=$topic_id" . (($start == 0) ? '' : "&amp;start=$start")),
				'S_MOD_ACTION' 			=> $s_quickmod_action,

				'L_RETURN_TO_FORUM'		=> $this->language->lang('RETURN_TO', $topic_data['forum_name']),
				'S_VIEWTOPIC'			=> true,
				'S_UNREAD_VIEW'			=> $view == 'unread',
				'S_DISPLAY_SEARCHBOX'	=> ($this->auth->acl_get('u_search') && $this->auth->acl_get('f_search', $forum_id) && $this->config['load_search']),
				'S_SEARCHBOX_ACTION'	=> append_sid("{$this->root_path}search.{$this->php_ext}"),
				'S_SEARCH_LOCAL_HIDDEN_FIELDS'	=> build_hidden_fields($s_search_hidden_fields),

				'S_DISPLAY_POST_INFO'	=> ($topic_data['forum_type'] == FORUM_POST && ($this->auth->acl_get('f_post', $forum_id) || $this->user->data['user_id'] == ANONYMOUS)),
				'S_DISPLAY_REPLY_INFO'	=> ($topic_data['forum_type'] == FORUM_POST && ($this->auth->acl_get('f_reply', $forum_id) || $this->user->data['user_id'] == ANONYMOUS)),
				'S_ENABLE_FEEDS_TOPIC'	=> ($this->config['feed_topic'] && !phpbb_optionget(FORUM_OPTION_FEED_EXCLUDE, $topic_data['forum_options'])),

				'U_TOPIC'				=> "{$server_path}viewtopic.{$this->php_ext}?f=$forum_id&amp;t=$topic_id",
				'U_FORUM'				=> $server_path,
				'U_VIEW_TOPIC' 			=> $viewtopic_url,
				'U_CANONICAL'			=> generate_board_url() . '/' . append_sid("viewtopic.{$this->php_ext}", "t=$topic_id" . (($start) ? "&amp;start=$start" : ''), true, ''),
				'U_VIEW_FORUM' 			=> $this->helper->route('vinabb_web_board_forum_route', ['forum_id' => $forum_id, 'seo' => $topic_data['forum_name_seo'] . constants::REWRITE_URL_SEO]),
				'U_VIEW_OLDER_TOPIC'	=> append_sid("{$this->root_path}viewtopic.{$this->php_ext}", "f=$forum_id&amp;t=$topic_id&amp;view=previous"),
				'U_VIEW_NEWER_TOPIC'	=> append_sid("{$this->root_path}viewtopic.{$this->php_ext}", "f=$forum_id&amp;t=$topic_id&amp;view=next"),
				'U_PRINT_TOPIC'			=> ($this->auth->acl_get('f_print', $forum_id)) ? $viewtopic_url . '&amp;view=print' : '',
				'U_EMAIL_TOPIC'			=> ($this->auth->acl_get('f_email', $forum_id) && $this->config['email_enable']) ? append_sid("{$this->root_path}memberlist.{$this->php_ext}", "mode=email&amp;t=$topic_id") : '',

				'U_WATCH_TOPIC'			=> $s_watching_topic['link'],
				'U_WATCH_TOPIC_TOGGLE'	=> $s_watching_topic['link_toggle'],
				'S_WATCH_TOPIC_TITLE'	=> $s_watching_topic['title'],
				'S_WATCH_TOPIC_TOGGLE'	=> $s_watching_topic['title_toggle'],
				'S_WATCHING_TOPIC'		=> $s_watching_topic['is_watching'],

				'U_BOOKMARK_TOPIC'		=> ($this->user->data['is_registered'] && $this->config['allow_bookmarks']) ? $viewtopic_url . '&amp;bookmark=1&amp;hash=' . generate_link_hash("topic_$topic_id") : '',
				'S_BOOKMARK_TOPIC'		=> ($this->user->data['is_registered'] && $this->config['allow_bookmarks'] && $topic_data['bookmarked']) ? $this->language->lang('BOOKMARK_TOPIC_REMOVE') : $this->language->lang('BOOKMARK_TOPIC'),
				'S_BOOKMARK_TOGGLE'		=> (!$this->user->data['is_registered'] || !$this->config['allow_bookmarks'] || !$topic_data['bookmarked']) ? $this->language->lang('BOOKMARK_TOPIC_REMOVE') : $this->language->lang('BOOKMARK_TOPIC'),
				'S_BOOKMARKED_TOPIC'	=> ($this->user->data['is_registered'] && $this->config['allow_bookmarks'] && $topic_data['bookmarked']),

				'U_POST_NEW_TOPIC' 		=> ($this->auth->acl_get('f_post', $forum_id) || $this->user->data['user_id'] == ANONYMOUS) ? append_sid("{$this->root_path}posting.{$this->php_ext}", "mode=post&amp;f=$forum_id") : '',
				'U_POST_REPLY_TOPIC' 	=> ($this->auth->acl_get('f_reply', $forum_id) || $this->user->data['user_id'] == ANONYMOUS) ? append_sid("{$this->root_path}posting.{$this->php_ext}", "mode=reply&amp;f=$forum_id&amp;t=$topic_id") : '',
				'U_BUMP_TOPIC'			=> (bump_topic_allowed($forum_id, $topic_data['topic_bumped'], $topic_data['topic_last_post_time'], $topic_data['topic_poster'], $topic_data['topic_last_poster_id'])) ? append_sid("{$this->root_path}posting.{$this->php_ext}", "mode=bump&amp;f=$forum_id&amp;t=$topic_id&amp;hash=" . generate_link_hash("topic_$topic_id")) : '')
		);

		// Does this topic contain a poll?
		if (!empty($topic_data['poll_start']))
		{
			$sql = 'SELECT o.*, p.bbcode_bitfield, p.bbcode_uid
				FROM ' . POLL_OPTIONS_TABLE . ' o, ' . POSTS_TABLE . " p
				WHERE o.topic_id = $topic_id
					AND p.post_id = {$topic_data['topic_first_post_id']}
					AND p.topic_id = o.topic_id
				ORDER BY o.poll_option_id";
			$result = $this->db->sql_query($sql);

			$poll_info = $vote_counts = array();
			while ($row = $this->db->sql_fetchrow($result))
			{
				$poll_info[] = $row;
				$option_id = (int) $row['poll_option_id'];
				$vote_counts[$option_id] = (int) $row['poll_option_total'];
			}
			$this->db->sql_freeresult($result);

			$cur_voted_id = array();
			if ($this->user->data['is_registered'])
			{
				$sql = 'SELECT poll_option_id
					FROM ' . POLL_VOTES_TABLE . '
					WHERE topic_id = ' . $topic_id . '
						AND vote_user_id = ' . $this->user->data['user_id'];
				$result = $this->db->sql_query($sql);

				while ($row = $this->db->sql_fetchrow($result))
				{
					$cur_voted_id[] = $row['poll_option_id'];
				}
				$this->db->sql_freeresult($result);
			}
			else
			{
				// Cookie based guest tracking ... I don't like this but hum ho
				// it's oft requested. This relies on "nice" users who don't feel
				// the need to delete cookies to mess with results.
				if ($this->request->is_set($this->config['cookie_name'] . '_poll_' . $topic_id, \phpbb\request\request_interface::COOKIE))
				{
					$cur_voted_id = explode(',', $this->request->variable($this->config['cookie_name'] . '_poll_' . $topic_id, '', true, \phpbb\request\request_interface::COOKIE));
					$cur_voted_id = array_map('intval', $cur_voted_id);
				}
			}

			// Can not vote at all if no vote permission
			$s_can_vote = ($this->auth->acl_get('f_vote', $forum_id) &&
				(($topic_data['poll_length'] != 0 && $topic_data['poll_start'] + $topic_data['poll_length'] > time()) || $topic_data['poll_length'] == 0) &&
				$topic_data['topic_status'] != ITEM_LOCKED &&
				$topic_data['forum_status'] != ITEM_LOCKED &&
				(!sizeof($cur_voted_id) ||
					($this->auth->acl_get('f_votechg', $forum_id) && $topic_data['poll_vote_change']))) ? true : false;
			$s_display_results = (!$s_can_vote || ($s_can_vote && sizeof($cur_voted_id)) || $view == 'viewpoll') ? true : false;

			if ($update && $s_can_vote)
			{
				if (!sizeof($voted_id) || sizeof($voted_id) > $topic_data['poll_max_options'] || in_array(VOTE_CONVERTED, $cur_voted_id) || !check_form_key('posting'))
				{
					$redirect_url = append_sid("{$this->root_path}viewtopic.{$this->php_ext}", "f=$forum_id&amp;t=$topic_id" . (($start == 0) ? '' : "&amp;start=$start"));

					meta_refresh(5, $redirect_url);
					if (!sizeof($voted_id))
					{
						$message = 'NO_VOTE_OPTION';
					}
					else if (sizeof($voted_id) > $topic_data['poll_max_options'])
					{
						$message = 'TOO_MANY_VOTE_OPTIONS';
					}
					else if (in_array(VOTE_CONVERTED, $cur_voted_id))
					{
						$message = 'VOTE_CONVERTED';
					}
					else
					{
						$message = 'FORM_INVALID';
					}

					$message = $this->language->lang($message) . '<br><br>' . $this->language->lang('RETURN_TOPIC', '<a href="' . $redirect_url . '">', '</a>');
					trigger_error($message);
				}

				foreach ($voted_id as $option)
				{
					if (in_array($option, $cur_voted_id))
					{
						continue;
					}

					$sql = 'UPDATE ' . POLL_OPTIONS_TABLE . '
						SET poll_option_total = poll_option_total + 1
						WHERE poll_option_id = ' . (int) $option . '
							AND topic_id = ' . (int) $topic_id;
					$this->db->sql_query($sql);

					$vote_counts[$option]++;

					if ($this->user->data['is_registered'])
					{
						$sql_ary = array(
							'topic_id'			=> (int) $topic_id,
							'poll_option_id'	=> (int) $option,
							'vote_user_id'		=> (int) $this->user->data['user_id'],
							'vote_user_ip'		=> (string) $this->user->ip,
						);

						$sql = 'INSERT INTO ' . POLL_VOTES_TABLE . ' ' . $this->db->sql_build_array('INSERT', $sql_ary);
						$this->db->sql_query($sql);
					}
				}

				foreach ($cur_voted_id as $option)
				{
					if (!in_array($option, $voted_id))
					{
						$sql = 'UPDATE ' . POLL_OPTIONS_TABLE . '
							SET poll_option_total = poll_option_total - 1
							WHERE poll_option_id = ' . (int) $option . '
								AND topic_id = ' . (int) $topic_id;
						$this->db->sql_query($sql);

						$vote_counts[$option]--;

						if ($this->user->data['is_registered'])
						{
							$sql = 'DELETE FROM ' . POLL_VOTES_TABLE . '
								WHERE topic_id = ' . (int) $topic_id . '
									AND poll_option_id = ' . (int) $option . '
									AND vote_user_id = ' . (int) $this->user->data['user_id'];
							$this->db->sql_query($sql);
						}
					}
				}

				if ($this->user->data['user_id'] == ANONYMOUS && !$this->user->data['is_bot'])
				{
					$this->user->set_cookie('poll_' . $topic_id, implode(',', $voted_id), time() + 31536000);
				}

				$sql = 'UPDATE ' . TOPICS_TABLE . '
					SET poll_last_vote = ' . time() . "
					WHERE topic_id = $topic_id";
				//, topic_last_post_time = ' . time() . " -- for bumping topics with new votes, ignore for now
				$this->db->sql_query($sql);

				$redirect_url = append_sid("{$this->root_path}viewtopic.{$this->php_ext}", "f=$forum_id&amp;t=$topic_id" . (($start == 0) ? '' : "&amp;start=$start"));
				$message = $this->language->lang('VOTE_SUBMITTED') . '<br><br>' . $this->language->lang('RETURN_TOPIC', '<a href="' . $redirect_url . '">', '</a>');

				if ($this->request->is_ajax())
				{
					// Filter out invalid options
					$valid_user_votes = array_intersect(array_keys($vote_counts), $voted_id);

					$data = array(
						'NO_VOTES'		=> $this->language->lang('NO_VOTES'),
						'success'		=> true,
						'user_votes'	=> array_flip($valid_user_votes),
						'vote_counts'	=> $vote_counts,
						'total_votes'	=> array_sum($vote_counts),
						'can_vote'		=> !sizeof($valid_user_votes) || ($this->auth->acl_get('f_votechg', $forum_id) && $topic_data['poll_vote_change']),
					);
					$json_response = new \phpbb\json_response();
					$json_response->send($data);
				}

				meta_refresh(5, $redirect_url);
				trigger_error($message);
			}

			$poll_total = 0;
			$poll_most = 0;
			foreach ($poll_info as $poll_option)
			{
				$poll_total += $poll_option['poll_option_total'];
				$poll_most = ($poll_option['poll_option_total'] >= $poll_most) ? $poll_option['poll_option_total'] : $poll_most;
			}

			$parse_flags = ($poll_info[0]['bbcode_bitfield'] ? OPTION_FLAG_BBCODE : 0) | OPTION_FLAG_SMILIES;

			for ($i = 0, $size = sizeof($poll_info); $i < $size; $i++)
			{
				$poll_info[$i]['poll_option_text'] = generate_text_for_display($poll_info[$i]['poll_option_text'], $poll_info[$i]['bbcode_uid'], $poll_info['bbcode_bitfield'], $parse_flags, true);
			}

			$topic_data['poll_title'] = generate_text_for_display($topic_data['poll_title'], $poll_info[0]['bbcode_uid'], $poll_info[0]['bbcode_bitfield'], $parse_flags, true);

			$poll_options_template_data = array();
			foreach ($poll_info as $poll_option)
			{
				$option_pct = ($poll_total > 0) ? $poll_option['poll_option_total'] / $poll_total : 0;
				$option_pct_txt = sprintf("%.1d%%", round($option_pct * 100));
				$option_pct_rel = ($poll_most > 0) ? $poll_option['poll_option_total'] / $poll_most : 0;
				$option_pct_rel_txt = sprintf("%.1d%%", round($option_pct_rel * 100));
				$option_most_votes = ($poll_option['poll_option_total'] > 0 && $poll_option['poll_option_total'] == $poll_most) ? true : false;

				$poll_options_template_data[] = array(
					'POLL_OPTION_ID' 			=> $poll_option['poll_option_id'],
					'POLL_OPTION_CAPTION' 		=> $poll_option['poll_option_text'],
					'POLL_OPTION_RESULT' 		=> $poll_option['poll_option_total'],
					'POLL_OPTION_PERCENT' 		=> $option_pct_txt,
					'POLL_OPTION_PERCENT_REL' 	=> $option_pct_rel_txt,
					'POLL_OPTION_PCT'			=> round($option_pct * 100),
					'POLL_OPTION_WIDTH'     	=> round($option_pct * 250),
					'POLL_OPTION_VOTED'			=> (in_array($poll_option['poll_option_id'], $cur_voted_id)) ? true : false,
					'POLL_OPTION_MOST_VOTES'	=> $option_most_votes,
				);
			}

			$poll_end = $topic_data['poll_length'] + $topic_data['poll_start'];

			$poll_template_data = array(
				'POLL_QUESTION'		=> $topic_data['poll_title'],
				'TOTAL_VOTES' 		=> $poll_total,
				'POLL_LEFT_CAP_IMG'	=> $this->user->img('poll_left'),
				'POLL_RIGHT_CAP_IMG'=> $this->user->img('poll_right'),

				'L_MAX_VOTES'		=> $this->language->lang('MAX_OPTIONS_SELECT', (int) $topic_data['poll_max_options']),
				'L_POLL_LENGTH'		=> ($topic_data['poll_length']) ? $this->language->lang(($poll_end > time()) ? 'POLL_RUN_TILL' : 'POLL_ENDED_AT', $this->user->format_date($poll_end)) : '',

				'S_HAS_POLL'		=> true,
				'S_CAN_VOTE'		=> $s_can_vote,
				'S_DISPLAY_RESULTS'	=> $s_display_results,
				'S_IS_MULTI_CHOICE'	=> ($topic_data['poll_max_options'] > 1) ? true : false,
				'S_POLL_ACTION'		=> $viewtopic_url,

				'U_VIEW_RESULTS'	=> $viewtopic_url . '&amp;view=viewpoll',
			);

			$this->template->assign_block_vars_array('poll_option', $poll_options_template_data);
			$this->template->assign_vars($poll_template_data);

			unset($poll_end, $poll_info, $poll_options_template_data, $poll_template_data, $voted_id);
		}

		// If the user is trying to reach the second half of the topic, fetch it starting from the end
		$store_reverse = false;
		$sql_limit = $this->config['posts_per_page'];

		if ($start > $total_posts / 2)
		{
			$store_reverse = true;

			// Select the sort order
			$direction = (($sort_dir == 'd') ? 'ASC' : 'DESC');

			$sql_limit = $this->pagination->reverse_limit($start, $sql_limit, $total_posts);
			$sql_start = $this->pagination->reverse_start($start, $sql_limit, $total_posts);
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

		// Container for user details, only process once
		$post_list = $user_cache = $id_cache = $attachments = $attach_list = $rowset = $update_count = $post_edit_list = $post_delete_list = array();
		$has_unapproved_attachments = $has_approved_attachments = $display_notice = false;

		// Go ahead and pull all data for this topic
		$sql = 'SELECT p.post_id
			FROM ' . POSTS_TABLE . ' p' . (($join_user_sql[$sort_key]) ? ', ' . USERS_TABLE . ' u': '') . "
			WHERE p.topic_id = $topic_id
				AND " . $this->content_visibility->get_visibility_sql('post', $forum_id, 'p.') . "
				" . (($join_user_sql[$sort_key]) ? 'AND u.user_id = p.poster_id': '') . "
				$limit_posts_time
			ORDER BY $sql_sort_order";
		$result = $this->db->sql_query_limit($sql, $sql_limit, $sql_start);

		$i = ($store_reverse) ? $sql_limit - 1 : 0;
		while ($row = $this->db->sql_fetchrow($result))
		{
			$post_list[$i] = (int) $row['post_id'];
			($store_reverse) ? $i-- : $i++;
		}
		$this->db->sql_freeresult($result);

		if (!sizeof($post_list))
		{
			if ($sort_days)
			{
				trigger_error('NO_POSTS_TIME_FRAME');
			}
			else
			{
				trigger_error('NO_TOPIC');
			}
		}

		// Holding maximum post time for marking topic read
		// We need to grab it because we do reverse ordering sometimes
		$max_post_time = 0;

		$sql_ary = array(
			'SELECT'	=> 'u.*, z.friend, z.foe, p.*',

			'FROM'		=> array(
				USERS_TABLE		=> 'u',
				POSTS_TABLE		=> 'p',
			),

			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(ZEBRA_TABLE => 'z'),
					'ON'	=> 'z.user_id = ' . $this->user->data['user_id'] . ' AND z.zebra_id = p.poster_id',
				),
			),

			'WHERE'		=> $this->db->sql_in_set('p.post_id', $post_list) . '
				AND u.user_id = p.poster_id',
		);
		$sql = $this->db->sql_build_query('SELECT', $sql_ary);
		$result = $this->db->sql_query($sql);

		$now = $this->user->create_datetime();
		$now = phpbb_gmgetdate($now->getTimestamp() + $now->getOffset());

		// Posts are stored in the $rowset array while $attach_list, $user_cache
		// and the global bbcode_bitfield are built
		while ($row = $this->db->sql_fetchrow($result))
		{
			// Set max_post_time
			if ($row['post_time'] > $max_post_time)
			{
				$max_post_time = $row['post_time'];
			}

			$poster_id = (int) $row['poster_id'];

			// Does post have an attachment? If so, add it to the list
			if ($row['post_attachment'] && $this->config['allow_attachments'])
			{
				$attach_list[] = (int) $row['post_id'];

				if ($row['post_visibility'] == ITEM_UNAPPROVED || $row['post_visibility'] == ITEM_REAPPROVE)
				{
					$has_unapproved_attachments = true;
				}
				else if ($row['post_visibility'] == ITEM_APPROVED)
				{
					$has_approved_attachments = true;
				}
			}

			$rowset_data = [
				'hide_post'	=> (($row['foe'] || $row['post_visibility'] == ITEM_DELETED) && ($view != 'show' || $post_id != $row['post_id'])) ? true : false,

				'post_id'			=> $row['post_id'],
				'post_time'			=> $row['post_time'],
				'user_id'			=> $row['user_id'],
				'username'			=> $row['username'],
				'user_colour'		=> $row['user_colour'],
				'topic_id'			=> $row['topic_id'],
				'forum_id'			=> $row['forum_id'],
				'post_subject'		=> $row['post_subject'],
				'post_edit_count'	=> $row['post_edit_count'],
				'post_edit_time'	=> $row['post_edit_time'],
				'post_edit_reason'	=> $row['post_edit_reason'],
				'post_edit_user'	=> $row['post_edit_user'],
				'post_edit_locked'	=> $row['post_edit_locked'],
				'post_delete_time'	=> $row['post_delete_time'],
				'post_delete_reason'=> $row['post_delete_reason'],
				'post_delete_user'	=> $row['post_delete_user'],

				// Make sure the icon actually exists
				'icon_id'			=> (isset($icons[$row['icon_id']]['img'], $icons[$row['icon_id']]['height'], $icons[$row['icon_id']]['width'])) ? $row['icon_id'] : 0,
				'post_attachment'	=> $row['post_attachment'],
				'post_visibility'	=> $row['post_visibility'],
				'post_reported'		=> $row['post_reported'],
				'post_username'		=> $row['post_username'],
				'post_text'			=> $row['post_text'],
				'bbcode_uid'		=> $row['bbcode_uid'],
				'bbcode_bitfield'	=> $row['bbcode_bitfield'],
				'enable_smilies'	=> $row['enable_smilies'],
				'enable_sig'		=> $row['enable_sig'],
				'friend'			=> $row['friend'],
				'foe'				=> $row['foe']
			];

			$rowset[$row['post_id']] = $rowset_data;

			// Cache various user specific data ... so we don't have to recompute
			// this each time the same user appears on this page
			if (!isset($user_cache[$poster_id]))
			{
				if ($poster_id == ANONYMOUS)
				{
					$user_cache_data = [
						'user_type'		=> USER_IGNORE,
						'joined'		=> '',
						'last_active'	=> '',
						'posts'			=> '',

						'sig'					=> '',
						'sig_bbcode_uid'		=> '',
						'sig_bbcode_bitfield'	=> '',

						'online'			=> false,
						'avatar'			=> ($this->user->optionget('viewavatars')) ? (($row['user_avatar_type'] == 'avatar.driver.gravatar') ? $this->ext_helper->get_gravatar_url($row) : phpbb_get_user_avatar($row)) : '',
						'rank_title'		=> '',
						'rank_image'		=> '',
						'rank_image_src'	=> '',
						'pm'				=> '',
						'email'				=> '',
						'jabber'			=> '',
						'search'			=> '',
						'age'				=> '',

						'username'		=> $row['username'],
						'user_colour'	=> $row['user_colour'],
						'contact_user'	=> '',

						'warnings'	=> 0,
						'allow_pm'	=> 0
					];

					$user_cache[$poster_id] = $user_cache_data;

					$user_rank_data = phpbb_get_user_rank($row, false);
					$user_cache[$poster_id]['rank_title'] = $user_rank_data['title'];
					$user_cache[$poster_id]['rank_image'] = $user_rank_data['img'];
					$user_cache[$poster_id]['rank_image_src'] = $user_rank_data['img_src'];
				}
				else
				{
					$user_sig = '';

					// We add the signature to every posters entry because enable_sig is post dependent
					if ($row['user_sig'] && $this->config['allow_sig'] && $this->user->optionget('viewsigs'))
					{
						$user_sig = $row['user_sig'];
					}

					$id_cache[] = $poster_id;

					$user_cache_data = [
						'user_type'				=> $row['user_type'],
						'user_inactive_reason'	=> $row['user_inactive_reason'],

						'joined'		=> $this->user->format_date($row['user_regdate']),
						'last_active'	=> $this->user->format_date($row['user_lastvisit']),
						'posts'			=> $row['user_posts'],
						'warnings'		=> (isset($row['user_warnings'])) ? $row['user_warnings'] : 0,

						'sig'					=> $user_sig,
						'sig_bbcode_uid'		=> (!empty($row['user_sig_bbcode_uid'])) ? $row['user_sig_bbcode_uid'] : '',
						'sig_bbcode_bitfield'	=> (!empty($row['user_sig_bbcode_bitfield'])) ? $row['user_sig_bbcode_bitfield'] : '',

						'viewonline'	=> $row['user_allow_viewonline'],
						'allow_pm'		=> $row['user_allow_pm'],

						'avatar'	=> ($this->user->optionget('viewavatars')) ? (($row['user_avatar_type'] == 'avatar.driver.gravatar') ? $this->ext_helper->get_gravatar_url($row) : phpbb_get_user_avatar($row)) : '',
						'age'		=> '',

						'rank_title'		=> '',
						'rank_image'		=> '',
						'rank_image_src'	=> '',

						'username'			=> $row['username'],
						'user_colour'		=> $row['user_colour'],
						'contact_user' 		=> $this->language->lang('CONTACT_USER', get_username_string('username', $poster_id, $row['username'], $row['user_colour'], $row['username'])),

						'online'	=> false,
						'jabber'	=> ($this->config['jab_enable'] && $row['user_jabber'] && $this->auth->acl_get('u_sendim')) ? append_sid("{$this->root_path}memberlist.{$this->php_ext}", "mode=contact&amp;action=jabber&amp;u=$poster_id") : '',
						'search'	=> ($this->config['load_search'] && $this->auth->acl_get('u_search')) ? append_sid("{$this->root_path}search.{$this->php_ext}", "author_id=$poster_id&amp;sr=posts") : '',

						'author_full'		=> get_username_string('full', $poster_id, $row['username'], $row['user_colour']),
						'author_colour'		=> get_username_string('colour', $poster_id, $row['username'], $row['user_colour']),
						'author_username'	=> get_username_string('username', $poster_id, $row['username'], $row['user_colour']),
						'author_profile'	=> get_username_string('profile', $poster_id, $row['username'], $row['user_colour'])
					];

					$user_cache[$poster_id] = $user_cache_data;

					$user_rank_data = phpbb_get_user_rank($row, $row['user_posts']);
					$user_cache[$poster_id]['rank_title'] = $user_rank_data['title'];
					$user_cache[$poster_id]['rank_image'] = $user_rank_data['img'];
					$user_cache[$poster_id]['rank_image_src'] = $user_rank_data['img_src'];

					if ((!empty($row['user_allow_viewemail']) && $this->auth->acl_get('u_sendemail')) || $this->auth->acl_get('a_email'))
					{
						$user_cache[$poster_id]['email'] = ($this->config['board_email_form'] && $this->config['email_enable']) ? append_sid("{$this->root_path}memberlist.{$this->php_ext}", "mode=email&amp;u=$poster_id") : (($this->config['board_hide_emails'] && !$this->auth->acl_get('a_email')) ? '' : 'mailto:' . $row['user_email']);
					}
					else
					{
						$user_cache[$poster_id]['email'] = '';
					}

					if ($this->config['allow_birthdays'] && !empty($row['user_birthday']))
					{
						list($bday_day, $bday_month, $bday_year) = array_map('intval', explode('-', $row['user_birthday']));

						if ($bday_year)
						{
							$diff = $now['mon'] - $bday_month;
							if ($diff == 0)
							{
								$diff = ($now['mday'] - $bday_day < 0) ? 1 : 0;
							}
							else
							{
								$diff = ($diff < 0) ? 1 : 0;
							}

							$user_cache[$poster_id]['age'] = (int) ($now['year'] - $bday_year - $diff);
						}
					}
				}
			}
		}
		$this->db->sql_freeresult($result);

		// Load custom profile fields
		if ($this->config['load_cpf_viewtopic'])
		{
			$profile_fields_cache = $this->load_cpf($id_cache);
		}

		// Generate online information for user
		if ($this->config['load_onlinetrack'] && sizeof($id_cache))
		{
			$sql = 'SELECT session_user_id, MAX(session_time) AS online_time, MIN(session_viewonline) AS viewonline
				FROM ' . SESSIONS_TABLE . '
				WHERE ' . $this->db->sql_in_set('session_user_id', $id_cache) . '
				GROUP BY session_user_id';
			$result = $this->db->sql_query($sql);

			$update_time = $this->config['load_online_time'] * 60;

			while ($row = $this->db->sql_fetchrow($result))
			{
				$user_cache[$row['session_user_id']]['last_active'] = $this->user->format_date($row['online_time']);
				$user_cache[$row['session_user_id']]['online'] = (time() - $update_time < $row['online_time'] && (($row['viewonline']) || $this->auth->acl_get('u_viewonline'))) ? true : false;
			}
			$this->db->sql_freeresult($result);
		}

		unset($id_cache);

		// Pull attachment data
		if (sizeof($attach_list))
		{
			if ($this->auth->acl_get('u_download') && $this->auth->acl_get('f_download', $forum_id))
			{
				$sql = 'SELECT *
					FROM ' . ATTACHMENTS_TABLE . '
					WHERE ' . $this->db->sql_in_set('post_msg_id', $attach_list) . '
						AND in_message = 0
					ORDER BY attach_id DESC, post_msg_id ASC';
				$result = $this->db->sql_query($sql);

				while ($row = $this->db->sql_fetchrow($result))
				{
					$attachments[$row['post_msg_id']][] = $row;
				}
				$this->db->sql_freeresult($result);

				// No attachments exist, but post table thinks they do so go ahead and reset post_attach flags
				if (!sizeof($attachments))
				{
					$sql = 'UPDATE ' . POSTS_TABLE . '
						SET post_attachment = 0
						WHERE ' . $this->db->sql_in_set('post_id', $attach_list);
					$this->db->sql_query($sql);

					// We need to update the topic indicator too if the complete topic is now without an attachment
					if (sizeof($rowset) != $total_posts)
					{
						// Not all posts are displayed so we query the db to find if there's any attachment for this topic
						$sql = 'SELECT a.post_msg_id as post_id
							FROM ' . ATTACHMENTS_TABLE . ' a, ' . POSTS_TABLE . " p
							WHERE p.topic_id = $topic_id
								AND p.post_visibility = " . ITEM_APPROVED . '
								AND p.topic_id = a.topic_id';
						$result = $this->db->sql_query_limit($sql, 1);
						$row = $this->db->sql_fetchrow($result);
						$this->db->sql_freeresult($result);

						if (!$row)
						{
							$sql = 'UPDATE ' . TOPICS_TABLE . "
								SET topic_attachment = 0
								WHERE topic_id = $topic_id";
							$this->db->sql_query($sql);
						}
					}
					else
					{
						$sql = 'UPDATE ' . TOPICS_TABLE . "
							SET topic_attachment = 0
							WHERE topic_id = $topic_id";
						$this->db->sql_query($sql);
					}
				}
				// Topic has approved attachments but its flag is wrong
				else if ($has_approved_attachments && !$topic_data['topic_attachment'])
				{
					$sql = 'UPDATE ' . TOPICS_TABLE . "
						SET topic_attachment = 1
						WHERE topic_id = $topic_id";
					$this->db->sql_query($sql);

					$topic_data['topic_attachment'] = 1;
				}
				// Topic has only unapproved attachments but we have the right to see and download them
				else if ($has_unapproved_attachments && !$topic_data['topic_attachment'])
				{
					$topic_data['topic_attachment'] = 1;
				}
			}
			else
			{
				$display_notice = true;
			}
		}

		// Get the list of users who can receive private messages
		$can_receive_pm_list = $this->auth->acl_get_list(array_keys($user_cache), 'u_readpm');
		$can_receive_pm_list = (empty($can_receive_pm_list) || !isset($can_receive_pm_list[0]['u_readpm'])) ? array() : $can_receive_pm_list[0]['u_readpm'];

		// Get the list of permanently banned users
		$permanently_banned_users = phpbb_get_banned_user_ids(array_keys($user_cache), false);

		$i_total = sizeof($rowset) - 1;
		$prev_post_id = '';

		$this->template->assign_vars([
			'S_HAS_ATTACHMENTS'	=> $topic_data['topic_attachment'],
			'S_NUM_POSTS'		=> sizeof($post_list)
		]);

		// Output the posts
		$first_unread = $post_unread = false;

		for ($i = 0, $end = sizeof($post_list); $i < $end; ++$i)
		{
			// A non-existing rowset only happens if there was no user present for the entered poster_id
			// This could be a broken posts table.
			if (!isset($rowset[$post_list[$i]]))
			{
				continue;
			}

			$row = $rowset[$post_list[$i]];
			$poster_id = $row['user_id'];

			// End signature parsing, only if needed
			if ($user_cache[$poster_id]['sig'] && $row['enable_sig'] && empty($user_cache[$poster_id]['sig_parsed']))
			{
				$parse_flags = ($user_cache[$poster_id]['sig_bbcode_bitfield'] ? OPTION_FLAG_BBCODE : 0) | OPTION_FLAG_SMILIES;
				$user_cache[$poster_id]['sig'] = generate_text_for_display($user_cache[$poster_id]['sig'], $user_cache[$poster_id]['sig_bbcode_uid'], $user_cache[$poster_id]['sig_bbcode_bitfield'],  $parse_flags, true);
				$user_cache[$poster_id]['sig_parsed'] = true;
			}

			// Parse the message and subject
			$parse_flags = ($row['bbcode_bitfield'] ? OPTION_FLAG_BBCODE : 0) | OPTION_FLAG_SMILIES;
			$message = generate_text_for_display($row['post_text'], $row['bbcode_uid'], $row['bbcode_bitfield'], $parse_flags, true);

			if (!empty($attachments[$row['post_id']]))
			{
				parse_attachments($forum_id, $message, $attachments[$row['post_id']], $update_count);
			}

			// Replace naughty words such as farty pants
			$row['post_subject'] = censor_text($row['post_subject']);

			// Highlight active words (primarily for search)
			if ($highlight_match)
			{
				$message = preg_replace('#(?!<.*)(?<!\w)(' . $highlight_match . ')(?!\w|[^<>]*(?:</s(?:cript|tyle))?>)#is', '<span class="posthilit">\1</span>', $message);
				$row['post_subject'] = preg_replace('#(?!<.*)(?<!\w)(' . $highlight_match . ')(?!\w|[^<>]*(?:</s(?:cript|tyle))?>)#is', '<span class="posthilit">\1</span>', $row['post_subject']);
			}

			// Editing information
			if (($row['post_edit_count'] && $this->config['display_last_edited']) || $row['post_edit_reason'])
			{
				// Get usernames for all following posts if not already stored
				if (!sizeof($post_edit_list) && ($row['post_edit_reason'] || ($row['post_edit_user'] && !isset($user_cache[$row['post_edit_user']]))))
				{
					// Remove all post_ids already parsed (we do not have to check them)
					$post_storage_list = (!$store_reverse) ? array_slice($post_list, $i) : array_slice(array_reverse($post_list), $i);

					$sql = 'SELECT DISTINCT u.user_id, u.username, u.user_colour
						FROM ' . POSTS_TABLE . ' p, ' . USERS_TABLE . ' u
						WHERE ' . $this->db->sql_in_set('p.post_id', $post_storage_list) . '
							AND p.post_edit_count <> 0
							AND p.post_edit_user <> 0
							AND p.post_edit_user = u.user_id';
					$result2 = $this->db->sql_query($sql);
					while ($user_edit_row = $this->db->sql_fetchrow($result2))
					{
						$post_edit_list[$user_edit_row['user_id']] = $user_edit_row;
					}
					$this->db->sql_freeresult($result2);

					unset($post_storage_list);
				}

				if ($row['post_edit_reason'])
				{
					// User having edited the post also being the post author?
					if (!$row['post_edit_user'] || $row['post_edit_user'] == $poster_id)
					{
						$display_username = get_username_string('full', $poster_id, $row['username'], $row['user_colour'], $row['post_username']);
					}
					else
					{
						$display_username = get_username_string('full', $row['post_edit_user'], $post_edit_list[$row['post_edit_user']]['username'], $post_edit_list[$row['post_edit_user']]['user_colour']);
					}

					$l_edited_by = $this->language->lang('EDITED_TIMES_TOTAL', (int) $row['post_edit_count'], $display_username, $this->user->format_date($row['post_edit_time'], false, true));
				}
				else
				{
					if ($row['post_edit_user'] && !isset($user_cache[$row['post_edit_user']]))
					{
						$user_cache[$row['post_edit_user']] = $post_edit_list[$row['post_edit_user']];
					}

					// User having edited the post also being the post author?
					if (!$row['post_edit_user'] || $row['post_edit_user'] == $poster_id)
					{
						$display_username = get_username_string('full', $poster_id, $row['username'], $row['user_colour'], $row['post_username']);
					}
					else
					{
						$display_username = get_username_string('full', $row['post_edit_user'], $user_cache[$row['post_edit_user']]['username'], $user_cache[$row['post_edit_user']]['user_colour']);
					}

					$l_edited_by = $this->language->lang('EDITED_TIMES_TOTAL', (int) $row['post_edit_count'], $display_username, $this->user->format_date($row['post_edit_time'], false, true));
				}
			}
			else
			{
				$l_edited_by = '';
			}

			// Deleting information
			if ($row['post_visibility'] == ITEM_DELETED && $row['post_delete_user'])
			{
				// Get usernames for all following posts if not already stored
				if (!sizeof($post_delete_list) && ($row['post_delete_reason'] || ($row['post_delete_user'] && !isset($user_cache[$row['post_delete_user']]))))
				{
					// Remove all post_ids already parsed (we do not have to check them)
					$post_storage_list = (!$store_reverse) ? array_slice($post_list, $i) : array_slice(array_reverse($post_list), $i);

					$sql = 'SELECT DISTINCT u.user_id, u.username, u.user_colour
						FROM ' . POSTS_TABLE . ' p, ' . USERS_TABLE . ' u
						WHERE ' . $this->db->sql_in_set('p.post_id', $post_storage_list) . '
							AND p.post_delete_user <> 0
							AND p.post_delete_user = u.user_id';
					$result2 = $this->db->sql_query($sql);
					while ($user_delete_row = $this->db->sql_fetchrow($result2))
					{
						$post_delete_list[$user_delete_row['user_id']] = $user_delete_row;
					}
					$this->db->sql_freeresult($result2);

					unset($post_storage_list);
				}

				if ($row['post_delete_user'] && !isset($user_cache[$row['post_delete_user']]))
				{
					$user_cache[$row['post_delete_user']] = $post_delete_list[$row['post_delete_user']];
				}

				$display_postername = get_username_string('full', $poster_id, $row['username'], $row['user_colour'], $row['post_username']);

				// User having deleted the post also being the post author?
				if (!$row['post_delete_user'] || $row['post_delete_user'] == $poster_id)
				{
					$display_username = $display_postername;
				}
				else
				{
					$display_username = get_username_string('full', $row['post_delete_user'], $user_cache[$row['post_delete_user']]['username'], $user_cache[$row['post_delete_user']]['user_colour']);
				}

				if ($row['post_delete_reason'])
				{
					$l_deleted_message = $this->language->lang('POST_DELETED_BY_REASON', $display_postername, $display_username, $this->user->format_date($row['post_delete_time'], false, true), $row['post_delete_reason']);
				}
				else
				{
					$l_deleted_message = $this->language->lang('POST_DELETED_BY', $display_postername, $display_username, $this->user->format_date($row['post_delete_time'], false, true));
				}
				$l_deleted_by = $this->language->lang('DELETED_INFORMATION', $display_username, $this->user->format_date($row['post_delete_time'], false, true));
			}
			else
			{
				$l_deleted_by = $l_deleted_message = '';
			}

			// Bump information
			if ($topic_data['topic_bumped'] && $row['post_id'] == $topic_data['topic_last_post_id'] && isset($user_cache[$topic_data['topic_bumper']]) )
			{
				// It is safe to grab the username from the user cache array, we are at the last
				// post and only the topic poster and last poster are allowed to bump.
				// Admins and mods are bound to the above rules too...
				$l_bumped_by = $this->language->lang('BUMPED_BY', $user_cache[$topic_data['topic_bumper']]['username'], $this->user->format_date($topic_data['topic_last_post_time'], false, true));
			}
			else
			{
				$l_bumped_by = '';
			}

			$cp_row = array();

			//
			if ($this->config['load_cpf_viewtopic'])
			{
				$cp_row = (isset($profile_fields_cache[$poster_id])) ? $this->profile_fields->generate_profile_fields_template_data($profile_fields_cache[$poster_id]) : array();
			}

			$post_unread = (isset($topic_tracking_info[$topic_id]) && $row['post_time'] > $topic_tracking_info[$topic_id]) ? true : false;

			$s_first_unread = false;
			if (!$first_unread && $post_unread)
			{
				$s_first_unread = $first_unread = true;
			}

			$force_edit_allowed = $force_delete_allowed = false;

			$s_cannot_edit = !$this->auth->acl_get('f_edit', $forum_id) || $this->user->data['user_id'] != $poster_id;
			$s_cannot_edit_time = $this->config['edit_time'] && $row['post_time'] <= time() - ($this->config['edit_time'] * 60);
			$s_cannot_edit_locked = $topic_data['topic_status'] == ITEM_LOCKED || $row['post_edit_locked'];

			$s_cannot_delete = $this->user->data['user_id'] != $poster_id || (
				!$this->auth->acl_get('f_delete', $forum_id) &&
				(!$this->auth->acl_get('f_softdelete', $forum_id) || $row['post_visibility'] == ITEM_DELETED)
			);
			$s_cannot_delete_lastpost = $topic_data['topic_last_post_id'] != $row['post_id'];
			$s_cannot_delete_time = $this->config['delete_time'] && $row['post_time'] <= time() - ($this->config['delete_time'] * 60);
			// we do not want to allow removal of the last post if a moderator locked it!
			$s_cannot_delete_locked = $topic_data['topic_status'] == ITEM_LOCKED || $row['post_edit_locked'];

			$edit_allowed = $force_edit_allowed || ($this->user->data['is_registered'] && ($this->auth->acl_get('m_edit', $forum_id) || (
				!$s_cannot_edit &&
				!$s_cannot_edit_time &&
				!$s_cannot_edit_locked
			)));

			$quote_allowed = $this->auth->acl_get('m_edit', $forum_id) || ($topic_data['topic_status'] != ITEM_LOCKED &&
				($this->user->data['user_id'] == ANONYMOUS || $this->auth->acl_get('f_reply', $forum_id))
			);

			// Only display the quote button if the post is quotable.  Posts not approved are not quotable.
			$quote_allowed = ($quote_allowed && $row['post_visibility'] == ITEM_APPROVED) ? true : false;

			$delete_allowed = $force_delete_allowed || ($this->user->data['is_registered'] && (
				($this->auth->acl_get('m_delete', $forum_id) || ($this->auth->acl_get('m_softdelete', $forum_id) && $row['post_visibility'] != ITEM_DELETED)) ||
				(!$s_cannot_delete && !$s_cannot_delete_lastpost && !$s_cannot_delete_time && !$s_cannot_delete_locked)
			));

			$softdelete_allowed = ($this->auth->acl_get('m_softdelete', $forum_id) ||
				($this->auth->acl_get('f_softdelete', $forum_id) && $this->user->data['user_id'] == $poster_id)) && ($row['post_visibility'] != ITEM_DELETED);

			$permanent_delete_allowed = ($this->auth->acl_get('m_delete', $forum_id) ||
				($this->auth->acl_get('f_delete', $forum_id) && $this->user->data['user_id'] == $poster_id));

			// Can this user receive a Private Message?
			$can_receive_pm = (
				// They must be a "normal" user
				$user_cache[$poster_id]['user_type'] != USER_IGNORE &&

				// They must not be deactivated by the administrator
				($user_cache[$poster_id]['user_type'] != USER_INACTIVE || $user_cache[$poster_id]['user_inactive_reason'] != INACTIVE_MANUAL) &&

				// They must be able to read PMs
				in_array($poster_id, $can_receive_pm_list) &&

				// They must not be permanently banned
				!in_array($poster_id, $permanently_banned_users) &&

				// They must allow users to contact via PM
				(($this->auth->acl_gets('a_', 'm_') || $this->auth->acl_getf_global('m_')) || $user_cache[$poster_id]['allow_pm'])
			);

			$u_pm = '';

			if ($this->config['allow_privmsg'] && $this->auth->acl_get('u_sendpm') && $can_receive_pm)
			{
				$u_pm = append_sid("{$this->root_path}ucp.{$this->php_ext}", 'i=pm&amp;mode=compose&amp;action=quotepost&amp;p=' . $row['post_id']);
			}

			//
			$post_row = [
				'POST_AUTHOR_FULL'		=> ($poster_id != ANONYMOUS) ? $user_cache[$poster_id]['author_full'] : get_username_string('full', $poster_id, $row['username'], $row['user_colour'], $row['post_username']),
				'POST_AUTHOR_COLOUR'	=> ($poster_id != ANONYMOUS) ? $user_cache[$poster_id]['author_colour'] : get_username_string('colour', $poster_id, $row['username'], $row['user_colour'], $row['post_username']),
				'POST_AUTHOR'			=> ($poster_id != ANONYMOUS) ? $user_cache[$poster_id]['author_username'] : get_username_string('username', $poster_id, $row['username'], $row['user_colour'], $row['post_username']),
				'U_POST_AUTHOR'			=> ($poster_id != ANONYMOUS) ? $user_cache[$poster_id]['author_profile'] : get_username_string('profile', $poster_id, $row['username'], $row['user_colour'], $row['post_username']),

				'RANK_TITLE_RAW'		=> $user_cache[$poster_id]['rank_title'],
				'RANK_TITLE'			=> ($this->language->is_set(['RANK_TITLES', strtoupper($user_cache[$poster_id]['rank_title'])])) ? $this->language->lang(['RANK_TITLES', strtoupper($user_cache[$poster_id]['rank_title'])]) : $user_cache[$poster_id]['rank_title'],
				'RANK_IMG'				=> $user_cache[$poster_id]['rank_image'],
				'RANK_IMG_SRC'			=> $user_cache[$poster_id]['rank_image_src'],
				'POSTER_JOINED'			=> $user_cache[$poster_id]['joined'],
				'POSTER_LAST_ACTIVE'	=> $user_cache[$poster_id]['last_active'],
				'POSTER_POSTS'			=> $user_cache[$poster_id]['posts'],
				'POSTER_AVATAR'			=> $user_cache[$poster_id]['avatar'],
				'POSTER_WARNINGS'		=> $this->auth->acl_get('m_warn') ? $user_cache[$poster_id]['warnings'] : '',
				'POSTER_AGE'			=> $user_cache[$poster_id]['age'],
				'CONTACT_USER'			=> $user_cache[$poster_id]['contact_user'],

				'POST_DATE'			=> $this->user->format_date($row['post_time'], false, ($view == 'print') ? true : false),
				'POST_SUBJECT'		=> $row['post_subject'],
				'MESSAGE'			=> $message,
				'SIGNATURE'			=> ($row['enable_sig']) ? $user_cache[$poster_id]['sig'] : '',
				'EDITED_MESSAGE'	=> $l_edited_by,
				'EDIT_REASON'		=> $row['post_edit_reason'],
				'DELETED_MESSAGE'	=> $l_deleted_by,
				'DELETE_REASON'		=> $row['post_delete_reason'],
				'BUMPED_MESSAGE'	=> $l_bumped_by,

				'POST_ICON_IMG'			=> ($topic_data['enable_icons'] && !empty($row['icon_id'])) ? $icons[$row['icon_id']]['img'] : '',
				'POST_ICON_IMG_WIDTH'	=> ($topic_data['enable_icons'] && !empty($row['icon_id'])) ? $icons[$row['icon_id']]['width'] : '',
				'POST_ICON_IMG_HEIGHT'	=> ($topic_data['enable_icons'] && !empty($row['icon_id'])) ? $icons[$row['icon_id']]['height'] : '',
				'POST_ICON_IMG_ALT' 	=> ($topic_data['enable_icons'] && !empty($row['icon_id'])) ? $icons[$row['icon_id']]['alt'] : '',
				'S_ONLINE'				=> ($poster_id == ANONYMOUS || !$this->config['load_onlinetrack']) ? false : (($user_cache[$poster_id]['online']) ? true : false),

				'U_EDIT'			=> ($edit_allowed) ? append_sid("{$this->root_path}posting.{$this->php_ext}", "mode=edit&amp;f=$forum_id&amp;p={$row['post_id']}") : '',
				'U_QUOTE'			=> ($quote_allowed) ? append_sid("{$this->root_path}posting.{$this->php_ext}", "mode=quote&amp;f=$forum_id&amp;p={$row['post_id']}") : '',
				'U_INFO'			=> ($this->auth->acl_get('m_info', $forum_id)) ? append_sid("{$this->root_path}mcp.{$this->php_ext}", "i=main&amp;mode=post_details&amp;f=$forum_id&amp;p=" . $row['post_id'], true, $this->user->session_id) : '',
				'U_DELETE'			=> ($delete_allowed) ? append_sid("{$this->root_path}posting.{$this->php_ext}", 'mode=' . (($softdelete_allowed) ? 'soft_delete' : 'delete') . "&amp;f=$forum_id&amp;p={$row['post_id']}") : '',

				'U_SEARCH'		=> $user_cache[$poster_id]['search'],
				'U_PM'			=> $u_pm,
				'U_EMAIL'		=> $user_cache[$poster_id]['email'],
				'U_JABBER'		=> $user_cache[$poster_id]['jabber'],

				'U_APPROVE_ACTION'		=> append_sid("{$this->root_path}mcp.{$this->php_ext}", "i=queue&amp;p={$row['post_id']}&amp;f=$forum_id&amp;redirect=" . urlencode(str_replace('&amp;', '&', $viewtopic_url . '&amp;p=' . $row['post_id'] . '#p' . $row['post_id']))),
				'U_REPORT'			=> ($this->auth->acl_get('f_report', $forum_id)) ? $this->helper->route('phpbb_report_post_controller', ['id' => $row['post_id']]) : '',
				'U_MCP_REPORT'		=> ($this->auth->acl_get('m_report', $forum_id)) ? append_sid("{$this->root_path}mcp.{$this->php_ext}", 'i=reports&amp;mode=report_details&amp;f=' . $forum_id . '&amp;p=' . $row['post_id'], true, $this->user->session_id) : '',
				'U_MCP_APPROVE'		=> ($this->auth->acl_get('m_approve', $forum_id)) ? append_sid("{$this->root_path}mcp.{$this->php_ext}", 'i=queue&amp;mode=approve_details&amp;f=' . $forum_id . '&amp;p=' . $row['post_id'], true, $this->user->session_id) : '',
				'U_MCP_RESTORE'		=> ($this->auth->acl_get('m_approve', $forum_id)) ? append_sid("{$this->root_path}mcp.{$this->php_ext}", 'i=queue&amp;mode=' . (($topic_data['topic_visibility'] != ITEM_DELETED) ? 'deleted_posts' : 'deleted_topics') . '&amp;f=' . $forum_id . '&amp;p=' . $row['post_id'], true, $this->user->session_id) : '',
				'U_MINI_POST'		=> append_sid("{$this->root_path}viewtopic.{$this->php_ext}", 'p=' . $row['post_id']) . '#p' . $row['post_id'],
				'U_NEXT_POST_ID'	=> ($i < $i_total && isset($rowset[$post_list[$i + 1]])) ? $rowset[$post_list[$i + 1]]['post_id'] : '',
				'U_PREV_POST_ID'	=> $prev_post_id,
				'U_NOTES'			=> ($this->auth->acl_getf_global('m_')) ? append_sid("{$this->root_path}mcp.{$this->php_ext}", 'i=notes&amp;mode=user_notes&amp;u=' . $poster_id, true, $this->user->session_id) : '',
				'U_WARN'			=> ($this->auth->acl_get('m_warn') && $poster_id != $this->user->data['user_id'] && $poster_id != ANONYMOUS) ? append_sid("{$this->root_path}mcp.{$this->php_ext}", 'i=warn&amp;mode=warn_post&amp;f=' . $forum_id . '&amp;p=' . $row['post_id'], true, $this->user->session_id) : '',

				'POST_ID'			=> $row['post_id'],
				'POST_NUMBER'		=> $i + $start + 1,
				'POSTER_ID'			=> $poster_id,
				'MINI_POST'			=> $this->language->lang(($post_unread) ? 'UNREAD_POST' : 'POST'),

				'S_HAS_ATTACHMENTS'	=> (!empty($attachments[$row['post_id']])) ? true : false,
				'S_MULTIPLE_ATTACHMENTS'	=> !empty($attachments[$row['post_id']]) && sizeof($attachments[$row['post_id']]) > 1,
				'S_POST_UNAPPROVED'	=> ($row['post_visibility'] == ITEM_UNAPPROVED || $row['post_visibility'] == ITEM_REAPPROVE) ? true : false,
				'S_POST_DELETED'	=> ($row['post_visibility'] == ITEM_DELETED) ? true : false,
				'L_POST_DELETED_MESSAGE'	=> $l_deleted_message,
				'S_POST_REPORTED'	=> ($row['post_reported'] && $this->auth->acl_get('m_report', $forum_id)) ? true : false,
				'S_DISPLAY_NOTICE'	=> $display_notice && $row['post_attachment'],
				'S_FRIEND'			=> ($row['friend']) ? true : false,
				'S_UNREAD_POST'		=> $post_unread,
				'S_FIRST_UNREAD'	=> $s_first_unread,
				'S_CUSTOM_FIELDS'	=> (isset($cp_row['row']) && sizeof($cp_row['row'])) ? true : false,
				'S_TOPIC_POSTER'	=> ($topic_data['topic_poster'] == $poster_id) ? true : false,

				'S_IGNORE_POST'		=> ($row['foe']) ? true : false,
				'L_IGNORE_POST'		=> ($row['foe']) ? $this->language->lang('POST_BY_FOE', get_username_string('full', $poster_id, $row['username'], $row['user_colour'], $row['post_username'])) : '',
				'S_POST_HIDDEN'		=> $row['hide_post'],
				'L_POST_DISPLAY'	=> ($row['hide_post']) ? $this->language->lang('POST_DISPLAY', '<a class="display_post" data-post-id="' . $row['post_id'] . '" href="' . $viewtopic_url . "&amp;p={$row['post_id']}&amp;view=show#p{$row['post_id']}" . '">', '</a>') : '',
				'S_DELETE_PERMANENT'	=> $permanent_delete_allowed
			];

			if (isset($cp_row['row']) && sizeof($cp_row['row']))
			{
				$post_row = array_merge($post_row, $cp_row['row']);
			}

			// Dump vars into template
			$this->template->assign_block_vars('postrow', $post_row);

			$contact_fields = [
				[
					'ID'		=> 'pm',
					'NAME' 		=> $this->language->lang('SEND_PRIVATE_MESSAGE'),
					'U_CONTACT'	=> $u_pm
				],
				[
					'ID'		=> 'email',
					'NAME'		=> $this->language->lang('SEND_EMAIL'),
					'U_CONTACT'	=> $user_cache[$poster_id]['email']
				],
				[
					'ID'		=> 'jabber',
					'NAME'		=> $this->language->lang('JABBER'),
					'U_CONTACT'	=> $user_cache[$poster_id]['jabber']
				]
			];

			foreach ($contact_fields as $field)
			{
				if ($field['U_CONTACT'])
				{
					$this->template->assign_block_vars('postrow.contact', $field);
				}
			}

			if (!empty($cp_row['blockrow']))
			{
				foreach ($cp_row['blockrow'] as $field_data)
				{
					$this->template->assign_block_vars('postrow.custom_fields', $field_data);

					if ($field_data['S_PROFILE_CONTACT'])
					{
						$this->template->assign_block_vars('postrow.contact', [
							'ID'		=> $field_data['PROFILE_FIELD_IDENT'],
							'NAME'		=> $field_data['PROFILE_FIELD_NAME'],
							'U_CONTACT'	=> $field_data['PROFILE_FIELD_CONTACT']
						]);
					}
				}
			}

			// Display not already displayed Attachments for this post, we already parsed them. ;)
			if (!empty($attachments[$row['post_id']]))
			{
				foreach ($attachments[$row['post_id']] as $attachment)
				{
					$this->template->assign_block_vars('postrow.attachment', [
						'DISPLAY_ATTACHMENT'	=> $attachment
					]);
				}
			}

			$prev_post_id = $row['post_id'];

			unset($rowset[$post_list[$i]]);
			unset($attachments[$row['post_id']]);
		}

		unset($rowset, $user_cache);

		// Update topic view and if necessary attachment view counters ... but only for humans and if this is the first 'page view'
		if (isset($this->user->data['session_page']) && !$this->user->data['is_bot'] && (strpos($this->user->data['session_page'], '&t=' . $topic_id) === false || isset($this->user->data['session_created'])))
		{
			$sql = 'UPDATE ' . TOPICS_TABLE . '
				SET topic_views = topic_views + 1, topic_last_view_time = ' . time() . "
				WHERE topic_id = $topic_id";
			$this->db->sql_query($sql);

			// Update the attachment download counts
			if (sizeof($update_count))
			{
				$sql = 'UPDATE ' . ATTACHMENTS_TABLE . '
					SET download_count = download_count + 1
					WHERE ' . $this->db->sql_in_set('attach_id', array_unique($update_count));
				$this->db->sql_query($sql);
			}
		}

		// Only mark topic if it's currently unread. Also make sure we do not set topic tracking back if earlier pages are viewed.
		if (isset($topic_tracking_info[$topic_id]) && $topic_data['topic_last_post_time'] > $topic_tracking_info[$topic_id] && $max_post_time > $topic_tracking_info[$topic_id])
		{
			markread('topic', $forum_id, $topic_id, $max_post_time);

			// Update forum info
			$all_marked_read = update_forum_tracking_info($forum_id, $topic_data['forum_last_post_time'], (isset($topic_data['forum_mark_time'])) ? $topic_data['forum_mark_time'] : false, false);
		}
		else
		{
			$all_marked_read = true;
		}

		// If there are absolutely no more unread posts in this forum
		// and unread posts shown, we can safely show the #unread link
		if ($all_marked_read)
		{
			if ($post_unread)
			{
				$this->template->assign_var('U_VIEW_UNREAD_POST', '#unread');
			}
			else if (isset($topic_tracking_info[$topic_id]) && $topic_data['topic_last_post_time'] > $topic_tracking_info[$topic_id])
			{
				$this->template->assign_var('U_VIEW_UNREAD_POST', append_sid("{$this->root_path}viewtopic.{$this->php_ext}", "f=$forum_id&amp;t=$topic_id&amp;view=unread") . '#unread');
			}
		}
		else if (!$all_marked_read)
		{
			$last_page = ((floor($start / $this->config['posts_per_page']) + 1) == max(ceil($total_posts / $this->config['posts_per_page']), 1)) ? true : false;

			// What can happen is that we are at the last displayed page. If so, we also display the #unread link based in $post_unread
			if ($last_page && $post_unread)
			{
				$this->template->assign_var('U_VIEW_UNREAD_POST', '#unread');
			}
			else if (!$last_page)
			{
				$this->template->assign_var('U_VIEW_UNREAD_POST', append_sid("{$this->root_path}viewtopic.{$this->php_ext}", "f=$forum_id&amp;t=$topic_id&amp;view=unread") . '#unread');
			}
		}

		// Let's set up quick_reply
		$s_quick_reply = false;

		if ($this->user->data['is_registered'] && $this->config['allow_quick_reply'] && ($topic_data['forum_flags'] & FORUM_FLAG_QUICK_REPLY) && $this->auth->acl_get('f_reply', $forum_id))
		{
			// Quick reply enabled forum
			$s_quick_reply = (($topic_data['forum_status'] == ITEM_UNLOCKED && $topic_data['topic_status'] == ITEM_UNLOCKED) || $this->auth->acl_get('m_edit', $forum_id)) ? true : false;
		}

		if ($s_can_vote || $s_quick_reply)
		{
			add_form_key('posting');

			if ($s_quick_reply)
			{
				// Load the WYSIWYG editor
				$this->ext_helper->load_sceditor();

				$s_attach_sig = $this->config['allow_sig'] && $this->user->optionget('attachsig') && $this->auth->acl_get('f_sigs', $forum_id) && $this->auth->acl_get('u_sig');
				$s_smilies = $this->config['allow_smilies'] && $this->user->optionget('smilies') && $this->auth->acl_get('f_smilies', $forum_id);
				$s_bbcode = $this->config['allow_bbcode'] && $this->user->optionget('bbcode') && $this->auth->acl_get('f_bbcode', $forum_id);
				$s_notify = $this->config['allow_topic_notify'] && ($this->user->data['user_notify'] || $s_watching_topic['is_watching']);

				$qr_hidden_fields = [
					'topic_cur_post_id'	=> (int) $topic_data['topic_last_post_id'],
					'lastclick'			=> (int) time(),
					'topic_id'			=> (int) $topic_data['topic_id'],
					'forum_id'			=> (int) $forum_id
				];

				// Originally we use checkboxes and check with isset(), so we only provide them if they would be checked
				(!$s_bbcode)					? $qr_hidden_fields['disable_bbcode'] = 1		: true;
				(!$s_smilies)					? $qr_hidden_fields['disable_smilies'] = 1		: true;
				(!$this->config['allow_post_links'])	? $qr_hidden_fields['disable_magic_url'] = 1	: true;
				($s_attach_sig)					? $qr_hidden_fields['attach_sig'] = 1			: true;
				($s_notify)						? $qr_hidden_fields['notify'] = 1				: true;
				($topic_data['topic_status'] == ITEM_LOCKED) ? $qr_hidden_fields['lock_topic'] = 1 : true;

				$this->template->assign_vars([
					'S_QUICK_REPLY'		=> true,
					'U_QR_ACTION'		=> append_sid("{$this->root_path}posting.{$this->php_ext}", "mode=reply&amp;f=$forum_id&amp;t=$topic_id"),
					'QR_HIDDEN_FIELDS'	=> build_hidden_fields($qr_hidden_fields),
					'SUBJECT'			=> 'Re: ' . censor_text($topic_data['topic_title'])
				]);
			}
		}
		// now I have the urge to wash my hands :(

		// We overwrite $_REQUEST['f'] if there is no forum specified
		// to be able to display the correct online list.
		// One downside is that the user currently viewing this topic/post is not taken into account.
		if (!$this->request->variable('f', 0))
		{
			$this->request->overwrite('f', $forum_id);
		}

		// We need to do the same with the topic_id. See #53025.
		if (!$this->request->variable('t', 0) && !empty($topic_id))
		{
			$this->request->overwrite('t', $topic_id);
		}

		$page_title = $topic_data['topic_title'] . ($start ? ' - ' . $this->language->lang('PAGE_TITLE_NUMBER', $this->pagination->get_on_page($this->config['posts_per_page'], $start)) : '');

		return $this->helper->render(($view == 'print') ? 'viewtopic_print.html' : 'viewtopic_body.html', $page_title, 200, true, $forum_id);
	}

	protected function bookmark($topic_id, $topic_url, $bookmarked)
	{
		if (check_link_hash($this->request->variable('hash', ''), "topic_$topic_id"))
		{
			if (!$bookmarked)
			{
				$sql = 'INSERT INTO ' . BOOKMARKS_TABLE . ' ' . $this->db->sql_build_array('INSERT', array(
					'user_id'	=> $this->user->data['user_id'],
					'topic_id'	=> $topic_id,
				));
				$this->db->sql_query($sql);
			}
			else
			{
				$sql = 'DELETE FROM ' . BOOKMARKS_TABLE . "
						WHERE user_id = {$this->user->data['user_id']}
							AND topic_id = $topic_id";
				$this->db->sql_query($sql);
			}
			$message = (($bookmarked) ? $this->language->lang('BOOKMARK_REMOVED') : $this->language->lang('BOOKMARK_ADDED'));

			if (!$this->request->is_ajax())
			{
				$message .= '<br><br>' . $this->language->lang('RETURN_TOPIC', '<a href="' . $topic_url . '">', '</a>');
			}
		}
		else
		{
			$message = $this->language->lang('BOOKMARK_ERR');

			if (!$this->request->is_ajax())
			{
				$message .= '<br><br>' . $this->language->lang('RETURN_TOPIC', '<a href="' . $topic_url . '">', '</a>');
			}
		}

		meta_refresh(3, $topic_url);
		trigger_error($message);
	}

	protected function load_cpf($id_cache)
	{
		// Grab all profile fields from users in id cache for later use - similar to the poster cache
		$profile_fields_tmp = $this->profile_fields->grab_profile_fields_data($id_cache);

		// filter out fields not to be displayed on viewtopic. Yes, it's a hack, but this shouldn't break any MODs.
		$profile_fields_cache = [];

		foreach ($profile_fields_tmp as $profile_user_id => $profile_fields)
		{
			$profile_fields_cache[$profile_user_id] = [];

			foreach ($profile_fields as $used_ident => $profile_field)
			{
				if ($profile_field['data']['field_show_on_vt'])
				{
					$profile_fields_cache[$profile_user_id][$used_ident] = $profile_field;
				}
			}
		}

		unset($profile_fields_tmp);

		return $profile_fields_cache;
	}
}
