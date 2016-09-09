<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

use vinabb\web\includes\constants;

/**
* @ignore
*/
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
require "{$phpbb_root_path}common.{$phpEx}";

$forum_id = $request->variable('f', 0);
$start = $request->variable('start', 0);
$page = floor($start / $config['topics_per_page']) + 1;

$response = new \Symfony\Component\HttpFoundation\RedirectResponse(
	$phpbb_container->get('controller.helper')->route('vinabb_web_board_forum_route', ($start) ? array('forum_id' => $forum_id, 'page' => constants::REWRITE_URL_PAGE . $page) : array('forum_id' => $forum_id)),
	301
);
$response->send();
