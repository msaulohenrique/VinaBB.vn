<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\migrations;

use phpbb\db\migration\migration;

class web_settings_version extends migration
{
	public function update_data()
	{
		return array(
			// Latest versions
			array('config.add', array('vinabb_web_latest_version_phpbb', '')),
			array('config.add', array('vinabb_web_latest_version_phpbb_download', '')),
			array('config.add', array('vinabb_web_latest_version_phpbb_github', '')),
			array('config.add', array('vinabb_web_latest_version_phpbb_legacy', '')),
			array('config.add', array('vinabb_web_latest_version_phpbb_legacy_download', '')),
			array('config.add', array('vinabb_web_latest_version_phpbb_legacy_github', '')),
			array('config.add', array('vinabb_web_latest_version_ivn', '')),
			array('config.add', array('vinabb_web_latest_version_ivn_download', '')),
			array('config.add', array('vinabb_web_latest_version_ivn_github', '')),
			array('config.add', array('vinabb_web_latest_version_ivn_legacy', '')),
			array('config.add', array('vinabb_web_latest_version_ivn_legacy_download', '')),
			array('config.add', array('vinabb_web_latest_version_ivn_legacy_github', '')),
			array('config.add', array('vinabb_web_latest_version_ivn_plus', '')),
			array('config.add', array('vinabb_web_latest_version_ivn_plus_download', '')),
			array('config.add', array('vinabb_web_latest_version_ivn_plus_github', '')),
			array('config.add', array('vinabb_web_latest_version_php', '')),
			array('config.add', array('vinabb_web_latest_version_php_url', '')),
			array('config.add', array('vinabb_web_latest_version_php_info', '')),
			array('config.add', array('vinabb_web_latest_version_php_legacy', '')),
			array('config.add', array('vinabb_web_latest_version_php_legacy_url', '')),
			array('config.add', array('vinabb_web_latest_version_php_legacy_info', '')),
		);
	}
}
