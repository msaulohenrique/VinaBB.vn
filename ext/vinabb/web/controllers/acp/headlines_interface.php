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
	*
	* @param string $lang 2-letter language ISO code
	*/
	public function display_headlines($lang = '');

	/**
	* Add a headline
	*
	* @param string $lang 2-letter language ISO code
	*/
	public function add_headline($lang = '');

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
	* @param string	$lang			2-letter language ISO code
	* @param int	$headline_id	Headline ID
	* @param string	$direction		The direction (up|down)
	*/
	public function move_headline($lang, $headline_id, $direction);

	/**
	* Delete a headline
	*
	* @param int $headline_id Headline ID
	*/
	public function delete_headline($headline_id);
}
