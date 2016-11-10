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
	'ACP_BB_AUTHORS'			=> 'Người phát triển',
	'ACP_BB_AUTHORS_EXPLAIN'	=> '',

	'LOG_BB_AUTHOR_ADD'		=> '<strong>Thư viện phpBB: Đã thêm người phát triển mới</strong><br>» %s',
	'LOG_BB_AUTHOR_DELETE'	=> '<strong>Thư viện phpBB: Đã gỡ bỏ người phát triển</strong><br>» %s',
	'LOG_BB_AUTHOR_DISABLE'	=> '<strong>Thư viện phpBB: Đã tắt người phát triển</strong><br>» %s',
	'LOG_BB_AUTHOR_EDIT'	=> '<strong>Thư viện phpBB: Đã sửa dữ liệu người phát triển</strong><br>» %s',
	'LOG_BB_AUTHOR_ENABLE'	=> '<strong>Thư viện phpBB: Đã bật người phát triển</strong><br>» %s',
]);
