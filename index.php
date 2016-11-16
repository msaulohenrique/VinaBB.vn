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

	// Index page title
	$page_title = $phpbb_container->get('language')->lang('VINABB');

	/**
	* You can use this event to modify the page title and load data for the index
	*
	* @event core.index_modify_page_title
	* @var	string	page_title		Title of the index page
	* @since 3.1.0-a1
	*/
	$vars = ['page_title'];
	extract($phpbb_dispatcher->trigger_event('core.index_modify_page_title', compact($vars)));

	// Output the page
	page_header($page_title, true);

	$template->set_filenames(['body' => '@vinabb_web/portal.html']);

	page_footer();
}
else
{
	trigger_error('BOARD_UNAVAILABLE');
}
