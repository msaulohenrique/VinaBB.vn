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
	'ACP_HEADLINES'			=> 'Headlines',
	'ACP_HEADLINES_EXPLAIN'	=> 'Heading titles on the index page.',

	'LOG_HEADLINE_ADD'		=> '<strong>Portal: Created new headline</strong><br>» %s',
	'LOG_HEADLINE_DELETE'	=> '<strong>Portal: Deleted headline</strong><br>» %s',
	'LOG_HEADLINE_EDIT'		=> '<strong>Portal: Edited headline</strong><br>» %s'
]);
