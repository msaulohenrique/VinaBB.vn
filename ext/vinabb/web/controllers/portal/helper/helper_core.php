<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\controllers\portal\helper;

use vinabb\web\includes\constants;

/**
* Helper for the portal
*/
class helper_core
{
	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \vinabb\web\controllers\cache\service_interface */
	protected $cache;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\content_visibility */
	protected $content_visibility;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\extension\manager */
	protected $ext_manager;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\notification\manager */
	protected $notification;

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
	protected $ext_root_path;

	/** @var array */
	protected $forum_data;

	/**
	* Constructor
	*
	* @param \phpbb\auth\auth									$auth				Authentication object
	* @param \vinabb\web\controllers\cache\service_interface	$cache				Cache service
	* @param \phpbb\config\config								$config				Config object
	* @param \phpbb\content_visibility							$content_visibility	Content visibility
	* @param \phpbb\db\driver\driver_interface					$db					Database object
	* @param \phpbb\extension\manager							$ext_manager		Extension manager
	* @param \phpbb\language\language							$language			Language object
	* @param \phpbb\notification\manager						$notification		Notification manager
	* @param \phpbb\request\request								$request			Request object
	* @param \phpbb\template\template							$template			Template object
	* @param \phpbb\user										$user				User object
	* @param \phpbb\controller\helper							$helper				Controller helper
	* @param string												$root_path			phpBB root path
	*/
	public function __construct(
		\phpbb\auth\auth $auth,
		\vinabb\web\controllers\cache\service_interface $cache,
		\phpbb\config\config $config,
		\phpbb\content_visibility $content_visibility,
		\phpbb\db\driver\driver_interface $db,
		\phpbb\extension\manager $ext_manager,
		\phpbb\language\language $language,
		\phpbb\notification\manager $notification,
		\phpbb\request\request $request,
		\phpbb\template\template $template,
		\phpbb\user $user,
		\phpbb\controller\helper $helper,
		$root_path
	)
	{
		$this->auth = $auth;
		$this->cache = $cache;
		$this->config = $config;
		$this->content_visibility = $content_visibility;
		$this->db = $db;
		$this->ext_manager = $ext_manager;
		$this->language = $language;
		$this->notification = $notification;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->helper = $helper;
		$this->root_path = $root_path;

		$this->ext_root_path = $this->ext_manager->get_extension_path('vinabb/web', true);
		$this->forum_data = $this->cache->get_forum_data();
	}

