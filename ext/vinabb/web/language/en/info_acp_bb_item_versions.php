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
	$lang = [];
}

/**
* All language files should use UTF-8 as their encoding
* and the files must not contain a BOM.
*/

$lang = array_merge($lang, [
	'ACP_BB_ACP_STYLE_VERSIONS'			=> 'Manage ACP style versions',
	'ACP_BB_ACP_STYLE_VERSIONS_EXPLAIN'	=> '',
	'ACP_BB_EXT_VERSIONS'				=> 'Manage extension versions',
	'ACP_BB_EXT_VERSIONS_EXPLAIN'		=> '',
	'ACP_BB_LANG_VERSIONS'				=> 'Manage language package versions',
	'ACP_BB_LANG_VERSIONS_EXPLAIN'		=> '',
	'ACP_BB_STYLE_VERSIONS'				=> 'Manage style versions',
	'ACP_BB_STYLE_VERSIONS_EXPLAIN'		=> '',
	'ACP_BB_TOOL_VERSIONS'				=> 'Manage tool versions',
	'ACP_BB_TOOL_VERSIONS_EXPLAIN'		=> '',

	'LOG_BB_ACP_STYLE_VERSION_ADD'		=> '<strong>phpBB Resource: Added new ACP style version</strong><br>» %s',
	'LOG_BB_ACP_STYLE_VERSION_DELETE'	=> '<strong>phpBB Resource: Deleted ACP style version</strong><br>» %s',
	'LOG_BB_ACP_STYLE_VERSION_EDIT'		=> '<strong>phpBB Resource: Edited ACP style version</strong><br>» %s',
	'LOG_BB_EXT_VERSION_ADD'			=> '<strong>phpBB Resource: Added new extension version</strong><br>» %s',
	'LOG_BB_EXT_VERSION_DELETE'			=> '<strong>phpBB Resource: Deleted extension version</strong><br>» %s',
	'LOG_BB_EXT_VERSION_EDIT'			=> '<strong>phpBB Resource: Edited extension version</strong><br>» %s',
	'LOG_BB_LANG_VERSION_ADD'			=> '<strong>phpBB Resource: Added new language package version</strong><br>» %s',
	'LOG_BB_LANG_VERSION_DELETE'		=> '<strong>phpBB Resource: Deleted language package version</strong><br>» %s',
	'LOG_BB_LANG_VERSION_EDIT'			=> '<strong>phpBB Resource: Edited language package version</strong><br>» %s',
	'LOG_BB_STYLE_VERSION_ADD'			=> '<strong>phpBB Resource: Added new style version</strong><br>» %s',
	'LOG_BB_STYLE_VERSION_DELETE'		=> '<strong>phpBB Resource: Deleted style version</strong><br>» %s',
	'LOG_BB_STYLE_VERSION_EDIT'			=> '<strong>phpBB Resource: Edited style version</strong><br>» %s',
	'LOG_BB_TOOL_VERSION_ADD'			=> '<strong>phpBB Resource: Added new tool version</strong><br>» %s',
	'LOG_BB_TOOL_VERSION_DELETE'		=> '<strong>phpBB Resource: Deleted tool version</strong><br>» %s',
	'LOG_BB_TOOL_VERSION_EDIT'			=> '<strong>phpBB Resource: Edited tool version</strong><br>» %s'
]);
