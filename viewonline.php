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
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
require "{$phpbb_root_path}phpbb.{$phpEx}";
require "{$phpbb_root_path}common.{$phpEx}";

$mode = $request->variable('mode', '');

$response = new \Symfony\Component\HttpFoundation\RedirectResponse(
	($phpbb_extension_manager->is_enabled('vinabb/web')) ? $phpbb_container->get('controller.helper')->route('vinabb_web_online_route', (!empty($mode)) ? array('mode' => $mode) : array()) : "{$phpbb_root_path}index.{$phpEx}",
	301
);
$response->send();
