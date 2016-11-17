<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\controllers\user;

/**
* Interface for the messenger controller
*/
interface messenger_interface
{
	/**
	* Main method
	*
	* @param string	$action		Service type
	* @param int	$user_id	User ID
	* @return \Symfony\Component\HttpFoundation\Response
	*/
	public function main($action, $user_id);
}
