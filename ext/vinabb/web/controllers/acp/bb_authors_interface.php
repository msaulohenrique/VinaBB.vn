<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\controllers\acp;

/**
* Interface for the bb_authors_module
*/
interface bb_authors_interface
{
	/**
	* Set form action URL
	*
	* @param string $u_action Form action
	*/
	public function set_form_action($u_action);

	/**
	* Display authors
	*/
	public function display_authors();

	/**
	* Add an author
	*/
	public function add_author();

	/**
	* Edit an author
	*
	* @param int $author_id Author ID
	*/
	public function edit_author($author_id);

	/**
	* Process data to be added or edited
	*
	* @param \vinabb\web\entities\bb_author_interface $entity BB author entity
	*/
	public function add_edit_data(\vinabb\web\entities\bb_author_interface $entity);

	/**
	* Delete an author
	*
	* @param int $author_id Author ID
	*/
	public function delete_author($author_id);
}
