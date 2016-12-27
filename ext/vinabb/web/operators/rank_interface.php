<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\operators;

/**
* Interface for a set of ranks
*/
interface rank_interface
{
	/**
	* Get all ranks
	*
	* @return array
	*/
	public function get_ranks();

	/**
	* Add a rank
	*
	* @param \vinabb\web\entities\rank_interface $entity Rank entity
	* @return \vinabb\web\entities\rank_interface
	*/
	public function add_rank(\vinabb\web\entities\rank_interface $entity);

	/**
	* Delete a rank
	*
	* @param int $id Rank ID
	* @return bool True if row was deleted, false otherwise
	*/
	public function delete_rank($id);
}
