<?php
/**
* This file is part of the VinaBB Styles Demo package.
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
			// Module categories
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
		);
	}
}
