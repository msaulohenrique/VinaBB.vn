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
	'ADD_ARTICLE'		=> 'Tạo bài viết mới',
	'ADD_PORTAL_CAT'	=> 'Tạo danh mục mới',
	'ARTICLE_DESC'		=> 'Giới thiệu',
	'ARTICLE_DETAILS'	=> 'Dữ liệu bài viết',
	'ARTICLE_NAME'		=> 'Tiêu đề',
	'ARTICLE_REVISION'	=> 'Tạo mới ngày gửi',
	'ARTICLE_TEXT'		=> 'Nội dung',

	'CONFIRM_ARTICLE_DELETE'	=> 'Bạn chắc chắn muốn xóa bài viết này?',
	'CONFIRM_PORTAL_CAT_DELETE'	=> 'Bạn chắc chắn muốn xóa danh mục này?',

	'EDIT_PORTAL_CAT'						=> 'Sửa danh mục',
	'ERROR_ARTICLE_CAT_SELECT'				=> 'Chưa chọn danh mục.',
	'ERROR_ARTICLE_DESC_EMPTY'				=> 'Chưa nhập giới thiệu.',
	'ERROR_ARTICLE_LANG_SELECT'				=> 'Chưa chọn ngôn ngữ.',
	'ERROR_ARTICLE_NAME_EMPTY'				=> 'Chưa nhập tên bài viết.',
	'ERROR_ARTICLE_TEXT_EMPTY'				=> 'Chưa nhập nội dung bài viết.',
	'ERROR_PORTAL_CAT_DELETE'				=> 'Không thể xóa danh mục đang được dùng.',
	'ERROR_PORTAL_CAT_NAME_EMPTY'			=> 'Chưa nhập tên danh mục.',
	'ERROR_PORTAL_CAT_VARNAME_DUPLICATE'	=> 'Tên biến danh mục “%s” bị trùng.',
	'ERROR_PORTAL_CAT_VARNAME_EMPTY'		=> 'Chưa nhập tên biến danh mục.',

	'MESSAGE_PORTAL_CAT_ADD'	=> 'Đã tạo danh mục.',
	'MESSAGE_PORTAL_CAT_DELETE'	=> 'Đã xóa danh mục.',
	'MESSAGE_PORTAL_CAT_EDIT'	=> 'Đã sửa danh mục.',

	'NO_PORTAL_CAT'		=> 'Danh mục không tồn tại.',
	'NO_PORTAL_CAT_ID'	=> 'Danh mục không xác định.',

	'PORTAL_CAT_ICON'			=> 'Biểu tượng',
	'PORTAL_CAT_ICON_EXPLAIN'	=> 'Ví dụ: <code>fa fa-home</code> hoặc <code>icon-home</code>',
	'PORTAL_CAT_NAME'			=> 'Tên danh mục',
	'PORTAL_CAT_VARNAME'		=> 'Tên biến',
));
