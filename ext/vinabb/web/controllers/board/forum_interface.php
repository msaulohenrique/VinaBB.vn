<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\controllers\board;

/**
* Interface for the forum page
*/
interface forum_interface
{
	/**
	* Main method
	*
	* @param int	$forum_id	Forum ID
	* @param string $page		Page number
	*/
	public function main($forum_id, $page);
}
