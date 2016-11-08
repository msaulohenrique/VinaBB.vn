<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\operators;

/**
* Interface for a set of pages
*/
interface page_interface
{
	/**
	* Get all pages
	*
	* @return array
	*/
	public function get_pages();

	/**
	* Add a page
	*
	* @return \vinabb\web\entity\page_interface
	*/
	public function add_page();

	/**
	* Delete a page
	*
	* @param int $id Page ID
	* @return bool True if row was deleted, false otherwise
	* @throws \vinabb\web\exception\out_of_bounds
	*/
	public function delete_page($id);
}
