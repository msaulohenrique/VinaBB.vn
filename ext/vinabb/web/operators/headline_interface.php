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
	* Get number of headlines
	*
	* @param string $lang 2-letter language ISO code
	* @return int
	*/
	public function count_headlines($lang = '');

	/**
	* Get all headlines
	*
	* @param string $lang 2-letter language ISO code
	* @return array
	*/
	public function get_headlines($lang = '');

	/**
	* Add a headline
	*
	* @param \vinabb\web\entities\headline_interface $entity Headline entity
	* @return \vinabb\web\entities\headline_interface
	*/
	public function add_headline(\vinabb\web\entities\headline_interface $entity);

	/**
	* Move a headline up/down
	*
	* @param string	$lang		2-letter language ISO code
	* @param int	$id			Headline ID
	* @param string	$direction	The direction: up|down
	* @return bool True if row was moved, false otherwise
	*/
	public function move_headline($lang, $id, $direction = 'up');

	/**
	* Delete a headline
	*
	* @param int $id Headline ID
	* @return bool True if row was deleted, false otherwise
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function delete_headline($id);
}
