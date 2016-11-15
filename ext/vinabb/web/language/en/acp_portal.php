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
	'ADD_ARTICLE'		=> 'Create new article',
	'ADD_CAT'			=> 'Create new category',
	'ARTICLE_DESC'		=> 'Description',
	'ARTICLE_DETAILS'	=> 'Article data',
	'ARTICLE_NAME'		=> 'Article name',
	'ARTICLE_REVISION'	=> 'Reset article time',
	'ARTICLE_TEXT'		=> 'Article text',

	'CAT_ICON'					=> 'Menu icon',
	'CAT_NAME'					=> 'Category name',
	'CAT_VARNAME'				=> 'Varname',
	'CONFIRM_DELETE_ARTICLE'	=> 'Are you sure you want to delete this article?',
	'CONFIRM_DELETE_CAT'		=> 'Are you sure you want to delete this category?',

	'EDIT_ARTICLE'						=> 'Edit article',
	'EDIT_CAT'							=> 'Edit category',
	'ERROR_ARTICLE_DESC_EMPTY'			=> 'You must enter a description.',
	'ERROR_ARTICLE_DESC_TOO_LONG'		=> 'The description is too long.',
	'ERROR_ARTICLE_ID_EMPTY'			=> 'No articles specified.',
	'ERROR_ARTICLE_ID_NOT_EXISTS'		=> 'The article does not exist.',
	'ERROR_ARTICLE_LANG_EMPTY'			=> 'You must select a language.',
	'ERROR_ARTICLE_LANG_NOT_EXISTS'		=> 'The language does not exist.',
	'ERROR_ARTICLE_NAME_EMPTY'			=> 'You must enter an article name.',
	'ERROR_ARTICLE_NAME_SEO_INVALID'	=> 'The article SEO name is invalid.',
	'ERROR_ARTICLE_NAME_TOO_LONG'		=> 'The article name is too long.',
	'ERROR_ARTICLE_TEXT_EMPTY'			=> 'You must enter an article text.',
	'ERROR_CAT_DELETE_IN_USE'			=> 'Could not delete this category while it is still in use.',
	'ERROR_CAT_ID_EMPTY'				=> 'You must select a category.',
	'ERROR_CAT_ID_NOT_EXISTS'			=> 'The category does not exist.',
	'ERROR_CAT_NAME_EMPTY'				=> 'You must enter a category name.',
	'ERROR_CAT_NAME_TOO_LONG'			=> 'The category name is too long.',
	'ERROR_CAT_NAME_VI_TOO_LONG'		=> 'The Vietnamese category name is too long.',
	'ERROR_CAT_VARNAME_DUPLICATE'		=> 'The category varname “%s” already exists.',
	'ERROR_CAT_VARNAME_EMPTY'			=> 'You must enter a category varname.',
	'ERROR_CAT_VARNAME_INVALID'			=> 'The category varname is invalid.',
	'ERROR_CAT_VARNAME_TOO_LONG'		=> 'The category varname is too long.',
	'ERROR_USER_ID_EMPTY'				=> 'No users specified.',
	'ERROR_USER_ID_NOT_EXISTS'			=> 'The user does not exist.',

	'MESSAGE_ARTICLE_ADD'		=> 'The article has been created.',
	'MESSAGE_ARTICLE_DELETE'	=> 'The article has been deleted.',
	'MESSAGE_ARTICLE_EDIT'		=> 'The article has been edited.',
	'MESSAGE_CAT_ADD'			=> 'The category has been created.',
	'MESSAGE_CAT_DELETE'		=> 'The category has been deleted.',
	'MESSAGE_CAT_EDIT'			=> 'The category has been edited.'
]);
