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
* ACP settings for the block "Manager"
*/
class web_settings_manager extends migration
{
	/**
	* Update data
	*
	* @return array
	*/
	public function update_data()
	{
		return [
			['config.add', ['vinabb_web_manager_name', '']],
			['config.add', ['vinabb_web_manager_name_vi', '']],
			['config.add', ['vinabb_web_manager_username', '']],
			['config.add', ['vinabb_web_manager_user_id', 0]]
		];
	}
}
