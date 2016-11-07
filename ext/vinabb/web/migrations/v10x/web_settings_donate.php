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
* ACP settings for the block "Donate"
*/
class web_settings_donate extends migration
{
	/**
	* Update data
	*
	* @return array
	*/
	public function update_data()
	{
		return [
			['config.add', ['vinabb_web_donate_year', 0]],
			['config.add', ['vinabb_web_donate_year_value', 0]],
			['config.add', ['vinabb_web_donate_fund', 0]],
			['config.add', ['vinabb_web_donate_currency', '']],
			['config.add', ['vinabb_web_donate_owner', '']],
			['config.add', ['vinabb_web_donate_owner_vi', '']],
			['config.add', ['vinabb_web_donate_email', '']],
			['config.add', ['vinabb_web_donate_bank', '']],
			['config.add', ['vinabb_web_donate_bank_vi', '']],
			['config.add', ['vinabb_web_donate_bank_acc', '']],
			['config.add', ['vinabb_web_donate_bank_swift', '']],
			['config.add', ['vinabb_web_donate_paypal', '']]
		];
	}
}
