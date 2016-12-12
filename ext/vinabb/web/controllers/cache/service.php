<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\controllers\cache;

use vinabb\web\includes\constants;

/**
* Extend the base cache service
*/
class service extends service_core implements service_interface
{
	/**
	* Get cache from table: _bb_categories
	*
	* @param int $bb_type phpBB resource type
	* @return array
	*/
	public function get_bb_cats($bb_type)
	{
		if (($rows = $this->driver->get('_vinabb_web_bb_categories_' . $bb_type)) === false)
		{
			$rows = [];

			/** @var \vinabb\web\entities\bb_category_interface $entity */
			foreach ($this->container->get('vinabb.web.operators.bb_category')->get_cats($bb_type) as $entity)
			{
				$rows[$entity->get_varname()] = [
					'id'		=> $entity->get_id(),
					'name'		=> $entity->get_name(),
					'name_vi'	=> ($entity->get_name_vi() == '') ? $entity->get_name() : $entity->get_name_vi(),
					'desc'		=> $entity->get_desc(),
					'desc_vi'	=> $entity->get_desc_vi(),
					'icon'		=> $entity->get_icon()
				];
			}

			$this->driver->put('_vinabb_web_bb_categories_' . $bb_type, $rows);
		}

		return $rows;
	}

	/**
	* Clear cache from table: _bb_categories
	*
	* @param int $bb_type phpBB resource type
	*/
	public function clear_bb_cats($bb_type)
	{
		$this->driver->destroy('_vinabb_web_bb_categories_' . $bb_type);
	}

	/**
	* Get cache from table: _bb_items
	*
	* @param int $bb_type phpBB resource type
	* @return array
	*/
	public function get_new_bb_items($bb_type)
	{
		if (($rows = $this->driver->get('_vinabb_web_new_bb_items_' . $bb_type)) === false)
		{
			$rows = [];

			/** @var \vinabb\web\entities\bb_item_interface $entity */
			foreach ($this->container->get('vinabb.web.operators.bb_item')->get_latest_items($bb_type, constants::NUM_NEW_ITEMS_ON_INDEX) as $entity)
			{
				$rows[] = [
					'id'		=> $entity->get_id(),
					'name'		=> $entity->get_name(),
					'varname'	=> $entity->get_varname(),
					'price'		=> $entity->get_price(),
					'added'		=> $entity->get_added(),
					'updated'	=> $entity->get_updated()
				];
			}

			$this->driver->put('_vinabb_web_new_bb_items_' . $bb_type, $rows);
		}

		return $rows;
	}

	/**
	* Clear cache from table: _bb_items
	*
	* @param int $bb_type phpBB resource type
	*/
	public function clear_new_bb_items($bb_type)
	{
		$this->driver->destroy('_vinabb_web_new_bb_items_' . $bb_type);
	}

	/**
	* Get cache from table: _portal_categories
	*
	* @return array
	*/
	public function get_portal_cats()
	{
		if (($rows = $this->driver->get('_vinabb_web_portal_categories')) === false)
		{
			$rows = [];

			/** @var \vinabb\web\entities\portal_category_interface $entity */
			foreach ($this->container->get('vinabb.web.operators.portal_category')->get_cats() as $entity)
			{
				$rows[$entity->get_id()] = [
					'parent_id'	=> $entity->get_parent_id(),
					'left_id'	=> $entity->get_left_id(),
					'right_id'	=> $entity->get_right_id(),
					'name'		=> $entity->get_name(),
					'name_vi'	=> ($entity->get_name_vi() == '') ? $entity->get_name() : $entity->get_name_vi(),
					'varname'	=> $entity->get_varname(),
					'icon'		=> $entity->get_icon()
				];
			}

			$this->driver->put('_vinabb_web_portal_categories', $rows);
		}

		return $rows;
	}

	/**
	* Clear cache from table: _portal_categories
	*/
	public function clear_portal_cats()
	{
		$this->driver->destroy('_vinabb_web_portal_categories');
	}

	/**
	* Get cache from table: _portal_articles
	*
	* @param string $lang 2-letter language ISO code
	* @return array
	*/
	public function get_index_articles($lang)
	{
		if (($rows = $this->driver->get('_vinabb_web_index_articles_' . $lang)) === false)
		{
			$rows = [];

			/** @var \vinabb\web\entities\portal_article_interface $entity */
			foreach ($this->container->get('vinabb.web.operators.portal_article')->get_latest_articles($lang, constants::NUM_ARTICLES_ON_INDEX) as $entity)
			{
				$rows[] = [
					'cat_id'		=> $entity->get_cat_id(),
					'id'			=> $entity->get_id(),
					'name'			=> $entity->get_name(),
					'name_seo'		=> $entity->get_name_seo(),
					'img'			=> $entity->get_img(),
					'desc'			=> $entity->get_desc(),
					'text'			=> $entity->get_text_for_display(),
					'views'			=> $entity->get_views(),
					'time'			=> $entity->get_time()
				];
			}

			$this->driver->put('_vinabb_web_index_articles_' . $lang, $rows);
		}

		return $rows;
	}

	/**
	* Clear cache from table: _portal_articles
	*
	* @param string $lang 2-letter language ISO code
	*/
	public function clear_index_articles($lang)
	{
		$this->driver->destroy('_vinabb_web_index_articles_' . $lang);
		$this->driver->destroy('_vinabb_web_index_comment_counter_' . $lang);
	}

