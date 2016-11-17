<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\controllers\user;

/**
* Interface for the email controller
*/
interface email_interface
{
	/**
	* Main method
	*
	* @param string	$type	Object type (user|topic)
	* @param int	$id		User or topic ID
	* @return \Symfony\Component\HttpFoundation\Response
	*/
	public function main($type, $id);
}