	/**
	* Mark notifications as read
	*/
	public function mark_read_notifications()
	{
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
				$notification = $this->notification->load_notifications('notification.method.board', ['notification_id' => $mark_notification]);

				if (isset($notification['notifications'][$mark_notification]))
				{
					$notification = $notification['notifications'][$mark_notification];
					$notification->mark_read();

					if ($this->request->is_ajax())
					{
						$json_response = new \phpbb\json_response();
						$json_response->send(['success' => true]);
					}

					if (($redirect = $this->request->variable('redirect', '')))
					{
						redirect(append_sid($this->root_path . $redirect));
					}

					redirect($notification->get_redirect_url());
				}
			}
		}
	}

	/**
	* Get latest topics
	*
	* @param string $block_name Twig loop name
	*/
	public function get_latest_topics($block_name = 'latest_topics')
	{
		$sql_ary = $this->build_latest_topics_sql();

		if ($sql_ary !== false)
		{
			$sql = $this->db->sql_build_query('SELECT', $sql_ary);
			$result = $this->db->sql_query_limit($sql, constants::NUM_NEW_ITEMS_ON_INDEX);
			$rows = $this->db->sql_fetchrowset($result);
			$this->db->sql_freeresult($result);

			foreach ($rows as $row)
			{
				$this->template->assign_block_vars($block_name, [
					'TITLE'	=> truncate_string($row['topic_title'], 48, 255, false, $this->language->lang('ELLIPSIS')),
					'URL'	=> $this->helper->route('vinabb_web_board_topic_route', ['forum_id' => $row['forum_id'], 'topic_id' => $row['topic_id'], 'seo' => $row['topic_title_seo'] . constants::REWRITE_URL_SEO])
				]);
			}
		}
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

			// Only pick forums in the user language
			$lang_forum_ids = [];

			foreach ($this->cache->get_forum_data() as $forum_id => $forum_data)
			{
				if ($forum_data['lang'] == $this->user->lang_name)
				{
					$lang_forum_ids[] = $forum_id;
				}
			}

			$forum_ids = array_intersect($forum_ids, $lang_forum_ids);
		}

		return $forum_ids;
	}

	/**
	* Build query for latest topics
	*
	* @return array|bool
	*/
	protected function build_latest_topics_sql()
	{
		// Determine forum IDs
		$forum_ids = array_diff($this->get_readable_forums(), $this->user->get_passworded_forums());

		if (!sizeof($forum_ids))
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

		$post_ids = [];
		while ($row = $this->db->sql_fetchrow($result))
		{
			$post_ids[] = (int) $row['topic_first_post_id'];
		}
		$this->db->sql_freeresult($result);

		if (!sizeof($post_ids))
		{
			return false;
		}

		$sql_ary = [
			'SELECT'	=> 'f.forum_id, f.forum_name,
							t.topic_id, t.topic_title, t.topic_title_seo, t.topic_time,
							p.post_id, p.post_time',
			'FROM'		=> [
				TOPICS_TABLE	=> 't',
				POSTS_TABLE		=> 'p'
			],
			'LEFT_JOIN'	=> [
				[
					'FROM'	=> [FORUMS_TABLE => 'f'],
					'ON'	=> 'p.forum_id = f.forum_id'
				],
			],
			'WHERE'		=> 'p.topic_id = t.topic_id
				AND ' . $this->db->sql_in_set('p.post_id', $post_ids),
			'ORDER_BY'	=> 'p.post_time DESC, p.post_id DESC'
		];

		return $sql_ary;
	}

	/**
	* Get latest reply posts (Not the first post in each topic)
	*
	* @param string $block_name Twig loop name
	*/
	public function get_latest_posts($block_name = 'latest_posts')
	{
		$sql_ary = $this->build_latest_posts_sql();

		if ($sql_ary !== false)
		{
			$sql = $this->db->sql_build_query('SELECT', $sql_ary);
			$result = $this->db->sql_query_limit($sql, constants::NUM_NEW_ITEMS_ON_INDEX);
			$rows = $this->db->sql_fetchrowset($result);
			$this->db->sql_freeresult($result);

			foreach ($rows as $row)
			{
				$this->template->assign_block_vars($block_name, [
					'SUBJECT'	=> truncate_string($row['post_subject'], 48, 255, false, $this->language->lang('ELLIPSIS')),
					'URL'		=> $this->helper->route('vinabb_web_board_post_route', ['forum_id' => $row['forum_id'], 'topic_id' => $row['topic_id'], 'post_id' => $row['post_id'], 'seo' => $row['post_subject_seo'] . constants::REWRITE_URL_SEO])
				]);
			}
		}
	}

	/**
	* Build query for latest posts
	*
	* @return array|bool
	*/
	protected function build_latest_posts_sql()
	{
		// Determine forum IDs
		$forum_ids = array_diff($this->get_readable_forums(), $this->user->get_passworded_forums());

		if (!sizeof($forum_ids))
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

		$topic_ids = [];
		$min_post_time = 0;

		while ($row = $this->db->sql_fetchrow())
		{
			$topic_ids[] = (int) $row['topic_id'];
			$min_post_time = (int) $row['topic_last_post_time'];
		}
		$this->db->sql_freeresult($result);

		if (!sizeof($topic_ids))
		{
			return false;
		}

		// Get the actual data
		$sql_ary = [
			'SELECT'	=>	'f.forum_id, f.forum_name,
							p.post_id, p.topic_id, p.post_time, p.post_subject, p.post_subject_seo,
							u.username, u.user_id',
			'FROM'		=> [
				USERS_TABLE		=> 'u',
				POSTS_TABLE		=> 'p'
			],
			'LEFT_JOIN'	=> [
				[
					'FROM'	=> [FORUMS_TABLE	=> 'f'],
					'ON'	=> 'f.forum_id = p.forum_id'
				],
			],
			'WHERE'		=> $this->db->sql_in_set('p.topic_id', $topic_ids) . '
				AND ' . $this->content_visibility->get_forums_visibility_sql('post', $forum_ids, 'p.') . '
				AND p.post_time >= ' . $min_post_time . '
				AND u.user_id = p.poster_id',
			'ORDER_BY'	=> 'p.post_time DESC, p.post_id DESC'
		];

		return $sql_ary;
	}

	/**
	* Get latest members
	*
	* @param string $block_name Twig loop name
	*/
	public function get_latest_users($block_name = 'latest_users')
	{
		$sql = 'SELECT user_id, username, user_colour
			FROM ' . USERS_TABLE . '
			WHERE ' . $this->db->sql_in_set('user_type', [USER_NORMAL, USER_FOUNDER]) . '
			ORDER BY user_regdate DESC';
		$result = $this->db->sql_query_limit($sql, constants::NUM_NEW_ITEMS_ON_INDEX);
		$rows = $this->db->sql_fetchrowset($result);
		$this->db->sql_freeresult($result);

		foreach ($rows as $row)
		{
			$this->template->assign_block_vars($block_name, [
				'NAME'	=> get_username_string('full', $row['user_id'], $row['username'], $row['user_colour'])
			]);
		}
	}

	/**
	* Get birthday list
	*
	* @return array
	*/
	public function get_birthdays()
	{
		$birthdays = [];

		if ($this->config['load_birthdays'] && $this->config['allow_birthdays'] && $this->auth->acl_gets('u_viewprofile', 'a_user', 'a_useradd', 'a_userdel'))
		{
			$time = $this->user->create_datetime();
			$now = phpbb_gmgetdate($time->getTimestamp() + $time->getOffset());

			// Display birthdays of 29th february on 28th february in non-leap-years
			$leap_year_birthdays = '';

			if ($now['mday'] == 28 && $now['mon'] == 2 && !$time->format('L'))
			{
				$leap_year_birthdays = ' OR u.user_birthday ' . $this->db->sql_like_expression('29- 2-' . $this->db->get_any_char());
			}

			$sql_ary = [
				'SELECT' => 'u.user_id, u.username, u.user_colour, u.user_birthday',
				'FROM' => [USERS_TABLE => 'u'],
				'LEFT_JOIN' => [
					[
						'FROM' => [BANLIST_TABLE => 'b'],
						'ON' => 'u.user_id = b.ban_userid'
					]
				],
				'WHERE' => '(b.ban_id IS NULL OR b.ban_exclude = 1)
					AND (u.user_birthday ' . $this->db->sql_like_expression(sprintf('%2d-%2d-', $now['mday'], $now['mon']) . $this->db->get_any_char()) . " $leap_year_birthdays)
					AND " . $this->db->sql_in_set('u.user_type', [USER_NORMAL, USER_FOUNDER])
			];
			$sql = $this->db->sql_build_query('SELECT', $sql_ary);
			$result = $this->db->sql_query($sql);
			$rows = $this->db->sql_fetchrowset($result);
			$this->db->sql_freeresult($result);

			foreach ($rows as $row)
			{
				$birthday_username = get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']);
				$birthday_year = (int) substr($row['user_birthday'], -4);
				$birthday_age = ($birthday_year) ? max(0, $now['year'] - $birthday_year) : '';
				$birthdays[] = [
					'USERNAME'	=> $birthday_username,
					'AGE'		=> $birthday_age
				];
			}
		}

		return $birthdays;
	}
}
