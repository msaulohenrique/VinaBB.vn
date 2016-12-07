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
* Common ACP settings
*/
class web_settings extends migration
{
	/**
	* Update data
	*
	* @return array
	*/
	public function update_data()
	{
		return [
			['config.add', ['vinabb_web_lang_switch', '']],
			['config.add', ['vinabb_web_maintenance_mode', 0]],
			['config.add', ['vinabb_web_maintenance_tpl', 1]],
			['config.add', ['vinabb_web_maintenance_time', 0]],
			['config_text.add', ['vinabb_web_maintenance_text', '']],
			['config_text.add', ['vinabb_web_maintenance_text_vi', '']]
		];
	}
}
