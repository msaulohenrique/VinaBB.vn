<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\controllers\acp;

/**
* Interface for the headlines_module
*/
interface headlines_interface
{
	/**
	* Set form data
	*
	* @param array $data Form data
	*/
	public function set_form_data($data);

	/**
	* Language selection
	*/
	public function select_lang();

	/**
	* Display headlines
	*/
	public function display_headlines();

	/**
	* Add a headline
	*/
	public function add_headline();

	/**
	* Edit a headline
	*
	* @param int $headline_id Headline ID
	*/
	public function edit_headline($headline_id);

	/**
	* Process data to be added or edited
	*
	* @param \vinabb\web\entities\headline_interface $entity Headline entity
	*/
	public function add_edit_data(\vinabb\web\entities\headline_interface $entity);

	/**
	* Move a headline up/down
	*
	* @param int	$headline_id	Headline ID
	* @param string	$direction		The direction (up|down)
	*/
	public function move_headline($headline_id, $direction);

	/**
	* Delete a headline
	*
	* @param int $headline_id Headline ID
	*/
	public function delete_headline($headline_id);
}
