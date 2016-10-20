<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\migrations\v10x;

use phpbb\db\migration\migration;

class bb_schema extends migration
{
	public function update_schema()
	{
		return array(
			'add_tables' => array(
				$this->table_prefix . 'bb_categories' => array(
					'COLUMNS' => array(
						'cat_id'		=> array('UINT', null, 'auto_increment'),
						'bb_type'		=> array('TINT:1', 0),
						'cat_name'		=> array('VCHAR_UNI', ''),
						'cat_name_vi'	=> array('VCHAR_UNI', ''),
						'cat_varname'	=> array('VCHAR', ''),
						'cat_icon'		=> array('VCHAR', ''),
						'cat_order'		=> array('USINT', 0),
					),
					'PRIMARY_KEY' => 'cat_id',
					'KEYS' => array(
						'bb'	=> array('INDEX', 'bb_type'),
					),
				),
				$this->table_prefix . 'bb_items' => array(
					'COLUMNS' => array(
						'item_id'					=> array('UINT', null, 'auto_increment'),
						'cat_id'					=> array('UINT', 0),
						'author_id'					=> array('UINT', 0),
						'bb_type'					=> array('TINT:1', 0),
						'item_name'					=> array('VCHAR_UNI', '', 'true_sort'),
						'item_name_vi'				=> array('VCHAR_UNI', '', 'true_sort'),
						'item_varname'				=> array('VCHAR', ''),
						'item_version'				=> array('VCHAR', ''),
						'item_phpbb_version'		=> array('VCHAR', ''),
						'item_desc'					=> array('MTEXT_UNI', ''),
						'item_desc_vi'				=> array('MTEXT_UNI', ''),
						'item_ext_style'			=> array('BOOL', 0),
						'item_ext_acp_style'		=> array('BOOL', 0),
						'item_ext_lang'				=> array('BOOL', 0),
						'item_ext_db_schema'		=> array('BOOL', 0),
						'item_ext_db_data'			=> array('BOOL', 0),
						'item_style_presets'		=> array('UINT', 0),
						'item_style_presets_aio'	=> array('BOOL', 0),
						'item_style_source'			=> array('BOOL', 0),
						'item_style_responsive'		=> array('BOOL', 0),
						'item_style_bootstrap'		=> array('BOOL', 0),
						'item_tool_os'				=> array('TINT:1', 0),
						'item_price'				=> array('BINT', 0),
						'item_url'					=> array('VCHAR', ''),
						'item_github'				=> array('VCHAR', ''),
						'item_added'				=> array('TIMESTAMP', 0),
						'item_updated'				=> array('TIMESTAMP', 0),
					),
					'PRIMARY_KEY' => 'item_id',
					'KEYS' => array(
						'bb'	=> array('INDEX', 'bb_type'),
						'c_id'	=> array('INDEX', 'cat_id'),
						'a_id'	=> array('INDEX', 'author_id'),
					),
				),
				$this->table_prefix . 'bb_authors' => array(
					'COLUMNS' => array(
						'author_id'				=> array('UINT', null, 'auto_increment'),
						'user_id'				=> array('ULINT', 0),
						'author_name'			=> array('VCHAR_UNI', ''),
						'author_firstname'		=> array('VCHAR_UNI', ''),
						'author_lastname'		=> array('VCHAR_UNI', ''),
						'author_is_group'		=> array('BOOL', 0),
						'author_group'			=> array('UINT', 0),
						'author_www'			=> array('VCHAR_UNI', 0),
						'author_email'			=> array('VCHAR_UNI', 0),
						'author_phpbb'			=> array('ULINT', 0),
						'author_github'			=> array('VCHAR', ''),
						'author_facebook'		=> array('VCHAR', ''),
						'author_twitter'		=> array('VCHAR', ''),
						'author_google'			=> array('VCHAR', ''),
						'author_google_plus'	=> array('VCHAR', ''),
						'author_skype'			=> array('VCHAR', ''),
					),
					'PRIMARY_KEY' => 'author_id',
					'KEYS' => array(
						'u_id'	=> array('INDEX', 'user_id'),
					),
				),
			),
		);
	}

	public function revert_schema()
	{
		return array(
			'drop_tables' => array(
				$this->table_prefix . 'bb_categories',
				$this->table_prefix . 'bb_items',
				$this->table_prefix . 'bb_authors',
			),
		);
	}
}
