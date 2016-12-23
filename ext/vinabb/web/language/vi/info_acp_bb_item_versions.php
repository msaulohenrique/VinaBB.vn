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
	'ACP_BB_ACP_STYLE_VERSIONS'			=> 'Quản lý phiên bản giao diện quản trị',
	'ACP_BB_ACP_STYLE_VERSIONS_EXPLAIN'	=> '',
	'ACP_BB_EXT_VERSIONS'				=> 'Quản lý phiên bản gói mở rộng',
	'ACP_BB_EXT_VERSIONS_EXPLAIN'		=> '',
	'ACP_BB_LANG_VERSIONS'				=> 'Quản lý phiên bản gói ngôn ngữ',
	'ACP_BB_LANG_VERSIONS_EXPLAIN'		=> '',
	'ACP_BB_STYLE_VERSIONS'				=> 'Quản lý phiên bản giao diện',
	'ACP_BB_STYLE_VERSIONS_EXPLAIN'		=> '',
	'ACP_BB_TOOL_VERSIONS'				=> 'Quản lý phiên bản công cụ',
	'ACP_BB_TOOL_VERSIONS_EXPLAIN'		=> '',

	'LOG_BB_ACP_STYLE_VERSION_ADD'		=> '<strong>Thư viện phpBB: Đã thêm phiên bản giao diện quản trị mới</strong><br>» %s',
	'LOG_BB_ACP_STYLE_VERSION_DELETE'	=> '<strong>Thư viện phpBB: Đã xóa phiên bản giao diện quản trị</strong><br>» %s',
	'LOG_BB_ACP_STYLE_VERSION_EDIT'		=> '<strong>Thư viện phpBB: Đã sửa phiên bản giao diện quản trị</strong><br>» %s',
	'LOG_BB_EXT_VERSION_ADD'			=> '<strong>Thư viện phpBB: Đã thêm phiên bản gói mở rộng mới</strong><br>» %s',
	'LOG_BB_EXT_VERSION_DELETE'			=> '<strong>Thư viện phpBB: Đã xóa phiên bản gói mở rộng</strong><br>» %s',
	'LOG_BB_EXT_VERSION_EDIT'			=> '<strong>Thư viện phpBB: Đã sửa phiên bản gói mở rộng</strong><br>» %s',
	'LOG_BB_LANG_VERSION_ADD'			=> '<strong>Thư viện phpBB: Đã thêm phiên bản gói ngôn ngữ mới</strong><br>» %s',
	'LOG_BB_LANG_VERSION_DELETE'		=> '<strong>Thư viện phpBB: Đã xóa phiên bản gói ngôn ngữ</strong><br>» %s',
	'LOG_BB_LANG_VERSION_EDIT'			=> '<strong>Thư viện phpBB: Đã sửa phiên bản gói ngôn ngữ</strong><br>» %s',
	'LOG_BB_STYLE_VERSION_ADD'			=> '<strong>Thư viện phpBB: Đã thêm phiên bản giao diện mới</strong><br>» %s',
	'LOG_BB_STYLE_VERSION_DELETE'		=> '<strong>Thư viện phpBB: Đã xóa phiên bản giao diện</strong><br>» %s',
	'LOG_BB_STYLE_VERSION_EDIT'			=> '<strong>Thư viện phpBB: Đã sửa phiên bản giao diện</strong><br>» %s',
	'LOG_BB_TOOL_VERSION_ADD'			=> '<strong>Thư viện phpBB: Đã thêm phiên bản công cụ mới</strong><br>» %s',
	'LOG_BB_TOOL_VERSION_DELETE'		=> '<strong>Thư viện phpBB: Đã xóa phiên bản công cụ</strong><br>» %s',
	'LOG_BB_TOOL_VERSION_EDIT'			=> '<strong>Thư viện phpBB: Đã sửa phiên bản công cụ</strong><br>» %s'
]);
