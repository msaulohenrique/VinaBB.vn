<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\operators;

/**
* Interface for a set of phpBB resource authors
*/
interface bb_author_interface
{
	/**
	* Get number of authors
	*
	* @param string $mode 'group', 'author' or empty for both
	* @return int
	*/
	public function count_authors($mode = '');

	/**
	* Get all authors
	*
	* @param string $mode 'group', 'author' or empty for both
	* @return array
	*/
	public function get_authors($mode = '');

	/**
	* Get authors in range for pagination
	*
	* @param string	$mode			'group', 'author' or empty for both
	* @param string	$order_field	Sort by this field
	* @param int	$limit			Number of items
	* @param int	$offset			Position of the start
	* @return array
	*/
	public function list_authors($mode = '', $order_field = 'author_name', $limit = 0, $offset = 0);

	/**
	* Add an author
	*
	* @param \vinabb\web\entities\bb_author_interface $entity BB Author entity
	* @return \vinabb\web\entities\bb_author_interface
	*/
	public function add_author(\vinabb\web\entities\bb_author_interface $entity);

	/**
	* Remove authors from a group
	*
	* @param int $id Author group ID
	*/
	public function unset_group($id);

	/**
	* Delete an author
	*
	* @param int $id Author ID
	* @return bool True if row was deleted, false otherwise
	*/
	public function delete_author($id);
}
