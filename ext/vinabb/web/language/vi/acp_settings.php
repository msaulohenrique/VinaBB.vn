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
	'CHECK_PHP_BRANCH'					=> 'Nhánh PHP mới nhất',
	'CHECK_PHP_LEGACY_BRANCH'			=> 'Nhánh PHP còn hỗ trợ',
	'CHECK_PHP_URL'						=> 'Liên kết kiểm tra phiên bản PHP',
	'CHECK_PHPBB_BRANCH'				=> 'Nhánh phpBB mới nhất',
	'CHECK_PHPBB_DEV_BRANCH'			=> 'Nhánh phpBB phát triển',
	'CHECK_PHPBB_DOWNLOAD_DEV_URL'		=> 'Liên kết tải về phpBB thử nghiệm',
	'CHECK_PHPBB_DOWNLOAD_URL'			=> 'Liên kết tải về phpBB',
	'CHECK_PHPBB_DOWNLOAD_URL_EXPLAIN'	=> 'Biến có sẵn: <code>{branch}, {version}</code>',
	'CHECK_PHPBB_LEGACY_BRANCH'			=> 'Nhánh phpBB còn hỗ trợ',
	'CHECK_PHPBB_URL'					=> 'Liên kết kiểm tra phiên bản phpBB',

	'DEFAULT_LANGUAGE'	=> 'Ngôn ngữ mặc định',
	'DONATE_EMAIL'		=> 'Hộp thư',
	'DONATE_FUND'		=> 'Số dư hiện tại',
	'DONATE_PAYPAL'		=> 'Trang PayPal',
	'DONATE_YEAR'		=> 'Năm đóng góp',
	'DONATE_YEAR_VALUE'	=> 'Số tiền đóng góp',

	'ERROR_MAINTENANCE_MODE_FOUNDER'		=> 'Không được phép đổi chế độ bảo trì thành “Sáng lập viên”.',
	'ERROR_CHECK_PHP_BRANCH_REGEX'			=> 'Số phiên bản nhánh PHP mới nhất không hợp lệ.',
	'ERROR_CHECK_PHP_LEGACY_BRANCH_REGEX'	=> 'Số phiên bản nhánh PHP còn hỗ trợ không hợp lệ.',
	'ERROR_CHECK_PHPBB_BRANCH_REGEX'		=> 'Số phiên bản nhánh phpBB mới nhất không hợp lệ.',
	'ERROR_CHECK_PHPBB_DEV_BRANCH_REGEX'	=> 'Số phiên bản nhánh phpBB phát triển không hợp lệ.',
	'ERROR_CHECK_PHPBB_LEGACY_BRANCH_REGEX'	=> 'Số phiên bản nhánh phpBB còn hỗ trợ không hợp lệ.',

	'FORUM_ID_ENGLISH'					=> 'Danh mục tiếng Anh',
	'FORUM_ID_ENGLISH_DISCUSSION'		=> 'Thảo luận (Tiếng Anh)',
	'FORUM_ID_ENGLISH_SUPPORT'			=> 'Hỗ trợ (Tiếng Anh)',
	'FORUM_ID_ENGLISH_TUTORIAL'			=> 'Hướng dẫn (Tiếng Anh)',
	'FORUM_ID_VIETNAMESE'				=> 'Danh mục tiếng Việt',
	'FORUM_ID_VIETNAMESE_DISCUSSION'	=> 'Thảo luận (Tiếng Việt)',
	'FORUM_ID_VIETNAMESE_EXT'			=> 'Gói mở rộng (Tiếng Việt)',
	'FORUM_ID_VIETNAMESE_STYLE'			=> 'Giao diện (Tiếng Việt)',
	'FORUM_ID_VIETNAMESE_SUPPORT'		=> 'Hỗ trợ (Tiếng Việt)',
	'FORUM_ID_VIETNAMESE_TUTORIAL'		=> 'Hướng dẫn (Tiếng Việt)',

	'LANG_ENABLE'			=> 'Cho phép chuyển ngôn ngữ',
	'LANG_SWITCH'			=> 'Ngôn ngữ chuyển',
	'LANG_SWITCH_EXPLAIN'	=> 'Ngôn ngữ chuyển và mặc định phải khác nhau.',

	'MAINTENANCE_MODE'					=> 'Chế độ bảo trì',
	'MAINTENANCE_MODE_ADMIN'			=> 'Quản trị viên',
	'MAINTENANCE_MODE_FOUNDER'			=> 'Sáng lập viên',
	'MAINTENANCE_MODE_MOD'				=> 'Điều hành viên',
	'MAINTENANCE_MODE_NONE'				=> 'Không',
	'MAINTENANCE_MODE_USER'				=> 'Người dùng',
	'MAINTENANCE_TEXT'					=> 'Thông báo bảo trì',
	'MAINTENANCE_TEXT_EXPLAIN'			=> 'Để trống để dùng thông báo mặc định từ tập tin ngôn ngữ.',
	'MAINTENANCE_TIME'					=> 'Thời gian bảo trì',
	'MAINTENANCE_TIME_EXPLAIN'			=> 'Số phút tính từ thời điểm bạn lưu thiết lập.',
	'MAINTENANCE_TIME_REMAIN'			=> 'Thời gian còn lại',
	'MAINTENANCE_TIME_RESET'			=> 'Tạo mới thời gian',
	'MAINTENANCE_TPL'					=> 'Dùng khuôn mẫu để hiển thị',
	'MANAGER_NAME'						=> 'Tên công ty',
	'MANAGER_USER_ID'					=> 'ID tài khoản quản lý',
	'MANAGER_USERNAME'					=> 'Tên tài khoản quản lý',
	'MAP_ADDRESS'						=> 'Địa chỉ',
	'MAP_API'							=> 'Khóa Google Maps JavaScript API',
	'MAP_PHONE'							=> 'Số điện thoại',
	'MAP_PHONE_NAME'					=> 'Tên liên hệ',
	'MESSAGE_MAIN_SETTINGS_UPDATE'		=> 'Đã cập nhật thiết lập chung.',
	'MESSAGE_SETUP_SETTINGS_UPDATE'		=> 'Đã cập nhật thiết lập một lần.',
	'MESSAGE_VERSION_SETTINGS_UPDATE'	=> 'Đã cập nhật thiết lập phiên bản.',

	'SELECT_LANGUAGE'	=> 'Chọn ngôn ngữ',
	'SOCIAL_LINKS'		=> 'Liên kết mạng xã hội'
]);
