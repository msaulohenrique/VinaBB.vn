<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\acp;

/**
* Hidden ACP module info: acp_bb_item_versions
*/
class bb_item_versions_info
{
	/**
	* Get module info
	*
	* @return array
	*/
	public function module()
	{
		return [
			'filename'	=> '\vinabb\web\acp\bb_item_versions_module',
			'title'		=> 'ACP_BB_EXT_VERSIONS',
			'version'	=> '1.0.0',
			'modes'		=> [
				'ext'		=> [
					'title'		=> 'ACP_BB_EXT_VERSIONS',
					'auth'		=> 'ext_vinabb/web && acl_a_board',
					'display'	=> false,
					'cat'		=> ['ACP_CAT_BB']
				],
				'style'		=> [
					'title'		=> 'ACP_BB_STYLE_VERSIONS',
					'auth'		=> 'ext_vinabb/web && acl_a_board',
					'display'	=> false,
					'cat'		=> ['ACP_CAT_BB']
				],
				'acp_style'	=> [
					'title'		=> 'ACP_BB_ACP_STYLE_VERSIONS',
					'auth'		=> 'ext_vinabb/web && acl_a_board',
					'display'	=> false,
					'cat'		=> ['ACP_CAT_BB']
				],
				'lang'		=> [
					'title'		=> 'ACP_BB_LANG_VERSIONS',
					'auth'		=> 'ext_vinabb/web && acl_a_board',
					'display'	=> false,
					'cat'		=> ['ACP_CAT_BB']
				],
				'tool'		=> [
					'title'		=> 'ACP_BB_TOOL_VERSIONS',
					'auth'		=> 'ext_vinabb/web && acl_a_board',
					'display'	=> false,
					'cat'		=> ['ACP_CAT_BB']
				]
			]
		];
	}
}
