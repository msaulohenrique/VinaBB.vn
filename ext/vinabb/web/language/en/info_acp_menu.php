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
	'ACP_MENU'			=> 'Menu',
	'ACP_MENU_EXPLAIN'	=> 'Menu items on the top of every page.',

	'LOG_MENU_ADD'		=> '<strong>Created new menu</strong><br>» %s',
	'LOG_MENU_DELETE'	=> '<strong>Deleted menu</strong><br>» %s',
	'LOG_MENU_EDIT'		=> '<strong>Edited menu</strong><br>» %s'
]);
