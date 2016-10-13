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
	'ACP_VINABB_SETTINGS_EXPLAIN'	=> '',

	'CHECK_PHP_LEGACY_VERSION'		=> 'Nhánh PHP còn hỗ trợ',
	'CHECK_PHP_URL'					=> 'Liên kết kiểm tra phiên bản PHP',
	'CHECK_PHP_VERSION'				=> 'Nhánh PHP mới nhất',
	'CHECK_PHPBB_LEGACY_VERSION'	=> 'Nhánh phpBB còn hỗ trợ',
	'CHECK_PHPBB_URL'				=> 'Liên kết kiểm tra phiên bản phpBB',
	'CHECK_PHPBB_VERSION'			=> 'Nhánh phpBB mới nhất',

	'DEFAULT_LANGUAGE'	=> 'Ngôn ngữ mặc định',

	'ERROR_MAINTENANCE_MODE_FOUNDER'	=> 'Không được phép đổi chế độ bảo trì thành “Sáng lập viên”.',

	'LANG_ENABLE'			=> 'Cho phép chuyển ngôn ngữ',
	'LANG_SWITCH'			=> 'Ngôn ngữ chuyển',
	'LANG_SWITCH_EXPLAIN'	=> 'Ngôn ngữ chuyển và mặc định phải khác nhau.',

	'MAINTENANCE_MODE'			=> 'Chế độ bảo trì',
	'MAINTENANCE_MODE_ADMIN'	=> 'Quản trị viên',
	'MAINTENANCE_MODE_FOUNDER'	=> 'Sáng lập viên',
	'MAINTENANCE_MODE_MOD'		=> 'Điều hành viên',
	'MAINTENANCE_MODE_NONE'		=> 'Không',
	'MAINTENANCE_MODE_USER'		=> 'Người dùng',
	'MAINTENANCE_TEXT'			=> 'Thông báo bảo trì (Tiếng Anh)',
	'MAINTENANCE_TEXT_EXPLAIN'	=> 'Để trống để dùng thông báo mặc định từ tập tin ngôn ngữ.',
	'MAINTENANCE_TEXT_VI'		=> 'Thông báo bảo trì (Tiếng Việt)',
	'MAINTENANCE_TIME'			=> 'Thời gian bảo trì',
	'MAINTENANCE_TIME_EXPLAIN'	=> 'Số phút tính từ thời điểm bạn lưu thiết lập.',
	'MAINTENANCE_TIME_REMAIN'	=> 'Thời gian còn lại',
	'MAINTENANCE_TIME_RESET'	=> 'Tạo mới thời gian',
	'MAINTENANCE_TPL'			=> 'Dùng khuôn mẫu để hiển thị',
	'MAP_ADDRESS'				=> 'Địa chỉ (Tiếng Anh)',
	'MAP_ADDRESS_VI'			=> 'Địa chỉ (Tiếng Việt)',
	'MAP_API'					=> 'Khóa Google Maps JavaScript API',
	'MAP_PHONE'					=> 'Số điện thoại',
	'MAP_PHONE_NAME'			=> 'Tên liên hệ',

	'NO_EXTRA_LANG_TO_SELECT'	=> 'Không có thêm ngôn ngữ khác để chọn.',

	'SELECT_LANGUAGE'	=> 'Chọn ngôn ngữ',
	'SOCIAL_LINKS'		=> 'Liên kết mạng xã hội',

	'VINABB_SETTINGS_UPDATED'	=> 'Đã cập nhật thiết lập VinaBB.vn.',
));
