<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\acp;

/**
* ACP module info: acp_bb_items
*/
class bb_items_info
{
	public function module()
	{
		return [
			'filename'	=> '\vinabb\web\acp\bb_items_module',
			'title'		=> 'ACP_BB_EXTS',
			'version'	=> '1.0.0',
			'modes'		=> [
				'ext'		=> [
					'title'	=> 'ACP_BB_EXTS',
					'auth'	=> 'ext_vinabb/web && acl_a_board',
					'cat'	=> ['ACP_CAT_BB']
				],
				'style'		=> [
					'title'	=> 'ACP_BB_STYLES',
					'auth'	=> 'ext_vinabb/web && acl_a_board',
					'cat'	=> ['ACP_CAT_BB']
				],
				'acp_style'	=> [
					'title'	=> 'ACP_BB_ACP_STYLES',
					'auth'	=> 'ext_vinabb/web && acl_a_board',
					'cat'	=> ['ACP_CAT_BB']
				],
				'lang'		=> [
					'title'	=> 'ACP_BB_LANGS',
					'auth'	=> 'ext_vinabb/web && acl_a_board',
					'cat'	=> ['ACP_CAT_BB']
				],
				'tool'		=> [
					'title'	=> 'ACP_BB_TOOLS',
					'auth'	=> 'ext_vinabb/web && acl_a_board',
					'cat'	=> ['ACP_CAT_BB']
				]
			]
		];
	}
}
