<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\migrations\v10x;

use phpbb\db\migration\migration;

class bb_config extends migration
{
	public function update_data()
	{
		return array(
			// Counter
			array('config.add', array('vinabb_web_total_bb_exts', 0, true)),
			array('config.add', array('vinabb_web_total_bb_styles', 0, true)),
			array('config.add', array('vinabb_web_total_bb_acp_styles', 0, true)),
			array('config.add', array('vinabb_web_total_bb_langs', 0, true)),
			array('config.add', array('vinabb_web_total_bb_tools', 0, true)),
			array('config.add', array('vinabb_web_total_bb_authors', 0, true)),
			array('config.add', array('vinabb_web_bb_exts_filesize', 0, true)),
			array('config.add', array('vinabb_web_bb_styles_filesize', 0, true)),
			array('config.add', array('vinabb_web_bb_acp_styles_filesize', 0, true)),
			array('config.add', array('vinabb_web_bb_langs_filesize', 0, true)),
			array('config.add', array('vinabb_web_bb_tools_filesize', 0, true)),

			// Config
			array('config.add', array('vinabb_web_newest_ext_id', 0)),
			array('config.add', array('vinabb_web_newest_ext_varname', '')),
			array('config.add', array('vinabb_web_newest_ext_time', 0)),
			array('config.add', array('vinabb_web_newest_style_id', 0)),
			array('config.add', array('vinabb_web_newest_style_varname', '')),
			array('config.add', array('vinabb_web_newest_style_time', 0)),
			array('config.add', array('vinabb_web_newest_acp_style_id', 0)),
			array('config.add', array('vinabb_web_newest_acp_style_varname', '')),
			array('config.add', array('vinabb_web_newest_acp_style_time', 0)),
			array('config.add', array('vinabb_web_newest_lang_id', 0)),
			array('config.add', array('vinabb_web_newest_lang_varname', '')),
			array('config.add', array('vinabb_web_newest_lang_time', 0)),
			array('config.add', array('vinabb_web_newest_tool_id', 0)),
			array('config.add', array('vinabb_web_newest_tool_varname', '')),
			array('config.add', array('vinabb_web_newest_tool_time', 0)),
		);
	}
}
