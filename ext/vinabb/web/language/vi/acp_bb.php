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
	'ACP_BB_ACP_STYLES_EXPLAIN'	=> '',
	'ACP_BB_EXTS_EXPLAIN'		=> '',
	'ACP_BB_LANGS_EXPLAIN'		=> '',
	'ACP_BB_STYLES_EXPLAIN'		=> '',
	'ACP_BB_TOOLS_EXPLAIN'		=> '',
	'ADD_BB_ACP_STYLE'			=> 'Thêm giao diện mới',
	'ADD_BB_CAT'				=> 'Tạo danh mục mới',
	'ADD_BB_EXT'				=> 'Thêm gói mở rộng mới',
	'ADD_BB_LANG'				=> 'Thêm gói ngôn ngữ mới',
	'ADD_BB_STYLE'				=> 'Thêm giao diện mới',
	'ADD_BB_TOOL'				=> 'Thêm công cụ mới',

	'BB_CAT_ICON'		=> 'Biểu tượng',
	'BB_CAT_NAME'		=> 'Tên danh mục',
	'BB_CAT_VARNAME'	=> 'Tên biến',

	'CONFIRM_BB_ACP_STYLE_DELETE'	=> 'Bạn chắc chắn muốn gỡ bỏ giao diện quản trị này?',
	'CONFIRM_BB_CAT_DELETE'			=> 'Bạn chắc chắn muốn xóa danh mục này?',
	'CONFIRM_BB_EXT_DELETE'			=> 'Bạn chắc chắn muốn gỡ bỏ gói mở rộng này?',
	'CONFIRM_BB_LANG_DELETE'		=> 'Bạn chắc chắn muốn gỡ bỏ gói ngôn ngữ này?',
	'CONFIRM_BB_STYLE_DELETE'		=> 'Bạn chắc chắn muốn gỡ bỏ giao diện này?',

	'EDIT_BB_CAT'							=> 'Sửa danh mục',
	'ERROR_BB_ACP_STYLE_NAME_EMPTY'			=> 'Chưa nhập tên giao diện.',
	'ERROR_BB_ACP_STYLE_VARNAME_DUPLICATE'	=> 'Tên biến giao diện “%s” bị trùng.',
	'ERROR_BB_ACP_STYLE_VARNAME_EMPTY'		=> 'Chưa nhập tên biến giao diện.',
	'ERROR_BB_ACP_STYLE_VERSION_INVALID'	=> 'Phiên bản giao diện không hợp lệ.',
	'ERROR_BB_CAT_DELETE'					=> 'Không thể xóa danh mục đang được dùng.',
	'ERROR_BB_CAT_NAME_EMPTY'				=> 'Chưa nhập tên danh mục.',
	'ERROR_BB_CAT_VARNAME_DUPLICATE'		=> 'Tên biến danh mục “%s” bị trùng.',
	'ERROR_BB_CAT_VARNAME_EMPTY'			=> 'Chưa nhập tên biến danh mục.',
	'ERROR_BB_EXT_VARNAME_DUPLICATE'		=> 'Tên định danh gói mở rộng “%s” bị trùng.',
	'ERROR_BB_EXT_VARNAME_EMPTY'			=> 'Chưa nhập tên định danh gói mở rộng.',
	'ERROR_BB_EXT_VERSION_INVALID'			=> 'Phiên bản gói mở rộng không hợp lệ.',
	'ERROR_BB_ITEM_CAT_SELECT'				=> 'Chưa chọn danh mục.',
	'ERROR_BB_ITEM_DESC_EMPTY'				=> 'Chưa nhập giới thiệu.',
	'ERROR_BB_ITEM_PHPBB_VERSION_SELECT'	=> 'Chưa chọn phiên bản phpBB.',
	'ERROR_BB_LANG_NAME_EMPTY'				=> 'Chưa nhập tên gói ngôn ngữ.',
	'ERROR_BB_LANG_VERSION_INVALID'			=> 'Phiên bản gói ngôn ngữ không hợp lệ.',
	'ERROR_BB_STYLE_NAME_EMPTY'				=> 'Chưa nhập tên giao diện.',
	'ERROR_BB_STYLE_VARNAME_DUPLICATE'		=> 'Tên biến giao diện “%s” bị trùng.',
	'ERROR_BB_STYLE_VARNAME_EMPTY'			=> 'Chưa nhập tên biến giao diện.',
	'ERROR_BB_STYLE_VERSION_INVALID'		=> 'Phiên bản giao diện không hợp lệ.',
	'ERROR_BB_TOOL_NAME_EMPTY'				=> 'Chưa nhập tên công cụ.',
	'ERROR_BB_TOOL_VERSION_INVALID'			=> 'Phiên bản công cụ không hợp lệ.',

	'ITEM_EXT_DETAILS'		=> 'Thuộc tính gói mở rộng',
	'ITEM_LANG_DETAILS'		=> 'Thuộc tính gói ngôn ngữ',
	'ITEM_STYLE_DETAILS'	=> 'Thuộc tính giao diện',
	'ITEM_TOOL_DETAILS'		=> 'Thuộc tính công cụ',

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
