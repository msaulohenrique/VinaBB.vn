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
* Database schema for headlines
*/
class headlines_schema extends migration
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
				$this->table_prefix . 'headlines' => [
					'COLUMNS' => [
						'headline_id'		=> ['UINT', null, 'auto_increment'],
						'headline_lang'		=> ['VCHAR:30', ''],
						'headline_name'		=> ['VCHAR_UNI', ''],
						'headline_desc'		=> ['VCHAR_UNI', ''],
						'headline_img'		=> ['VCHAR', ''],
						'headline_url'		=> ['VCHAR', ''],
						'headline_order'	=> ['USINT', 0]
					],
					'PRIMARY_KEY' => 'headline_id'
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
				$this->table_prefix . 'headlines'
			]
		];
	}
}
