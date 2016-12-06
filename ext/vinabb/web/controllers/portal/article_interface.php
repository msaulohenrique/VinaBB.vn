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
	* View details an article
	*
	* @param int	$article_id	Article ID
	* @param bool	$print		Print mode
	* @return \Symfony\Component\HttpFoundation\Response
	*/
	public function article($article_id, $print = false);

	/**
	* Print the article
	*
	* @param int $article_id Article ID
	* @return \Symfony\Component\HttpFoundation\Response
	*/
	public function print_page($article_id);
}
