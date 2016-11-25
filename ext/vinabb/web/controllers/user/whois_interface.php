<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\controllers\user;

/**
* Interface for the whois controller
*/
interface whois_interface
{
	/**
	* Main method
	*
	* @param string $session_id Session ID
	* @return \Symfony\Component\HttpFoundation\Response
	*/
	public function main($session_id);
}
