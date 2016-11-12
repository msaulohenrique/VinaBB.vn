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
	'ACP_VINABB_SETTINGS'					=> 'Thiết lập chung',
	'ACP_VINABB_SETTINGS_EXPLAIN'			=> '',
	'ACP_VINABB_SETTINGS_SETUP'				=> 'Thiết lập một lần',
	'ACP_VINABB_SETTINGS_SETUP_EXPLAIN'		=> '',
	'ACP_VINABB_SETTINGS_VERSION'			=> 'Thiết lập phiên bản',
	'ACP_VINABB_SETTINGS_VERSION_EXPLAIN'	=> '',

	'LOG_VINABB_CHANGE_CHECK_PHP_BRANCH'			=> '<strong>VinaBB: Đã đổi nhánh PHP mới nhất cần kiểm tra</strong><br>» Nhánh cũ: %1$s<br>» Nhánh mới: %2$s',
	'LOG_VINABB_CHANGE_CHECK_PHP_LEGACY_BRANCH'		=> '<strong>VinaBB: Đã đổi nhánh PHP còn hỗ trợ cần kiểm tra</strong><br>» Nhánh cũ: %1$s<br>» Nhánh mới: %2$s',
	'LOG_VINABB_CHANGE_CHECK_PHPBB_BRANCH'			=> '<strong>VinaBB: Đã đổi nhánh phpBB mới nhất cần kiểm tra</strong><br>» Nhánh cũ: %1$s<br>» Nhánh mới: %2$s',
	'LOG_VINABB_CHANGE_CHECK_PHPBB_DEV_BRANCH'		=> '<strong>VinaBB: Đã đổi nhánh phpBB phát triển cần kiểm tra</strong><br>» Nhánh cũ: %1$s<br>» Nhánh mới: %2$s',
	'LOG_VINABB_CHANGE_CHECK_PHPBB_LEGACY_BRANCH'	=> '<strong>VinaBB: Đã đổi nhánh phpBB còn hỗ trợ cần kiểm tra</strong><br>» Nhánh cũ: %1$s<br>» Nhánh mới: %2$s',
	'LOG_VINABB_SETTINGS'							=> '<strong>VinaBB: Đã chỉnh thiết lập chung</strong>',
	'LOG_VINABB_SETTINGS_SETUP'						=> '<strong>VinaBB: Đã chỉnh thiết lập một lần</strong>',
	'LOG_VINABB_SETTINGS_VERSION'					=> '<strong>VinaBB: Đã chỉnh thiết lập phiên bản</strong>'
]);
