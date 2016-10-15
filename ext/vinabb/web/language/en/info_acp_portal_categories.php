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
	'ACP_PORTAL_CATS'	=> 'News categories',

	'LOG_PORTAL_CAT_ADD'	=> '<strong>Portal: Created new category</strong><br>» %s',
	'LOG_PORTAL_CAT_DELETE'	=> '<strong>Portal: Deleted category</strong><br>» %s',
	'LOG_PORTAL_CAT_EDIT'	=> '<strong>Portal: Edited category</strong><br>» %s',
));