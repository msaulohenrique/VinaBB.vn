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
	/** @var string */
	protected $bb_categories_table;

	/**
	* Constructor
	*
	* @param \phpbb\cache\driver\driver_interface $driver
	* @param \phpbb\config\config $config
	* @param \phpbb\db\driver\driver_interface $db
	* @param string $root_path
	* @param string $php_ext
	* @param string $bb_categories_table
	*/
	public function __construct(
		\phpbb\cache\driver\driver_interface $driver,
		\phpbb\config\config $config,
		\phpbb\db\driver\driver_interface $db,
		$root_path,
		$php_ext,
		$bb_categories_table
	)
	{
		$this->set_driver($driver);
		$this->config = $config;
		$this->db = $db;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;
		$this->bb_categories_table = $bb_categories_table;
	}

	function get_config_text_data()
	{
		if (($config_text_data = $this->driver->get('_config_text_data')) === false)
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

			$this->driver->put('_config_text_data', $config_text_data);
		}
	}

	function clear_config_text_data()
	{
		$this->driver->destroy('_config_text_data');
	}

	function get_lang_data()
	{
		if (($lang_data = $this->driver->get('_lang_data')) === false)
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

			$this->driver->put('_lang_data', $lang_data);
		}
	}

	function clear_lang_data()
	{
		$this->driver->destroy('_lang_data');
	}

	function get_forum_data()
	{
		if (($forum_data = $this->driver->get('_forum_data')) === false)
		{
			$sql = 'SELECT *
				FROM ' . FORUMS_TABLE;
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

			$this->driver->put('_forum_data', $forum_data);
		}

		return $forum_data;
	}

	function clear_forum_data()
	{
		$this->driver->destroy('_forum_data');
	}

	function get_bb_cat_data($bb_type)
	{
		if (($bb_cat_data = $this->driver->get('_bb_' . strtolower($bb_type) . '_cat_data')) === false)
		{
			$sql = 'SELECT *
				FROM ' . $this->bb_categories_table . '
				WHERE bb_type = ' . constants::BB_TYPE_ . strtoupper($bb_type);
			$result = $this->db->sql_query($sql);

			$bb_cat_data = array();
			while ($row = $this->db->sql_fetchrow($result))
			{
				$bb_cat_data[$row['cat_id']] = array(
					'name'		=> $row['cat_name'],
					'name_vi'	=> $row['cat_name_vi'],
					'varname'	=> $row['cat_varname'],
				);
			}
			$this->db->sql_freeresult($result);

			$this->driver->put('_bb_' . strtolower($bb_type) . '_cat_data', $bb_cat_data);
		}
	}

	function clear_bb_cat_data($bb_type)
	{
		$this->driver->destroy('_bb_' . strtolower($bb_type) . '_cat_data');
	}
}
