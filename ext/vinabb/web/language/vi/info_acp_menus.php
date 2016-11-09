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
	'ACP_MENUS'			=> 'Trình đơn',
	'ACP_MENUS_EXPLAIN'	=> 'Trình đơn xuất hiện đầu mỗi trang.',

	'LOG_MENU_ADD'		=> '<strong>Đã tạo trình đơn mới</strong><br>» %s',
	'LOG_MENU_DELETE'	=> '<strong>Đã xóa trình đơn</strong><br>» %s',
	'LOG_MENU_EDIT'		=> '<strong>Đã sửa trình đơn</strong><br>» %s'
]);
