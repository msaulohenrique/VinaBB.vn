<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\controllers\board;

/**
* Interface for the single post page
*/
interface post_interface
{
	/**
	* Main method
	*
	* @param int	$post_id		Post ID
	* @param string	$tpl_filename	Template filename
	* @return \Symfony\Component\HttpFoundation\Response
	* @throws \phpbb\exception\http_exception
	*/
	public function main($post_id, $tpl_filename = 'viewpost_body.html');

	/**
	* Get poster data
	*
	* @param int $poster_id Poster user ID
	*/
	public function get_poster_info($poster_id);
}
