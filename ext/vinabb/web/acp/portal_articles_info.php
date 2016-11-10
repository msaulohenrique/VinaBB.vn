<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\acp;

/**
* ACP module info: acp_portal_articles
*/
class portal_articles_info
{
	public function module()
	{
		return [
			'filename'	=> '\vinabb\web\acp\portal_articles_module',
			'title'		=> 'ACP_PORTAL_ARTICLES',
			'version'	=> '1.0.0',
			'modes'		=> [
				'main'	=> [
					'title'	=> 'ACP_PORTAL_ARTICLES',
					'auth'	=> 'ext_vinabb/web && acl_a_board',
					'cat'	=> ['ACP_CAT_PORTAL']
				]
			]
		];
	}
}
