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
	'CHECK_PHP_BRANCH'					=> 'Latest PHP branch',
	'CHECK_PHP_LEGACY_BRANCH'			=> 'Legacy PHP branch',
	'CHECK_PHP_URL'						=> 'PHP version tracking URL',
	'CHECK_PHPBB_BRANCH'				=> 'Latest phpBB branch',
	'CHECK_PHPBB_DEV_BRANCH'			=> 'Development phpBB branch',
	'CHECK_PHPBB_DOWNLOAD_DEV_URL'		=> 'phpBB unstable download URL',
	'CHECK_PHPBB_DOWNLOAD_URL'			=> 'phpBB download URL',
	'CHECK_PHPBB_DOWNLOAD_URL_EXPLAIN'	=> 'Available variables: <code>{branch}, {version}</code>',
	'CHECK_PHPBB_LEGACY_BRANCH'			=> 'Legacy phpBB branch',
	'CHECK_PHPBB_URL'					=> 'phpBB version tracking URL',

	'DEFAULT_LANGUAGE'	=> 'Default language',
	'DONATE_EMAIL'		=> 'Email',
	'DONATE_FUND'		=> 'Fund value',
	'DONATE_PAYPAL'		=> 'PayPal link',
	'DONATE_YEAR'		=> 'Donation year',
	'DONATE_YEAR_VALUE'	=> 'Donation value',

	'ERROR_MAINTENANCE_MODE_FOUNDER'			=> 'You are not authorized to change the maintenance mode to “Founder”.',
	'ERROR_CHECK_PHP_BRANCH_INVALID'			=> 'The latest PHP branch version is invalid.',
	'ERROR_CHECK_PHP_LEGACY_BRANCH_INVALID'		=> 'The legacy PHP branch version is invalid.',
	'ERROR_CHECK_PHPBB_BRANCH_INVALID'			=> 'The latest phpBB branch version is invalid.',
	'ERROR_CHECK_PHPBB_DEV_BRANCH_INVALID'		=> 'The development phpBB branch version is invalid.',
	'ERROR_CHECK_PHPBB_LEGACY_BRANCH_INVALID'	=> 'The legacy phpBB branch version is invalid.',

	'FORUM_ID_ENGLISH'					=> 'English category',
	'FORUM_ID_ENGLISH_DISCUSSION'		=> 'Discussions (English)',
	'FORUM_ID_ENGLISH_SUPPORT'			=> 'Support (English)',
	'FORUM_ID_ENGLISH_TUTORIAL'			=> 'Tutorial (English)',
	'FORUM_ID_VIETNAMESE'				=> 'Vietnamese category',
	'FORUM_ID_VIETNAMESE_DISCUSSION'	=> 'Discussions (Vietnamese)',
	'FORUM_ID_VIETNAMESE_EXT'			=> 'Extensions (Vietnamese)',
	'FORUM_ID_VIETNAMESE_STYLE'			=> 'Styles (Vietnamese)',
	'FORUM_ID_VIETNAMESE_SUPPORT'		=> 'Support (Vietnamese)',
	'FORUM_ID_VIETNAMESE_TUTORIAL'		=> 'Tutorial (Vietnamese)',

	'LANG_ENABLE'			=> 'Enable language switcher',
	'LANG_SWITCH'			=> 'Switch language',
	'LANG_SWITCH_EXPLAIN'	=> 'The switch and default language must be different.',

	'MAINTENANCE_MODE'					=> 'Maintenance mode',
	'MAINTENANCE_MODE_ADMIN'			=> 'Administrator',
	'MAINTENANCE_MODE_FOUNDER'			=> 'Founder',
	'MAINTENANCE_MODE_MOD'				=> 'Moderator',
	'MAINTENANCE_MODE_NONE'				=> 'None',
	'MAINTENANCE_MODE_USER'				=> 'User',
	'MAINTENANCE_TEXT'					=> 'Maintenance text',
	'MAINTENANCE_TEXT_EXPLAIN'			=> 'Leave this field blank to use the default text from language file.',
	'MAINTENANCE_TIME'					=> 'Maintenance time',
	'MAINTENANCE_TIME_EXPLAIN'			=> 'Number of minutes since you saved these settings.',
	'MAINTENANCE_TIME_REMAIN'			=> 'Time remaining',
	'MAINTENANCE_TIME_RESET'			=> 'Reset time',
	'MAINTENANCE_TPL'					=> 'Use template to display',
	'MANAGER_NAME'						=> 'Company name',
	'MANAGER_USER_ID'					=> 'Manager user ID',
	'MANAGER_USERNAME'					=> 'Manager username',
	'MAP_ADDRESS'						=> 'Address',
	'MAP_API'							=> 'Google Maps JavaScript API key',
	'MAP_PHONE'							=> 'Phone number',
	'MAP_PHONE_NAME'					=> 'Contact name',
	'MESSAGE_MAIN_SETTINGS_UPDATE'		=> 'The general settings have been updated.',
	'MESSAGE_SETUP_SETTINGS_UPDATE'		=> 'The once settings have been updated.',
	'MESSAGE_VERSION_SETTINGS_UPDATE'	=> 'The version settings have been updated.',

	'SELECT_LANGUAGE'	=> 'Select a language',
	'SOCIAL_LINKS'		=> 'Social links'
]);
