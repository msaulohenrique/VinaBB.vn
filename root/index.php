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
include $phpbb_root_path . 'common.' . $phpEx;

// Start session management
$user->session_begin();
$auth->acl($user->data);

$hash = $request->variable('hash', '');
$mark = $request->variable('mark', '');
$mark_time = $request->variable('mark_time', 0);

/** @var \phpbb\controller\helper $controller_helper */
$controller_helper = $phpbb_container->get('controller.helper');

$response = new \Symfony\Component\HttpFoundation\RedirectResponse(
	$controller_helper->route('vinabb_web_board_route', ($mark == 'forums') ? array('board' => 'board', 'hash' => $hash, 'mark' => 'forums', 'mark_time' => $mark_time) : array('board' => 'board')),
	301
);
$response->send();
