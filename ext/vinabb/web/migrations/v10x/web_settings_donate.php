<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\migrations\v10x;

use phpbb\db\migration\migration;

class web_settings_donate extends migration
{
	public function update_data()
	{
		return array(
			array('config.add', array('vinabb_web_donate_year', 0)),
			array('config.add', array('vinabb_web_donate_year_value', 0)),
			array('config.add', array('vinabb_web_donate_fund', 0)),
			array('config.add', array('vinabb_web_donate_currency', '')),
			array('config.add', array('vinabb_web_donate_owner', '')),
			array('config.add', array('vinabb_web_donate_owner_vi', '')),
			array('config.add', array('vinabb_web_donate_email', '')),
			array('config.add', array('vinabb_web_donate_bank', '')),
			array('config.add', array('vinabb_web_donate_bank_vi', '')),
			array('config.add', array('vinabb_web_donate_bank_acc', '')),
			array('config.add', array('vinabb_web_donate_bank_swift', '')),
			array('config.add', array('vinabb_web_donate_paypal', '')),
		);
	}
}
