<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\operators;

/**
* Interface for a set of languages
*/
interface language_interface
{
	/**
	* Get number of languages
	*
	* @return int
	*/
	public function count_langs();

	/**
	* Get all languages
	*
	* @return array
	*/
	public function get_langs();

	/**
	* Add a language
	*
	* @param \vinabb\web\entities\language_interface $entity Language entity
	* @return \vinabb\web\entities\language_interface
	*/
	public function add_lang(\vinabb\web\entities\language_interface $entity);

	/**
	* Delete a language
	*
	* @param int $id Language ID
	* @return bool True if row was deleted, false otherwise
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function delete_lang($id);
}
