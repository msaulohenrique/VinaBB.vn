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
* ACP settings for the block "Map"
*/
class web_settings_map extends migration
{
	/**
	* Update data
	*
	* @return array
	*/
	public function update_data()
	{
		return [
			['config.add', ['vinabb_web_map_api', '']],
			['config.add', ['vinabb_web_map_lat', '0']],
			['config.add', ['vinabb_web_map_lng', '0']],
			['config.add', ['vinabb_web_map_address', '']],
			['config.add', ['vinabb_web_map_address_vi', '']],
			['config.add', ['vinabb_web_map_phone', '']],
			['config.add', ['vinabb_web_map_phone_name', '']]
		];
	}
}
