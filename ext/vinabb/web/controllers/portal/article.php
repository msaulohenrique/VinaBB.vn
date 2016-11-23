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
* Controller for the article
*/
class article implements article_interface
{
	/** @var \vinabb\web\controllers\cache\service_interface */
	protected $cache;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\language\language */
	protected $language;

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

	/** @var string */
	protected $root_path;

	/** @var string */
	protected $php_ext;

	/** @var string */
	protected $portal_articles_table;

	/** @var string */
	protected $portal_comments_table;

	/** @var array */
	protected $portal_cats;

	/**
	* Constructor
	*
	* @param \vinabb\web\controllers\cache\service_interface $cache
	* @param \phpbb\config\config $config
	* @param \phpbb\db\driver\driver_interface $db
	* @param \phpbb\language\language $language
	* @param \vinabb\web\controllers\pagination $pagination
	* @param \phpbb\request\request $request
	* @param \phpbb\template\template $template
	* @param \phpbb\user $user
	* @param \phpbb\controller\helper $helper
	* @param \vinabb\web\controllers\helper_interface $ext_helper
	* @param string $root_path
	* @param string $php_ext
	* @param string $portal_articles_table
	* @param string $portal_comments_table
	*/
	public function __construct(
		\vinabb\web\controllers\cache\service_interface $cache,
		\phpbb\config\config $config,
		\phpbb\db\driver\driver_interface $db,
		\phpbb\language\language $language,
		\vinabb\web\controllers\pagination $pagination,
		\phpbb\request\request $request,
		\phpbb\template\template $template,
		\phpbb\user $user,
		\phpbb\controller\helper $helper,
		\vinabb\web\controllers\helper_interface $ext_helper,
		$root_path,
		$php_ext,
		$portal_articles_table,
		$portal_comments_table
	)
	{
		$this->cache = $cache;
		$this->config = $config;
		$this->db = $db;
		$this->language = $language;
		$this->pagination = $pagination;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->helper = $helper;
		$this->ext_helper = $ext_helper;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;
		$this->portal_articles_table = $portal_articles_table;
		$this->portal_comments_table = $portal_comments_table;

		$this->portal_cats = $this->cache->get_portal_cats();
	}

	/**
	* View details an article
	*
	* @param $article_id Article ID
	* @return \Symfony\Component\HttpFoundation\Response
	*/
	public function article($article_id)
	{
		$page_title = $this->language->lang('VINABB');

		if (!$article_id)
		{
			trigger_error('NO_PORTAL_ARTICLE_ID');
		}
		else
		{
			$sql = 'SELECT *
				FROM ' . $this->portal_articles_table . "
				WHERE article_id = $article_id";
			$result = $this->db->sql_query($sql);
			$article_data = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);

			if ($article_data === false)
			{
				trigger_error('NO_PORTAL_ARTICLE');
			}
			else
			{
				$page_title = $article_data['article_name'];
				$category_name = ($this->user->lang_name == constants::LANG_VIETNAMESE) ? $this->portal_cats[$article_data['cat_id']]['name_vi'] : $this->portal_cats[$article_data['cat_id']]['name'];
				$cat_varname = $this->portal_cats[$article_data['cat_id']]['varname'];

				// Breadcrumb
				$this->ext_helper->set_breadcrumb($this->language->lang('NEWS'), $this->helper->route('vinabb_web_portal_route'));
				$this->ext_helper->set_breadcrumb($category_name, $this->helper->route('vinabb_web_portal_cat_route', ['varname' => $cat_varname]));
				$this->ext_helper->set_breadcrumb($this->language->lang('PORTAL_ARTICLE'));

				$this->template->assign_vars([
					'ARTICLE_NAME'	=> $article_data['article_name'],
					'ARTICLE_DESC'	=> $article_data['article_desc'],
					'ARTICLE_TEXT'	=> generate_text_for_display($article_data['article_text'], $article_data['article_text_uid'], $article_data['article_text_bitfield'], $article_data['article_text_options']),
					'ARTICLE_TIME'	=> $this->user->format_date($article_data['article_time'])
				]);
			}
		}

		return $this->helper->render('portal_article.html', $page_title);
	}
}
