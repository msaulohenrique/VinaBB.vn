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
	'ADD_MENU'	=> 'Create new menu',

	'CONFIRM_DELETE_MENU'	=> 'Are you sure you want to delete this menu?',

	'EDIT_MENU'						=> 'Edit menu',
	'ERROR_MENU_CHANGE_PARENT'		=> 'Could not change the parent menu. Error: %s',
	'ERROR_MENU_DELETE'				=> 'Could not delete this menu. Error: %s',
	'ERROR_MENU_NAME_EMPTY'			=> 'You must enter a menu title.',
	'ERROR_MENU_NAME_TOO_LONG'		=> 'The menu title is too long.',
	'ERROR_MENU_NAME_VI_TOO_LONG'	=> 'The Vietnamese menu title is too long.',

	'MENU_LINK'				=> 'Menu link',
	'MENU_NAME'				=> 'Menu title',
	'MENU_TARGET'			=> 'Open in new tab',
	'MENU_TYPE'				=> 'Menu type',
	'MESSAGE_MENU_ADD'		=> 'The menu has been created.',
	'MESSAGE_MENU_DELETE'	=> 'The menu has been deleted.',
	'MESSAGE_MENU_EDIT'		=> 'The menu has been edited.'
]);
