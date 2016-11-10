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

	'EDIT_ARTICLE'					=> 'Edit article',
	'EDIT_CAT'						=> 'Edit category',
	'ERROR_ARTICLE_CAT_SELECT'		=> 'You must select a category.',
	'ERROR_ARTICLE_DESC_EMPTY'		=> 'You must enter a description.',
	'ERROR_ARTICLE_LANG_SELECT'		=> 'You must select a language.',
	'ERROR_ARTICLE_NAME_EMPTY'		=> 'You must enter an article name.',
	'ERROR_ARTICLE_TEXT_EMPTY'		=> 'You must enter an article text.',
	'ERROR_CAT_DELETE'				=> 'Could not delete this category while it is still in use.',
	'ERROR_CAT_NAME_EMPTY'			=> 'You must enter a category name.',
	'ERROR_CAT_VARNAME_DUPLICATE'	=> 'The category varname “%s” already exists.',
	'ERROR_CAT_VARNAME_EMPTY'		=> 'You must enter a category varname.',
	'ERROR_CAT_VARNAME_INVALID'		=> 'The category varname is invalid.',

	'MESSAGE_ARTICLE_ADD'		=> 'The article has been created.',
	'MESSAGE_ARTICLE_DELETE'	=> 'The article has been deleted.',
	'MESSAGE_ARTICLE_EDIT'		=> 'The article has been edited.',
	'MESSAGE_CAT_ADD'			=> 'The category has been created.',
	'MESSAGE_CAT_DELETE'		=> 'The category has been deleted.',
	'MESSAGE_CAT_EDIT'			=> 'The category has been edited.',

	'NO_CAT'	=> 'The category does not exist.',
	'NO_CAT_ID'	=> 'No categories specified.'
]);
