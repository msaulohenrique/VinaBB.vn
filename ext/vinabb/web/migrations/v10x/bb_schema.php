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
* Database schema for phpBB Resource
*/
class bb_schema extends migration
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
				$this->table_prefix . 'bb_categories' => [
					'COLUMNS' => [
						'cat_id'		=> ['UINT', null, 'auto_increment'],
						'bb_type'		=> ['TINT:1', 0],
						'cat_name'		=> ['VCHAR_UNI', ''],
						'cat_name_vi'	=> ['VCHAR_UNI', ''],
						'cat_varname'	=> ['VCHAR', ''],
						'cat_desc'		=> ['VCHAR_UNI', ''],
						'cat_desc_vi'	=> ['VCHAR_UNI', ''],
						'cat_icon'		=> ['VCHAR', ''],
						'cat_order'		=> ['USINT', 0]
					],
					'PRIMARY_KEY' => 'cat_id',
					'KEYS' => [
						'bb'	=> ['INDEX', 'bb_type']
					]
				],
				$this->table_prefix . 'bb_items' => [
					'COLUMNS' => [
						'item_id'					=> ['UINT', null, 'auto_increment'],
						'bb_type'					=> ['TINT:1', 0],
						'cat_id'					=> ['UINT', 0],
						'author_id'					=> ['UINT', 0],
						'item_name'					=> ['VCHAR_UNI', '', 'true_sort'],
						'item_varname'				=> ['VCHAR', ''],
						'item_desc'					=> ['TEXT_UNI', ''],
						'item_desc_uid'				=> ['VCHAR:8', ''],
						'item_desc_bitfield'		=> ['VCHAR:255', ''],
						'item_desc_options'			=> ['UINT:11', 0],
						'item_desc_vi'				=> ['TEXT_UNI', ''],
						'item_desc_vi_uid'			=> ['VCHAR:8', ''],
						'item_desc_vi_bitfield'		=> ['VCHAR:255', ''],
						'item_desc_vi_options'		=> ['UINT:11', 0],
						'item_ext_style'			=> ['BOOL', 0],
						'item_ext_acp_style'		=> ['BOOL', 0],
						'item_ext_lang'				=> ['BOOL', 0],
						'item_ext_db_schema'		=> ['BOOL', 0],
						'item_ext_db_data'			=> ['BOOL', 0],
						'item_style_presets'		=> ['UINT', 0],
						'item_style_presets_aio'	=> ['BOOL', 0],
						'item_style_source'			=> ['BOOL', 0],
						'item_style_responsive'		=> ['BOOL', 0],
						'item_style_bootstrap'		=> ['BOOL', 0],
						'item_lang_iso'				=> ['VCHAR:30', ''],
						'item_tool_os'				=> ['TINT:2', 0],
						'item_price'				=> ['BINT', 0],
						'item_url'					=> ['VCHAR', ''],
						'item_github'				=> ['VCHAR', ''],
						'item_added'				=> ['TIMESTAMP', 0],
						'item_updated'				=> ['TIMESTAMP', 0]
					],
					'PRIMARY_KEY' => 'item_id',
					'KEYS' => [
						'bb'	=> ['INDEX', 'bb_type'],
						'c_id'	=> ['INDEX', 'cat_id'],
						'a_id'	=> ['INDEX', 'author_id']
					]
				],
				$this->table_prefix . 'bb_item_versions' => [
					'COLUMNS' => [
						'item_id'	=> ['UINT', 0],
					],
					'KEYS' => [
						'i_id'	=> ['INDEX', 'item_id'],
					]
				],
				$this->table_prefix . 'bb_authors' => [
					'COLUMNS' => [
						'author_id'				=> ['UINT', null, 'auto_increment'],
						'user_id'				=> ['ULINT', 0],
						'author_name'			=> ['VCHAR_UNI', ''],
						'author_firstname'		=> ['VCHAR_UNI', ''],
						'author_lastname'		=> ['VCHAR_UNI', ''],
						'author_is_group'		=> ['BOOL', 0],
						'author_group'			=> ['UINT', 0],
						'author_www'			=> ['VCHAR_UNI', 0],
						'author_email'			=> ['VCHAR_UNI', 0],
						'author_phpbb'			=> ['ULINT', 0],
						'author_github'			=> ['VCHAR', ''],
						'author_facebook'		=> ['VCHAR', ''],
						'author_twitter'		=> ['VCHAR', ''],
						'author_google'			=> ['VCHAR', ''],
						'author_google_plus'	=> ['VCHAR', ''],
						'author_skype'			=> ['VCHAR', '']
					],
					'PRIMARY_KEY' => 'author_id',
					'KEYS' => [
						'u_id'	=> ['INDEX', 'user_id']
					]
				],
				$this->table_prefix . 'bb_rates' => [
					'COLUMNS' => [
						'user_id'		=> ['ULINT', 0],
						'item_id'		=> ['UINT', 0],
						'author_id'		=> ['UINT', 0],
						'rate_value'	=> ['TINT:1', 0]
					],
					'KEYS' => [
						'u_id'	=> ['INDEX', 'user_id'],
						'i_id'	=> ['INDEX', 'item_id'],
						'a_id'	=> ['INDEX', 'author_id']
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
				$this->table_prefix . 'bb_categories',
				$this->table_prefix . 'bb_items',
				$this->table_prefix . 'bb_item_versions',
				$this->table_prefix . 'bb_authors',
				$this->table_prefix . 'bb_rates'
			]
		];
	}
}
