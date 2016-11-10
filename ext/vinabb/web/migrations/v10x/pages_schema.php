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
class pages_schema extends migration
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
				$this->table_prefix . 'pages' => [
					'COLUMNS' => [
						'page_id'					=> ['UINT', null, 'auto_increment'],
						'page_name'					=> ['VCHAR_UNI', ''],
						'page_name_vi'				=> ['VCHAR_UNI', ''],
						'page_varname'				=> ['VCHAR', ''],
						'page_desc'					=> ['TEXT_UNI', ''],
						'page_desc_vi'				=> ['TEXT_UNI', ''],
						'page_text'					=> ['MTEXT_UNI', ''],
						'page_text_uid'				=> ['VCHAR:8', ''],
						'page_text_bitfield'		=> ['VCHAR:255', ''],
						'page_text_options'			=> ['UINT:11', 0],
						'page_text_vi'				=> ['MTEXT_UNI', ''],
						'page_text_vi_uid'			=> ['VCHAR:8', ''],
						'page_text_vi_bitfield'		=> ['VCHAR:255', ''],
						'page_text_vi_options'		=> ['UINT:11', 0],
						'page_enable'				=> ['BOOL', 0],
						'page_enable_guest'			=> ['BOOL', 0],
						'page_enable_bot'			=> ['BOOL', 0],
						'page_enable_new_user'		=> ['BOOL', 0],
						'page_enable_user'			=> ['BOOL', 0],
						'page_enable_mod'			=> ['BOOL', 0],
						'page_enable_global_mod'	=> ['BOOL', 0],
						'page_enable_admin'			=> ['BOOL', 0],
						'page_enable_founder'		=> ['BOOL', 0]
					],
					'PRIMARY_KEY' => 'page_id'
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
				$this->table_prefix . 'pages'
			]
		];
	}
}
