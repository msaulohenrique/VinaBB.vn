<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

/**
* DO NOT CHANGE
*/
if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

/**
* All language files should use UTF-8 as their encoding
* and the files must not contain a BOM.
*/

$lang = array_merge($lang, array(
	'ACP_BB_ACP_STYLE_CATS'			=> 'ACP style categories',
	'ACP_BB_ACP_STYLE_CATS_EXPLAIN'	=> '',
	'ACP_BB_EXT_CATS'				=> 'Extension categories',
	'ACP_BB_EXT_CATS_EXPLAIN'		=> '',
	'ACP_BB_LANG_CATS'				=> 'Language categories',
	'ACP_BB_LANG_CATS_EXPLAIN'		=> '',
	'ACP_BB_STYLE_CATS'				=> 'Style categories',
	'ACP_BB_STYLE_CATS_EXPLAIN'		=> '',
	'ACP_BB_TOOL_CATS'				=> 'Tool categories',
	'ACP_BB_TOOL_CATS_EXPLAIN'		=> '',

	'LOG_BB_ACP_STYLE_CAT_ADD'		=> '<strong>phpBB Resource: Created new ACP style category</strong><br>» %s',
	'LOG_BB_ACP_STYLE_CAT_DELETE'	=> '<strong>phpBB Resource: Deleted ACP style category</strong><br>» %s',
	'LOG_BB_ACP_STYLE_CAT_EDIT'		=> '<strong>phpBB Resource: Edited ACP style category</strong><br>» %s',
	'LOG_BB_EXT_CAT_ADD'			=> '<strong>phpBB Resource: Created new extension category</strong><br>» %s',
	'LOG_BB_EXT_CAT_DELETE'			=> '<strong>phpBB Resource: Deleted extension category</strong><br>» %s',
	'LOG_BB_EXT_CAT_EDIT'			=> '<strong>phpBB Resource: Edited extension category</strong><br>» %s',
	'LOG_BB_LANG_CAT_ADD'			=> '<strong>phpBB Resource: Created new language category</strong><br>» %s',
	'LOG_BB_LANG_CAT_DELETE'		=> '<strong>phpBB Resource: Deleted language category</strong><br>» %s',
	'LOG_BB_LANG_CAT_EDIT'			=> '<strong>phpBB Resource: Edited language category</strong><br>» %s',
	'LOG_BB_STYLE_CAT_ADD'			=> '<strong>phpBB Resource: Created new style category</strong><br>» %s',
	'LOG_BB_STYLE_CAT_DELETE'		=> '<strong>phpBB Resource: Deleted style category</strong><br>» %s',
	'LOG_BB_STYLE_CAT_EDIT'			=> '<strong>phpBB Resource: Edited style category</strong><br>» %s',
	'LOG_BB_TOOL_CAT_ADD'			=> '<strong>phpBB Resource: Created new tool category</strong><br>» %s',
	'LOG_BB_TOOL_CAT_DELETE'		=> '<strong>phpBB Resource: Deleted tool category</strong><br>» %s',
	'LOG_BB_TOOL_CAT_EDIT'			=> '<strong>phpBB Resource: Edited tool category</strong><br>» %s',
));
