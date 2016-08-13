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

	'DEFAULT_LANGUAGE'	=> 'Default language',

	'LANG_ENABLE'			=> 'Enable language switcher',
	'LANG_SWITCH'			=> 'Switch language',
	'LANG_SWITCH_EXPLAIN'	=> 'The switch and default language must be different.',

	'MAINTENANCE_MODE'			=> 'Maintenance mode',
	'MAINTENANCE_MODE_ADMIN'	=> 'Administrator',
	'MAINTENANCE_MODE_FOUNDER'	=> 'Founder',
	'MAINTENANCE_MODE_MOD'		=> 'Moderator',
	'MAINTENANCE_MODE_NONE'		=> 'None',
	'MAINTENANCE_MODE_SERVER'	=> 'Server',
	'MAINTENANCE_MODE_USER'		=> 'User',
	'MAINTENANCE_TEXT'			=> 'Maintenance text',
	'MAINTENANCE_TEXT_EXPLAIN'	=> 'Leave this field blank to use the default text from language file.',
	'MAINTENANCE_TIME'			=> 'Maintenance time',
	'MAINTENANCE_TIME_EXPLAIN'	=> 'Number of minutes since you saved these settings.',
	'MAINTENANCE_TIME_REMAIN'	=> 'Time remaining',
	'MAINTENANCE_TPL'			=> 'Use template to display',

	'NO_EXTRA_LANG_TO_SELECT'	=> 'No extra languages to select.',

	'SELECT_LANGUAGE'	=> 'Select a language',

	'VINABB_SETTINGS_UPDATED'	=> 'The VinaBB.vn settings have been updated.',
));
