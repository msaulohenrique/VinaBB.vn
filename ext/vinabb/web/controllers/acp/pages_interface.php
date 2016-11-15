<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\controllers\acp;

/**
* Interface for the pages_module
*/
interface pages_interface
{
	/**
	* Set form action URL
	*
	* @param string $u_action Form action
	*/
	public function set_form_action($u_action);

	/**
	* Display pages
	*/
	public function display_pages();

	/**
	* Add a page
	*/
	public function add_page();

	/**
	* Edit a page
	*
	* @param int $page_id Page ID
	*/
	public function edit_page($page_id);

	/**
	* Process page data to be added or edited
	*
	* @param \vinabb\web\entities\page_interface $entity Page entity
	*/
	public function add_edit_page_data($entity);

	/**
	* Deleta a page
	*
	* @param int $page_id Page ID
	*/
	public function delete_page($page_id);
}
