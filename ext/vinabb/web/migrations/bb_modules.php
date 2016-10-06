<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\migrations;

use phpbb\db\migration\migration;

class bb_modules extends migration
{
	static public function depends_on()
	{
		return array('\vinabb\web\migrations\bb_categories');
	}

	public function update_data()
	{
		return array(
			array('module.add', array(
				'acp',
				'ACP_CAT_BB',
				array(
					'module_basename' => '\vinabb\web\acp\bb_categories_module',
					'modes' => array('ext', 'style', 'acp_style', 'lang', 'tool'),
				),
			)),
			array('module.add', array(
				'acp',
				'ACP_CAT_BB',
				array(
					'module_basename' => '\vinabb\web\acp\bb_items_module',
					'modes' => array('ext', 'style', 'acp_style', 'lang', 'tool'),
				),
			)),
		);
	}
}
