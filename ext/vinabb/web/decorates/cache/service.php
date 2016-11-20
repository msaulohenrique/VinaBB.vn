<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\decorates\cache;

use vinabb\web\includes\constants;

/**
* Extend the base cache service
*/
class service extends \phpbb\cache\service
{
	/** @var \vinabb\web\operators\forum_interface */
	protected $forum_operators;

	/** @var \vinabb\web\operators\bb_category_interface */
	protected $bb_cat_operators;

	/** @var string */
	protected $bb_items_table;

	/** @var \vinabb\web\operators\portal_category_interface */
	protected $portal_cat_operators;

	/** @var string */
	protected $portal_articles_table;

	/** @var string */
	protected $portal_comments_table;

	/** @var \vinabb\web\operators\page_interface */
	protected $page_operators;

	/** @var \vinabb\web\operators\menu_interface */
	protected $menu_operators;

	/** @var \vinabb\web\operators\headline_interface */
	protected $headline_operators;

	/**
	* Constructor
	*
	* @param \phpbb\cache\driver\driver_interface $driver
	* @param \phpbb\config\config $config
	* @param \phpbb\db\driver\driver_interface $db
	* @param string $root_path
	* @param string $php_ext
	* @param \vinabb\web\operators\forum_interface $forum_operators
	* @param \vinabb\web\operators\bb_category_interface $bb_cat_operators
	* @param string $bb_items_table
	* @param \vinabb\web\operators\portal_category_interface $portal_cat_operators
	* @param string $portal_articles_table
	* @param string $portal_comments_table
	* @param \vinabb\web\operators\page_interface $page_operators
	* @param \vinabb\web\operators\menu_interface $menu_operators
	* @param \vinabb\web\operators\headline_interface $headline_operators
	*/
	public function __construct(
		\phpbb\cache\driver\driver_interface $driver,
		\phpbb\config\config $config,
		\phpbb\db\driver\driver_interface $db,
		$root_path,
		$php_ext,
		\vinabb\web\operators\forum_interface $forum_operators,
		\vinabb\web\operators\bb_category_interface $bb_cat_operators,
		$bb_items_table,
		\vinabb\web\operators\portal_category_interface $portal_cat_operators,
		$portal_articles_table,
		$portal_comments_table,
		\vinabb\web\operators\page_interface $page_operators,
		\vinabb\web\operators\menu_interface $menu_operators,
		\vinabb\web\operators\headline_interface $headline_operators
	)
	{
		$this->set_driver($driver);
		$this->config = $config;
		$this->db = $db;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;
		$this->forum_operators = $forum_operators;
		$this->bb_cat_operators = $bb_cat_operators;
		$this->bb_items_table = $bb_items_table;
		$this->portal_cat_operators = $portal_cat_operators;
		$this->portal_articles_table = $portal_articles_table;
		$this->portal_comments_table = $portal_comments_table;
		$this->page_operators = $page_operators;
		$this->menu_operators = $menu_operators;
		$this->headline_operators = $headline_operators;
	}

	/**
	* Get cache from table: _config_text
	*
	* @return array
	*/
	public function get_config_text()
	{
		if (($config_text = $this->driver->get('_vinabb_web_config_text')) === false)
		{
			$sql = 'SELECT *
				FROM ' . CONFIG_TEXT_TABLE;
			$result = $this->db->sql_query($sql);

			$config_text = [];
			while ($row = $this->db->sql_fetchrow($result))
			{
				$config_text[$row['config_name']] = $row['config_value'];
			}
			$this->db->sql_freeresult($result);

			$this->driver->put('_vinabb_web_config_text', $config_text);
		}

		return $config_text;
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
		if (($lang_data = $this->driver->get('_vinabb_web_languages')) === false)
		{
			$sql = 'SELECT *
				FROM ' . LANG_TABLE;
			$result = $this->db->sql_query($sql);

			$lang_data = [];
			while ($row = $this->db->sql_fetchrow($result))
			{
				$lang_data[$row['lang_iso']] = [
					'dir'			=> $row['lang_dir'],
					'english_name'	=> $row['lang_english_name'],
					'local_name'	=> $row['lang_local_name'],
					'author'		=> $row['lang_author']
				];
			}
			$this->db->sql_freeresult($result);

			$this->driver->put('_vinabb_web_languages', $lang_data);
		}

		return $lang_data;
	}

	/**
	* Clear cache from table: _lang
	*/
	public function clear_lang_data()
	{
		$this->driver->destroy('_vinabb_web_languages');
	}

