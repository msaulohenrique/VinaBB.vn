<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\controllers\acp;

/**
* Interface for the menus_module
*/
interface menus_interface
{
	/**
	* Set form action URL
	*
	* @param string $u_action Form action
	*/
	public function set_form_action($u_action);

	/**
	* Display menus
	*
	* @param int $parent_id Parent ID
	*/
	public function display_menus($parent_id = 0);

	/**
	* Add a menu
	*
	* @param int $parent_id Parent ID
	*/
	public function add_menu($parent_id = 0);

	/**
	* Edit a menu
	*
	* @param int $menu_id Menu ID
	*/
	public function edit_menu($menu_id);

	/**
	* Process menu data to be added or edited
	*
	* @param \vinabb\web\entities\menu_interface $entity Menu entity
	*/
	public function add_edit_menu_data($entity);

	/**
	* Move a menu up/down
	*
	* @param int	$menu_id	Menu ID
	* @param string	$direction	The direction (up|down)
	* @param int	$amount		The number of places to move
	*/
	public function move_menu($menu_id, $direction, $amount = 1);

	/**
	* Delete a menu
	*
	* @param int $menu_id Menu ID
	*/
	public function delete_menu($menu_id);
}
