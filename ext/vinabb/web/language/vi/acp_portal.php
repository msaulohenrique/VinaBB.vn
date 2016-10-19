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
	'ADD_PORTAL_CAT'	=> 'Tạo danh mục mới',

	'PORTAL_CAT_NAME'		=> 'Tên danh mục',
	'PORTAL_CAT_NAME_VI'	=> 'Tên danh mục (Tiếng Việt)',
	'PORTAL_CAT_VARNAME'	=> 'Tên biến',

	'CONFIRM_PORTAL_CAT_DELETE'	=> 'Bạn chắc chắn muốn xóa danh mục này?',

	'ERROR_PORTAL_CAT_DELETE'				=> 'Không thể xóa danh mục đang được dùng.',
	'ERROR_PORTAL_CAT_NAME_EMPTY'			=> 'Chưa nhập tên danh mục.',
	'ERROR_PORTAL_CAT_VARNAME_DUPLICATE'	=> 'Tên biến danh mục “%s” bị trùng.',
	'ERROR_PORTAL_CAT_VARNAME_EMPTY'		=> 'Chưa nhập tên biến danh mục.',

	'MESSAGE_PORTAL_CAT_ADD'	=> 'Đã tạo danh mục.',
	'MESSAGE_PORTAL_CAT_DELETE'	=> 'Đã xóa danh mục.',
	'MESSAGE_PORTAL_CAT_EDIT'	=> 'Đã sửa danh mục.',

	'NO_PORTAL_CAT'		=> 'Danh mục không tồn tại.',
	'NO_PORTAL_CAT_ID'	=> 'Danh mục không xác định.',
));
