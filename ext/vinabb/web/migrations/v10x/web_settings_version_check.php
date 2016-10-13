<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\migrations\v10x;

use phpbb\db\migration\migration;

class web_settings_version_check extends migration
{
	public function update_data()
	{
		return array(
			// Check new versions
			array('config.add', array('vinabb_web_check_gc', 0, 1)),
			array('config.add', array('vinabb_web_check_phpbb_url', '')),
			array('config.add', array('vinabb_web_check_phpbb_version', '')),
			array('config.add', array('vinabb_web_check_phpbb_legacy_version', '')),
			array('config.add', array('vinabb_web_check_php_url', '')),
			array('config.add', array('vinabb_web_check_php_version', '')),
			array('config.add', array('vinabb_web_check_php_legacy_version', '')),
		);
	}
}
