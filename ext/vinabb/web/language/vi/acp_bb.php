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
	'ACP_BB_ACP_STYLES_EXPLAIN'	=> '',
	'ACP_BB_EXTS_EXPLAIN'		=> '',
	'ACP_BB_LANGS_EXPLAIN'		=> '',
	'ACP_BB_STYLES_EXPLAIN'		=> '',
	'ACP_BB_TOOLS_EXPLAIN'		=> '',
	'ADD_BB_CAT'				=> 'Tạo danh mục mới',

	'BB_CAT_NAME'		=> 'Tên danh mục',
	'BB_CAT_NAME_VI'	=> 'Tên danh mục (Tiếng Việt)',
	'BB_CAT_VARNAME'	=> 'Tên biến',

	'CONFIRM_BB_CAT_DELETE'	=> 'Bạn chắc chắn muốn xóa danh mục này?',

	'ERROR_BB_CAT_DELETE'				=> 'Không thể xóa danh mục đang được dùng.',
	'ERROR_BB_CAT_NAME_EMPTY'			=> 'Chưa nhập tên danh mục.',
	'ERROR_BB_CAT_VARNAME_DUPLICATE'	=> 'Tên biến danh mục “%s” bị trùng.',
	'ERROR_BB_CAT_VARNAME_EMPTY'		=> 'Chưa nhập tên biến danh mục.',

	'MESSAGE_BB_ACP_STYLE_ADD'		=> 'Đã thêm giao diện quản trị.',
	'MESSAGE_BB_ACP_STYLE_DELETE'	=> 'Đã gỡ bỏ giao diện quản trị.',
	'MESSAGE_BB_ACP_STYLE_EDIT'		=> 'Đã sửa thông tin giao diện quản trị.',
	'MESSAGE_BB_CAT_ADD'			=> 'Đã tạo danh mục.',
	'MESSAGE_BB_CAT_DELETE'			=> 'Đã xóa danh mục.',
	'MESSAGE_BB_CAT_EDIT'			=> 'Đã sửa danh mục.',
	'MESSAGE_BB_EXT_ADD'			=> 'Đã thêm gói mở rộng.',
	'MESSAGE_BB_EXT_DELETE'			=> 'Đã gở bỏ gói mở rộng.',
	'MESSAGE_BB_EXT_EDIT'			=> 'Đã sửa thông tin gói mở rộng.',
	'MESSAGE_BB_LANG_ADD'			=> 'Đã thêm gói ngôn ngữ.',
	'MESSAGE_BB_LANG_DELETE'		=> 'Đã gỡ bỏ gói ngôn ngữ.',
	'MESSAGE_BB_LANG_EDIT'			=> 'Đã sửa thông tin gói ngôn ngữ.',
	'MESSAGE_BB_STYLE_ADD'			=> 'Đã thêm giao diện.',
	'MESSAGE_BB_STYLE_DELETE'		=> 'Đã gỡ bỏ giao diện.',
	'MESSAGE_BB_STYLE_EDIT'			=> 'Đã sửa thông tin giao diện.',
	'MESSAGE_BB_TOOL_ADD'			=> 'Đã thêm công cụ.',
	'MESSAGE_BB_TOOL_DELETE'		=> 'Đã gỡ bỏ công cụ.',
	'MESSAGE_BB_TOOL_EDIT'			=> 'Đã sửa thông tin công cụ.',

	'NO_BB_ACP_STYLE'		=> 'Giao diện quản trị không tồn tại.',
	'NO_BB_ACP_STYLE_ID'	=> 'Giao diện quản trị không xác định.',
	'NO_BB_CAT'				=> 'Danh mục không tồn tại.',
	'NO_BB_CAT_ID'			=> 'Danh mục không xác định.',
	'NO_BB_EXT'				=> 'Gói mở rộng không tồn tại.',
	'NO_BB_EXT_ID'			=> 'Gói mở rộng không xác định.',
	'NO_BB_LANG'			=> 'Gói ngôn ngữ không tồn tại.',
	'NO_BB_LANG_ID'			=> 'Gói ngôn ngữ không xác định.',
	'NO_BB_STYLE'			=> 'Giao diện không tồn tại.',
	'NO_BB_STYLE_ID'		=> 'Giao diện không xác định.',
	'NO_BB_TOOL'			=> 'Công cụ không tồn tại.',
	'NO_BB_TOOL_ID'			=> 'Công cụ không xác định.',
));