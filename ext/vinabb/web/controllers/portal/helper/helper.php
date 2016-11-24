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
class helper implements helper_interface
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

	/** @var \vinabb\web\controllers\pagination */
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

	/** @var \phpbb\path_helper */
	protected $path_helper;

	/** @var string */
	protected $root_path;

	/** @var string */
	protected $ext_root_path;

	/** @var string */
	protected $ext_web_path;

	/** @var array */
	protected $forum_data;

	/** @var array */
	protected $portal_cats;

	/**
	* Constructor
	*
	* @param \phpbb\auth\auth $auth
	* @param \vinabb\web\controllers\cache\service_interface $cache
	* @param \phpbb\config\config $config
	* @param \phpbb\content_visibility $content_visibility
	* @param \phpbb\db\driver\driver_interface $db
	* @param \phpbb\extension\manager $ext_manager
	* @param \phpbb\language\language $language
	* @param \phpbb\notification\manager $notification
	* @param \vinabb\web\controllers\pagination $pagination
	* @param \phpbb\request\request $request
	* @param \phpbb\template\template $template
	* @param \phpbb\user $user
	* @param \phpbb\controller\helper $helper
	* @param \vinabb\web\controllers\helper_interface $ext_helper
	* @param \phpbb\path_helper $path_helper
	* @param string $root_path
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
		\vinabb\web\controllers\pagination $pagination,
		\phpbb\request\request $request,
		\phpbb\template\template $template,
		\phpbb\user $user,
		\phpbb\controller\helper $helper,
		\vinabb\web\controllers\helper_interface $ext_helper,
		\phpbb\path_helper $path_helper,
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
		$this->pagination = $pagination;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->helper = $helper;
		$this->ext_helper = $ext_helper;
		$this->path_helper = $path_helper;
		$this->root_path = $root_path;

		$this->ext_root_path = $this->ext_manager->get_extension_path('vinabb/web', true);
		$this->ext_web_path = $this->path_helper->update_web_root_path($this->ext_root_path);
		$this->forum_data = $this->cache->get_forum_data();
		$this->portal_cats = $this->cache->get_portal_cats();
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
	* Check all of new versions
	*/
	public function check_new_versions()
	{
		if (time() > $this->config['vinabb_web_check_gc'] + (constants::CHECK_VERSION_HOURS * 60 * 60))
		{
			$this->fetch_phpbb_version();
			$this->fetch_php_version();
			$this->fetch_vinabb_version();

			// Save this time
			$this->config->set('vinabb_web_check_gc', time(), true);
		}
	}

	/**
	* Get and set latest phpBB versions
	*/
	public function fetch_phpbb_version()
	{
		$raw = $this->ext_helper->fetch_url($this->config['vinabb_web_check_phpbb_url']);

		// Parse JSON
		$phpbb_data = json_decode($raw, true);

		// Latest version
		$latest_phpbb_version = isset($phpbb_data['stable'][$this->config['vinabb_web_check_phpbb_branch']]['current']) ? strtoupper($phpbb_data['stable'][$this->config['vinabb_web_check_phpbb_branch']]['current']) : '';

		if (version_compare($latest_phpbb_version, $this->config['vinabb_web_check_phpbb_version'], '>'))
		{
			$this->config->set('vinabb_web_check_phpbb_version', $latest_phpbb_version);
		}

		// Legacy version
		$latest_phpbb_legacy_version = isset($phpbb_data['stable'][$this->config['vinabb_web_check_phpbb_legacy_branch']]['current']) ? strtoupper($phpbb_data['stable'][$this->config['vinabb_web_check_phpbb_legacy_branch']]['current']) : '';

		if (version_compare($latest_phpbb_legacy_version, $this->config['vinabb_web_check_phpbb_legacy_version'], '>'))
		{
			$this->config->set('vinabb_web_check_phpbb_legacy_version', $latest_phpbb_legacy_version);
		}

		// Development version
		$latest_phpbb_dev_version = isset($phpbb_data['unstable'][$this->config['vinabb_web_check_phpbb_dev_branch']]['current']) ? strtoupper($phpbb_data['unstable'][$this->config['vinabb_web_check_phpbb_dev_branch']]['current']) : '';

		if (version_compare($latest_phpbb_dev_version, $this->config['vinabb_web_check_phpbb_dev_version'], '>'))
		{
			$this->config->set('vinabb_web_check_phpbb_dev_version', $latest_phpbb_dev_version);
		}
	}

	/**
	* Get and set latest PHP versions
	*/
	public function fetch_php_version()
	{
		$raw = $this->ext_helper->fetch_url($this->config['vinabb_web_check_php_url']);
		$raw = str_replace('php:version', 'php-version', $raw);

		// Parse XML
		$php_data = simplexml_load_string($raw);
		$latest_php_version = $latest_php_version_url = $latest_php_legacy_version = $latest_php_legacy_version_url = '';

		// Find the latest version from feed data
		foreach ($php_data->entry as $entry)
		{
			$php_version = isset($entry->{'php-version'}) ? $entry->{'php-version'} : '';
			$php_version_url = isset($entry->id) ? $entry->id : '';

			if ($this->config['vinabb_web_check_php_branch'] != '' && substr($php_version, 0, strlen($this->config['vinabb_web_check_php_branch'])) == $this->config['vinabb_web_check_php_branch'] && version_compare($php_version, $latest_php_version, '>'))
			{
				$latest_php_version = $php_version;
				$latest_php_version_url = $php_version_url;
			}
			else if ($this->config['vinabb_web_check_php_legacy_branch'] != '' && substr($php_version, 0, strlen($this->config['vinabb_web_check_php_legacy_branch'])) == $this->config['vinabb_web_check_php_legacy_branch'] && version_compare($php_version, $latest_php_legacy_version, '>'))
			{
				$latest_php_legacy_version = $php_version;
				$latest_php_legacy_version_url = $php_version_url;
			}
		}

		if (version_compare($latest_php_version, $this->config['vinabb_web_check_php_version'], '>'))
		{
			$this->config->set('vinabb_web_check_php_version', $latest_php_version);
			$this->config->set('vinabb_web_check_php_version_url', $latest_php_version_url);
		}

		if (version_compare($latest_php_legacy_version, $this->config['vinabb_web_check_php_legacy_version'], '>'))
		{
			$this->config->set('vinabb_web_check_php_legacy_version', $latest_php_legacy_version);
			$this->config->set('vinabb_web_check_php_legacy_version_url', $latest_php_legacy_version_url);
		}
	}

	/**
	* Get and set latest VinaBB.vn version
	*/
	public function fetch_vinabb_version()
	{
		$raw = file_get_contents("{$this->ext_root_path}composer.json");

		// Parse JSON
		$vinabb_data = json_decode($raw, true);
		$vinabb_version = isset($vinabb_data['version']) ? strtoupper($vinabb_data['version']) : '';

		if (version_compare($vinabb_version, $this->config['vinabb_web_check_vinabb_version'], '>'))
		{
			$this->config->set('vinabb_web_check_vinabb_version', $vinabb_version);
		}
	}

	/**
	* Get all of news categories
	*
	* @param string $block_name Twig loop name
	*/
	public function get_portal_cats($block_name = 'portal_cats')
	{
		foreach ($this->portal_cats as $cat_id => $cat_data)
		{
			$this->template->assign_block_vars($block_name, [
				'ID'		=> $cat_id,
				'NAME'		=> ($this->user->lang_name == constants::LANG_VIETNAMESE) ? $cat_data['name_vi'] : $cat_data['name'],
				'VARNAME'	=> $cat_data['varname'],
				'ICON'		=> $cat_data['icon'],
				'URL'		=> $this->helper->route('vinabb_web_portal_cat_route', ['varname' => $cat_data['varname']])
			]);
		}
	}

	/**
	* Get latest articles on index page
	*
	* @param string $block_name Twig loop name
	*/
	public function get_latest_articles($block_name = 'latest_articles')
	{
		$comment_counter = $this->cache->get_index_comment_counter($this->user->lang_name);

		foreach ($this->cache->get_index_articles($this->user->lang_name) as $article_data)
		{
			$this->template->assign_block_vars($block_name, [
				'CATEGORY'	=> ($this->user->lang_name == constants::LANG_VIETNAMESE) ? $this->portal_cats[$article_data['cat_id']]['name_vi'] : $this->portal_cats[$article_data['cat_id']]['name'],
				'CAT_URL'	=> $this->helper->route('vinabb_web_portal_cat_route', ['varname' => $this->portal_cats[$article_data['cat_id']]['varname']]),
				'NAME'		=> $article_data['name'],
				'DESC'		=> $article_data['desc'],
				'TIME'		=> $this->user->format_date($article_data['time']),
				'URL'		=> $this->helper->route('vinabb_web_portal_article_route', ['varname' => $this->portal_cats[$article_data['cat_id']]['varname'], 'article_id' => $article_data['id'], 'seo' => $article_data['name_seo'] . constants::REWRITE_URL_SEO]),
				'COMMENTS'	=> isset($comment_counter[$article_data['id']]) ? $comment_counter[$article_data['id']] : 0
			]);
		}
	}

	/**
	* Get latest phpBB resource items
	*
	* @param string $block_name_prefix	Prefix of Twig loop name
	* @param string $block_name_suffix	Suffix of Twig loop name
	*/
	public function get_latest_bb_items($block_name_prefix = 'bb_new_', $block_name_suffix = 's')
	{
		$bb_types = ['ext', 'style', 'acp_style', 'tool'];

		foreach ($bb_types as $bb_type)
		{
			$new_items = $this->cache->get_new_bb_items($bb_type);

			foreach ($new_items as $new_item)
			{
				$this->template->assign_block_vars($block_name_prefix . $bb_type . $block_name_suffix, [
					'NAME'		=> $new_item['name'],
					'VARNAME'	=> $new_item['varname'],
					'VERSION'	=> $new_item['version'],
					'PRICE'		=> $new_item['price'],
					'NEW'		=> $new_item['added'] + (24 * 60 * 60) > $new_item['updated']
				]);
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
					'TITLE'	=> truncate_string($row['topic_title'], 48, 255, false, $this->language->lang('ELLIPSIS'))
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
							t.topic_id, t.topic_title, t.topic_time,
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
					'SUBJECT'	=> truncate_string($row['post_subject'], 48, 255, false, $this->language->lang('ELLIPSIS'))
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
							p.post_id, p.topic_id, p.post_time, p.post_subject,
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
	* Generate template variables for latest version blocks
	*/
	public function get_version_tpl()
	{
		$this->template->assign_vars([
			'LATEST_PHPBB_VERSION'				=> $this->config['vinabb_web_check_phpbb_version'],
			'LATEST_PHPBB_DOWNLOAD_URL'			=> str_replace(['{branch}', '{version}'], [$this->config['vinabb_web_check_phpbb_branch'], $this->config['vinabb_web_check_phpbb_version']], htmlspecialchars_decode($this->config['vinabb_web_check_phpbb_download_url'])),
			'LATEST_PHPBB_GITHUB_URL'			=> str_replace(['{branch}', '{version}'], [$this->config['vinabb_web_check_phpbb_branch'], $this->config['vinabb_web_check_phpbb_version']], htmlspecialchars_decode($this->config['vinabb_web_check_phpbb_github_url'])),
			'LATEST_PHPBB_LEGACY_VERSION'		=> $this->config['vinabb_web_check_phpbb_legacy_version'],
			'LATEST_PHPBB_LEGACY_DOWNLOAD_URL'	=> str_replace(['{branch}', '{version}'], [$this->config['vinabb_web_check_phpbb_legacy_branch'], $this->config['vinabb_web_check_phpbb_legacy_version']], htmlspecialchars_decode($this->config['vinabb_web_check_phpbb_download_url'])),
			'LATEST_PHPBB_LEGACY_GITHUB_URL'	=> str_replace(['{branch}', '{version}'], [$this->config['vinabb_web_check_phpbb_legacy_branch'], $this->config['vinabb_web_check_phpbb_legacy_version']], htmlspecialchars_decode($this->config['vinabb_web_check_phpbb_github_url'])),
			'LATEST_PHPBB_DEV_VERSION'			=> $this->config['vinabb_web_check_phpbb_dev_version'],
			'LATEST_PHPBB_DEV_DOWNLOAD_URL'		=> str_replace(['{branch}', '{version}'], [$this->config['vinabb_web_check_phpbb_dev_branch'], $this->config['vinabb_web_check_phpbb_dev_version']], htmlspecialchars_decode($this->config['vinabb_web_check_phpbb_download_dev_url'])),
			'LATEST_PHPBB_DEV_GITHUB_URL'		=> str_replace(['{branch}', '{version}'], [$this->config['vinabb_web_check_phpbb_dev_branch'], $this->config['vinabb_web_check_phpbb_dev_version']], htmlspecialchars_decode($this->config['vinabb_web_check_phpbb_github_url'])),

			'LATEST_IVN_VERSION'		=> $this->config['vinabb_web_check_ivn_version'],
			'LATEST_IVN_LEGACY_VERSION'	=> $this->config['vinabb_web_check_ivn_legacy_version'],
			'LATEST_IVN_DEV_VERSION'	=> $this->config['vinabb_web_check_ivn_dev_version'],
			'LATEST_IVNPLUS_VERSION'	=> $this->config['vinabb_web_check_ivnplus_version'],

			'LATEST_PHP_VERSION'			=> $this->config['vinabb_web_check_php_version'],
			'LATEST_PHP_VERSION_URL'		=> htmlspecialchars_decode($this->config['vinabb_web_check_php_version_url']),
			'LATEST_PHP_LEGACY_VERSION'		=> $this->config['vinabb_web_check_php_legacy_version'],
			'LATEST_PHP_LEGACY_VERSION_URL'	=> htmlspecialchars_decode($this->config['vinabb_web_check_php_legacy_version_url']),
			'LATEST_VINABB_VERSION'			=> $this->config['vinabb_web_check_vinabb_version'],

			'LATEST_VINABB_GITHUB_PATH'			=> constants::VINABB_GITHUB_PATH,
			'LATEST_VINABB_GITHUB_URL'			=> constants::VINABB_GITHUB_URL,
			'LATEST_VINABB_GITHUB_DOWNLOAD_URL'	=> constants::VINABB_GITHUB_DOWNLOAD_URL,
			'LATEST_VINABB_GITHUB_FORK_URL'		=> constants::VINABB_GITHUB_FORK_URL,
			'LATEST_VINABB_TRAVIS_URL'			=> constants::VINABB_TRAVIS_URL,
			'LATEST_VINABB_TRAVIS_IMG_URL'		=> constants::VINABB_TRAVIS_IMG_URL,
			'LATEST_VINABB_INSIGHT_URL'			=> constants::VINABB_INSIGHT_URL,
			'LATEST_VINABB_INSIGHT_IMG_URL'		=> constants::VINABB_INSIGHT_IMG_URL,
			'LATEST_VINABB_SCRUTINIZER_URL'		=> constants::VINABB_SCRUTINIZER_URL,
			'LATEST_VINABB_SCRUTINIZER_IMG_URL'	=> constants::VINABB_SCRUTINIZER_IMG_URL,
			'LATEST_VINABB_CODECLIMATE_URL'		=> constants::VINABB_CODECLIMATE_URL,
			'LATEST_VINABB_CODECLIMATE_IMG_URL'	=> constants::VINABB_CODECLIMATE_IMG_URL
		]);
	}

	/**
	* Generate template variables for the donation block
	*/
	public function get_donate_tpl()
	{
		$this->template->assign_vars([
			'DONATE_LAST_YEAR'	=> max(0, $this->config['vinabb_web_donate_year'] - 1),
			'DONATE_PERCENT'	=> round($this->config['vinabb_web_donate_fund'] / max(1, $this->config['vinabb_web_donate_year_value']) * 100, 0),
			'DONATE_YEAR'		=> $this->config['vinabb_web_donate_year'],
			'DONATE_YEAR_VALUE'	=> $this->config['vinabb_web_donate_year_value'],
			'DONATE_FUND'		=> $this->config['vinabb_web_donate_fund'],
			'DONATE_CURRENCY'	=> $this->config['vinabb_web_donate_currency'],
			'DONATE_OWNER'		=> ($this->user->lang_name == constants::LANG_VIETNAMESE && $this->config['vinabb_web_donate_owner_vi'] != '') ? $this->config['vinabb_web_donate_owner_vi'] : $this->config['vinabb_web_donate_owner'],
			'DONATE_EMAIL'		=> $this->config['vinabb_web_donate_email'],
			'DONATE_BANK'		=> ($this->user->lang_name == constants::LANG_VIETNAMESE && $this->config['vinabb_web_donate_bank_vi'] != '') ? $this->config['vinabb_web_donate_bank_vi'] : $this->config['vinabb_web_donate_bank'],
			'DONATE_BANK_ACC'	=> $this->config['vinabb_web_donate_bank_acc'],
			'DONATE_BANK_SWIFT'	=> $this->config['vinabb_web_donate_bank_swift'],
			'DONATE_PAYPAL'		=> htmlspecialchars_decode($this->config['vinabb_web_donate_paypal'])
		]);
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
				$leap_year_birthdays = " OR u.user_birthday LIKE '" . $this->db->sql_escape(sprintf('%2d-%2d-', 29, 2)) . "%'";
			}
			$sql_ary = [
				'SELECT' => 'u.user_id, u.username, u.user_colour, u.user_birthday',
				'FROM' => [
					USERS_TABLE => 'u'
				],
				'LEFT_JOIN' => [
					[
						'FROM' => [BANLIST_TABLE => 'b'],
						'ON' => 'u.user_id = b.ban_userid'
					]
				],
				'WHERE' => "(b.ban_id IS NULL OR b.ban_exclude = 1)
					AND (u.user_birthday LIKE '" . $this->db->sql_escape(sprintf('%2d-%2d-', $now['mday'], $now['mon'])) . "%' $leap_year_birthdays)
					AND u.user_type IN (" . USER_NORMAL . ', ' . USER_FOUNDER . ')'
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
