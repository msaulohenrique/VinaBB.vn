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
* Add the ACP tab "VinaBB" and the first module category "VinaBB.vn Settings"
*/
class module_categories extends migration
{
	/**
	* Update data
	*
	* @return array
	*/
	public function update_data()
	{
		return [
			// Root category
			['module.add', ['acp', '', 'ACP_CAT_VINABB']],

			// Sub-category
			['module.add', ['acp', 'ACP_CAT_VINABB', 'ACP_CAT_VINABB_SETTINGS']]
		];
	}
}
