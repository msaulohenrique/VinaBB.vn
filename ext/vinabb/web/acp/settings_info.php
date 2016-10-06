<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\acp;

class settings_info
{
	public function module()
	{
		return array(
			'filename'		=> '\vinabb\web\acp\settings_module',
			'title'			=> 'ACP_VINABB_SETTINGS',
			'version'		=> '1.0.0',
			'modes'			=> array(
				'settings'	=> array(
					'title'	=> 'ACP_VINABB_SETTINGS',
					'auth'	=> 'ext_vinabb/web && acl_a_board',
					'cat'	=> array('ACP_CAT_VINABB_SETTINGS'),
				),
			),
		);
	}
}