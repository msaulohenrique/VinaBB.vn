<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\controllers\portal;

use Symfony\Component\DependencyInjection\ContainerInterface;
use vinabb\web\includes\constants;

/**
* Controller for the portal and news category
*/
class portal implements portal_interface
{
	/** @var \vinabb\web\controllers\cache\service_interface */
	protected $cache;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var ContainerInterface $container */
	protected $container;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \vinabb\web\controllers\pagination */
	protected $pagination;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var \vinabb\web\controllers\helper_interface */
	protected $ext_helper;

	/** @var \vinabb\web\controllers\portal\helper\helper_interface */
	protected $portal_helper;

	/** @var array */
	protected $forum_data;

	/** @var array */
	protected $portal_cats;

	/**
	* Constructor
	*
	* @param \vinabb\web\controllers\cache\service_interface		$cache			Cache service
	* @param \phpbb\config\config									$config			Config object
	* @param ContainerInterface										$container		Service container
	* @param \phpbb\language\language								$language		Language object
	* @param \vinabb\web\controllers\pagination						$pagination		Pagination object
	* @param \phpbb\template\template								$template		Template object
	* @param \phpbb\user											$user			User object
	* @param \phpbb\controller\helper								$helper			Controller helper
	* @param \vinabb\web\controllers\helper_interface				$ext_helper		Extension helper
	* @param \vinabb\web\controllers\portal\helper\helper_interface	$portal_helper	Portal helper
	*/
	public function __construct(
		\vinabb\web\controllers\cache\service_interface $cache,
		\phpbb\config\config $config,
		ContainerInterface $container,
		\phpbb\language\language $language,
		\vinabb\web\controllers\pagination $pagination,
		\phpbb\template\template $template,
		\phpbb\user $user,
		\phpbb\controller\helper $helper,
		\vinabb\web\controllers\helper_interface $ext_helper,
		\vinabb\web\controllers\portal\helper\helper_interface $portal_helper
	)
	{
		$this->cache = $cache;
		$this->config = $config;
		$this->container = $container;
		$this->language = $language;
		$this->pagination = $pagination;
		$this->template = $template;
		$this->user = $user;
		$this->helper = $helper;
		$this->ext_helper = $ext_helper;
		$this->portal_helper = $portal_helper;

		$this->forum_data = $this->cache->get_forum_data();
		$this->portal_cats = $this->cache->get_portal_cats();
	}

	/**
	* Index page
	*
	* @param bool	$index_page			true: Use on the index page (Get x latest articles from all categories - cached)
	*									false: Use with a news category (Get all articles from that category)
	* @param bool	$mark_notifications	true to check marking notifications as read
	*/
	public function index($index_page = true, $mark_notifications = true)
	{
		// Mark notification as read
		if ($mark_notifications)
		{
			$this->portal_helper->mark_read_notifications();
		}

		// Check new versions
		$this->portal_helper->check_new_versions();

		// News categories
		$this->portal_helper->get_portal_cats();

		// Headlines
		$this->portal_helper->get_headlines();

		// Latest articles
		if ($index_page)
		{
			$this->portal_helper->get_latest_articles('articles');
			$this->ext_helper->set_breadcrumb($this->language->lang('NEWS'));
		}

		// Latest phpBB resources
		$this->portal_helper->get_latest_bb_items();

		// Latest topics
		$this->portal_helper->get_latest_topics();

		// Latest posts
		$this->portal_helper->get_latest_posts();

		// Latest users
		$this->portal_helper->get_latest_users();

		// Latest versions
		$this->portal_helper->get_version_tpl();

		// Donate
		$this->portal_helper->get_donate_tpl();

		// Birthday list
		$birthdays = $this->portal_helper->get_birthdays();
		$this->template->assign_block_vars_array('birthdays', $birthdays);

		// Output
		$this->template->assign_vars([
			'LEGEND'				=> $this->ext_helper->get_group_legend(),
			'TOTAL_BIRTHDAY_USERS'	=> sizeof($birthdays),

			'FORUM_VIETNAMESE'	=> ($this->config['vinabb_web_forum_id_vietnamese']) ? $this->forum_data[$this->config['vinabb_web_forum_id_vietnamese']]['name'] : '',
			'FORUM_ENGLISH'		=> ($this->config['vinabb_web_forum_id_english']) ? $this->forum_data[$this->config['vinabb_web_forum_id_english']]['name'] : '',

			'U_FORUM_VIETNAMESE'	=> $this->helper->route('vinabb_web_board_forum_route', ['forum_id' => $this->config['vinabb_web_forum_id_vietnamese'], 'seo' => ($this->config['vinabb_web_forum_id_vietnamese'] ? $this->forum_data[$this->config['vinabb_web_forum_id_vietnamese']]['name_seo'] : constants::LANG_VIETNAMESE) . constants::REWRITE_URL_SEO]),
			'U_FORUM_ENGLISH'		=> $this->helper->route('vinabb_web_board_forum_route', ['forum_id' => $this->config['vinabb_web_forum_id_english'], 'seo' => ($this->config['vinabb_web_forum_id_english'] ? $this->forum_data[$this->config['vinabb_web_forum_id_english']]['name_seo'] : constants::LANG_ENGLISH) . constants::REWRITE_URL_SEO]),

			'S_INDEX'					=> true,
			'S_DISPLAY_BIRTHDAY_LIST'	=> $this->config['load_birthdays']
		]);
	}

