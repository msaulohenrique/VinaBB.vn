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
* Add ACP modules for phpBB Resource
*/
class bb_modules extends migration
{
	/**
	* List of required migrations
	*
	* @return array
	*/
	static public function depends_on()
	{
		return ['\vinabb\web\migrations\v10x\bb_categories'];
	}

	/**
	* Update data
	*
	* @return array
	*/
	public function update_data()
	{
		return [
			[
				'module.add',
				[
					'acp',
					'ACP_CAT_BB',
					[
						'module_basename'	=> '\vinabb\web\acp\bb_categories_module',
						'modes' 			=> ['ext', 'style', 'acp_style', 'lang', 'tool']
					],
				]
			],
			[
				'module.add',
				[
					'acp',
					'ACP_CAT_BB',
					[
						'module_basename'	=> '\vinabb\web\acp\bb_items_module',
						'modes'				=> ['ext', 'style', 'acp_style', 'lang', 'tool']
					]
				]
			]
		];
	}
}
