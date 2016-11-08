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
	'ACP_PAGES'			=> 'Pages',
	'ACP_PAGES_EXPLAIN'	=> 'Custom pages with the static content.',

	'LOG_PAGE_ADD'		=> '<strong>Created new page</strong><br>» %s',
	'LOG_PAGE_DELETE'	=> '<strong>Deleted page</strong><br>» %s',
	'LOG_PAGE_EDIT'		=> '<strong>Edited page</strong><br>» %s'
]);
