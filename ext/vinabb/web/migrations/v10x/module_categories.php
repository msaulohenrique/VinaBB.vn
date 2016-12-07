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
	* List of required migrations
	*
	* @return array
	*/
	static public function depends_on()
	{
		return ['\phpbb\db\migration\data\v320\v320rc1'];
	}

	/**
	* Update data
	*
	* @return array
	*/
	public function update_data()
	{
		return [
			// Root category
			['module.add', ['acp', 0, 'ACP_CAT_VINABB']],
		];
	}
}
