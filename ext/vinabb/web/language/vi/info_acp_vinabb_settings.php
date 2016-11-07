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
	'ACP_VINABB_SETTINGS'	=> 'Thiết lập VinaBB.vn',

	'LOG_VINABB_SETTINGS'	=> '<strong>Đã chỉnh thiết lập VinaBB.vn</strong>'
]);
