<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\operators;

/**
* Interface for a set of users
*/
interface user_interface
{
	/**
	* Get number of users
	*
	* @return int
	*/
	public function count_users();

	/**
	* Get all users
	*
	* @return array
	*/
	public function get_users();

	/**
	* Get users in range for pagination
	*
	* @param string	$order_field	Sort by this field
	* @param int	$limit			Number of items
	* @param int	$offset			Position of the start
	* @return array
	*/
	public function list_users($order_field = 'user_regdate', $limit = 0, $offset = 0);

	/**
	* Get latest users
	*
	* @param int $limit Number of items
	* @return array
	*/
	public function get_latest_users($limit = 10);

	/**
	* Add an user
	*
	* @param \vinabb\web\entities\user_interface $entity User entity
	* @return \vinabb\web\entities\user_interface
	*/
	public function add_user(\vinabb\web\entities\user_interface $entity);

	/**
	* Delete an user
	*
	* @param int $id User ID
	* @return bool True if row was deleted, false otherwise
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function delete_user($id);
}
