<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\acp;

class pages_info
{
	public function module()
	{
		return array(
			'filename'	=> '\vinabb\web\acp\pages_module',
			'title'		=> 'ACP_PAGES',
			'version'	=> '1.0.0',
			'modes'		=> array(
				'cats'	=> array(
					'title'	=> 'ACP_PAGES',
					'auth'	=> 'ext_vinabb/web && acl_a_board',
					'cat'	=> array('ACP_CAT_PORTAL'),
				),
			),
		);
	}
}
