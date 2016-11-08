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
	'ACP_PAGES'			=> 'Trang',
	'ACP_PAGES_EXPLAIN'	=> 'Trang tùy biến với nội dung tĩnh.',

	'LOG_PAGE_ADD'		=> '<strong>Đã tạo trang mới</strong><br>» %s',
	'LOG_PAGE_DELETE'	=> '<strong>Đã xóa trang</strong><br>» %s',
	'LOG_PAGE_EDIT'		=> '<strong>Đã sửa trang</strong><br>» %s'
]);
