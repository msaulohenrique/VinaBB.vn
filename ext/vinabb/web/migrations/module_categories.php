<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\migrations;

use phpbb\db\migration\migration;

class module_categories extends migration
{
	public function update_data()
	{
		return array(
			// Categories
			array('module.add', array(
				'acp',
				'',
				'ACP_CAT_VINABB'
			)),

			// Sub-categories
			array('module.add', array(
				'acp',
				'ACP_CAT_VINABB',
				'ACP_CAT_VINABB_SETTINGS'
			)),
			array('module.add', array(
				'acp',
				'ACP_CAT_VINABB',
				'ACP_CAT_VINABB_BB'
			)),
			array('module.add', array(
				'acp',
				'ACP_CAT_VINABB',
				'ACP_CAT_VINABB_CMS'
			)),
		);
	}
}
