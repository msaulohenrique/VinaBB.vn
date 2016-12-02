<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\controllers\cache;

use Symfony\Component\DependencyInjection\ContainerInterface;
use vinabb\web\includes\constants;

/**
* Extend the base cache service
*/
class service implements service_interface
{
	/** @var \phpbb\cache\driver\driver_interface */
	protected $driver;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var ContainerInterface */
	protected $container;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/**
	* Constructor
	*
	* @param \phpbb\cache\driver\driver_interface $driver
	* @param \phpbb\config\config $config
	* @param ContainerInterface $container
	* @param \phpbb\db\driver\driver_interface $db
	*/
	public function __construct(
		\phpbb\cache\driver\driver_interface $driver,
		\phpbb\config\config $config,
		ContainerInterface $container,
		\phpbb\db\driver\driver_interface $db
	)
	{
		$this->set_driver($driver);
		$this->config = $config;
		$this->container = $container;
		$this->db = $db;
	}

	/**
	* Returns the cache driver used by this cache service.
	*
	* @return \phpbb\cache\driver\driver_interface The cache driver
	*/
	public function get_driver()
	{
		return $this->driver;
	}

	/**
	* Replaces the cache driver used by this cache service.
	*
	* @param \phpbb\cache\driver\driver_interface $driver The cache driver
	*/
	public function set_driver(\phpbb\cache\driver\driver_interface $driver)
	{
		$this->driver = $driver;
	}

	public function __call($method, $arguments)
	{
		return call_user_func_array([$this->driver, $method], $arguments);
	}

	/**
	* Get cache from table: _config_text
	*
	* @return array
	*/
	public function get_config_text()
	{
		if (($rows = $this->driver->get('_vinabb_web_config_text')) === false)
		{
			$sql = 'SELECT *
				FROM ' . CONFIG_TEXT_TABLE;
			$result = $this->db->sql_query($sql);

			$rows = [];
			while ($row = $this->db->sql_fetchrow($result))
			{
				$rows[$row['config_name']] = $row['config_value'];
			}
			$this->db->sql_freeresult($result);

			$this->driver->put('_vinabb_web_config_text', $rows);
		}

		return $rows;
	}

	/**
	* Clear cache from table: _config_text
	*/
	public function clear_config_text()
	{
		$this->driver->destroy('_vinabb_web_config_text');
	}

	/**
	* Get cache from table: _lang
	*
	* @return array
	*/
	public function get_lang_data()
	{
		if (($rows = $this->driver->get('_vinabb_web_languages')) === false)
		{
			$rows = [];

			/** @var \vinabb\web\entities\language_interface $entity */
			foreach ($this->container->get('vinabb.web.operators.language')->get_langs() as $entity)
			{
				$rows[$entity->get_iso()] = [
					'dir'			=> $entity->get_dir(),
					'english_name'	=> $entity->get_english_name(),
					'local_name'	=> $entity->get_local_name(),
					'author'		=> $entity->get_author()
				];
			}

			$this->driver->put('_vinabb_web_languages', $rows);
		}

		return $rows;
	}

	/**
	* Clear cache from table: _lang
	*/
	public function clear_lang_data()
	{
		$this->driver->destroy('_vinabb_web_languages');
	}

	/**
	* Get cache from table: _groups
	*
	* @return array
	*/
	public function get_groups()
	{
		if (($rows = $this->driver->get('_vinabb_web_groups')) === false)
		{
			$rows = [];

			/** @var \vinabb\web\entities\group_interface $entity */
			foreach ($this->container->get('vinabb.web.operators.group')->get_groups() as $entity)
			{
				$rows[$entity->get_id()] = [
					'name'	=> $entity->get_name(),
					'type'	=> $entity->get_type(),
					'desc'	=> $entity->get_desc_for_display()
				];
			}

			$this->driver->put('_vinabb_web_groups', $rows);
		}

		return $rows;
	}

	/**
	* Clear cache from table: _groups
	*/
	public function clear_groups()
	{
		$this->driver->destroy('_vinabb_web_groups');
	}

	/**
	* Get cache from table: _teampage
	*
	* @return array
	*/
	public function get_teams()
	{
		if (($rows = $this->driver->get('_vinabb_web_teams')) === false)
		{
			$rows = [];

			/** @var \vinabb\web\entities\team_interface $entity */
			foreach ($this->container->get('vinabb.web.operators.team')->get_teams() as $entity)
			{
				$rows[$entity->get_id()] = [
					'group_id'	=> $entity->get_group_id(),
					'name'		=> $entity->get_name(),
					'parent'	=> $entity->get_parent()
				];
			}

			$this->driver->put('_vinabb_web_teams', $rows);
		}

		return $rows;
	}

	/**
	* Clear cache from table: _teampage
	*/
	public function clear_teams()
	{
		$this->driver->destroy('_vinabb_web_teams');
	}

