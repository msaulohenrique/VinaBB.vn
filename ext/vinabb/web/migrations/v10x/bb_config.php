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
* ACP settings for phpBB Resource
*/
class bb_config extends migration
{
	/**
	* Update data
	*
	* @return array
	*/
	public function update_data()
	{
		return [
			// Counter
			['config.add', ['vinabb_web_total_bb_exts', 0, true]],
			['config.add', ['vinabb_web_total_bb_styles', 0, true]],
			['config.add', ['vinabb_web_total_bb_acp_styles', 0, true]],
			['config.add', ['vinabb_web_total_bb_langs', 0, true]],
			['config.add', ['vinabb_web_total_bb_tools', 0, true]],
			['config.add', ['vinabb_web_total_bb_authors', 0, true]],
			['config.add', ['vinabb_web_total_bb_subscribers', 0, true]],
			['config.add', ['vinabb_web_bb_exts_filesize', 0, true]],
			['config.add', ['vinabb_web_bb_styles_filesize', 0, true]],
			['config.add', ['vinabb_web_bb_acp_styles_filesize', 0, true]],
			['config.add', ['vinabb_web_bb_langs_filesize', 0, true]],
			['config.add', ['vinabb_web_bb_tools_filesize', 0, true]]
		];
	}
}
