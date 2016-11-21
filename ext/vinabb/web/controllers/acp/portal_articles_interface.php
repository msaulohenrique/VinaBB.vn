<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\controllers\acp;

/**
* Interface for the portal_articles_module
*/
interface portal_articles_interface
{
	/**
	* Set form action URL
	*
	* @param string $u_action Form action
	*/
	public function set_form_action($u_action);

	/**
	* Display articles
	*/
	public function display_articles();

	/**
	* Add an article
	*/
	public function add_article();

	/**
	* Edit an article
	*
	* @param int $article_id Article ID
	*/
	public function edit_article($article_id);

	/**
	* Process data to be added or edited
	*
	* @param \vinabb\web\entities\portal_article_interface $entity Page entity
	*/
	public function add_edit_data(\vinabb\web\entities\portal_article_interface $entity);

	/**
	* Delete an article
	*
	* @param int $article_id Article ID
	*/
	public function delete_article($article_id);
}
