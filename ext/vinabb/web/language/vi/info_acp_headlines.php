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
	'ACP_HEADLINES'			=> 'Quảng cáo',
	'ACP_HEADLINES_EXPLAIN'	=> 'Các hình ảnh giới thiệu trên trang chủ.',

	'LOG_HEADLINE_ADD'		=> '<strong>Trang chủ: Đã tạo quảng cáo mới</strong><br>» %s',
	'LOG_HEADLINE_DELETE'	=> '<strong>Trang chủ: Đã xóa quảng cáo</strong><br>» %s',
	'LOG_HEADLINE_EDIT'		=> '<strong>Trang chủ: Đã sửa quảng cáo</strong><br>» %s'
]);
