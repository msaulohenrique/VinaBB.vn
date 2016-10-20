<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\migrations\v10x;

use phpbb\db\migration\migration;

class portal_config extends migration
{
	public function update_data()
	{
		return array(
			// Counter
			array('config.add', array('vinabb_web_total_articles', 0, true)),
		);
	}
}
