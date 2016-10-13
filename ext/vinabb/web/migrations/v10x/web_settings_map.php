<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\migrations\v10x;

use phpbb\db\migration\migration;

class web_settings_map extends migration
{
	public function update_data()
	{
		return array(
			// Map
			array('config.add', array('vinabb_web_map_api', '')),
			array('config.add', array('vinabb_web_map_lat', '0')),
			array('config.add', array('vinabb_web_map_lng', '0')),
			array('config.add', array('vinabb_web_map_address', '')),
			array('config.add', array('vinabb_web_map_address_vi', '')),
			array('config.add', array('vinabb_web_map_phone', '')),
			array('config.add', array('vinabb_web_map_phone_name', '')),
		);
	}
}
