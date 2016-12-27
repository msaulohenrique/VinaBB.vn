<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\operators;

/**
* Interface for a set of poll options
*/
interface poll_option_interface
{
	/**
	* Get all options
	*
	* @param int $topic_id Topic ID
	* @return array
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function get_options($topic_id = 0);

	/**
	* Add an option
	*
	* @param \vinabb\web\entities\poll_option_interface $entity Poll option entity
	* @return \vinabb\web\entities\poll_option_interface
	*/
	public function add_option(\vinabb\web\entities\poll_option_interface $entity);

	/**
	* Delete an option
	*
	* @param int $id Draft ID
	* @return bool True if row was deleted, false otherwise
	*/
	public function delete_option($id);
}