	/**
	* Alternative method for index page
	*
	* @return \Symfony\Component\HttpFoundation\Response
	*/
	public function news()
	{
		$this->index(true, false);

		return $this->helper->render('portal.html', $this->language->lang('VINABB'), 200, true);
	}

	/**
	* Display articles from a news category
	*
	* @param string	$varname	URL varname
	* @param string	$page		The page number
	*
	* @return \Symfony\Component\HttpFoundation\Response
	*/
	public function category($varname, $page)
	{
		// Display index blocks
		$this->index(false, false);

		// Pagination
		$page = max(1, floor(str_replace(constants::REWRITE_URL_PAGE, '', $page)));
		$start = floor(($page - 1) * constants::NUM_ARTICLES_ON_INDEX);

		// Get cat_id from $cat_varname
		$current_cat_id = 0;
		$current_category_name = '';

		foreach ($this->portal_cats as $cat_id => $cat_data)
		{
			if ($varname == $cat_data['varname'])
			{
				$current_cat_id = $cat_id;
				$current_category_name = ($this->user->lang_name == constants::LANG_VIETNAMESE) ? $this->portal_cats[$cat_id]['name_vi'] : $this->portal_cats[$cat_id]['name'];
			}
		}

		// Display articles
		$articles = [];
		$article_count = 0;
		$start = $this->list_articles($this->user->lang_name, $current_cat_id, $articles, $article_count, constants::NUM_ARTICLES_ON_INDEX, $start);

		foreach ($articles as $row)
		{
			$this->template->assign_block_vars('articles', [
				'CATEGORY'	=> ($this->user->lang_name == constants::LANG_VIETNAMESE) ? $this->portal_cats[$row['cat_id']]['name_vi'] : $this->portal_cats[$row['cat_id']]['name'],
				'CAT_URL'	=> $this->helper->route('vinabb_web_portal_cat_route', ['varname' => $this->portal_cats[$row['cat_id']]['varname']]),
				'NAME'		=> $row['name'],
				'IMG'		=> $row['img'],
				'DESC'		=> $row['desc'],
				'TIME'		=> $this->user->format_date($row['time']),
				'URL'		=> $this->helper->route('vinabb_web_portal_article_route', ['varname' => $this->portal_cats[$row['cat_id']]['varname'], 'article_id' => $row['id'], 'seo' => $row['name_seo'] . constants::REWRITE_URL_SEO]),
				'COMMENTS'	=> 0,

				'S_NEW'	=> ($row['time'] + (constants::FLAG_DAY_NEW_ARTICLE * 24 * 60 * 60)) > time()
			]);
		}

		// Generate pagination
		$this->pagination->generate_template_pagination('vinabb_web_portal_cat_route', ['varname' => $current_cat_id ? $varname : 'all'], 'pagination', $article_count, constants::NUM_ARTICLES_ON_INDEX, $start);

		// Breadcrumb
		if ($current_cat_id)
		{
			$this->ext_helper->set_breadcrumb($this->language->lang('NEWS'), $this->helper->route('vinabb_web_portal_route'));
			$this->ext_helper->set_breadcrumb($current_category_name);
		}

		return $this->helper->render('portal.html', ($current_category_name != '') ? $current_category_name : $this->language->lang('NEWS'), 200, true);
	}

	/**
	* List news articles with pagination
	*
	* @param string	$lang			2-letter language ISO code
	* @param int	$cat_id			Category ID
	* @param array	$articles		Array of articles
	* @param int	$article_count	Number of articles
	* @param int	$limit			Articles per page
	* @param int	$offset			Position of the start
	*
	* @return int Position of the start
	*/
	public function list_articles($lang, $cat_id, &$articles, &$article_count, $limit = 0, $offset = 0)
	{
		$operators = $this->container->get('vinabb.web.operators.portal_article');
		$article_count = $operators->count_articles($lang, $cat_id);

		if ($article_count == 0)
		{
			return 0;
		}

		if ($offset >= $article_count)
		{
			$offset = ($offset - $limit < 0) ? 0 : $offset - $limit;
		}

		/** @var \vinabb\web\entities\portal_article_interface $entity */
		foreach ($operators->list_articles($lang, $cat_id, 'article_time DESC', $limit, $offset) as $entity)
		{
			$articles[] = [
				'cat_id'		=> $entity->get_cat_id(),
				'id'			=> $entity->get_id(),
				'name'			=> $entity->get_name(),
				'name_seo'		=> $entity->get_name_seo(),
				'desc'			=> $entity->get_desc(),
				'text'			=> $entity->get_text_for_display(),
				'views'			=> $entity->get_views(),
				'time'			=> $entity->get_time()
			];
		}

		return $offset;
	}
}
