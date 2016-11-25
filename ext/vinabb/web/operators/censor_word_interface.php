<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\operators;

/**
* Interface for a set of censor words
*/
interface censor_word_interface
{
	/**
	* Get all words
	*
	* @return array
	*/
	public function get_words();

	/**
	* Add a word
	*
	* @param \vinabb\web\entities\censor_word_interface $entity Censor word entity
	* @return \vinabb\web\entities\censor_word_interface
	*/
	public function add_word(\vinabb\web\entities\censor_word_interface $entity);

	/**
	* Delete a word
	*
	* @param int $id Word ID
	* @return bool True if row was deleted, false otherwise
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function delete_word($id);
}
