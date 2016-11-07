<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\migrations\v10x;

use phpbb\db\migration\migration;

/**
* Database schema for portal
*/
class portal_schema extends migration
{
	/**
	* Update schema
	*
	* @return array
	*/
	public function update_schema()
	{
		return [
			'add_tables' => [
				$this->table_prefix . 'portal_categories' => [
					'COLUMNS' => [
						'cat_id'		=> ['UINT', null, 'auto_increment'],
						'parent_id'		=> ['UINT', 0],
						'left_id'		=> ['UINT', 0],
						'right_id'		=> ['UINT', 0],
						'cat_name'		=> ['VCHAR_UNI', ''],
						'cat_name_vi'	=> ['VCHAR_UNI', ''],
						'cat_varname'	=> ['VCHAR', ''],
						'cat_icon'		=> ['VCHAR', '']
					],
					'PRIMARY_KEY' => 'cat_id'
				],
				$this->table_prefix . 'portal_articles' => [
					'COLUMNS' => [
						'article_id'			=> ['UINT', null, 'auto_increment'],
						'cat_id'				=> ['UINT', 0],
						'article_name'			=> ['VCHAR_UNI', ''],
						'article_name_seo'		=> ['VCHAR', ''],
						'article_lang'			=> ['VCHAR:30', ''],
						'article_img'			=> ['VCHAR', ''],
						'article_desc'			=> ['TEXT_UNI', ''],
						'article_text'			=> ['MTEXT_UNI', ''],
						'article_text_uid'		=> ['VCHAR:8', ''],
						'article_text_bitfield'	=> ['VCHAR:255', ''],
						'article_text_options'	=> ['UINT:11', 0],
						'article_enable'		=> ['BOOL', 1],
						'article_views'			=> ['UINT', 0],
						'article_time'			=> ['TIMESTAMP', 0]
					],
					'PRIMARY_KEY' => 'article_id',
					'KEYS' => [
						'c_id'	=> ['INDEX', 'cat_id']
					],
				],
				$this->table_prefix . 'portal_comments' => [
					'COLUMNS' => [
						'comment_id'			=> ['UINT', null, 'auto_increment'],
						'user_id'				=> ['ULINT', 0],
						'article_id'			=> ['UINT', 0],
						'comment_rate'			=> ['TINT:1', 0],
						'comment_text'			=> ['TEXT_UNI', ''],
						'comment_text_uid'		=> ['VCHAR:8', ''],
						'comment_text_bitfield'	=> ['VCHAR:255', ''],
						'comment_text_options'	=> ['UINT:11', 0],
						'comment_mode'			=> ['TINT:1', 0],
						'comment_time'			=> ['TIMESTAMP', 0]
					],
					'PRIMARY_KEY' => 'comment_id',
					'KEYS' => [
						'u_id'	=> ['INDEX', 'user_id'],
						'a_id'	=> ['INDEX', 'article_id']
					]
				]
			]
		];
	}

	/**
	* Revert schema
	*
	* @return array
	*/
	public function revert_schema()
	{
		return [
			'drop_tables' => [
				$this->table_prefix . 'portal_categories',
				$this->table_prefix . 'portal_articles',
				$this->table_prefix . 'portal_comments'
			]
		];
	}
}
