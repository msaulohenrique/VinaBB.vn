<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\operators;

/**
* Interface for a set of articles
*/
interface portal_article_interface
{
	/**
	* Get all articles
	*
	* @return array
	*/
	public function get_articles();

	/**
	* Add an article
	*
	* @param \vinabb\web\entities\portal_article_interface $entity Article entity
	* @return \vinabb\web\entities\portal_article_interface
	*/
	public function add_article($entity);

	/**
	* Delete an article
	*
	* @param int $id Article ID
	* @return bool True if row was deleted, false otherwise
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function delete_article($id);
}
