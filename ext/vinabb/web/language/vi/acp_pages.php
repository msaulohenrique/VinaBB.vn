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
	'ADD_PAGE'	=> 'Tạo trang mới',

	'CONFIRM_DELETE_PAGE'	=> 'Bạn chắc chắn muốn xóa trang này?',

	'EDIT_PAGE'						=> 'Sửa trang',
	'ERROR_PAGE_DELETE'				=> 'Không thể xóa trang này. Lỗi: %s',
	'ERROR_PAGE_NAME_EMPTY'			=> 'Chưa nhập tiêu đề trang.',
	'ERROR_PAGE_NAME_TOO_LONG'		=> 'Tiêu đề trang quá dài.',
	'ERROR_PAGE_NAME_VI_TOO_LONG'	=> 'Tiêu đề trang tiếng Việt quá dài.',
	'ERROR_PAGE_VARNAME_DUPLICATE'	=> 'Tên biến trang “%s” bị trùng.',
	'ERROR_PAGE_VARNAME_EMPTY'		=> 'Chưa nhập tên biến trang.',
	'ERROR_PAGE_VARNAME_INVALID'	=> 'Tên biến trang không hợp lệ.',
	'ERROR_PAGE_VARNAME_TOO_LONG'	=> 'Tên biến trang quá dài.',

	'MESSAGE_PAGE_ADD'		=> 'Đã tạo trang.',
	'MESSAGE_PAGE_DELETE'	=> 'Đã xóa trang.',
	'MESSAGE_PAGE_EDIT'		=> 'Đã sửa trang.',

	'PAGE_DESC'		=> 'Giới thiệu',
	'PAGE_NAME'		=> 'Tiêu đề trang',
	'PAGE_TEXT'		=> 'Nội dung',
	'PAGE_VARNAME'	=> 'Tên biến'
]);
