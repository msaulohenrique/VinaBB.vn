<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\acp;

/**
* ACP module info: acp_bb_categories
*/
class bb_categories_info
{
	public function module()
	{
		return [
			'filename'		=> '\vinabb\web\acp\bb_categories_module',
			'title'			=> 'ACP_BB_EXT_CATS',
			'version'		=> '1.0.0',
			'modes'			=> [
				'ext'		=> [
					'title'	=> 'ACP_BB_EXT_CATS',
					'auth'	=> 'ext_vinabb/web && acl_a_board',
					'cat'	=> ['ACP_CAT_BB']
				],
				'style'		=> [
					'title'	=> 'ACP_BB_STYLE_CATS',
					'auth'	=> 'ext_vinabb/web && acl_a_board',
					'cat'	=> ['ACP_CAT_BB']
				],
				'acp_style'	=> [
					'title'	=> 'ACP_BB_ACP_STYLE_CATS',
					'auth'	=> 'ext_vinabb/web && acl_a_board',
					'cat'	=> ['ACP_CAT_BB']
				],
				'lang'		=> [
					'title'	=> 'ACP_BB_LANG_CATS',
					'auth'	=> 'ext_vinabb/web && acl_a_board',
					'cat'	=> ['ACP_CAT_BB']
				],
				'tool'		=> [
					'title'	=> 'ACP_BB_TOOL_CATS',
					'auth'	=> 'ext_vinabb/web && acl_a_board',
					'cat'	=> ['ACP_CAT_BB']
				]
			]
		];
	}
}
