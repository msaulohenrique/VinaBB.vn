<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\migrations\v10x;

use phpbb\db\migration\migration;

class bb_categories extends migration
{
	static public function depends_on()
	{
		return array('\vinabb\web\migrations\v10x\module_categories');
	}

	public function update_data()
	{
		return array(
			array('module.add', array(
				'acp',
				'ACP_CAT_VINABB',
				'ACP_CAT_BB'
			)),
		);
	}
}
