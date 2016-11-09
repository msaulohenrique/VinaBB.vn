<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\controllers\board;

interface board_interface
{
	/**
	* Board index page
	*
	* @return \Symfony\Component\HttpFoundation\Response
	*/
	public function main();
}
