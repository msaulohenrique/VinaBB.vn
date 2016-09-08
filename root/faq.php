<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

/**
* @ignore
*/
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
require "{$phpbb_root_path}common.{$phpEx}";

$mode = $request->variable('mode', '');

$response = new \Symfony\Component\HttpFoundation\RedirectResponse(
	$controller_helper->route(
		($mode == 'bbcode') ? 'phpbb_help_bbcode_controller' : 'phpbb_help_faq_controller'
	),
	301
);
$response->send();
