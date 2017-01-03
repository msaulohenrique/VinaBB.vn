<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\controllers\portal;

/**
* Interface for the article
*/
interface article_interface
{
	/**
	* View an article
	*
	* @param int	$article_id		Article ID
	* @param string	$tpl_filename	Template filename
	* @return \Symfony\Component\HttpFoundation\Response
	* @throws \phpbb\exception\http_exception
	*/
	public function article($article_id, $tpl_filename = 'portal_article.html');

	/**
	* Print the article
	*
	* @param int $article_id Article ID
	* @return \Symfony\Component\HttpFoundation\Response
	*/
	public function print_page($article_id);

	/**
	* Generate template variables for the article author
	*
	* @param int $user_id User ID
	*/
	public function get_author_info($user_id);

	/**
	* Display comments from the article
	*
	* @param int	$article_id		Article ID
	* @param int	$author_user_id	User ID
	*/
	public function display_comments($article_id, $author_user_id);
}
