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
* ACP settings for the social links
*/
class web_settings_social extends migration
{
	/**
	* Update data
	*
	* @return array
	*/
	public function update_data()
	{
		return [
			['config.add', ['vinabb_web_facebook_url', '']],
			['config.add', ['vinabb_web_twitter_url', '']],
			['config.add', ['vinabb_web_google_plus_url', '']],
			['config.add', ['vinabb_web_github_url', '']]
		];
	}
}
