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
	'ACP_PORTAL_CATS'			=> 'Danh mục tin tức',
	'ACP_PORTAL_CATS_EXPLAIN'	=> 'Danh mục tin tức hiện trên trình đơn trái của trang chủ.',

	'LOG_PORTAL_CAT_ADD'	=> '<strong>Trang chủ: Đã tạo danh mục mới</strong><br>» %s',
	'LOG_PORTAL_CAT_DELETE'	=> '<strong>Trang chủ: Đã xóa danh mục</strong><br>» %s',
	'LOG_PORTAL_CAT_EDIT'	=> '<strong>Trang chủ: Đã sửa danh mục</strong><br>» %s',
));
