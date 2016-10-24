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
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

/**
* All language files should use UTF-8 as their encoding
* and the files must not contain a BOM.
*/

$lang = array_merge($lang, array(
	'ACP_BB_ACP_STYLES'			=> 'Quản lý giao diện quản trị',
	'ACP_BB_ACP_STYLES_EXPLAIN'	=> '',
	'ACP_BB_EXTS'				=> 'Quản lý gói mở rộng',
	'ACP_BB_EXTS_EXPLAIN'		=> '',
	'ACP_BB_LANGS'				=> 'Quản lý gói ngôn ngữ',
	'ACP_BB_LANGS_EXPLAIN'		=> '',
	'ACP_BB_STYLES'				=> 'Quản lý giao diện',
	'ACP_BB_STYLES_EXPLAIN'		=> '',
	'ACP_BB_TOOLS'				=> 'Quản lý công cụ',
	'ACP_BB_TOOLS_EXPLAIN'		=> '',

	'LOG_BB_ACP_STYLE_ADD'		=> '<strong>Thư viện phpBB: Đã thêm giao diện quản trị mới</strong><br>» %s',
	'LOG_BB_ACP_STYLE_DELETE'	=> '<strong>Thư viện phpBB: Đã xóa giao diện quản trị</strong><br>» %s',
	'LOG_BB_ACP_STYLE_DISABLE'	=> '<strong>Thư viện phpBB: Đã tắt giao diện quản trị</strong><br>» %s',
	'LOG_BB_ACP_STYLE_EDIT'		=> '<strong>Thư viện phpBB: Đã sửa giao diện quản trị</strong><br>» %s',
	'LOG_BB_ACP_STYLE_ENABLE'	=> '<strong>Thư viện phpBB: Đã bật giao diện quản trị</strong><br>» %s',
	'LOG_BB_EXT_ADD'			=> '<strong>Thư viện phpBB: Đã thêm gói mở rộng mới</strong><br>» %s',
	'LOG_BB_EXT_DELETE'			=> '<strong>Thư viện phpBB: Đã xóa gói mở rộng</strong><br>» %s',
	'LOG_BB_EXT_DISABLE'		=> '<strong>Thư viện phpBB: Đã tắt gói mở rộng</strong><br>» %s',
	'LOG_BB_EXT_EDIT'			=> '<strong>Thư viện phpBB: Đã sửa gói mở rộng</strong><br>» %s',
	'LOG_BB_EXT_ENABLE'			=> '<strong>Thư viện phpBB: Đã bật gói mở rộng</strong><br>» %s',
	'LOG_BB_LANG_ADD'			=> '<strong>Thư viện phpBB: Đã thêm gói ngôn ngữ mới</strong><br>» %s',
	'LOG_BB_LANG_DELETE'		=> '<strong>Thư viện phpBB: Đã xóa gói ngôn ngữ</strong><br>» %s',
	'LOG_BB_LANG_DISABLE'		=> '<strong>Thư viện phpBB: Đã tắt gói ngôn ngữ</strong><br>» %s',
	'LOG_BB_LANG_EDIT'			=> '<strong>Thư viện phpBB: Đã sửa gói ngôn ngữ</strong><br>» %s',
	'LOG_BB_LANG_ENABLE'		=> '<strong>Thư viện phpBB: Đã bật gói ngôn ngữ</strong><br>» %s',
	'LOG_BB_STYLE_ADD'			=> '<strong>Thư viện phpBB: Đã thêm giao diện mới</strong><br>» %s',
	'LOG_BB_STYLE_DELETE'		=> '<strong>Thư viện phpBB: Đã xóa giao diện</strong><br>» %s',
	'LOG_BB_STYLE_DISABLE'		=> '<strong>Thư viện phpBB: Đã tắt giao diện</strong><br>» %s',
	'LOG_BB_STYLE_EDIT'			=> '<strong>Thư viện phpBB: Đã sửa giao diện</strong><br>» %s',
	'LOG_BB_STYLE_ENABLE'		=> '<strong>Thư viện phpBB: Đã bật giao diện</strong><br>» %s',
	'LOG_BB_TOOL_ADD'			=> '<strong>Thư viện phpBB: Đã thêm công cụ mới</strong><br>» %s',
	'LOG_BB_TOOL_DELETE'		=> '<strong>Thư viện phpBB: Đã xóa công cụ</strong><br>» %s',
	'LOG_BB_TOOL_DISABLE'		=> '<strong>Thư viện phpBB: Đã tắt công cụ</strong><br>» %s',
	'LOG_BB_TOOL_EDIT'			=> '<strong>Thư viện phpBB: Đã sửa công cụ</strong><br>» %s',
	'LOG_BB_TOOL_ENABLE'		=> '<strong>Thư viện phpBB: Đã bật công cụ</strong><br>» %s',
));
