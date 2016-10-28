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
	$lang = array();
}

/**
* All language files should use UTF-8 as their encoding
* and the files must not contain a BOM.
*/

$lang = array_merge($lang, array(
	'ACP_BB_ACP_STYLES_EXPLAIN'	=> '',
	'ACP_BB_EXTS_EXPLAIN'		=> '',
	'ACP_BB_LANGS_EXPLAIN'		=> '',
	'ACP_BB_STYLES_EXPLAIN'		=> '',
	'ACP_BB_TOOLS_EXPLAIN'		=> '',
	'ACP_STYLE_VARNAME'			=> 'Varname',
	'ADD_BB_ACP_STYLE'			=> 'Add new style',
	'ADD_BB_CAT'				=> 'Create new category',
	'ADD_BB_EXT'				=> 'Add new extension',
	'ADD_BB_LANG'				=> 'Add new language package',
	'ADD_BB_STYLE'				=> 'Add new style',
	'ADD_BB_TOOL'				=> 'Add new tool',

	'BB_CAT_ICON'		=> 'Category icon',
	'BB_CAT_NAME'		=> 'Category name',
	'BB_CAT_VARNAME'	=> 'Varname',

	'CONFIRM_BB_ACP_STYLE_DELETE'	=> 'Are you sure you want to remove this ACP style?',
	'CONFIRM_BB_CAT_DELETE'			=> 'Are you sure you want to delete this category?',
	'CONFIRM_BB_EXT_DELETE'			=> 'Are you sure you want to remove this extension?',
	'CONFIRM_BB_LANG_DELETE'		=> 'Are you sure you want to remove this language pack?',
	'CONFIRM_BB_STYLE_DELETE'		=> 'Are you sure you want to remove this style?',

	'EDIT_BB_CAT'							=> 'Edit category',
	'ERROR_BB_ACP_STYLE_NAME_EMPTY'			=> 'You must enter a style name.',
	'ERROR_BB_ACP_STYLE_VARNAME_DUPLICATE'	=> 'The style varname “%s” already exists.',
	'ERROR_BB_ACP_STYLE_VARNAME_EMPTY'		=> 'You must enter a style varname.',
	'ERROR_BB_ACP_STYLE_VARNAME_INVALID'	=> 'The style varname is invalid.',
	'ERROR_BB_ACP_STYLE_VERSION_EMPTY'		=> 'You must enter a style version.',
	'ERROR_BB_ACP_STYLE_VERSION_INVALID'	=> 'The style version is invalid.',
	'ERROR_BB_CAT_DELETE'					=> 'Could not delete this category while it is still in use.',
	'ERROR_BB_CAT_NAME_EMPTY'				=> 'You must enter a category name.',
	'ERROR_BB_CAT_VARNAME_DUPLICATE'		=> 'The category varname “%s” already exists.',
	'ERROR_BB_CAT_VARNAME_EMPTY'			=> 'You must enter a category varname.',
	'ERROR_BB_CAT_VARNAME_INVALID'			=> 'The category varname is invalid.',
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
	'EXT_VARNAME'							=> 'Package name',

	'ITEM_EXT_DETAILS'		=> 'Extension properties',
	'ITEM_LANG_DETAILS'		=> 'Language package properties',
	'ITEM_STYLE_DETAILS'	=> 'Style properties',
	'ITEM_TOOL_DETAILS'		=> 'Tool properties',

	'LANG_VARNAME'	=> 'Varname',

	'MESSAGE_BB_ACP_STYLE_ADD'		=> 'The ACP style has been added.',
	'MESSAGE_BB_ACP_STYLE_DELETE'	=> 'The ACP style has been removed.',
	'MESSAGE_BB_ACP_STYLE_EDIT'		=> 'The ACP style information has been edited.',
	'MESSAGE_BB_CAT_ADD'			=> 'The category has been created.',
	'MESSAGE_BB_CAT_DELETE'			=> 'The category has been deleted.',
	'MESSAGE_BB_CAT_EDIT'			=> 'The category has been edited.',
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

	'NO_BB_ACP_STYLE'		=> 'The ACP style does not exist.',
	'NO_BB_ACP_STYLE_ID'	=> 'No ACP styles specified.',
	'NO_BB_CAT'				=> 'The category does not exist.',
	'NO_BB_CAT_ID'			=> 'No categories specified.',
	'NO_BB_EXT'				=> 'The extension does not exist.',
	'NO_BB_EXT_ID'			=> 'No extensions specified.',
	'NO_BB_LANG'			=> 'The language package does not exist.',
	'NO_BB_LANG_ID'			=> 'No language packages specified.',
	'NO_BB_STYLE'			=> 'The style does not exist.',
	'NO_BB_STYLE_ID'		=> 'No styles specified.',
	'NO_BB_TOOL'			=> 'The tool does not exist.',
	'NO_BB_TOOL_ID'			=> 'No tools specified.',

	'STYLE_VARNAME'	=> 'Varname',

	'TOOL_VARNAME'	=> 'Varname',
));
