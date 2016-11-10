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
* Database schema for menu manager
*/
class menus_schema extends migration
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
				$this->table_prefix . 'menus' => [
					'COLUMNS' => [
						'menu_id'					=> ['UINT', null, 'auto_increment'],
						'parent_id'					=> ['UINT', 0],
						'left_id'					=> ['UINT', 0],
						'right_id'					=> ['UINT', 0],
						'menu_parents'				=> ['MTEXT_UNI', ''],
						'menu_name'					=> ['VCHAR_UNI', ''],
						'menu_name_vi'				=> ['VCHAR_UNI', ''],
						'menu_type'					=> ['TINT:1', 0],
						'menu_icon'					=> ['VCHAR', ''],
						'menu_data'					=> ['VCHAR', ''],
						'menu_target'				=> ['BOOL', 0],
						'menu_enable_guest'			=> ['BOOL', 0],
						'menu_enable_bot'			=> ['BOOL', 0],
						'menu_enable_new_user'		=> ['BOOL', 0],
						'menu_enable_user'			=> ['BOOL', 0],
						'menu_enable_mod'			=> ['BOOL', 0],
						'menu_enable_global_mod'	=> ['BOOL', 0],
						'menu_enable_admin'			=> ['BOOL', 0],
						'menu_enable_founder'		=> ['BOOL', 0]
					],
					'PRIMARY_KEY' => 'menu_id'
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
				$this->table_prefix . 'menus'
			]
		];
	}
}
