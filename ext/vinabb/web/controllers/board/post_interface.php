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
	* @param int $post_id Post ID
	* @return \Symfony\Component\HttpFoundation\Response
	*/
	public function main($post_id);
}
