<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\controllers\user;

interface online_interface
{
	/**
	* 'Who is online' page
	*
	* @param $mode View mode
	* @return \Symfony\Component\HttpFoundation\Response
	*/
	public function main($mode);

	/**
	* Whois requested
	*
	* @param string $session_id Session ID
	* @return \Symfony\Component\HttpFoundation\Response
	*/
	public function whois($session_id);
}
