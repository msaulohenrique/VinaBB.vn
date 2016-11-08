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
	* List of required migrations
	*
	* @return array
	*/
	static public function depends_on()
	{
		return ['\vinabb\web\migrations\v10x\module_categories'];
	}

	/**
	* Update data
	*
	* @return array
	*/
	public function update_data()
	{
		return [
			// Config
			['config.add', ['vinabb_web_lang_enable', 0]],
			['config.add', ['vinabb_web_lang_switch', '']],
			['config.add', ['vinabb_web_maintenance_mode', 0]],
			['config.add', ['vinabb_web_maintenance_tpl', 1]],
			['config.add', ['vinabb_web_maintenance_time', 0]],
			['config_text.add', ['vinabb_web_maintenance_text', '']],
			['config_text.add', ['vinabb_web_maintenance_text_vi', '']],

			// Main setting module
			[
				'module.add',
				[
					'acp',
					'ACP_CAT_VINABB_SETTINGS',
					[
						'module_basename'	=> '\vinabb\web\acp\settings_module',
						'modes'				=> ['main']
					]
				]
			]
		];
	}
}
