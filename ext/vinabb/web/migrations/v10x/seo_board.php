<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\migrations\v10x;

use phpbb\db\migration\migration;
use vinabb\web\includes\constants;

/**
* Add SEO columns for forum/topic/user
*/
class seo_board extends migration
{
	/**
	* Update schema
	*
	* @return array
	*/
	public function update_schema()
	{
		return [
			'add_columns'	=> [
				$this->table_prefix . 'forums'	=> [
					'forum_name_seo'	=> ['VCHAR', '']
				],
				$this->table_prefix . 'topics'	=> [
					'topic_title_seo'	=> ['VCHAR', '']
				],
				$this->table_prefix . 'users'	=> [
					'username_seo'		=> ['VCHAR', '']
				]
			]
		];
	}

	/**
	* Update data
	*
	* @return array
	*/
	public function update_data()
	{
		return [['custom', [[&$this, 'update_seo_columns']]]];
	}

	/**
	* Revert schema
	*
	* @return array
	*/
	public function revert_schema()
	{
		return [
			'drop_columns'	=> [
				$this->table_prefix . 'forums'	=> ['forum_name_seo'],
				$this->table_prefix . 'topics'	=> ['topic_title_seo'],
				$this->table_prefix . 'users'	=> ['username_seo']
			]
		];
	}

	/**
	* Update SEO column value for current entities
	*/
	public function update_seo_columns()
	{
		$tables_list = [
			$this->table_prefix . 'forums'	=> [
				'id'			=> 'forum_id',
				'column'		=> 'forum_name',
				'seo_column'	=> 'forum_name_seo'
			],
			$this->table_prefix . 'topics'	=> [
				'id'			=> 'topic_id',
				'column'		=> 'topic_title',
				'seo_column'	=> 'topic_title_seo'
			],
			$this->table_prefix . 'users'	=> [
				'id'			=> 'user_id',
				'column'		=> 'username',
				'seo_column'	=> 'username_seo'
			]
		];

		$forum_seo_names = [];
		foreach ($tables_list as $table_name => $data)
		{
			list($column_id, $column, $seo_column) = array_values($data);

			$sql = "SELECT $column_id, $column
				FROM $table_name";
			$result = $this->db->sql_query($sql);

			while ($row = $this->db->sql_fetchrow($result))
			{
				$clean_name = $this->clean_url($row[$column]);

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
		$duplicate_forum_seo_names = [];

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

	/**
	* Create clean URLs from titles. It works with many languages
	*
	* @author hello@weblap.ro
	* @param $text
	*
	* @return string
	*/
	protected function clean_url($text)
	{
		return strtolower(
			preg_replace(
				['/[^a-zA-Z0-9 -]/', '/[ -]+/', '/^-|-$/'],
				['', '-', ''],
				$this->remove_accents($text)
			)
		);
	}

	/**
	* Remove all accents
	*
	* @param string $text Input text
	* @return string Result text
	*/
	protected function remove_accents($text = '')
	{
		$find = ['á', 'Á', 'à', 'À', 'ả', 'Ả', 'ã', 'Ã', 'ạ', 'Ạ', 'ă', 'Ă', 'ắ', 'Ắ', 'ằ', 'Ằ', 'ẳ', 'Ẳ', 'ẵ', 'Ẵ', 'ặ', 'Ặ', 'â', 'Â', 'ấ', 'Ấ', 'ầ', 'Ầ', 'ẩ', 'Ẩ', 'ẫ', 'Ẫ', 'ậ', 'Ậ', 'đ', 'Đ', 'é', 'É', 'è', 'È', 'ẻ', 'Ẻ', 'ẽ', 'Ẽ', 'ẹ', 'Ẹ', 'ê', 'Ê', 'ế', 'Ế', 'ề', 'Ề', 'ể', 'Ể', 'ễ', 'Ễ', 'ệ', 'Ệ', 'í', 'Í', 'ì', 'Ì', 'ỉ', 'Ỉ', 'ĩ', 'Ĩ', 'ị', 'Ị', 'ó', 'Ó', 'ò', 'Ò', 'ỏ', 'Ỏ', 'õ', 'Õ', 'ọ', 'Ọ', 'ô', 'Ô', 'ố', 'Ố', 'ồ', 'Ồ', 'ổ', 'Ổ', 'ỗ', 'Ỗ', 'ộ', 'Ộ', 'ơ', 'Ơ', 'ớ', 'Ớ', 'ờ', 'Ờ', 'ở', 'Ở', 'ỡ', 'Ỡ', 'ợ', 'Ợ', 'ú', 'Ú', 'ù', 'Ù', 'ủ', 'Ủ', 'ũ', 'Ũ', 'ụ', 'Ụ', 'ư', 'Ư', 'ứ', 'Ứ', 'ừ', 'Ừ', 'ử', 'Ử', 'ữ', 'Ữ', 'ự', 'Ự', 'ý', 'Ý', 'ỳ', 'Ỳ', 'ỷ', 'Ỷ', 'ỹ', 'Ỹ', 'ỵ', 'Ỵ'];
		$replace = ['a', 'A', 'a', 'A', 'a', 'A', 'a', 'A', 'a', 'A', 'a', 'A', 'a', 'A', 'a', 'A', 'a', 'A', 'a', 'A', 'a', 'A', 'a', 'A', 'a', 'A', 'a', 'A', 'a', 'A', 'a', 'A', 'a', 'A', 'd', 'D', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'o', 'O', 'o', 'O', 'o', 'O', 'o', 'O', 'o', 'O', 'o', 'O', 'o', 'O', 'o', 'O', 'o', 'O', 'o', 'O', 'o', 'O', 'o', 'O', 'o', 'O', 'o', 'O', 'o', 'O', 'o', 'O', 'o', 'O', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'y', 'Y', 'y', 'Y', 'y', 'Y', 'y', 'Y', 'y', 'Y'];

		return str_replace($find, $replace, $text);
	}
}
