<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\controller;

use vinabb\web\includes\constants;

class portal
{
	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\cache\service */
	protected $cache;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\content_visibility */
	protected $content_visibility;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\event\dispatcher_interface */
	protected $dispatcher;

	/** @var \phpbb\file_downloader */
	protected $file_downloader;

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
	* @param \phpbb\cache\service $cache
	* @param \phpbb\config\config $config
	* @param \phpbb\content_visibility $content_visibility
	* @param \phpbb\db\driver\driver_interface $db
	* @param \phpbb\event\dispatcher_interface $dispatcher
	* @param \phpbb\file_downloader $file_downloader
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
		\phpbb\cache\service $cache,
		\phpbb\config\config $config,
		\phpbb\content_visibility $content_visibility,
		\phpbb\db\driver\driver_interface $db,
		\phpbb\event\dispatcher_interface $dispatcher,
		\phpbb\file_downloader $file_downloader,
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
		$this->cache = $cache;
		$this->config = $config;
		$this->content_visibility = $content_visibility;
		$this->db = $db;
		$this->dispatcher = $dispatcher;
		$this->file_downloader = $file_downloader;
		$this->language = $language;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->helper = $helper;
		$this->group_helper = $group_helper;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;

		$this->forum_data = $this->cache->get_forum_data();
	}

	public function index()
	{
		// Check new versions
		if (time() > $this->config['vinabb_web_check_gc'] + (constants::CHECK_VERSION_HOURS * 60 * 60))
		{
			// Get latest phpBB versions
			if ($this->config['vinabb_web_check_phpbb_url'])
			{
				$raw = $this->fetch_url($this->config['vinabb_web_check_phpbb_url']);

				// Parse JSON
				if (!empty($raw))
				{
					$phpbb_data = json_decode($raw, true);

					if ($this->config['vinabb_web_check_phpbb_branch'])
					{
						$latest_phpbb_version = isset($phpbb_data['stable'][$this->config['vinabb_web_check_phpbb_branch']]['current']) ? $phpbb_data['stable'][$this->config['vinabb_web_check_phpbb_branch']]['current'] : $this->config['vinabb_web_check_phpbb_branch'] . '.x';
						$this->config->set('vinabb_web_check_phpbb_version', $latest_phpbb_version);
					}

					if ($this->config['vinabb_web_check_phpbb_legacy_branch'])
					{
						$latest_phpbb_legacy_version = isset($phpbb_data['stable'][$this->config['vinabb_web_check_phpbb_legacy_branch']]['current']) ? $phpbb_data['stable'][$this->config['vinabb_web_check_phpbb_legacy_branch']]['current'] : $this->config['vinabb_web_check_phpbb_legacy_branch'] . '.x';
						$this->config->set('vinabb_web_check_phpbb_legacy_version', $latest_phpbb_legacy_version);
					}
				}
			}

			// Get latest PHP versions
			if ($this->config['vinabb_web_check_php_url'])
			{
				$raw = $this->fetch_url($this->config['vinabb_web_check_php_url']);

				// Parse XML
				if (!empty($raw))
				{
					$php_data = simplexml_load_string($raw);
				}
			}

			// Save this time
			$this->config->set('vinabb_web_check_gc', time(), true);
		}

		// Latest topics
		$sql_ary = $this->get_latest_topics_sql();

		if ($sql_ary !== false)
		{
			$sql = $this->db->sql_build_query('SELECT', $sql_ary);
			$result = $this->db->sql_query_limit($sql, constants::NUM_NEW_ITEMS_ON_INDEX);
			$rows = $this->db->sql_fetchrowset($result);
			$this->db->sql_freeresult($result);

			foreach ($rows as $row)
			{
				$this->template->assign_block_vars('latest_topics', array(
					'TITLE'	=> truncate_string($row['topic_title'], 48, 255, false, $this->language->lang('ELLIPSIS')),
				));
			}
		}

		// Latest posts
		$sql_ary = $this->get_latest_posts_sql();

		if ($sql_ary !== false)
		{
			$sql = $this->db->sql_build_query('SELECT', $sql_ary);
			$result = $this->db->sql_query_limit($sql, constants::NUM_NEW_ITEMS_ON_INDEX);
			$rows = $this->db->sql_fetchrowset($result);
			$this->db->sql_freeresult($result);

			foreach ($rows as $row)
			{
				$this->template->assign_block_vars('latest_posts', array(
					'SUBJECT'	=> truncate_string($row['post_subject'], 48, 255, false, $this->language->lang('ELLIPSIS')),
				));
			}
		}

		// Latest users
		$sql = 'SELECT user_id, username, user_colour
			FROM ' . USERS_TABLE . '
			WHERE ' . $this->db->sql_in_set('user_type', array(USER_NORMAL, USER_FOUNDER)) . '
			ORDER BY user_regdate DESC';
		$result = $this->db->sql_query_limit($sql, constants::NUM_NEW_ITEMS_ON_INDEX);
		$rows = $this->db->sql_fetchrowset($result);
		$this->db->sql_freeresult($result);

		foreach ($rows as $row)
		{
			$this->template->assign_block_vars('latest_users', array(
				'NAME'	=> get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']),
			));
		}

		// Group legend for online users
		$order_legend = ($this->config['legend_sort_groupname']) ? 'group_name' : 'group_legend';

		if ($this->auth->acl_gets('a_group', 'a_groupadd', 'a_groupdel'))
		{
			$sql = 'SELECT group_id, group_name, group_colour, group_type, group_legend
				FROM ' . GROUPS_TABLE . '
				WHERE group_legend > 0
				ORDER BY ' . $order_legend;
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
				ORDER BY g.' . $order_legend;
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

		// Birthday list
		$birthdays = array();

		if ($this->config['load_birthdays'] && $this->config['allow_birthdays'] && $this->auth->acl_gets('u_viewprofile', 'a_user', 'a_useradd', 'a_userdel'))
		{
			$time = $this->user->create_datetime();
			$now = phpbb_gmgetdate($time->getTimestamp() + $time->getOffset());

			// Display birthdays of 29th february on 28th february in non-leap-years
			$leap_year_birthdays = '';

			if ($now['mday'] == 28 && $now['mon'] == 2 && !$time->format('L'))
			{
				$leap_year_birthdays = " OR u.user_birthday LIKE '" . $this->db->sql_escape(sprintf('%2d-%2d-', 29, 2)) . "%'";
			}
			$sql_ary = array(
				'SELECT' => 'u.user_id, u.username, u.user_colour, u.user_birthday',
				'FROM' => array(
					USERS_TABLE => 'u',
				),
				'LEFT_JOIN' => array(
					array(
						'FROM' => array(BANLIST_TABLE => 'b'),
						'ON' => 'u.user_id = b.ban_userid',
					),
				),
				'WHERE' => "(b.ban_id IS NULL OR b.ban_exclude = 1)
					AND (u.user_birthday LIKE '" . $this->db->sql_escape(sprintf('%2d-%2d-', $now['mday'], $now['mon'])) . "%' $leap_year_birthdays)
					AND u.user_type IN (" . USER_NORMAL . ', ' . USER_FOUNDER . ')',
			);

			/**
			* Event to modify the SQL query to get birthdays data
			*
			* @event core.index_modify_birthdays_sql
			* @var	array	now			The assoc array with the 'now' local timestamp data
			* @var	array	sql_ary		The SQL array to get the birthdays data
			* @var	object	time		The user related Datetime object
			* @since 3.1.7-RC1
			*/
			$vars = array('now', 'sql_ary', 'time');
			extract($this->dispatcher->trigger_event('core.index_modify_birthdays_sql', compact($vars)));

			$sql = $this->db->sql_build_query('SELECT', $sql_ary);
			$result = $this->db->sql_query($sql);
			$rows = $this->db->sql_fetchrowset($result);
			$this->db->sql_freeresult($result);

			foreach ($rows as $row)
			{
				$birthday_username = get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']);
				$birthday_year = (int) substr($row['user_birthday'], -4);
				$birthday_age = ($birthday_year) ? max(0, $now['year'] - $birthday_year) : '';
				$birthdays[] = array(
					'USERNAME'	=> $birthday_username,
					'AGE'		=> $birthday_age,
				);
			}

			/**
			* Event to modify the birthdays list
			*
			* @event core.index_modify_birthdays_list
			* @var	array	birthdays		Array with the users birthdays data
			* @var	array	rows			Array with the birthdays SQL query result
			* @since 3.1.7-RC1
			*/
			$vars = array('birthdays', 'rows');
			extract($this->dispatcher->trigger_event('core.index_modify_birthdays_list', compact($vars)));

			$this->template->assign_block_vars_array('birthdays', $birthdays);
		}

		// Output
		$this->template->assign_vars(array(
			'LEGEND'				=> $legend,
			'TOTAL_BIRTHDAY_USERS'	=> sizeof($birthdays),
			'NEWEST_USER'			=> $this->language->lang('NEWEST_USER', get_username_string('full', $this->config['newest_user_id'], $this->config['newest_username'], $this->config['newest_user_colour'])),

			'LATEST_PHPBB_VERSION'			=> $this->config['vinabb_web_check_phpbb_version'],
			'LATEST_LEGACY_PHPBB_VERSION'	=> $this->config['vinabb_web_check_phpbb_legacy_version'],
			'LATEST_PHP_VERSION'			=> $this->config['vinabb_web_check_php_version'],
			'LATEST_LEGACY_PHP_VERSION'		=> $this->config['vinabb_web_check_php_legacy_version'],

			'FORUM_VIETNAMESE'	=> $this->forum_data[constants::FORUM_ID_VIETNAMESE]['name'],
			'FORUM_ENGLISH'		=> $this->forum_data[constants::FORUM_ID_ENGLISH]['name'],

			'U_FORUM_VIETNAMESE'	=> $this->helper->route('vinabb_web_board_forum_route', array('forum_id' => constants::FORUM_ID_VIETNAMESE, 'seo' => $this->forum_data[constants::FORUM_ID_VIETNAMESE]['name_seo'] . constants::REWRITE_URL_SEO)),
			'U_FORUM_ENGLISH'		=> $this->helper->route('vinabb_web_board_forum_route', array('forum_id' => constants::FORUM_ID_ENGLISH, 'seo' => $this->forum_data[constants::FORUM_ID_ENGLISH]['name_seo'] . constants::REWRITE_URL_SEO)),

			'S_INDEX'					=> true,
			'S_DISPLAY_BIRTHDAY_LIST'	=> $this->config['load_birthdays'],
		));
	}

	/**
	* Fetch content from an URL
	*
	* @param $url
	* @return string
	*/
	protected function fetch_url($url)
	{
		$raw = '';

		// Test URL
		$test = get_headers($url);

		if (strpos($test[0], '200') !== false)
		{
			if (function_exists('curl_version'))
			{
				$curl = curl_init($url);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				$raw = curl_exec($curl);
				curl_close($curl);
			}
			else
			{
				$url_parts = parse_url($url);

				try
				{
					$raw = $this->file_downloader->get($url_parts['host'], '', $url_parts['path'], ($url_parts['scheme'] == 'https') ? 443 : 80);
				}
				catch (\phpbb\exception\runtime_exception $e)
				{
					throw new \RuntimeException($this->file_downloader->get_error_string());
				}
			}
		}

		return $raw;
	}

	/**
	* Returns the IDs of the forums readable by the current user
	*
	* @return int[]
	*/
	protected function get_readable_forums()
	{
		static $forum_ids;

		if (!isset($forum_ids))
		{
			$forum_ids = array_keys($this->auth->acl_getf('f_read', true));
		}

		return $forum_ids;
	}

	/**
	* Build query for latest topics
	*
	* @return array|bool
	*/
	protected function get_latest_topics_sql()
	{
		// Determine forum IDs
		$forum_ids = array_diff($this->get_readable_forums(), $this->user->get_passworded_forums());

		if (empty($forum_ids))
		{
			return false;
		}

		// We really have to get the post ids first!
		$sql = 'SELECT topic_first_post_id, topic_time
			FROM ' . TOPICS_TABLE . '
			WHERE topic_moved_id = 0
				AND ' . $this->content_visibility->get_forums_visibility_sql('topic', $forum_ids) . '
			ORDER BY topic_time DESC';
		$result = $this->db->sql_query_limit($sql, constants::NUM_NEW_ITEMS_ON_INDEX);

		$post_ids = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$post_ids[] = (int) $row['topic_first_post_id'];
		}
		$this->db->sql_freeresult($result);

		if (empty($post_ids))
		{
			return false;
		}

		$sql_ary = array(
			'SELECT'	=> 'f.forum_id, f.forum_name,
							t.topic_id, t.topic_title, t.topic_poster, t.topic_first_poster_name, t.topic_posts_approved, t.topic_posts_unapproved, t.topic_posts_softdeleted, t.topic_views, t.topic_time, t.topic_last_post_time,
							p.post_id, p.post_time, p.post_edit_time, p.post_text, p.bbcode_bitfield, p.bbcode_uid, p.enable_bbcode, p.enable_smilies, p.enable_magic_url, p.post_attachment, t.topic_visibility',
			'FROM'		=> array(
				TOPICS_TABLE	=> 't',
				POSTS_TABLE		=> 'p',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(FORUMS_TABLE => 'f'),
					'ON'	=> 'p.forum_id = f.forum_id',
				),
			),
			'WHERE'		=> 'p.topic_id = t.topic_id
				AND ' . $this->db->sql_in_set('p.post_id', $post_ids),
			'ORDER_BY'	=> 'p.post_time DESC, p.post_id DESC',
		);

		return $sql_ary;
	}

	/**
	* Build query for latest posts
	*
	* @return array|bool
	*/
	protected function get_latest_posts_sql()
	{
		// Determine forum IDs
		$forum_ids = array_diff($this->get_readable_forums(), $this->user->get_passworded_forums());

		if (empty($forum_ids))
		{
			return false;
		}

		// Determine topics with recent activity
		$sql = 'SELECT topic_id, topic_last_post_time
			FROM ' . TOPICS_TABLE . '
			WHERE topic_moved_id = 0
				AND ' . $this->content_visibility->get_forums_visibility_sql('topic', $forum_ids) . '
				AND topic_first_post_id <> topic_last_post_id
			ORDER BY topic_last_post_time DESC, topic_last_post_id DESC';
		$result = $this->db->sql_query_limit($sql, constants::NUM_NEW_ITEMS_ON_INDEX);

		$topic_ids = array();
		$min_post_time = 0;

		while ($row = $this->db->sql_fetchrow())
		{
			$topic_ids[] = (int) $row['topic_id'];
			$min_post_time = (int) $row['topic_last_post_time'];
		}
		$this->db->sql_freeresult($result);

		if (empty($topic_ids))
		{
			return false;
		}

		// Get the actual data
		$sql_ary = array(
			'SELECT'	=>	'f.forum_id, f.forum_name, ' .
				'p.post_id, p.topic_id, p.post_time, p.post_edit_time, p.post_visibility, p.post_subject, p.post_text, p.bbcode_bitfield, p.bbcode_uid, p.enable_bbcode, p.enable_smilies, p.enable_magic_url, p.post_attachment, ' .
				'u.username, u.user_id',
			'FROM'		=> array(
				USERS_TABLE		=> 'u',
				POSTS_TABLE		=> 'p',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(FORUMS_TABLE	=> 'f'),
					'ON'	=> 'f.forum_id = p.forum_id',
				),
			),
			'WHERE'		=> $this->db->sql_in_set('p.topic_id', $topic_ids) . '
				AND ' . $this->content_visibility->get_forums_visibility_sql('post', $forum_ids, 'p.') . '
				AND p.post_time >= ' . $min_post_time . '
				AND u.user_id = p.poster_id',
			'ORDER_BY'	=> 'p.post_time DESC, p.post_id DESC',
		);

		return $sql_ary;
	}
}
