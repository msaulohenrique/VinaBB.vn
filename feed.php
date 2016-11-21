<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Exception\InvalidParameterException;

/**
* @ignore
**/
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
require "{$phpbb_root_path}phpbb.{$phpEx}";
require "{$phpbb_root_path}common.{$phpEx}";

$forum_id = $request->variable('f', 0);
$topic_id = $request->variable('t', 0);
$mode = $request->variable('mode', '');

$controller_helper = $phpbb_container->get('controller.helper');

if ($forum_id)
{
	$url = $controller_helper->route('phpbb_feed_forum', array('forum_id' => $forum_id));
}
else if ($topic_id)
{
	$url = $controller_helper->route('phpbb_feed_topic', array('topic_id' => $topic_id));
}
else
{
	try
	{
		$url = $controller_helper->route('phpbb_feed_overall', array('mode' => $mode));
	}
	catch (InvalidParameterException $e)
	{
		$url = $controller_helper->route('phpbb_feed_index');
	}
}

$response = new RedirectResponse($url, 301);
$response->send();
