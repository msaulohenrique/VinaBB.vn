<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\migrations\v10x;

use phpbb\db\migration\migration;

class portal_modules extends migration
{
	static public function depends_on()
	{
		return array('\vinabb\web\migrations\v10x\portal_categories');
	}

	public function update_data()
	{
		return array(
			array('module.add', array(
				'acp',
				'ACP_CAT_PORTAL',
				array(
					'module_basename' => '\vinabb\web\acp\portal_categories_module',
					'modes' => array('cats'),
				),
			)),
			array('module.add', array(
				'acp',
				'ACP_CAT_PORTAL',
				array(
					'module_basename' => '\vinabb\web\acp\portal_articles_module',
					'modes' => array('articles'),
				),
			)),
		);
	}
}
