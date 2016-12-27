<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\operators;

/**
* Interface for a set of post icons
*/
interface post_icon_interface
{
	/**
	* Get number of icons
	*
	* @return int
	*/
	public function count_icons();

	/**
	* Get all icons
	*
	* @return array
	*/
	public function get_icons();

	/**
	* Add an icon
	*
	* @param \vinabb\web\entities\post_icon_interface $entity Icon entity
	* @return \vinabb\web\entities\post_icon_interface
	*/
	public function add_icon(\vinabb\web\entities\post_icon_interface $entity);

	/**
	* Delete an icon
	*
	* @param int $id Icon ID
	* @return bool True if row was deleted, false otherwise
	*/
	public function delete_icon($id);
}
