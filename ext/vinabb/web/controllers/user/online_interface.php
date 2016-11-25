<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\controllers\user;

/**
* Interface for the online controller
*/
interface online_interface
{
	/**
	* Main method
	*
	* @param $mode View mode
	* @return \Symfony\Component\HttpFoundation\Response
	*/
	public function main($mode);

	/**
	* Get number of online guests
	*
	* @return int
	*/
	public function get_guest_counter();
}
