<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\controller\acp;

interface portal_categories_interface
{
	/**
	* Display the news categories
	*
	* @return void
	*/
	public function display_cats();

	/**
	* Add a news category
	*
	* @return void
	*/
	public function add_cat();

	/**
	* Edit a news category
	*
	* @param int	$cat_id	Category ID
	* @return void
	*/
	public function edit_cat($cat_id);

	/**
	* Move a news category up/down
	*
	* @param int	$cat_id		Category ID
	* @param string	$direction	The direction (up|down)
	* @param int	$amount		The number of places to move the rule
	* @return void
	*/
	public function move_cat($cat_id, $direction, $amount = 1);

	/**
	* Delete a news category
	*
	* @param int	$cat_id	Category ID
	* @return void
	*/
	public function delete_cat($cat_id);

	/**
	* Set page URL
	*
	* @param string	$u_action	Form action
	* @return void
	*/
	public function set_page_url($u_action);
}
