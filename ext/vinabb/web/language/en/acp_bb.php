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
	'ACP_STYLE_VARNAME'			=> 'Varname',
	'ADD_AUTHOR'				=> 'Add new developer',
	'ADD_BB_ACP_STYLE'			=> 'Add new style',
	'ADD_BB_EXT'				=> 'Add new extension',
	'ADD_BB_LANG'				=> 'Add new language package',
	'ADD_BB_STYLE'				=> 'Add new style',
	'ADD_BB_TOOL'				=> 'Add new tool',
	'ADD_CAT'					=> 'Create new category',

	'CAT_DESC'						=> 'Description',
	'CAT_ICON'						=> 'Menu icon',
	'CAT_NAME'						=> 'Category name',
	'CAT_VARNAME'					=> 'Varname',
	'CONFIRM_DELETE_AUTHOR'			=> 'Are you sure you want to remove this developer?',
	'CONFIRM_DELETE_BB_ACP_STYLE'	=> 'Are you sure you want to remove this ACP style?',
	'CONFIRM_DELETE_BB_EXT'			=> 'Are you sure you want to remove this extension?',
	'CONFIRM_DELETE_BB_LANG'		=> 'Are you sure you want to remove this language pack?',
	'CONFIRM_DELETE_BB_STYLE'		=> 'Are you sure you want to remove this style?',
	'CONFIRM_DELETE_BB_TOOL'		=> 'Are you sure you want to remove this tool?',
	'CONFIRM_DELETE_CAT'			=> 'Are you sure you want to delete this category?',

	'EDIT_BB_ACP_STYLE'						=> 'Edit style',
	'EDIT_BB_EXT'							=> 'Edit extension',
	'EDIT_BB_LANG'							=> 'Edit language package',
	'EDIT_BB_STYLE'							=> 'Edit style',
	'EDIT_BB_TOOL'							=> 'Edit tool',
	'EDIT_CAT'								=> 'Edit category',
	'ERROR_BB_ACP_STYLE_NAME_EMPTY'			=> 'You must enter a style name.',
	'ERROR_BB_ACP_STYLE_VARNAME_DUPLICATE'	=> 'The style varname “%s” already exists.',
	'ERROR_BB_ACP_STYLE_VARNAME_EMPTY'		=> 'You must enter a style varname.',
	'ERROR_BB_ACP_STYLE_VARNAME_INVALID'	=> 'The style varname is invalid.',
	'ERROR_BB_ACP_STYLE_VERSION_EMPTY'		=> 'You must enter a style version.',
	'ERROR_BB_ACP_STYLE_VERSION_INVALID'	=> 'The style version is invalid.',
	'ERROR_BB_EXT_NAME_EMPTY'				=> 'You must enter an extension name.',
	'ERROR_BB_EXT_VARNAME_DUPLICATE'		=> 'The extension package name “%s” already exists.',
	'ERROR_BB_EXT_VARNAME_EMPTY'			=> 'You must enter an extension package name.',
	'ERROR_BB_EXT_VARNAME_INVALID'			=> 'The extension package name is invalid.',
	'ERROR_BB_EXT_VERSION_EMPTY'			=> 'You must enter an extension version.',
	'ERROR_BB_EXT_VERSION_INVALID'			=> 'The extension version is invalid.',
	'ERROR_BB_ITEM_CAT_SELECT'				=> 'You must select a category.',
	'ERROR_BB_ITEM_DESC_EMPTY'				=> 'You must enter a description.',
	'ERROR_BB_ITEM_PHPBB_VERSION_SELECT'	=> 'You must select a phpBB version.',
	'ERROR_BB_LANG_NAME_EMPTY'				=> 'You must enter a language package name.',
	'ERROR_BB_LANG_VARNAME_DUPLICATE'		=> 'The language package varname “%s” already exists.',
	'ERROR_BB_LANG_VARNAME_EMPTY'			=> 'You must enter a language package varname.',
	'ERROR_BB_LANG_VARNAME_INVALID'			=> 'The language package varname is invalid.',
	'ERROR_BB_LANG_VERSION_EMPTY'			=> 'You must enter a language package version.',
	'ERROR_BB_LANG_VERSION_INVALID'			=> 'The language package version is invalid.',
	'ERROR_BB_STYLE_NAME_EMPTY'				=> 'You must enter a style name.',
	'ERROR_BB_STYLE_VARNAME_DUPLICATE'		=> 'The style varname “%s” already exists.',
	'ERROR_BB_STYLE_VARNAME_EMPTY'			=> 'You must enter a style varname.',
	'ERROR_BB_STYLE_VARNAME_INVALID'		=> 'The style varname is invalid.',
	'ERROR_BB_STYLE_VERSION_EMPTY'			=> 'You must enter a style version.',
	'ERROR_BB_STYLE_VERSION_INVALID'		=> 'The style version is invalid.',
	'ERROR_BB_TOOL_NAME_EMPTY'				=> 'You must enter a tool name.',
	'ERROR_BB_TOOL_VARNAME_DUPLICATE'		=> 'The tool varname “%s” already exists.',
	'ERROR_BB_TOOL_VARNAME_EMPTY'			=> 'You must enter a tool varname.',
	'ERROR_BB_TOOL_VARNAME_INVALID'			=> 'The tool varname is invalid.',
	'ERROR_BB_TOOL_VERSION_EMPTY'			=> 'You must enter a tool version.',
	'ERROR_BB_TOOL_VERSION_INVALID'			=> 'The tool version is invalid.',
	'ERROR_CAT_DELETE'						=> 'Could not delete this category while it is still in use.',
	'ERROR_CAT_NAME_EMPTY'					=> 'You must enter a category name.',
	'ERROR_CAT_VARNAME_DUPLICATE'			=> 'The category varname “%s” already exists.',
	'ERROR_CAT_VARNAME_EMPTY'				=> 'You must enter a category varname.',
	'ERROR_CAT_VARNAME_INVALID'				=> 'The category varname is invalid.',
	'EXT_VARNAME'							=> 'Package name',

	'ITEM_EXT_DETAILS'		=> 'Extension properties',
	'ITEM_LANG_DETAILS'		=> 'Language package properties',
	'ITEM_STYLE_DETAILS'	=> 'Style properties',
	'ITEM_TOOL_DETAILS'		=> 'Tool properties',

	'LANG_VARNAME'	=> 'Varname',

	'MESSAGE_BB_ACP_STYLE_ADD'		=> 'The ACP style has been added.',
	'MESSAGE_BB_ACP_STYLE_DELETE'	=> 'The ACP style has been removed.',
	'MESSAGE_BB_ACP_STYLE_EDIT'		=> 'The ACP style information has been edited.',
	'MESSAGE_BB_EXT_ADD'			=> 'The extension has been added.',
	'MESSAGE_BB_EXT_DELETE'			=> 'The extension has been removed.',
	'MESSAGE_BB_EXT_EDIT'			=> 'The extension information has been edited.',
	'MESSAGE_BB_LANG_ADD'			=> 'The language package has been added.',
	'MESSAGE_BB_LANG_DELETE'		=> 'The language package has been removed.',
	'MESSAGE_BB_LANG_EDIT'			=> 'The language package information has been edited.',
	'MESSAGE_BB_STYLE_ADD'			=> 'The style has been added.',
	'MESSAGE_BB_STYLE_DELETE'		=> 'The style has been removed.',
	'MESSAGE_BB_STYLE_EDIT'			=> 'The style information has been edited.',
	'MESSAGE_BB_TOOL_ADD'			=> 'The tool has been added.',
	'MESSAGE_BB_TOOL_DELETE'		=> 'The tool has been removed.',
	'MESSAGE_BB_TOOL_EDIT'			=> 'The tool information has been edited.',
	'MESSAGE_CAT_ADD'				=> 'The category has been created.',
	'MESSAGE_CAT_DELETE'			=> 'The category has been deleted.',
	'MESSAGE_CAT_EDIT'				=> 'The category has been edited.',

	'STYLE_VARNAME'	=> 'Varname',

	'TOOL_VARNAME'	=> 'Varname'
]);
