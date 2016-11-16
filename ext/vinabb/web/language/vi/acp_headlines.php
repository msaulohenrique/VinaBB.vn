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
	'ADD_HEADLINE'	=> 'Tạo quảng cáo mới',

	'CONFIRM_DELETE_HEADLINE'	=> 'Bạn chắc chắn muốn xóa quảng cáo này?',

	'EDIT_HEADLINE'						=> 'Sửa quảng cáo',
	'ERROR_HEADLINE_DELETE'				=> 'Không thể xóa quảng cáo này. Lỗi: %s',
	'ERROR_HEADLINE_DESC_EMPTY'			=> 'Chưa nhập giới thiệu.',
	'ERROR_HEADLINE_DESC_TOO_LONG'		=> 'Giới thiệu quá dài.',
	'ERROR_HEADLINE_IMG_EMPTY'			=> 'Chưa chọn hình quảng cáo.',
	'ERROR_HEADLINE_LANG_EMPTY'			=> 'Chưa chọn ngôn ngữ.',
	'ERROR_HEADLINE_LANG_NOT_EXISTS'	=> 'Ngôn ngữ không tồn tại.',
	'ERROR_HEADLINE_NAME_EMPTY'			=> 'Chưa nhập tiêu đề.',
	'ERROR_HEADLINE_NAME_TOO_LONG'		=> 'Tiêu đề quá dài.',
	'ERROR_HEADLINE_URL_EMPTY'			=> 'Chưa nhập liên kết.',

	'HEADLINE_DESC'	=> 'Giới thiệu',
	'HEADLINE_IMG'	=> 'Hình quảng cáo',
	'HEADLINE_NAME'	=> 'Tiêu đề',
	'HEADLINE_URL'	=> 'Liên kết',

	'MESSAGE_HEADLINE_ADD'		=> 'Đã tạo quảng cáo.',
	'MESSAGE_HEADLINE_DELETE'	=> 'Đã xóa quảng cáo.',
	'MESSAGE_HEADLINE_EDIT'		=> 'Đã sửa quảng cáo.'
]);
