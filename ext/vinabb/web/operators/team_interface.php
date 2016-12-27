<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\operators;

/**
* Interface for a set of teams
*/
interface team_interface
{
	/**
	* Get all teams
	*
	* @return array
	*/
	public function get_teams();

	/**
	* Add a team
	*
	* @param \vinabb\web\entities\team_interface $entity Team entity
	* @return \vinabb\web\entities\team_interface
	*/
	public function add_team(\vinabb\web\entities\team_interface $entity);

	/**
	* Delete a team
	*
	* @param int $id Team ID
	* @return bool True if row was deleted, false otherwise
	*/
	public function delete_team($id);
}
