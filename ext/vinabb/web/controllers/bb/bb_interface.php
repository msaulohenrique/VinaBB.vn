<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\controllers\bb;

interface bb_interface
{
	/**
	* List categories of each resource types (bb_type)
	*
	* @param $type phpBB resource type URL varname
	* @return \Symfony\Component\HttpFoundation\Response
	*/
	public function index($type);
}
