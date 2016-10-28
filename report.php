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

$post_id = $request->variable('p', 0);
$pm_id = $request->variable('pm', 0);

$response = new \Symfony\Component\HttpFoundation\RedirectResponse(
	$phpbb_container->get('controller.helper')->route(($post_id) ? 'phpbb_report_post_controller' : 'phpbb_report_pm_controller', array('id' => ($post_id) ? $post_id : $pm_id)),
	301
);
$response->send();
