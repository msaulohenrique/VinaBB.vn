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
	'ACP_VINABB_SETTINGS'					=> 'General settings',
	'ACP_VINABB_SETTINGS_EXPLAIN'			=> '',
	'ACP_VINABB_SETTINGS_SETUP'				=> 'Once settings',
	'ACP_VINABB_SETTINGS_SETUP_EXPLAIN'		=> '',
	'ACP_VINABB_SETTINGS_VERSION'			=> 'Version settings',
	'ACP_VINABB_SETTINGS_VERSION_EXPLAIN'	=> '',

	'LOG_VINABB_CHANGE_CHECK_PHP_BRANCH'			=> '<strong>VinaBB: Changed PHP latest branch in checking</strong><br>» Old branch: %1$s<br>» New branch: %2$s',
	'LOG_VINABB_CHANGE_CHECK_PHP_LEGACY_BRANCH'		=> '<strong>VinaBB: Changed PHP legacy branch in checking</strong><br>» Old branch: %1$s<br>» New branch: %2$s',
	'LOG_VINABB_CHANGE_CHECK_PHPBB_BRANCH'			=> '<strong>VinaBB: Changed phpBB latest branch in checking</strong><br>» Old branch: %1$s<br>» New branch: %2$s',
	'LOG_VINABB_CHANGE_CHECK_PHPBB_DEV_BRANCH'		=> '<strong>VinaBB: Changed phpBB development branch in checking</strong><br>» Old branch: %1$s<br>» New branch: %2$s',
	'LOG_VINABB_CHANGE_CHECK_PHPBB_LEGACY_BRANCH'	=> '<strong>VinaBB: Changed phpBB legacy branch in checking</strong><br>» Old branch: %1$s<br>» New branch: %2$s',
	'LOG_VINABB_SETTINGS'							=> '<strong>VinaBB: Altered general settings</strong>',
	'LOG_VINABB_SETTINGS_SETUP'						=> '<strong>VinaBB: Altered once settings</strong>',
	'LOG_VINABB_SETTINGS_VERSION'					=> '<strong>VinaBB: Altered version settings</strong>'
]);
