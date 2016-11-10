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
	'ACP_BB_AUTHORS'			=> 'Manage developers',
	'ACP_BB_AUTHORS_EXPLAIN'	=> '',

	'LOG_BB_AUTHOR_ADD'		=> '<strong>phpBB Resource: Added new developer</strong><br>» %s',
	'LOG_BB_AUTHOR_DELETE'	=> '<strong>phpBB Resource: Removed developer</strong><br>» %s',
	'LOG_BB_AUTHOR_DISABLE'	=> '<strong>phpBB Resource: Disabled developer</strong><br>» %s',
	'LOG_BB_AUTHOR_EDIT'	=> '<strong>phpBB Resource: Edited developer data</strong><br>» %s',
	'LOG_BB_AUTHOR_ENABLE'	=> '<strong>phpBB Resource: Enabled developer</strong><br>» %s',
]);
