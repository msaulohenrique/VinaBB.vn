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

$id = $request->variable('i', '');
$mode = $request->variable('mode', '');

if (empty($id) && in_array($mode, array('activate', 'resend_act', 'sendpassword', 'register', 'confirm', 'login', 'login_link', 'logout', 'terms', 'privacy', 'delete_cookies', 'switch_perm', 'restore_perm')))
{
	$mode = 'front';
}

$response = new \Symfony\Component\HttpFoundation\RedirectResponse(
	$controller_helper->route('vinabb_web_ucp_route', array('id' => $id, 'mode' => $mode)),
	301
);
$response->send();
