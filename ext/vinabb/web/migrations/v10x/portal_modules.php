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
* Add ACP modules for portal
*/
class portal_modules extends migration
{
	/**
	* List of required migrations
	*
	* @return array
	*/
	static public function depends_on()
	{
		return ['\vinabb\web\migrations\v10x\portal_categories'];
	}

	/**
	* Update data
	*
	* @return array
	*/
	public function update_data()
	{
		return [
			['module.add', ['acp', 'ACP_CAT_PORTAL', [
				'module_basename'	=> '\vinabb\web\acp\portal_categories_module',
				'modes'				=> ['main']
			]]],
			['module.add', ['acp', 'ACP_CAT_PORTAL', [
				'module_basename'	=> '\vinabb\web\acp\portal_articles_module',
				'modes'				=> ['main']
			]]],
			['module.add', ['acp', 'ACP_CAT_PORTAL', [
				'module_basename'	=> '\vinabb\web\acp\portal_comments_module',
				'modes'				=> ['main']
			]]]
		];
	}
}
