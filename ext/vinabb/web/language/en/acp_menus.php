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
	'ERROR_MENU_MOVE'				=> 'Could not move this menu. Error: %s',
	'ERROR_MENU_NAME_EMPTY'			=> 'You must enter a menu title.',
	'ERROR_MENU_NAME_TOO_LONG'		=> 'The menu title is too long.',
	'ERROR_MENU_NAME_VI_TOO_LONG'	=> 'The Vietnamese menu title is too long.',
	'ERROR_MENU_TYPE_EMPTY'			=> 'You must select a menu type.',

	'MENU_DETAILS'			=> 'Menu details',
	'MENU_ICON'				=> 'Menu icon',
	'MENU_LINK'				=> 'Menu link',
	'MENU_NAME'				=> 'Menu title',
	'MENU_PARENT'			=> 'Parent menu',
	'MENU_TARGET'			=> 'Open in new tab',
	'MENU_TYPE'				=> 'Menu type',
	'MENU_TYPE_BB_TIP'		=> 'This menu type lists phpBB resource types and categories as its child menus. You can not add sub-menus to it later.',
	'MENU_TYPE_BOARD_TIP'	=> 'This menu type lists categories and forums from the board as its child menus. You can not add sub-menus to it later.',
	'MENU_TYPE_PORTAL_TIP'	=> 'This menu type lists news categories as its child menus. You can not add sub-menus to it later.',
	'MENU_TYPES'			=> [
		1	=> 'Custom URL',
		2	=> 'Route',
		3	=> 'Page',
		4	=> 'Forum page',
		5	=> 'User page',
		6	=> 'Group page',
		7	=> 'Board',
		8	=> 'Portal',
		9	=> 'phpBB Resource'
	],
	'MESSAGE_MENU_ADD'		=> 'The menu has been created.',
	'MESSAGE_MENU_DELETE'	=> 'The menu has been deleted.',
	'MESSAGE_MENU_EDIT'		=> 'The menu has been edited.',

	'SELECT_MENU_TYPE'	=> 'Select a menu type'
]);
