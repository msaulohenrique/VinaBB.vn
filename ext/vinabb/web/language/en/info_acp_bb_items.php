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
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

/**
* All language files should use UTF-8 as their encoding
* and the files must not contain a BOM.
*/

$lang = array_merge($lang, array(
	'ACP_BB_ACP_STYLES'			=> 'Manage ACP styles',
	'ACP_BB_ACP_STYLES_EXPLAIN'	=> '',
	'ACP_BB_EXTS'				=> 'Manage extensions',
	'ACP_BB_EXTS_EXPLAIN'		=> '',
	'ACP_BB_LANGS'				=> 'Manage language packages',
	'ACP_BB_LANGS_EXPLAIN'		=> '',
	'ACP_BB_STYLES'				=> 'Manage styles',
	'ACP_BB_STYLES_EXPLAIN'		=> '',
	'ACP_BB_TOOLS'				=> 'Manage tools',
	'ACP_BB_TOOLS_EXPLAIN'		=> '',

	'LOG_BB_ACP_STYLE_ADD'		=> '<strong>phpBB Resource: Added new ACP style</strong><br>» %s',
	'LOG_BB_ACP_STYLE_DELETE'	=> '<strong>phpBB Resource: Deleted ACP style</strong><br>» %s',
	'LOG_BB_ACP_STYLE_DISABLE'	=> '<strong>phpBB Resource: Disabled ACP style</strong><br>» %s',
	'LOG_BB_ACP_STYLE_EDIT'		=> '<strong>phpBB Resource: Edited ACP style</strong><br>» %s',
	'LOG_BB_ACP_STYLE_ENABLE'	=> '<strong>phpBB Resource: Enabled ACP style</strong><br>» %s',
	'LOG_BB_EXT_ADD'			=> '<strong>phpBB Resource: Added new extension</strong><br>» %s',
	'LOG_BB_EXT_DELETE'			=> '<strong>phpBB Resource: Deleted extension</strong><br>» %s',
	'LOG_BB_EXT_DISABLE'		=> '<strong>phpBB Resource: Disabled extension</strong><br>» %s',
	'LOG_BB_EXT_EDIT'			=> '<strong>phpBB Resource: Edited extension</strong><br>» %s',
	'LOG_BB_EXT_ENABLE'			=> '<strong>phpBB Resource: Enabled extension</strong><br>» %s',
	'LOG_BB_LANG_ADD'			=> '<strong>phpBB Resource: Added new language package</strong><br>» %s',
	'LOG_BB_LANG_DELETE'		=> '<strong>phpBB Resource: Deleted language package</strong><br>» %s',
	'LOG_BB_LANG_DISABLE'		=> '<strong>phpBB Resource: Disabled language package</strong><br>» %s',
	'LOG_BB_LANG_EDIT'			=> '<strong>phpBB Resource: Edited language package</strong><br>» %s',
	'LOG_BB_LANG_ENABLE'		=> '<strong>phpBB Resource: Enabled language package</strong><br>» %s',
	'LOG_BB_STYLE_ADD'			=> '<strong>phpBB Resource: Added new style</strong><br>» %s',
	'LOG_BB_STYLE_DELETE'		=> '<strong>phpBB Resource: Deleted style</strong><br>» %s',
	'LOG_BB_STYLE_DISABLE'		=> '<strong>phpBB Resource: Disabled style</strong><br>» %s',
	'LOG_BB_STYLE_EDIT'			=> '<strong>phpBB Resource: Edited style</strong><br>» %s',
	'LOG_BB_STYLE_ENABLE'		=> '<strong>phpBB Resource: Enabled style</strong><br>» %s',
	'LOG_BB_TOOL_ADD'			=> '<strong>phpBB Resource: Added new tool</strong><br>» %s',
	'LOG_BB_TOOL_DELETE'		=> '<strong>phpBB Resource: Deleted tool</strong><br>» %s',
	'LOG_BB_TOOL_DISABLE'		=> '<strong>phpBB Resource: Disabled tool</strong><br>» %s',
	'LOG_BB_TOOL_EDIT'			=> '<strong>phpBB Resource: Edited tool</strong><br>» %s',
	'LOG_BB_TOOL_ENABLE'		=> '<strong>phpBB Resource: Enabled tool</strong><br>» %s',
));
