<?php
/**
* This file is part of the VinaBB Styles Demo package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\migrations;

use phpbb\db\migration\migration;

class maintenance extends migration
{
	public function update_data()
	{
		return array(
			// Config
			array('config.add', array('vinabb_web_maintenance_mode', 0)),
			array('config.add', array('vinabb_web_maintenance_tpl', 1)),
			array('config.add', array('vinabb_web_maintenance_time', 0)),
			array('config.add', array('vinabb_web_maintenance_text', '')),

			// Modules
			array('module.add', array(
				'acp',
				'',
				'ACP_CAT_VINABB'
			)),

			array('module.add', array(
				'acp',
				'ACP_CAT_VINABB',
				'ACP_CAT_VINABB_SETTINGS'
			)),

			array('module.add', array(
				'acp',
				'ACP_CAT_VINABB_SETTINGS',
				array(
					'module_basename'	=> '\vinabb\web\acp\settings_module',
					'modes'				=> array('settings'),
				),
			)),
		);
	}
}
