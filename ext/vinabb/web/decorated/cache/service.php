<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\decorated\cache;

use vinabb\web\includes\constants;

class service extends \phpbb\cache\service
{
	/** @var \vinabb\web\controller\helper */
	protected $ext_helper;

	/** @var string */
	protected $bb_categories_table;

	/** @var string */
	protected $bb_items_table;

	/** @var string */
	protected $portal_categories_table;

	/** @var string */
	protected $portal_articles_table;

	/**
	* Constructor
	*
	* @param \phpbb\cache\driver\driver_interface $driver
	* @param \phpbb\config\config $config
	* @param \phpbb\db\driver\driver_interface $db
	* @param \vinabb\web\controller\helper $ext_helper
	* @param string $root_path
	* @param string $php_ext
	* @param string $bb_categories_table
	* @param string $bb_items_table
	* @param string $portal_categories_table
	* @param string $portal_articles_table
	*/
	public function __construct(
		\phpbb\cache\driver\driver_interface $driver,
		\phpbb\config\config $config,
		\phpbb\db\driver\driver_interface $db,
		\vinabb\web\controller\helper $ext_helper,
		$root_path,
		$php_ext,
		$bb_categories_table,
		$bb_items_table,
		$portal_categories_table,
		$portal_articles_table
	)
	{
		$this->set_driver($driver);
		$this->config = $config;
		$this->db = $db;
		$this->ext_helper = $ext_helper;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;
		$this->bb_categories_table = $bb_categories_table;
		$this->bb_items_table = $bb_items_table;
		$this->portal_categories_table = $portal_categories_table;
		$this->portal_articles_table = $portal_articles_table;
	}

	function get_config_text()
	{
		if (($config_text_data = $this->driver->get('_vinabb_web_config_text')) === false)
		{
			$sql = 'SELECT *
				FROM ' . CONFIG_TEXT_TABLE;
			$result = $this->db->sql_query($sql);

			$config_text_data = array();
			while ($row = $this->db->sql_fetchrow($result))
			{
				$config_text_data[$row['config_name']] = $row['config_value'];
			}
			$this->db->sql_freeresult($result);

			$this->driver->put('_vinabb_web_config_text', $config_text_data);
		}
	}

	function clear_config_text()
	{
		$this->driver->destroy('_vinabb_web_config_text');
	}

	function get_lang_data()
	{
		if (($lang_data = $this->driver->get('_vinabb_web_languages')) === false)
		{
			$sql = 'SELECT *
				FROM ' . LANG_TABLE;
			$result = $this->db->sql_query($sql);

			$lang_data = array();
			while ($row = $this->db->sql_fetchrow($result))
			{
				$lang_data[$row['lang_iso']] = array(
					'dir'			=> $row['lang_dir'],
					'english_name'	=> $row['lang_english_name'],
					'local_name'	=> $row['lang_local_name'],
					'author'		=> $row['lang_author'],
				);
			}
			$this->db->sql_freeresult($result);

			$this->driver->put('_vinabb_web_languages', $lang_data);
		}

		return $lang_data;
	}

	function clear_lang_data()
	{
		$this->driver->destroy('_vinabb_web_languages');
	}

	function get_forum_data($sort = false)
	{
		$sort_suffix = ($sort) ? '_sorted' : '';

		if (($forum_data = $this->driver->get('_vinabb_web_forums' . $sort_suffix)) === false)
		{
			$sql_order = ($sort) ? 'ORDER BY left_id' : '';

			$sql = 'SELECT *
				FROM ' . FORUMS_TABLE . "
				$sql_order";
			$result = $this->db->sql_query($sql);

			$forum_data = array();
			while ($row = $this->db->sql_fetchrow($result))
			{
				$forum_data[$row['forum_id']] = array(
					'parent_id'		=> $row['parent_id'],
					'left_id'		=> $row['left_id'],
					'right_id'		=> $row['right_id'],
					'name'			=> $row['forum_name'],
					'desc'			=> $row['forum_desc'],
					'desc_bitfield'	=> $row['forum_desc_bitfield'],
					'desc_options'	=> $row['forum_desc_options'],
					'desc_uid'		=> $row['forum_desc_uid'],
					'type'			=> $row['forum_type'],
					'status'		=> $row['forum_status'],
					'name_seo'		=> $row['forum_name_seo'],
				);
			}
			$this->db->sql_freeresult($result);

			$this->driver->put('_vinabb_web_forums' . $sort_suffix, $forum_data);
		}

		return $forum_data;
	}

