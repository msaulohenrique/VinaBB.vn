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
* ACP settings for pre-defined forums
*/
class web_settings_forum_ids extends migration
{
	/**
	* Update data
	*
	* @return array
	*/
	public function update_data()
	{
		return [
			['config.add', ['vinabb_web_forum_id_vietnamese', 0]],
			['config.add', ['vinabb_web_forum_id_vietnamese_support', 0]],
			['config.add', ['vinabb_web_forum_id_vietnamese_ext', 0]],
			['config.add', ['vinabb_web_forum_id_vietnamese_style', 0]],
			['config.add', ['vinabb_web_forum_id_vietnamese_tutorial', 0]],
			['config.add', ['vinabb_web_forum_id_vietnamese_discussion', 0]],
			['config.add', ['vinabb_web_forum_id_english', 0]],
			['config.add', ['vinabb_web_forum_id_english_support', 0]],
			['config.add', ['vinabb_web_forum_id_english_tutorial', 0]],
			['config.add', ['vinabb_web_forum_id_english_discussion', 0]]
		];
	}
}
