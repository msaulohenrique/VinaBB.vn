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
	'EMOTICON_TEXT'	=> array(
		'HAPPY'	=> 'Ha ha',
	),

	'G_MANAGEMENT_TEAM'	=> 'Managers',
	'G_MODERATOR_TEAM'	=> 'Moderators',
	'G_SUPPORT_TEAM'	=> 'Supporters',

	'VINABB'	=> 'VinaBB',
));
