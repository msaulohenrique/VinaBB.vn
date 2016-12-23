<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\acp;

/**
* ACP module info: acp_headlines
*/
class headlines_info
{
	/**
	* Get module info
	*
	* @return array
	*/
	public function module()
	{
		return [
			'filename'	=> '\vinabb\web\acp\headlines_module',
			'title'		=> 'ACP_HEADLINES',
			'version'	=> '1.0.0',
			'modes'		=> [
				'main'	=> [
					'title'	=> 'ACP_HEADLINES',
					'auth'	=> 'ext_vinabb/web && acl_a_board',
					'cat'	=> ['ACP_CAT_PORTAL']
				]
			]
		];
	}
}
