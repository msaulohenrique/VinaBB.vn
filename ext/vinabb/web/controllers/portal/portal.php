<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\controllers\portal;

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
	* @param \vinabb\web\controllers\cache\service_interface $cache
	* @param \phpbb\config\config $config
	* @param \phpbb\language\language $language
	* @param \vinabb\web\controllers\pagination $pagination
	* @param \phpbb\template\template $template
	* @param \phpbb\user $user
	* @param \phpbb\controller\helper $helper
	* @param \vinabb\web\controllers\helper_interface $ext_helper
	* @param \vinabb\web\controllers\portal\helper\helper_interface $portal_helper
	*/
	public function __construct(
		\vinabb\web\controllers\cache\service_interface $cache,
		\phpbb\config\config $config,
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
	* @param bool $index_page	true: Use on the index page (Get x latest articles from all categories - cached)
	*							false: Use with a news category (Get all articles from that category)
	*/
	public function index($index_page = true)
	{
		// Check new versions
		$this->portal_helper->check_new_versions();

		// News categories
		$this->portal_helper->get_portal_cats();

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
		$this->index();

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
		$this->index(false);

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
		$start = $this->ext_helper->list_articles($this->user->lang_name, $current_cat_id, $articles, $article_count, constants::NUM_ARTICLES_ON_INDEX, $start);

		foreach ($articles as $row)
		{
			$this->template->assign_block_vars('articles', [
				'CATEGORY'	=> ($this->user->lang_name == constants::LANG_VIETNAMESE) ? $this->portal_cats[$row['cat_id']]['name_vi'] : $this->portal_cats[$row['cat_id']]['name'],
				'CAT_URL'	=> $this->helper->route('vinabb_web_portal_cat_route', ['varname' => $this->portal_cats[$row['cat_id']]['varname']]),
				'NAME'		=> $row['article_name'],
				'DESC'		=> $row['article_desc'],
				'TIME'		=> $this->user->format_date($row['article_time']),
				'URL'		=> $this->helper->route('vinabb_web_portal_article_route', ['varname' => $this->portal_cats[$row['cat_id']]['varname'], 'article_id' => $row['article_id'], 'seo' => $row['article_name_seo'] . constants::REWRITE_URL_SEO])
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
}
