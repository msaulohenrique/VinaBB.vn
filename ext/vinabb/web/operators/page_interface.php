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
	* Get number of pages
	*
	* @return int
	*/
	public function count_pages();

	/**
	* Get all pages
	*
	* @return array
	*/
	public function get_pages();

	/**
	* Add a page
	*
	* @param \vinabb\web\entities\page_interface $entity Page entity
	* @return \vinabb\web\entities\page_interface
	*/
	public function add_page(\vinabb\web\entities\page_interface $entity);

	/**
	* Delete a page
	*
	* @param int $id Page ID
	* @return bool True if row was deleted, false otherwise
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function delete_page($id);
}