	/**
	* Get comment counter for get_index_articles()
	*
	* @param string	$lang	2-letter language ISO code
	* @return array
	*/
	public function get_index_comment_counter($lang)
	{
		if (($rows = $this->driver->get('_vinabb_web_index_comment_counter_' . $lang)) === false)
		{
			$article_ids = [0];

			foreach ($this->get_index_articles($lang) as $article_data)
			{
				$article_ids[] = $article_data['id'];
			}

			$sql = 'SELECT COUNT(comment_id) AS total_comments
				FROM ' . $this->container->getParameter('vinabb.web.tables.portal_comments') . '
				WHERE ' . $this->db->sql_in_set('article_id', $article_ids) . '
				GROUP BY article_id';
			$result = $this->db->sql_query($sql);

			$rows = [];
			while ($row = $this->db->sql_fetchrow($result))
			{
				$rows[$row['article_id']] = $row['total_comments'];
			}
			$this->db->sql_freeresult($result);

			$this->driver->put('_vinabb_web_index_comment_counter_' . $lang, $rows);
		}

		return $rows;
	}

	/**
	* Clear comment counter for get_index_articles()
	*
	* @param string $lang 2-letter language ISO code
	*/
	public function clear_index_comment_counter($lang)
	{
		$this->driver->destroy('_vinabb_web_index_comment_counter_' . $lang);
	}

	/**
	* Get cache from table: _pages
	*
	* @return array
	*/
	public function get_pages()
	{
		if (($rows = $this->driver->get('_vinabb_web_pages')) === false)
		{
			$rows = [];

			/** @var \vinabb\web\entities\page_interface $entity */
			foreach ($this->container->get('vinabb.web.operators.page')->get_pages() as $entity)
			{
				$rows[$entity->get_id()] = [
					'name'				=> $entity->get_name(),
					'name_vi'			=> ($entity->get_name_vi() == '') ? $entity->get_name() : $entity->get_name_vi(),
					'varname'			=> $entity->get_varname(),
					'desc'				=> $entity->get_desc(),
					'desc_vi'			=> $entity->get_desc_vi(),
					'text'				=> $entity->get_text_for_display(),
					'text_vi'			=> $entity->get_text_vi_for_display(),
					'enable_guest'		=> $entity->get_enable_guest(),
					'enable_bot'		=> $entity->get_enable_bot(),
					'enable_new_user'	=> $entity->get_enable_new_user(),
					'enable_user'		=> $entity->get_enable_user(),
					'enable_mod'		=> $entity->get_enable_mod(),
					'enable_global_mod'	=> $entity->get_enable_global_mod(),
					'enable_admin'		=> $entity->get_enable_admin(),
					'enable_founder'	=> $entity->get_enable_founder()
				];
			}

			$this->driver->put('_vinabb_web_pages', $rows);
		}

		return $rows;
	}

	/**
	* Clear cache from table: _pages
	*/
	public function clear_pages()
	{
		$this->driver->destroy('_vinabb_web_pages');
	}

	/**
	* Get cache from table: _menus
	*
	* @return array
	*/
	public function get_menus()
	{
		if (($rows = $this->driver->get('_vinabb_web_menus')) === false)
		{
			$rows = [];

			/** @var \vinabb\web\entities\menu_interface $entity */
			foreach ($this->container->get('vinabb.web.operators.menu')->get_menus() as $entity)
			{
				$rows[$entity->get_id()] = [
					'parent_id'			=> $entity->get_parent_id(),
					'left_id'			=> $entity->get_left_id(),
					'right_id'			=> $entity->get_right_id(),
					'name'				=> $entity->get_name(),
					'name_vi'			=> ($entity->get_name_vi() == '') ? $entity->get_name() : $entity->get_name_vi(),
					'type'				=> $entity->get_type(),
					'icon'				=> $entity->get_icon(),
					'data'				=> $entity->get_data(),
					'target'			=> $entity->get_target(),
					'enable_guest'		=> $entity->get_enable_guest(),
					'enable_bot'		=> $entity->get_enable_bot(),
					'enable_new_user'	=> $entity->get_enable_new_user(),
					'enable_user'		=> $entity->get_enable_user(),
					'enable_mod'		=> $entity->get_enable_mod(),
					'enable_global_mod'	=> $entity->get_enable_global_mod(),
					'enable_admin'		=> $entity->get_enable_admin(),
					'enable_founder'	=> $entity->get_enable_founder()
				];
			}

			$this->driver->put('_vinabb_web_menus', $rows);
		}

		return $rows;
	}

	/**
	* Clear cache from table: _menus
	*/
	public function clear_menus()
	{
		$this->driver->destroy('_vinabb_web_menus');
	}

	/**
	* Get cache from table: _headlines
	*
	* @param string $lang 2-letter language ISO code
	* @return array
	*/
	public function get_headlines($lang)
	{
		if (($rows = $this->driver->get('_vinabb_web_headlines_' . $lang)) === false)
		{
			$rows = [];

			/** @var \vinabb\web\entities\headline_interface $entity */
			foreach ($this->container->get('vinabb.web.operators.headline')->get_headlines($lang) as $entity)
			{
				$rows[] = [
					'id'	=> $entity->get_id(),
					'name'	=> $entity->get_name(),
					'desc'	=> $entity->get_desc(),
					'img'	=> $entity->get_img(),
					'url'	=> $entity->get_url()
				];
			}

			$this->driver->put('_vinabb_web_headlines_' . $lang, $rows);
		}

		return $rows;
	}

	/**
	* Clear cache from table: _headlines
	*
	* @param string $lang 2-letter language ISO code
	*/
	public function clear_headlines($lang)
	{
		$this->driver->destroy('_vinabb_web_headlines_' . $lang);
	}
}
