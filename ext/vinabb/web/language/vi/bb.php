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

/**
* These are errors which can be triggered by sending invalid data to the extension API.
*
* These errors will never show to a user unless they are either
* modifying the extension code OR unless they are writing an extension
* which makes calls to this extension.
*
* Translators: Do not need to translate these language strings ;)
*/
$lang = array_merge($lang, [
	'ACP_STYLE_DETAILS'	=> 'Thông tin giao diện',
	'ACP_STYLE_NAME'	=> 'Tên giao diện',
	'ACP_STYLE_VERSION'	=> 'Phiên bản giao diện',

	'EXT_DETAILS'				=> 'Thông tin gói mở rộng',
	'EXT_NAME'					=> 'Tên gói mở rộng',
	'EXT_PROPERTIES'			=> [
		'ACP_STYLE'			=> 'Giao diện quản trị',
		'CONSOLE'			=> 'Dòng lệnh',
		'CRON'				=> 'Thao tác hẹn giờ',
		'DB_DATA'			=> 'Thay đổi dữ liệu',
		'DB_SCHEMA'			=> 'Thay đổi cấu trúc dữ liệu',
		'EVENT'				=> 'Sự kiện PHP',
		'EVENT_ACP_STYLE'	=> 'Sự kiện giao diện quản trị',
		'EVENT_STYLE'		=> 'Sự kiện giao diện',
		'EXT_PHP'			=> 'Trình cài đặt gói mở rộng',
		'LANG'				=> 'Tập tin ngôn ngữ',
		'MODULE_ACP'		=> 'Gói chức năng quản trị',
		'MODULE_MCP'		=> 'Gói chức năng điều hành',
		'MODULE_UCP'		=> 'Gói chức năng người dùng',
		'NOTIFICATION'		=> 'Thông báo',
		'STYLE'				=> 'Giao diện'
	],
	'EXT_PROPERTIES_EXPLAIN'	=> [
		'ACP_STYLE'			=> 'Thêm các tập tin HTML/CSS/JS mới vào giao diện quản trị.',
		'CONSOLE'			=> 'Thêm các dòng lệnh mới.',
		'CRON'				=> 'Thêm các thao tác hẹn giờ mới.',
		'DB_DATA'			=> 'Chèn, cập nhật, xóa dòng trong cơ sở dữ liệu và sửa mục cấu hình.',
		'DB_SCHEMA'			=> 'Tạo, sửa, xóa bảng, cột và chỉ mục trong cơ sở dữ liệu.',
		'EVENT'				=> 'Sửa mã PHP thông qua các sự kiện.',
		'EVENT_ACP_STYLE'	=> 'Chèn mã HTML/CSS/JS vào tập tin khuôn mẫu quản trị thông qua các sự kiện.',
		'EVENT_STYLE'		=> 'Chèn mã HTML/CSS/JS vào tập tin khuôn mẫu thông qua các sự kiện.',
		'EXT_PHP'			=> 'Thực thi một vài thao tác trong quá trình cài đặt hay gỡ bỏ gói mở rộng.',
		'LANG'				=> 'Thêm các tập tin ngôn ngữ mới.',
		'MODULE_ACP'		=> 'Thêm các gói chức năng quản trị mới.',
		'MODULE_MCP'		=> 'Thêm các gói chức năng điều hành mới.',
		'MODULE_UCP'		=> 'Thêm các gói chức năng người dùng mới.',
		'NOTIFICATION'		=> 'Thêm các loại thông báo tương tác mới.',
		'STYLE'				=> 'Thêm các tập tin HTML/CSS/JS mới vào giao diện.'
	],
	'EXT_VERSION'				=> 'Phiên bản gói mở rộng',

	'LANG_DETAILS'	=> 'Thông tin gói ngôn ngữ',
	'LANG_NAME'		=> 'Tên gói ngôn ngữ',
	'LANG_VERSION'	=> 'Phiên bản gói ngôn ngữ',

	'STYLE_DETAILS'		=> 'Thông tin giao diện',
	'STYLE_NAME'		=> 'Tên giao diện',
	'STYLE_PROPERTIES'	=> [
		'BOOTSTRAP'		=> 'Bootstrap',
		'PRESETS'		=> 'Số bản màu',
		'PRESETS_AIO'	=> 'Tất cả bản màu trong một',
		'RESPONSIVE'	=> 'Hỗ trợ đa thiết bị',
		'SOURCE'		=> 'Hình thiết kế gốc'
	],
	'STYLE_VERSION'		=> 'Phiên bản giao diện',

	'TOOL_DETAILS'	=> 'Thông tin công cụ',
	'TOOL_NAME'		=> 'Tên công cụ',
	'TOOL_VERSION'	=> 'Phiên bản công cụ'
]);
