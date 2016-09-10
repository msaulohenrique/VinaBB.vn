<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\migrations;

use phpbb\db\migration\migration;
use vinabb\web\includes\constants;
use vinabb\web\controller\helper;

class seo_board extends migration
{
	public function update_schema()
	{
		return array(
			'add_columns'	=> array(
				$this->table_prefix . 'forums'	=> array(
					'forum_name_seo'	=> array('VCHAR', ''),
				),
				$this->table_prefix . 'topics'	=> array(
					'topic_title_seo'	=> array('VCHAR', ''),
				),
				$this->table_prefix . 'users'	=> array(
					'username_seo'		=> array('VCHAR', ''),
				),
			),
		);
	}

	public function update_data()
	{
		return array(
			array('custom', array(
				array(&$this, 'update_seo_columns'),
			)),
		);
	}

	public function revert_schema()
	{
		return array(
			'drop_columns'	=> array(
				$this->table_prefix . 'forums'	=> array(
					'forum_name_seo',
				),
				$this->table_prefix . 'topics'	=> array(
					'topic_title_seo',
				),
				$this->table_prefix . 'users'	=> array(
					'username_seo',
				),
			),
		);
	}

	public function update_seo_columns()
	{
		$helper = new helper();

		$tables_list = array(
			$this->table_prefix . 'forums'	=> array('id' => 'forum_id', 'column' => 'forum_name', 'seo_column' => 'forum_name_seo'),
			$this->table_prefix . 'topics'	=> array('id' => 'topic_id', 'column' => 'topic_title', 'seo_column' => 'topic_title_seo'),
			$this->table_prefix . 'users'	=> array('id' => 'user_id', 'column' => 'username', 'seo_column' => 'username_seo'),
		);

		$forum_seo_names = array();
		foreach ($tables_list as $table_name => $data)
		{
			list($column_id, $column, $seo_column) = array_values($data);

			$sql = "SELECT $column_id, $column
				FROM $table_name";
			$result = $this->db->sql_query($sql);

			while ($row = $this->db->sql_fetchrow($result))
			{
				$clean_name = $helper->clean_url($row[$column]);

				$sql = "UPDATE $table_name
					SET $seo_column = '" . $clean_name . "'
					WHERE $column_id = " . $row[$column_id];
				$this->sql_query($sql);

				if ($table_name == $this->table_prefix . 'forums')
				{
					$forum_seo_names[$row[$column_id]] = $clean_name;
				}
			}
			$this->db->sql_freeresult($result);
		}

		// If there have more than 2 same forum SEO names, add parent forum SEO name as prefix
		$duplicate_forum_seo_names = array();

		foreach (array_count_values($forum_seo_names) as $forum_seo_name => $count)
		{
			if ($count > 1)
			{
				$duplicate_forum_seo_names[] = $forum_seo_name;
			}
		}

		if (sizeof($duplicate_forum_seo_names))
		{
			$sql = 'SELECT forum_id, parent_id
				FROM ' . $this->table_prefix . 'forums
				WHERE parent_id <> 0
					AND ' . $this->db->sql_in_set('forum_name_seo', $duplicate_forum_seo_names);
			$result = $this->db->sql_query($sql);

			while ($row = $this->db->sql_fetchrow($result))
			{
				$new_clean_name = $forum_seo_names[$row['parent_id']] . constants::REWRITE_URL_FORUM_CAT . $forum_seo_names[$row['forum_id']];

				$sql = 'UPDATE ' . $this->table_prefix . "forums
					SET forum_name_seo = '" . $new_clean_name . "'
					WHERE forum_id = " . $row['forum_id'];
				$this->sql_query($sql);
			}
			$this->db->sql_freeresult($result);
		}
	}
}
