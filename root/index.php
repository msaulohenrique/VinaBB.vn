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

if ($phpbb_extension_manager->is_enabled('vinabb/web'))
{
	$phpbb_container->get('vinabb.web.portal')->index();
}
else
{
	trigger_error('Please replace front files by original phpBB files ;)');
}
