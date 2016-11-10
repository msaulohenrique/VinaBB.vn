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
	'ACP_PORTAL_COMMENTS'			=> 'Bình luận chờ duyệt',
	'ACP_PORTAL_COMMENTS_EXPLAIN'	=> '',

	'LOG_PORTAL_COMMENT_DELETE'	=> '<strong>Trang chủ: Đã từ chối bình luận</strong><br>» %s',
	'LOG_PORTAL_COMMENT_EDIT'	=> '<strong>Trang chủ: Đã duyệt bình luận</strong><br>» %s'
]);
