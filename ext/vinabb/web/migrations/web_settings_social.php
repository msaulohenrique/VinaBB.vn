<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\migrations;

use phpbb\db\migration\migration;

class web_settings_social extends migration
{
	public function update_data()
	{
		return array(
			// Config
			array('config.add', array('vinabb_web_facebook_url', '')),
			array('config.add', array('vinabb_web_twitter_url', '')),
			array('config.add', array('vinabb_web_google_plus_url', '')),
			array('config.add', array('vinabb_web_github_url', '')),
		);
	}
}
