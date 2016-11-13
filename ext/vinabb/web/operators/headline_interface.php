<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\operators;

/**
* Interface for a set of headlines
*/
interface headline_interface
{
	/**
	* Get all headlines
	*
	* @return array
	*/
	public function get_headlines();

	/**
	* Add a headline
	*
	* @return \vinabb\web\entities\headline_interface
	*/
	public function add_headline();

	/**
	* Delete a headline
	*
	* @param int $id Headline ID
	* @return bool True if row was deleted, false otherwise
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function delete_headline($id);
}
