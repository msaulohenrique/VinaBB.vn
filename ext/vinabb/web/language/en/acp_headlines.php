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
	'ADD_HEADLINE'	=> 'Create new headline',

	'CONFIRM_DELETE_HEADLINE'	=> 'Are you sure you want to delete this headline?',

	'EDIT_HEADLINE'								=> 'Edit headline',
	'ERROR_HEADLINE_DELETE'						=> 'Could not delete this headline. Error: %s',
	'ERROR_HEADLINE_DESC_EMPTY'					=> 'You must enter a description.',
	'ERROR_HEADLINE_DESC_TOO_LONG'				=> 'The description is too long.',
	'ERROR_HEADLINE_IMG_DISALLOWED_EXTENSION'	=> 'The headline image’s file extension “%s” is not allowed.',
	'ERROR_HEADLINE_IMG_EMPTY'					=> 'You must upload a headline image.',
	'ERROR_HEADLINE_LANG_EMPTY'					=> 'You must select a language.',
	'ERROR_HEADLINE_LANG_NOT_EXISTS'			=> 'The language does not exist.',
	'ERROR_HEADLINE_MOVE'						=> 'Could not move this headline. Error: %s',
	'ERROR_HEADLINE_NAME_EMPTY'					=> 'You must enter a title.',
	'ERROR_HEADLINE_NAME_TOO_LONG'				=> 'The title is too long.',

	'HEADLINE_DESC'		=> 'Description',
	'HEADLINE_DETAILS'	=> 'Headline details',
	'HEADLINE_IMG'		=> 'Headline image',
	'HEADLINE_NAME'		=> 'Title',
	'HEADLINE_URL'		=> 'Headline URL',

	'MESSAGE_HEADLINE_ADD'		=> 'The headline has been created.',
	'MESSAGE_HEADLINE_DELETE'	=> 'The headline has been deleted.',
	'MESSAGE_HEADLINE_EDIT'		=> 'The headline has been edited.'
]);
