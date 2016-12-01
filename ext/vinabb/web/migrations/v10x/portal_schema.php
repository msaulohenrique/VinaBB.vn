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
				$this->table_prefix . 'portal_categories'	=> $this->get_schema_portal_categories(),
				$this->table_prefix . 'portal_articles'		=> $this->get_schema_portal_articles(),
				$this->table_prefix . 'portal_comments'		=> $this->get_schema_portal_comments(),
				$this->table_prefix . 'portal_rates'		=> $this->get_schema_portal_rates()
			]
		];
	}

	/**
	* Get schema for the table: _portal_categories
	*
	* @return array
	*/
	protected function get_schema_portal_categories()
	{
		return [
			'COLUMNS' => [
				'cat_id'		=> ['UINT', null, 'auto_increment'],
				'parent_id'		=> ['UINT', 0],
				'left_id'		=> ['UINT', 0],
				'right_id'		=> ['UINT', 0],
				'cat_parents'	=> ['MTEXT_UNI', ''],
				'cat_name'		=> ['VCHAR_UNI', ''],
				'cat_name_vi'	=> ['VCHAR_UNI', ''],
				'cat_varname'	=> ['VCHAR', ''],
				'cat_icon'		=> ['VCHAR', '']
			],
			'PRIMARY_KEY' => 'cat_id'
		];
	}

	/**
	* Get schema for the table: _portal_articles
	*
	* @return array
	*/
	protected function get_schema_portal_articles()
	{
		return [
			'COLUMNS' => [
				'article_id'			=> ['UINT', null, 'auto_increment'],
				'cat_id'				=> ['UINT', 0],
				'user_id'				=> ['ULINT', 0],
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
				'c_id'	=> ['INDEX', 'cat_id'],
				'u_id'	=> ['INDEX', 'user_id']
			]
		];
	}

	/**
	* Get schema for the table: _portal_comments
	*
	* @return array
	*/
	protected function get_schema_portal_comments()
	{
		return [
			'COLUMNS' => [
				'comment_id'			=> ['UINT', null, 'auto_increment'],
				'user_id'				=> ['ULINT', 0],
				'article_id'			=> ['UINT', 0],
				'comment_text'			=> ['TEXT_UNI', ''],
				'comment_text_uid'		=> ['VCHAR:8', ''],
				'comment_text_bitfield'	=> ['VCHAR:255', ''],
				'comment_text_options'	=> ['UINT:11', 0],
				'comment_pending'		=> ['BOOL', 0],
				'comment_time'			=> ['TIMESTAMP', 0]
			],
			'PRIMARY_KEY' => 'comment_id',
			'KEYS' => [
				'u_id'	=> ['INDEX', 'user_id'],
				'a_id'	=> ['INDEX', 'article_id']
			]
		];
	}

	/**
	* Get schema for the table: _portal_rates
	*
	* @return array
	*/
	protected function get_schema_portal_rates()
	{
		return [
			'COLUMNS' => [
				'user_id'		=> ['ULINT', 0],
				'article_id'	=> ['UINT', 0],
				'rate_value'	=> ['USINT', 0]
			],
			'KEYS' => [
				'u_id'	=> ['INDEX', 'user_id'],
				'a_id'	=> ['INDEX', 'article_id']
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
				$this->table_prefix . 'portal_comments',
				$this->table_prefix . 'portal_rates'
			]
		];
	}
}
