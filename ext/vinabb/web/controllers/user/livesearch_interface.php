<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\controllers\user;

/**
* Interface for the livesearch controller
*/
interface livesearch_interface
{
	/**
	* Main method
	*
	* @param string $username Keyword
	*/
	public function main($username);
}
