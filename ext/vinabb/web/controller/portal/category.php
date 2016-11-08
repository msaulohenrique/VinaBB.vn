<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\controller\portal;

use vinabb\web\includes\constants;

class category extends portal
{
	/**
	* Display articles from a news category
	*
	* @param $varname
	* @param $page
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
		$articles = array();
		$article_count = 0;
		$start = $this->ext_helper->list_articles($current_cat_id, $articles, $article_count, constants::NUM_ARTICLES_ON_INDEX, $start);

		foreach ($articles as $row)
		{
			$this->template->assign_block_vars('articles', array(
				'CATEGORY'	=> ($this->user->lang_name == constants::LANG_VIETNAMESE) ? $this->portal_cats[$row['cat_id']]['name_vi'] : $this->portal_cats[$row['cat_id']]['name'],
				'CAT_URL'	=> $this->helper->route('vinabb_web_portal_cat_route', array('varname' => $this->portal_cats[$row['cat_id']]['varname'])),
				'NAME'		=> $row['article_name'],
				'DESC'		=> $row['article_desc'],
				'TIME'		=> $this->user->format_date($row['article_time']),
				'URL'		=> $this->helper->route('vinabb_web_portal_article_route', array('varname' => $this->portal_cats[$row['cat_id']]['varname'], 'article_id' => $row['article_id'], 'seo' => $row['article_name_seo'] . constants::REWRITE_URL_SEO))
			));
		}

		// Generate pagination
		$this->pagination->generate_template_pagination('vinabb_web_portal_cat_route', array('varname' => ($current_cat_id ? $varname : 'all')), 'pagination', 'start', $article_count, constants::NUM_ARTICLES_ON_INDEX, $start);

		// Breadcrumb
		if ($current_cat_id)
		{
			$this->ext_helper->set_breadcrumb($this->language->lang('NEWS'), $this->helper->route('vinabb_web_portal_route'));
			$this->ext_helper->set_breadcrumb($current_category_name);
		}

		return $this->helper->render('portal.html', !empty($current_category_name) ? $current_category_name : $this->language->lang('NEWS'), 200, true);
	}
}