	function clear_forum_data()
	{
		$this->driver->destroy('_vinabb_web_forums');
		$this->driver->destroy('_vinabb_web_forums_sorted');
	}

	function get_bb_cats($bb_type)
	{
		if (($bb_cats = $this->driver->get('_vinabb_web_bb_' . strtolower($bb_type) . '_categories')) === false)
		{
			$sql = 'SELECT *
				FROM ' . $this->bb_categories_table . '
				WHERE bb_type = ' . $this->ext_helper->get_bb_type_constants($bb_type);
			$result = $this->db->sql_query($sql);

			$bb_cats = array();
			while ($row = $this->db->sql_fetchrow($result))
			{
				$bb_cats[$row['cat_id']] = array(
					'name'		=> $row['cat_name'],
					'name_vi'	=> $row['cat_name_vi'],
					'varname'	=> $row['cat_varname'],
				);
			}
			$this->db->sql_freeresult($result);

			$this->driver->put('_vinabb_web_bb_' . strtolower($bb_type) . '_categories', $bb_cats);
		}

		return $bb_cats;
	}

	function clear_bb_cats($bb_type)
	{
		$this->driver->destroy('_vinabb_web_bb_' . strtolower($bb_type) . '_categories');
	}

	function get_new_bb_items($bb_type)
	{
		if (($new_items = $this->driver->get('_vinabb_web_bb_new_' . strtolower($bb_type) . 's')) === false)
		{
			$sql = 'SELECT *
				FROM ' . $this->bb_items_table . '
				WHERE bb_type = ' . $this->ext_helper->get_bb_type_constants($bb_type) . '
				ORDER BY item_updated DESC';
			$result = $this->db->sql_query_limit($sql, constants::NUM_NEW_ITEMS_ON_INDEX);

			$new_items = array();
			while ($row = $this->db->sql_fetchrow($result))
			{
				$new_items[] = array(
					'id'		=> $row['item_id'],
					'name'		=> $row['item_name'],
					'varname'	=> $row['item_varname'],
					'version'	=> $row['item_version'],
					'price'		=> $row['item_price'],
					'added'		=> $row['item_added'],
					'updated'	=> $row['item_updated'],
				);
			}
			$this->db->sql_freeresult($result);

			$this->driver->put('_vinabb_web_bb_new_' . strtolower($bb_type) . 's', $new_items);
		}

		return $new_items;
	}

	function clear_new_bb_items($bb_type)
	{
		$this->driver->destroy('_vinabb_web_bb_new_' . strtolower($bb_type) . 's');
	}

	function get_portal_cats()
	{
		if (($portal_cats = $this->driver->get('_vinabb_web_portal_categories')) === false)
		{
			$sql = 'SELECT *
				FROM ' . $this->portal_categories_table . '
				ORDER BY cat_order';
			$result = $this->db->sql_query($sql);

			$portal_cats = array();
			while ($row = $this->db->sql_fetchrow($result))
			{
				$portal_cats[$row['cat_id']] = array(
					'name'		=> $row['cat_name'],
					'name_vi'	=> $row['cat_name_vi'],
					'varname'	=> $row['cat_varname'],
					'icon'		=> $row['cat_icon'],
				);
			}
			$this->db->sql_freeresult($result);

			$this->driver->put('_vinabb_web_portal_categories', $portal_cats);
		}

		return $portal_cats;
	}

	function clear_portal_cats()
	{
		$this->driver->destroy('_vinabb_web_portal_categories');
	}

	function get_index_articles($lang)
	{
		if (($index_articles = $this->driver->get('_vinabb_web_index_articles_' . $lang)) === false)
		{
			$sql = 'SELECT *
				FROM ' . $this->portal_articles_table . "
				WHERE article_lang = '" . $this->db->sql_escape($lang) . "'
				ORDER BY article_time DESC";
			$result = $this->db->sql_query_limit($sql, constants::NUM_ARTICLES_ON_INDEX);

			$index_articles = array();
			while ($row = $this->db->sql_fetchrow($result))
			{
				$index_articles[] = array(
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
					'time'			=> $row['article_time'],
				);
			}
			$this->db->sql_freeresult($result);

			$this->driver->put('_vinabb_web_index_articles_' . $lang, $index_articles);
		}

		return $index_articles;
	}

	function clear_index_articles($lang)
	{
		$this->driver->destroy('_vinabb_web_index_articles_' . $lang);
	}
}