	/**
	* Get cache from table: _forums
	*
	* @return array
	*/
	public function get_forum_data()
	{
		if (($rows = $this->driver->get('_vinabb_web_forums')) === false)
		{
			$rows = [];

			/** @var \vinabb\web\entities\forum_interface $entity */
			foreach ($this->container->get('vinabb.web.operators.forum')->get_forums() as $entity)
			{
				$rows[$entity->get_id()] = [
					'parent_id'			=> $entity->get_parent_id(),
					'left_id'			=> $entity->get_left_id(),
					'right_id'			=> $entity->get_right_id(),
					'name'				=> $entity->get_name(),
					'name_seo'			=> $entity->get_name_seo(),
					'desc'				=> $entity->get_desc_for_display(),
					'desc_raw'			=> $entity->get_desc_for_edit(),
					'rules'				=> $entity->get_rules_for_display(),
					'topics_per_page'	=> $entity->get_topics_per_page(),
					'type'				=> $entity->get_type(),
					'status'			=> $entity->get_status()
				];
			}

			$this->driver->put('_vinabb_web_forums', $rows);
		}

		return $rows;
	}

	/**
	* Clear cache from table: _forums
	*/
	public function clear_forum_data()
	{
		$this->driver->destroy('_vinabb_web_forums');
	}

	/**
	* Get cache from table: _icons
	*
	* @return array
	*/
	public function get_post_icons()
	{
		if (($rows = $this->driver->get('_vinabb_web_post_icons')) === false)
		{
			$rows = [];

			/** @var \vinabb\web\entities\post_icon_interface $entity */
			foreach ($this->container->get('vinabb.web.operators.post_icon')->get_icons() as $entity)
			{
				$rows[$entity->get_id()] = [
					'url'		=> $entity->get_url(),
					'width'		=> $entity->get_width(),
					'height'	=> $entity->get_height(),
					'alt'		=> $entity->get_alt(),
					'display'	=> $entity->get_display_on_posting()
				];
			}

			$this->driver->put('_vinabb_web_post_icons', $rows);
		}

		return $rows;
	}

	/**
	* Clear cache from table: _icons
	*/
	public function clear_post_icons()
	{
		$this->driver->destroy('_vinabb_web_post_icons');
	}

	/**
	* Get cache from table: _smilies
	*
	* @return array
	*/
	public function get_smilies()
	{
		if (($rows = $this->driver->get('_vinabb_web_smilies')) === false)
		{
			$rows = [];

			/** @var \vinabb\web\entities\smiley_interface $entity */
			foreach ($this->container->get('vinabb.web.operators.smiley')->get_smilies() as $entity)
			{
				$rows[$entity->get_code()] = [
					'id'		=> $entity->get_id(),
					'emotion'	=> $entity->get_emotion(),
					'url'		=> $entity->get_url(),
					'width'		=> $entity->get_width(),
					'height'	=> $entity->get_height(),
					'display'	=> $entity->get_display_on_posting()
				];
			}

			$this->driver->put('_vinabb_web_smilies', $rows);
		}

		return $rows;
	}

	/**
	* Clear cache from table: _smilies
	*/
	public function clear_smilies()
	{
		$this->driver->destroy('_vinabb_web_smilies');
	}

	/**
	* Get cache from table: _ranks
	*
	* @return array
	*/
	public function get_ranks()
	{
		if (($rows = $this->driver->get('_vinabb_web_ranks')) === false)
		{
			$rows = [];

			/** @var \vinabb\web\entities\rank_interface $entity */
			foreach ($this->container->get('vinabb.web.operators.rank')->get_ranks() as $entity)
			{
				$rows[$entity->get_id()] = [
					'title'		=> $entity->get_title(),
					'min'		=> $entity->get_min(),
					'special'	=> $entity->get_special(),
					'image'		=> $entity->get_image()
				];
			}

			$this->driver->put('_vinabb_web_ranks', $rows);
		}

		return $rows;
	}

	/**
	* Clear cache from table: _ranks
	*/
	public function clear_ranks()
	{
		$this->driver->destroy('_vinabb_web_ranks');
	}

	/**
	* Get cache from table: _words
	*
	* @return array
	*/
	public function get_censor_words()
	{
		if (($rows = $this->driver->get('_vinabb_web_censor_words')) === false)
		{
			$rows = [];

			/** @var \vinabb\web\entities\censor_word_interface $entity */
			foreach ($this->container->get('vinabb.web.operators.censor_word')->get_words() as $entity)
			{
				$rows[$entity->get_id()] = [
					'word'			=> $entity->get_word(),
					'replacement'	=> $entity->get_replacement()
				];
			}

			$this->driver->put('_vinabb_web_censor_words', $rows);
		}

		return $rows;
	}

	/**
	* Clear cache from table: _words
	*/
	public function clear_censor_words()
	{
		$this->driver->destroy('_vinabb_web_censor_words');
	}

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
				$rows[$entity->get_id()] = [
					'name'		=> $entity->get_name(),
					'name_vi'	=> ($entity->get_name_vi() == '') ? $entity->get_name() : $entity->get_name_vi(),
					'varname'	=> $entity->get_varname(),
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
