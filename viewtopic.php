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

if ($phpbb_extension_manager->is_enabled('vinabb/web'))
{
	// Constants
	$constants = $phpbb_container->get('vinabb.web.constants');

	// Parameters from old URLs
	$forum_id = $request->variable('f', 0);
	$topic_id = $request->variable('t', 0);
	$post_id = $request->variable('p', 0);
	$start = $request->variable('start', 0);

	// Build new URL parameters
	if ($post_id)
	{
		$route_name = 'vinabb_web_board_post_route';
		$url_params['forum_id'] = $forum_id ? $forum_id : $constants::REWRITE_URL_FORUM_ZERO;
		$url_params['topic_id'] = $topic_id;
		$url_params['post_id'] = $post_id;
	}
	else
	{
		$route_name = 'vinabb_web_board_topic_route';
		$url_params['forum_id'] = $forum_id ? $forum_id : $constants::REWRITE_URL_FORUM_ZERO;
		$url_params['topic_id'] = $topic_id;

		// Convert from 'start=' to '/page-{x}'
		if ($start)
		{
			$url_params['page'] = $constants::REWRITE_URL_PAGE . (floor($start / $config['posts_per_page']) + 1);
		}
	}

	$url = $phpbb_container->get('controller.helper')->route($route_name, $url_params);
}
else
{
	$url = "{$phpbb_root_path}index.{$phpEx}";
}

// Let's go to our new home and send to search engines the HTTP response '301 Moved Permanently'
$response = new \Symfony\Component\HttpFoundation\RedirectResponse($url, 301);
$response->send();
