<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\acp;

/**
* ACP module info: acp_bb_authors
*/
class bb_authors_info
{
	/**
	* Get module info
	*
	* @return array
	*/
	public function module()
	{
		return [
			'filename'	=> '\vinabb\web\acp\bb_authors_module',
			'title'		=> 'ACP_BB_AUTHORS',
			'version'	=> '1.0.0',
			'modes'		=> [
				'main'	=> [
					'title'	=> 'ACP_BB_AUTHORS',
					'auth'	=> 'ext_vinabb/web && acl_a_board',
					'cat'	=> ['ACP_CAT_BB']
				]
			]
		];
	}
}
