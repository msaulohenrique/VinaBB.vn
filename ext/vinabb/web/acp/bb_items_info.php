<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\acp;

class bb_items_info
{
	public function module()
	{
		return array(
			'filename'	=> '\vinabb\web\acp\bb_items_module',
			'title'		=> 'ACP_BB_EXT_CATS',
			'version'	=> '1.0.0',
			'modes'		=> array(
				'ext'		=> array(
					'title'	=> 'ACP_BB_EXTS',
					'auth'	=> 'ext_vinabb/web && acl_a_board',
					'cat'	=> array('ACP_CAT_BB'),
				),
				'style'		=> array(
					'title'	=> 'ACP_BB_STYLES',
					'auth'	=> 'ext_vinabb/web && acl_a_board',
					'cat'	=> array('ACP_CAT_BB'),
				),
				'acp_style'	=> array(
					'title'	=> 'ACP_BB_ACP_STYLES',
					'auth'	=> 'ext_vinabb/web && acl_a_board',
					'cat'	=> array('ACP_CAT_BB'),
				),
				'lang'		=> array(
					'title'	=> 'ACP_BB_LANGS',
					'auth'	=> 'ext_vinabb/web && acl_a_board',
					'cat'	=> array('ACP_CAT_BB'),
				),
				'tool'		=> array(
					'title'	=> 'ACP_BB_TOOLS',
					'auth'	=> 'ext_vinabb/web && acl_a_board',
					'cat'	=> array('ACP_CAT_BB'),
				),
			),
		);
	}
}
