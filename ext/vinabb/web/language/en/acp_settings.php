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
	'ACP_VINABB_SETTINGS_EXPLAIN'	=> '',

	'CHECK_PHP_BRANCH'			=> 'Latest PHP branch',
	'CHECK_PHP_LEGACY_BRANCH'	=> 'Legacy PHP branch',
	'CHECK_PHP_URL'				=> 'PHP version tracking URL',
	'CHECK_PHPBB_BRANCH'		=> 'Latest phpBB branch',
	'CHECK_PHPBB_LEGACY_BRANCH'	=> 'Legacy phpBB branch',
	'CHECK_PHPBB_URL'			=> 'phpBB version tracking URL',

	'DEFAULT_LANGUAGE'	=> 'Default language',

	'ERROR_MAINTENANCE_MODE_FOUNDER'	=> 'You are not authorized to change the maintenance mode to “Founder”.',

	'LANG_ENABLE'			=> 'Enable language switcher',
	'LANG_SWITCH'			=> 'Switch language',
	'LANG_SWITCH_EXPLAIN'	=> 'The switch and default language must be different.',

	'MAINTENANCE_MODE'			=> 'Maintenance mode',
	'MAINTENANCE_MODE_ADMIN'	=> 'Administrator',
	'MAINTENANCE_MODE_FOUNDER'	=> 'Founder',
	'MAINTENANCE_MODE_MOD'		=> 'Moderator',
	'MAINTENANCE_MODE_NONE'		=> 'None',
	'MAINTENANCE_MODE_USER'		=> 'User',
	'MAINTENANCE_TEXT'			=> 'Maintenance text (English)',
	'MAINTENANCE_TEXT_EXPLAIN'	=> 'Leave this field blank to use the default text from language file.',
	'MAINTENANCE_TEXT_VI'		=> 'Maintenance text (Vietnamese)',
	'MAINTENANCE_TIME'			=> 'Maintenance time',
	'MAINTENANCE_TIME_EXPLAIN'	=> 'Number of minutes since you saved these settings.',
	'MAINTENANCE_TIME_REMAIN'	=> 'Time remaining',
	'MAINTENANCE_TIME_RESET'	=> 'Reset time',
	'MAINTENANCE_TPL'			=> 'Use template to display',
	'MAP_ADDRESS'				=> 'Address (English)',
	'MAP_ADDRESS_VI'			=> 'Address (Vietnamese)',
	'MAP_API'					=> 'Google Maps JavaScript API key',
	'MAP_PHONE'					=> 'Phone number',
	'MAP_PHONE_NAME'			=> 'Contact name',

	'NO_EXTRA_LANG_TO_SELECT'	=> 'No extra languages to select.',

	'SELECT_LANGUAGE'	=> 'Select a language',
	'SOCIAL_LINKS'		=> 'Social links',

	'VINABB_SETTINGS_UPDATED'	=> 'The VinaBB.vn settings have been updated.',
));
