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
	$lang = array();
}

/**
* All language files should use UTF-8 as their encoding
* and the files must not contain a BOM.
*/

$lang = array_merge($lang, array(
	'ACP_PORTAL_ARTICLES'			=> 'Quản lý bài viết',
	'ACP_PORTAL_ARTICLES_EXPLAIN'	=> '',

	'LOG_PORTAL_ARTICLE_ADD'	=> '<strong>Trang chủ: Đã tạo bài viết mới</strong><br>» %s',
	'LOG_PORTAL_ARTICLE_DELETE'	=> '<strong>Trang chủ: Đã xóa bài viết</strong><br>» %s',
	'LOG_PORTAL_ARTICLE_EDIT'	=> '<strong>Trang chủ: Đã sửa bài viết</strong><br>» %s',
));
