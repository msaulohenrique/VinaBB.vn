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
	'ADD_PORTAL_CAT'	=> 'Create new category',

	'PORTAL_CAT_NAME'		=> 'Category name',
	'PORTAL_CAT_NAME_VI'	=> 'Category name (Vietnamese)',
	'PORTAL_CAT_VARNAME'	=> 'Varname',

	'CONFIRM_PORTAL_CAT_DELETE'	=> 'Are you sure you want to delete this category?',

	'ERROR_PORTAL_CAT_DELETE'				=> 'Could not delete this category while it is still in use.',
	'ERROR_PORTAL_CAT_NAME_EMPTY'			=> 'You must enter a category name.',
	'ERROR_PORTAL_CAT_VARNAME_DUPLICATE'	=> 'The category varname “%s” already exists.',
	'ERROR_PORTAL_CAT_VARNAME_EMPTY'		=> 'You must enter a category varname.',

	'MESSAGE_PORTAL_CAT_ADD'	=> 'The category has been created.',
	'MESSAGE_PORTAL_CAT_DELETE'	=> 'The category has been deleted.',
	'MESSAGE_PORTAL_CAT_EDIT'	=> 'The category has been edited.',

	'NO_PORTAL_CAT'		=> 'The category does not exist.',
	'NO_PORTAL_CAT_ID'	=> 'No categories specified.',
));
