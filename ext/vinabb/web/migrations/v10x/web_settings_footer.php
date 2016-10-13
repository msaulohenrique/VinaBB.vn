<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\migrations\v10x;

use phpbb\db\migration\migration;

class web_settings_footer extends migration
{
	public function update_data()
	{
		return array(
			// Footer
			array('config.add', array('vinabb_web_manager_name', '')),
			array('config.add', array('vinabb_web_manager_username', '')),
		);
	}
}
