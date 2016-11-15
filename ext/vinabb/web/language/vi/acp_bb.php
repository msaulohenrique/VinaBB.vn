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
	'ACP_STYLE_VARNAME'			=> 'Tên biến',
	'ADD_AUTHOR'				=> 'Thêm người phát triển mới',
	'ADD_BB_ACP_STYLE'			=> 'Thêm giao diện mới',
	'ADD_BB_EXT'				=> 'Thêm gói mở rộng mới',
	'ADD_BB_LANG'				=> 'Thêm gói ngôn ngữ mới',
	'ADD_BB_STYLE'				=> 'Thêm giao diện mới',
	'ADD_BB_TOOL'				=> 'Thêm công cụ mới',
	'ADD_CAT'					=> 'Tạo danh mục mới',

	'CAT_DESC'						=> 'Giới thiệu',
	'CAT_ICON'						=> 'Biểu tượng trình đơn',
	'CAT_NAME'						=> 'Tên danh mục',
	'CAT_VARNAME'					=> 'Tên biến',
	'CONFIRM_DELETE_AUTHOR'			=> 'Bạn chắc chắn muốn gỡ bỏ người phát triển này?',
	'CONFIRM_DELETE_BB_ACP_STYLE'	=> 'Bạn chắc chắn muốn gỡ bỏ giao diện quản trị này?',
	'CONFIRM_DELETE_BB_EXT'			=> 'Bạn chắc chắn muốn gỡ bỏ gói mở rộng này?',
	'CONFIRM_DELETE_BB_LANG'		=> 'Bạn chắc chắn muốn gỡ bỏ gói ngôn ngữ này?',
	'CONFIRM_DELETE_BB_STYLE'		=> 'Bạn chắc chắn muốn gỡ bỏ giao diện này?',
	'CONFIRM_DELETE_BB_TOOL'		=> 'Bạn chắc chắn muốn gỡ bỏ công cụ này?',
	'CONFIRM_DELETE_CAT'			=> 'Bạn chắc chắn muốn xóa danh mục này?',

	'EDIT_AUTHOR'							=> 'Sửa người phát triển',
	'EDIT_BB_ACP_STYLE'						=> 'Sửa giao diện',
	'EDIT_BB_EXT'							=> 'Sửa gói mở rộng',
	'EDIT_BB_LANG'							=> 'Sửa gói ngôn ngữ',
	'EDIT_BB_STYLE'							=> 'Sửa giao diện',
	'EDIT_BB_TOOL'							=> 'Sửa công cụ',
	'EDIT_CAT'								=> 'Sửa danh mục',
	'ERROR_AUTHOR_GROUP_NOT_EXISTS'			=> 'Nhóm phát triển không tồn tại.',
	'ERROR_AUTHOR_ID_EMPTY'					=> 'Chưa chọn người phát triển.',
	'ERROR_AUTHOR_ID_NOT_EXISTS'			=> 'Người phát triển không tồn tại.',
	'ERROR_AUTHOR_NAME_EMPTY'				=> 'Chưa nhập tên người phát triển.',
	'ERROR_AUTHOR_NAME_SEO_INVALID'			=> 'Chuỗi SEO tên người phát triển không hợp lệ.',
	'ERROR_AUTHOR_NAME_TOO_LONG'			=> 'Tên người phát triển quá dài.',
	'ERROR_BB_ACP_STYLE_NAME_EMPTY'			=> 'Chưa nhập tên giao diện.',
	'ERROR_BB_ACP_STYLE_NAME_TOO_LONG'		=> 'Tên giao diện quá dài.',
	'ERROR_BB_ACP_STYLE_VARNAME_DUPLICATE'	=> 'Tên biến giao diện “%s” bị trùng.',
	'ERROR_BB_ACP_STYLE_VARNAME_EMPTY'		=> 'Chưa nhập tên biến giao diện.',
	'ERROR_BB_ACP_STYLE_VARNAME_INVALID'	=> 'Tên biến giao diện không hợp lệ.',
	'ERROR_BB_ACP_STYLE_VARNAME_TOO_LONG'	=> 'Tên biến giao diện quá dài.',
	'ERROR_BB_EXT_NAME_EMPTY'				=> 'Chưa nhập tên gói mở rộng.',
	'ERROR_BB_EXT_NAME_TOO_LONG'			=> 'Tên gói mở rộng quá dài.',
	'ERROR_BB_EXT_VARNAME_DUPLICATE'		=> 'Tên định danh gói mở rộng “%s” bị trùng.',
	'ERROR_BB_EXT_VARNAME_EMPTY'			=> 'Chưa nhập tên định danh gói mở rộng.',
	'ERROR_BB_EXT_VARNAME_INVALID'			=> 'Tên định danh gói mở rộng không hợp lệ.',
	'ERROR_BB_EXT_VARNAME_TOO_LONG'			=> 'Tên định danh gói mở rộng quá dài.',
	'ERROR_BB_LANG_NAME_EMPTY'				=> 'Chưa nhập tên gói ngôn ngữ.',
	'ERROR_BB_LANG_NAME_TOO_LONG'			=> 'Tên gói ngôn ngữ quá dài.',
	'ERROR_BB_LANG_VARNAME_DUPLICATE'		=> 'Tên biến gói ngôn ngữ “%s” bị trùng.',
	'ERROR_BB_LANG_VARNAME_EMPTY'			=> 'Chưa nhập tên biến gói ngôn ngữ.',
	'ERROR_BB_LANG_VARNAME_INVALID'			=> 'Tên biến gói ngôn ngữ không hợp lệ.',
	'ERROR_BB_LANG_VARNAME_TOO_LONG'		=> 'Tên biến gói ngôn ngữ quá dài.',
	'ERROR_BB_STYLE_NAME_EMPTY'				=> 'Chưa nhập tên giao diện.',
	'ERROR_BB_STYLE_NAME_TOO_LONG'			=> 'Tên giao diện quá dài.',
	'ERROR_BB_STYLE_VARNAME_DUPLICATE'		=> 'Tên biến giao diện “%s” bị trùng.',
	'ERROR_BB_STYLE_VARNAME_EMPTY'			=> 'Chưa nhập tên biến giao diện.',
	'ERROR_BB_STYLE_VARNAME_INVALID'		=> 'Tên biến giao diện không hợp lệ.',
	'ERROR_BB_STYLE_VARNAME_TOO_LONG'		=> 'Tên biến giao diện quá dài.',
	'ERROR_BB_TOOL_NAME_EMPTY'				=> 'Chưa nhập tên công cụ.',
	'ERROR_BB_TOOL_NAME_TOO_LONG'			=> 'Tên công cụ quá dài.',
	'ERROR_BB_TOOL_VARNAME_DUPLICATE'		=> 'Tên biến công cụ “%s” bị trùng.',
	'ERROR_BB_TOOL_VARNAME_EMPTY'			=> 'Chưa nhập tên biến công cụ.',
	'ERROR_BB_TOOL_VARNAME_INVALID'			=> 'Tên biến công cụ không hợp lệ.',
	'ERROR_BB_TOOL_VARNAME_TOO_LONG'		=> 'Tên biến công cụ quá dài.',
	'ERROR_CAT_DELETE_IN_USE'				=> 'Không thể xóa danh mục đang được dùng.',
	'ERROR_CAT_ID_EMPTY'					=> 'Chưa chọn danh mục.',
	'ERROR_CAT_ID_NOT_EXISTS'				=> 'Danh mục không tồn tại.',
	'ERROR_CAT_NAME_EMPTY'					=> 'Chưa nhập tên danh mục.',
	'ERROR_CAT_NAME_TOO_LONG'				=> 'Tên danh mục quá dài.',
	'ERROR_CAT_VARNAME_DUPLICATE'			=> 'Tên biến danh mục “%s” bị trùng.',
	'ERROR_CAT_VARNAME_EMPTY'				=> 'Chưa nhập tên biến danh mục.',
	'ERROR_CAT_VARNAME_INVALID'				=> 'Tên biến danh mục không hợp lệ.',
	'ERROR_CAT_VARNAME_TOO_LONG'			=> 'Tên biến danh mục quá dài.',
	'EXT_VARNAME'							=> 'Tên định danh',

	'ITEM_EXT_DETAILS'		=> 'Thuộc tính gói mở rộng',
	'ITEM_LANG_DETAILS'		=> 'Thuộc tính gói ngôn ngữ',
	'ITEM_STYLE_DETAILS'	=> 'Thuộc tính giao diện',
	'ITEM_TOOL_DETAILS'		=> 'Thuộc tính công cụ',

	'LANG_VARNAME'	=> 'Tên biến',

	'MESSAGE_AUTHPR_ADD'			=> 'Đã thêm người phát triển.',
	'MESSAGE_AUTHOR_DELETE'			=> 'Đã gỡ bỏ người phát triển.',
	'MESSAGE_AUTHOR_EDIT'			=> 'Đã sửa thông tin người phát triển.',
	'MESSAGE_BB_ACP_STYLE_ADD'		=> 'Đã thêm giao diện quản trị.',
	'MESSAGE_BB_ACP_STYLE_DELETE'	=> 'Đã gỡ bỏ giao diện quản trị.',
	'MESSAGE_BB_ACP_STYLE_EDIT'		=> 'Đã sửa thông tin giao diện quản trị.',
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
	'MESSAGE_CAT_ADD'				=> 'Đã tạo danh mục.',
	'MESSAGE_CAT_DELETE'			=> 'Đã xóa danh mục.',
	'MESSAGE_CAT_EDIT'				=> 'Đã sửa danh mục.',

	'STYLE_VARNAME'	=> 'Tên biến',

	'TOOL_VARNAME'	=> 'Tên biến'
]);
