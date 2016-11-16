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
	* Set form action URL
	*
	* @param string $u_action Form action
	*/
	public function set_form_action($u_action);

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
	* Process headline data to be added or edited
	*
	* @param \vinabb\web\entities\headline_interface $entity Headline entity
	*/
	public function add_edit_headline_data($entity);

	/**
	* Delete a headline
	*
	* @param int $headline_id Headline ID
	*/
	public function delete_headline($headline_id);
}