	/**
	* Get cache from table: _forums
	*
	* @return array
	*/
	public function get_forum_data()
	{
		if (($forum_data = $this->driver->get('_vinabb_web_forums')) === false)
		{
			$entities = $this->forum_operators->get_forums();
			$forum_data = [];

			/** @var \vinabb\web\entities\forum_interface $entity */
			foreach ($entities as $entity)
			{
				$forum_data[$entity->get_id()] = [
					'parent_id'			=> $entity->get_parent_id(),
					'left_id'			=> $entity->get_left_id(),
					'right_id'			=> $entity->get_right_id(),
					'name'				=> $entity->get_name(),
					'name_seo'			=> $entity->get_name_seo(),
					'desc'				=> $entity->get_desc_for_display(),
					'rules'				=> $entity->get_rules_for_display(),
					'topics_per_page'	=> $entity->get_topics_per_page(),
					'type'				=> $entity->get_type(),
					'status'			=> $entity->get_status()
				];
			}

			$this->driver->put('_vinabb_web_forums', $forum_data);
		}

		return $forum_data;
	}

	/**
	* Clear cache from table: _forums
	*/
	public function clear_forum_data()
	{
		$this->driver->destroy('_vinabb_web_forums');
	}

	/**
	* Get cache from table: _smilies
	*
	* @return array
	*/
	public function get_smilies()
	{
		if (($smilies = $this->driver->get('_vinabb_web_smilies')) === false)
		{
			$sql = 'SELECT *
				FROM ' . SMILIES_TABLE . '
				ORDER BY smiley_order';
			$result = $this->db->sql_query($sql);

			$smilies = [];
			while ($row = $this->db->sql_fetchrow($result))
			{
				$smilies[$row['code']] = [
					'id'		=> $row['smiley_id'],
					'emotion'	=> $row['emotion'],
					'url'		=> $row['smiley_url'],
					'width'		=> $row['smiley_width'],
					'height'	=> $row['smiley_height'],
					'display'	=> $row['display_on_posting']
				];
			}
			$this->db->sql_freeresult($result);

			$this->driver->put('_vinabb_web_smilies', $smilies);
		}

		return $smilies;
	}

	/**
	* Clear cache from table: _smilies
	*/
	public function clear_smilies()
	{
		$this->driver->destroy('_vinabb_web_smilies');
	}

