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
	'ADD_MENU'	=> 'Tạo trình đơn mới',

	'CONFIRM_DELETE_MENU'	=> 'Bạn chắc chắn muốn xóa trình đơn này?',

	'EDIT_MENU'						=> 'Sửa trình đơn',
	'ERROR_MENU_CHANGE_PARENT'		=> 'Không thể đổi trình đơn cha. Lỗi: %s',
	'ERROR_MENU_DELETE'				=> 'Không thể xóa trình đơn này. Lỗi: %s',
	'ERROR_MENU_MOVE'				=> 'Không thể di chuyển trình đơn này. Lỗi: %s',
	'ERROR_MENU_NAME_EMPTY'			=> 'Chưa nhập tên trình đơn.',
	'ERROR_MENU_NAME_TOO_LONG'		=> 'Tên trình đơn quá dài.',
	'ERROR_MENU_NAME_VI_TOO_LONG'	=> 'Tên trình đơn tiếng Việt quá dài.',

	'MENU_DETAILS'			=> 'Dữ liệu trình đơn',
	'MENU_ICON'				=> 'Biểu tượng',
	'MENU_LINK'				=> 'Liên kết',
	'MENU_NAME'				=> 'Tên trình đơn',
	'MENU_PARENT'			=> 'Trình đơn cha',
	'MENU_TARGET'			=> 'Mở trang mới',
	'MENU_TYPE'				=> 'Loại trình đơn',
	'MESSAGE_MENU_ADD'		=> 'Đã tạo trình đơn.',
	'MESSAGE_MENU_DELETE'	=> 'Đã xóa trình đơn.',
	'MESSAGE_MENU_EDIT'		=> 'Đã sửa trình đơn.'
]);
