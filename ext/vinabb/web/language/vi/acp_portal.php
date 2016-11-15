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
	'ADD_ARTICLE'		=> 'Tạo bài viết mới',
	'ADD_CAT'			=> 'Tạo danh mục mới',
	'ARTICLE_DESC'		=> 'Giới thiệu',
	'ARTICLE_DETAILS'	=> 'Dữ liệu bài viết',
	'ARTICLE_NAME'		=> 'Tiêu đề',
	'ARTICLE_REVISION'	=> 'Tạo mới ngày gửi',
	'ARTICLE_TEXT'		=> 'Nội dung',

	'CAT_ICON'					=> 'Biểu tượng trình đơn',
	'CAT_NAME'					=> 'Tên danh mục',
	'CAT_VARNAME'				=> 'Tên biến',
	'CONFIRM_DELETE_ARTICLE'	=> 'Bạn chắc chắn muốn xóa bài viết này?',
	'CONFIRM_DELETE_CAT'		=> 'Bạn chắc chắn muốn xóa danh mục này?',

	'EDIT_ARTICLE'						=> 'Sửa bài viết',
	'EDIT_CAT'							=> 'Sửa danh mục',
	'ERROR_ARTICLE_DESC_EMPTY'			=> 'Chưa nhập giới thiệu.',
	'ERROR_ARTICLE_DESC_TOO_LONG'		=> 'Giới thiệu quá dài.',
	'ERROR_ARTICLE_ID_EMPTY'			=> 'Bài viết không xác định.',
	'ERROR_ARTICLE_ID_NOT_EXISTS'		=> 'Bài viết không tồn tại.',
	'ERROR_ARTICLE_LANG_EMPTY'			=> 'Chưa chọn ngôn ngữ.',
	'ERROR_ARTICLE_LANG_NOT_EXISTS'		=> 'Ngôn ngữ không tồn tại.',
	'ERROR_ARTICLE_NAME_EMPTY'			=> 'Chưa nhập tên bài viết.',
	'ERROR_ARTICLE_NAME_SEO_INVALID'	=> 'Chuỗi SEO tên bài viết không hợp lệ.',
	'ERROR_ARTICLE_NAME_TOO_LONG'		=> 'Tên bài viết quá dài.',
	'ERROR_ARTICLE_TEXT_EMPTY'			=> 'Chưa nhập nội dung bài viết.',
	'ERROR_CAT_CHANGE_PARENT'			=> 'Không thể đổi danh mục cha. Lỗi: %s',
	'ERROR_CAT_DELETE'					=> 'Không thể xóa danh mục này. Lỗi: %s',
	'ERROR_CAT_DELETE_IN_USE'			=> 'Không thể xóa danh mục đang được dùng.',
	'ERROR_CAT_ID_EMPTY'				=> 'Chưa chọn danh mục.',
	'ERROR_CAT_ID_NOT_EXISTS'			=> 'Danh mục không tồn tại.',
	'ERROR_CAT_NAME_EMPTY'				=> 'Chưa nhập tên danh mục.',
	'ERROR_CAT_NAME_TOO_LONG'			=> 'Tên danh mục quá dài.',
	'ERROR_CAT_NAME_VI_TOO_LONG'		=> 'Tên danh mục tiếng Việt quá dài.',
	'ERROR_CAT_VARNAME_DUPLICATE'		=> 'Tên biến danh mục “%s” bị trùng.',
	'ERROR_CAT_VARNAME_EMPTY'			=> 'Chưa nhập tên biến danh mục.',
	'ERROR_CAT_VARNAME_INVALID'			=> 'Tên biến danh mục không hợp lệ.',
	'ERROR_CAT_VARNAME_TOO_LONG'		=> 'Tên biến danh mục quá dài.',

	'MESSAGE_ARTICLE_ADD'		=> 'Đã tạo bài viết.',
	'MESSAGE_ARTICLE_DELETE'	=> 'Đã xóa bài viết.',
	'MESSAGE_ARTICLE_EDIT'		=> 'Đã sửa bài viết.',
	'MESSAGE_CAT_ADD'			=> 'Đã tạo danh mục.',
	'MESSAGE_CAT_DELETE'		=> 'Đã xóa danh mục.',
	'MESSAGE_CAT_EDIT'			=> 'Đã sửa danh mục.'
]);