	/**
	* Get cache from table: _bb_categories
	*
	* @param int $bb_type phpBB resource type
	* @return array
	*/
	public function get_bb_cats($bb_type)
	{
		if (($bb_cats = $this->driver->get('_vinabb_web_bb_categories_' . $bb_type)) === false)
		{
			$entities = $this->bb_cat_operators->get_cats($bb_type);
			$bb_cats = [];

			/** @var \vinabb\web\entities\bb_category_interface $entity */
			foreach ($entities as $entity)
			{
				$bb_cats[$entity->get_id()] = [
					'name'		=> $entity->get_name(),
					'name_vi'	=> ($entity->get_name_vi() == '') ? $entity->get_name() : $entity->get_name_vi(),
					'varname'	=> $entity->get_varname(),
					'desc'		=> $entity->get_desc(),
					'desc_vi'	=> $entity->get_desc_vi(),
					'icon'		=> $entity->get_icon()
				];
			}

			$this->driver->put('_vinabb_web_bb_categories_' . $bb_type, $bb_cats);
		}

		return $bb_cats;
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
		if (($new_items = $this->driver->get('_vinabb_web_new_bb_items_' . $bb_type)) === false)
		{
			$sql = 'SELECT *
				FROM ' . $this->bb_items_table . '
				WHERE bb_type = ' . (int) $bb_type . '
				ORDER BY item_updated DESC';
			$result = $this->db->sql_query_limit($sql, constants::NUM_NEW_ITEMS_ON_INDEX);

			$new_items = [];
			while ($row = $this->db->sql_fetchrow($result))
			{
				$new_items[] = [
					'id'		=> $row['item_id'],
					'name'		=> $row['item_name'],
					'varname'	=> $row['item_varname'],
					'version'	=> $row['item_version'],
					'price'		=> $row['item_price'],
					'added'		=> $row['item_added'],
					'updated'	=> $row['item_updated']
				];
			}
			$this->db->sql_freeresult($result);

			$this->driver->put('_vinabb_web_new_bb_items_' . $bb_type, $new_items);
		}

		return $new_items;
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
		if (($portal_cats = $this->driver->get('_vinabb_web_portal_categories')) === false)
		{
			$entities = $this->portal_cat_operators->get_cats();
			$portal_cats = [];

			/** @var \vinabb\web\entities\portal_category_interface $entity */
			foreach ($entities as $entity)
			{
				$portal_cats[$entity->get_id()] = [
					'parent_id'	=> $entity->get_parent_id(),
					'left_id'	=> $entity->get_left_id(),
					'right_id'	=> $entity->get_right_id(),
					'name'		=> $entity->get_name(),
					'name_vi'	=> ($entity->get_name_vi() == '') ? $entity->get_name() : $entity->get_name_vi(),
					'varname'	=> $entity->get_varname(),
					'icon'		=> $entity->get_icon()
				];
			}

			$this->driver->put('_vinabb_web_portal_categories', $portal_cats);
		}

		return $portal_cats;
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
		if (($index_articles = $this->driver->get('_vinabb_web_index_articles_' . $lang)) === false)
		{
			$sql = 'SELECT *
				FROM ' . $this->portal_articles_table . "
				WHERE article_lang = '" . $this->db->sql_escape($lang) . "'
				ORDER BY article_time DESC";
			$result = $this->db->sql_query_limit($sql, constants::NUM_ARTICLES_ON_INDEX);

			$index_articles = [];
			while ($row = $this->db->sql_fetchrow($result))
			{
				$index_articles[] = [
					'cat_id'		=> $row['cat_id'],
					'id'			=> $row['article_id'],
					'name'			=> $row['article_name'],
					'name_seo'		=> $row['article_name_seo'],
					'desc'			=> $row['article_desc'],
					'text'			=> $row['article_text'],
					'text_uid'		=> $row['article_text_uid'],
					'text_bitfield'	=> $row['article_text_bitfield'],
					'text_options'	=> $row['article_text_options'],
					'views'			=> $row['article_views'],
					'time'			=> $row['article_time']
				];
			}
			$this->db->sql_freeresult($result);

			$this->driver->put('_vinabb_web_index_articles_' . $lang, $index_articles);
		}

		return $index_articles;
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
		if (($comment_count = $this->driver->get('_vinabb_web_index_comment_counter_' . $lang)) === false)
		{
			$article_ids = [0];

			foreach ($this->get_index_articles($lang) as $article_data)
			{
				$article_ids[] = $article_data['id'];
			}

			$sql = 'SELECT COUNT(comment_id) AS total_comments
				FROM ' . $this->portal_comments_table . '
				WHERE ' . $this->db->sql_in_set('article_id', $article_ids) . '
				GROUP BY article_id';
			$result = $this->db->sql_query($sql);

			$comment_count = [];
			while ($row = $this->db->sql_fetchrow($result))
			{
				$comment_count[$row['article_id']] = $row['total_comments'];
			}
			$this->db->sql_freeresult($result);

			$this->driver->put('_vinabb_web_index_comment_counter_' . $lang, $comment_count);
		}

		return $comment_count;
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
		if (($pages = $this->driver->get('_vinabb_web_pages')) === false)
		{
			$entities = $this->page_operators->get_pages();
			$pages = [];

			/** @var \vinabb\web\entities\page_interface $entity */
			foreach ($entities as $entity)
			{
				$pages[$entity->get_id()] = [
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

			$this->driver->put('_vinabb_web_pages', $pages);
		}

		return $pages;
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
		if (($menus = $this->driver->get('_vinabb_web_menus')) === false)
		{
			$entities = $this->menu_operators->get_menus();
			$menus = [];

			/** @var \vinabb\web\entities\menu_interface $entity */
			foreach ($entities as $entity)
			{
				$menus[$entity->get_id()] = [
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

			$this->driver->put('_vinabb_web_menus', $menus);
		}

		return $menus;
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
		if (($headlines = $this->driver->get('_vinabb_web_headlines_' . $lang)) === false)
		{
			$entities = $this->headline_operators->get_headlines($lang);
			$headlines = [];

			/** @var \vinabb\web\entities\headline_interface $entity */
			foreach ($entities as $entity)
			{
				$headlines[] = [
					'id'	=> $entity->get_id(),
					'name'	=> $entity->get_name(),
					'desc'	=> $entity->get_desc(),
					'img'	=> $entity->get_img(),
					'url'	=> $entity->get_url()
				];
			}

			$this->driver->put('_vinabb_web_headlines_' . $lang, $headlines);
		}

		return $headlines;
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
