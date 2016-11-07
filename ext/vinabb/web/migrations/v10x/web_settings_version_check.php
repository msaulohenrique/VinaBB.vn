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
* ACP settings for checking version blocks
*/
class web_settings_version_check extends migration
{
	/**
	* Update data
	*
	* @return array
	*/
	public function update_data()
	{
		return [
			['config.add', ['vinabb_web_check_gc', 0, 1]],
			['config.add', ['vinabb_web_check_phpbb_url', '']],
			['config.add', ['vinabb_web_check_phpbb_download_url', '']],
			['config.add', ['vinabb_web_check_phpbb_download_dev_url', '']],
			['config.add', ['vinabb_web_check_phpbb_github_url', '']],
			['config.add', ['vinabb_web_check_phpbb_branch', '']],
			['config.add', ['vinabb_web_check_phpbb_version', '']],
			['config.add', ['vinabb_web_check_phpbb_legacy_branch', '']],
			['config.add', ['vinabb_web_check_phpbb_legacy_version', '']],
			['config.add', ['vinabb_web_check_phpbb_dev_branch', '']],
			['config.add', ['vinabb_web_check_phpbb_dev_version', '']],
			['config.add', ['vinabb_web_check_php_url', '']],
			['config.add', ['vinabb_web_check_php_branch', '']],
			['config.add', ['vinabb_web_check_php_version', '']],
			['config.add', ['vinabb_web_check_php_version_url', '']],
			['config.add', ['vinabb_web_check_php_legacy_branch', '']],
			['config.add', ['vinabb_web_check_php_legacy_version', '']],
			['config.add', ['vinabb_web_check_php_legacy_version_url', '']],
			['config.add', ['vinabb_web_check_vinabb_version', '']],
			['config.add', ['vinabb_web_check_ivn_version', '']],
			['config.add', ['vinabb_web_check_ivn_legacy_version', '']],
			['config.add', ['vinabb_web_check_ivn_dev_version', '']],
			['config.add', ['vinabb_web_check_ivnplus_version', '']]
		];
	}
}
