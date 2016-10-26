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
	'ACP_BB_ACP_STYLE_CATS'			=> 'Danh mục giao diện quản trị',
	'ACP_BB_ACP_STYLE_CATS_EXPLAIN'	=> '',
	'ACP_BB_EXT_CATS'				=> 'Danh mục gói mở rộng',
	'ACP_BB_EXT_CATS_EXPLAIN'		=> '',
	'ACP_BB_LANG_CATS'				=> 'Danh mục ngôn ngữ',
	'ACP_BB_LANG_CATS_EXPLAIN'		=> '',
	'ACP_BB_STYLE_CATS'				=> 'Danh mục giao diện',
	'ACP_BB_STYLE_CATS_EXPLAIN'		=> '',
	'ACP_BB_TOOL_CATS'				=> 'Danh mục công cụ',
	'ACP_BB_TOOL_CATS_EXPLAIN'		=> '',

	'LOG_BB_ACP_STYLE_CAT_ADD'		=> '<strong>Thư viện phpBB: Đã tạo danh mục giao diện quản trị mới</strong><br>» %s',
	'LOG_BB_ACP_STYLE_CAT_DELETE'	=> '<strong>Thư viện phpBB: Đã xóa danh mục giao diện quản trị</strong><br>» %s',
	'LOG_BB_ACP_STYLE_CAT_EDIT'		=> '<strong>Thư viện phpBB: Đã sửa danh mục giao diện quản trị</strong><br>» %s',
	'LOG_BB_EXT_CAT_ADD'			=> '<strong>Thư viện phpBB: Đã tạo danh mục gói mở rộng mới</strong><br>» %s',
	'LOG_BB_EXT_CAT_DELETE'			=> '<strong>Thư viện phpBB: Đã xóa danh mục gói mở rộng</strong><br>» %s',
	'LOG_BB_EXT_CAT_EDIT'			=> '<strong>Thư viện phpBB: Đã sửa danh mục gói mở rộng</strong><br>» %s',
	'LOG_BB_LANG_CAT_ADD'			=> '<strong>Thư viện phpBB: Đã tạo danh mục ngôn ngữ mới</strong><br>» %s',
	'LOG_BB_LANG_CAT_DELETE'		=> '<strong>Thư viện phpBB: Đã xóa danh mục ngôn ngữ</strong><br>» %s',
	'LOG_BB_LANG_CAT_EDIT'			=> '<strong>Thư viện phpBB: Đã sửa danh mục ngôn ngữ</strong><br>» %s',
	'LOG_BB_STYLE_CAT_ADD'			=> '<strong>Thư viện phpBB: Đã tạo danh mục giao diện mới</strong><br>» %s',
	'LOG_BB_STYLE_CAT_DELETE'		=> '<strong>Thư viện phpBB: Đã xóa danh mục giao diện</strong><br>» %s',
	'LOG_BB_STYLE_CAT_EDIT'			=> '<strong>Thư viện phpBB: Đã sửa danh mục giao diện</strong><br>» %s',
	'LOG_BB_TOOL_CAT_ADD'			=> '<strong>Thư viện phpBB: Đã tạo danh mục công cụ mới</strong><br>» %s',
	'LOG_BB_TOOL_CAT_DELETE'		=> '<strong>Thư viện phpBB: Đã xóa danh mục công cụ</strong><br>» %s',
	'LOG_BB_TOOL_CAT_EDIT'			=> '<strong>Thư viện phpBB: Đã sửa danh mục công cụ</strong><br>» %s',
));
