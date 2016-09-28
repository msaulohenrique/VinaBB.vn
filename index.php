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

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup();

if ($phpbb_extension_manager->is_enabled('vinabb/web'))
{
	// Include the portal controller
	$phpbb_container->get('vinabb.web.portal')->index();

	// Output the page
	page_header($phpbb_container->get('language')->lang('VINABB'));
	
	$template->set_filenames(array(
		'body' => '@vinabb_web/portal_body.html'
	));

	page_footer();
}
else
{
	trigger_error('BOARD_UNAVAILABLE');
}
