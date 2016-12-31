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

/**
* These are errors which can be triggered by sending invalid data to the extension API.
*
* These errors will never show to a user unless they are either
* modifying the extension code OR unless they are writing an extension
* which makes calls to this extension.
*
* Translators: Do not need to translate these language strings ;)
*/
$lang = array_merge($lang, [
	'ACP_STYLE_DETAILS'	=> 'Style information',
	'ACP_STYLE_NAME'	=> 'Style name',
	'ACP_STYLE_VERSION'	=> 'Style version',

	'EXT_DETAILS'				=> 'Extension information',
	'EXT_NAME'					=> 'Extension name',
	'EXT_PROPERTIES'			=> [
		'ACP_STYLE'			=> 'ACP Styles',
		'CONSOLE'			=> 'Console Commands',
		'CRON'				=> 'Cron Tasks',
		'DB_DATA'			=> 'Table Data',
		'DB_SCHEMA'			=> 'Database Schema',
		'EVENT'				=> 'PHP Events',
		'EVENT_ACP_STYLE'	=> 'ACP Template Events',
		'EVENT_STYLE'		=> 'Template Events',
		'EXT_PHP'			=> 'Extension Installer',
		'LANG'				=> 'Language Files',
		'MODULE_ACP'		=> 'ACP Modules',
		'MODULE_MCP'		=> 'MCP Modules',
		'MODULE_UCP'		=> 'UCP Modules',
		'NOTIFICATION'		=> 'Notifications',
		'STYLE'				=> 'Styles'
	],
	'EXT_PROPERTIES_EXPLAIN'	=> [
		'ACP_STYLE'			=> 'Add new HTML/CSS/JS files to the ACP style.',
		'CONSOLE'			=> 'Add new console commands.',
		'CRON'				=> 'Add new cron tasks.',
		'DB_DATA'			=> 'Insert, update, delete rows in the database tables and modify config items.',
		'DB_SCHEMA'			=> 'Create, alter, drop tables, columns and indexes in the database.',
		'EVENT'				=> 'Modify PHP code via events.',
		'EVENT_ACP_STYLE'	=> 'Insert HTML/CSS/JS code into ACP template files via events.',
		'EVENT_STYLE'		=> 'Insert HTML/CSS/JS code into template files via events.',
		'EXT_PHP'			=> 'Execute some actions while installing or uninstalling the extension.',
		'LANG'				=> 'Add new language files.',
		'MODULE_ACP'		=> 'Add new ACP modules.',
		'MODULE_MCP'		=> 'Add new MCP modules.',
		'MODULE_UCP'		=> 'Add new UCP modules.',
		'NOTIFICATION'		=> 'Add new notification types.',
		'STYLE'				=> 'Add new HTML/CSS/JS files to your styles.'
	],
	'EXT_VERSION'				=> 'Extension version',

	'LANG_DETAILS'	=> 'Language package information',
	'LANG_NAME'		=> 'Language package name',
	'LANG_VERSION'	=> 'Language package version',

	'STYLE_DETAILS'		=> 'Style information',
	'STYLE_NAME'		=> 'Style name',
	'STYLE_PROPERTIES'	=> [
		'BOOTSTRAP'		=> 'Bootstrap',
		'PRESETS'		=> 'Presets',
		'PRESETS_AIO'	=> 'All-in-one presets',
		'RESPONSIVE'	=> 'Responsive',
		'SOURCE'		=> 'Source files'
	],
	'STYLE_VERSION'		=> 'Style version',

	'TOOL_DETAILS'	=> 'Tool information',
	'TOOL_NAME'		=> 'Tool name',
	'TOOL_VERSION'	=> 'Tool version'
]);
