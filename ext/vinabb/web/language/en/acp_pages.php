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
	'ADD_PAGE'	=> 'Create new page',

	'CONFIRM_DELETE_PAGE'	=> 'Are you sure you want to delete this page?',

	'EDIT_PAGE'						=> 'Edit page',
	'ERROR_PAGE_DELETE'				=> 'Could not delete this page. Error: %s',
	'ERROR_PAGE_NAME_EMPTY'			=> 'You must enter a page title.',
	'ERROR_PAGE_NAME_TOO_LONG'		=> 'The page title is too long.',
	'ERROR_PAGE_NAME_VI_TOO_LONG'	=> 'The Vietnamese page title is too long.',
	'ERROR_PAGE_VARNAME_DUPLICATE'	=> 'The page varname “%s” already exists.',
	'ERROR_PAGE_VARNAME_EMPTY'		=> 'You must enter a page varname.',
	'ERROR_PAGE_VARNAME_INVALID'	=> 'The page varname is invalid.',
	'ERROR_PAGE_VARNAME_TOO_LONG'	=> 'The page varname is too long.',

	'MESSAGE_PAGE_ADD'		=> 'The page has been created.',
	'MESSAGE_PAGE_DELETE'	=> 'The page has been deleted.',
	'MESSAGE_PAGE_EDIT'		=> 'The page has been edited.',

	'PAGE_DESC'		=> 'Description',
	'PAGE_NAME'		=> 'Page title',
	'PAGE_TEXT'		=> 'Page content',
	'PAGE_VARNAME'	=> 'Varname'
]);
