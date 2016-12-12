<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\controllers\portal;

/**
* Interface for the portal and news category
*/
interface portal_interface
{
	/**
	* Index page
	*
	* @param bool $index_page	true: Use on the index page (Get x latest articles from all categories - cached)
	*							false: Use with a news category (Get all articles from that category)
	*/
	public function index($index_page = true);

	/**
	* Alternative method for index page
	*
	* @return \Symfony\Component\HttpFoundation\Response
	*/
	public function news();

	/**
	* Display articles from a news category
	*
	* @param string	$varname	URL varname
	* @param string	$page		The page number
	*
	* @return \Symfony\Component\HttpFoundation\Response
	*/
	public function category($varname, $page);

	/**
	* List news articles with pagination
	*
	* @param string	$lang			2-letter language ISO code
	* @param int	$cat_id			Category ID
	* @param array	$articles		Array of articles
	* @param int	$article_count	Number of articles
	* @param int	$limit			Articles per page
	* @param int	$offset			Position of the start
	*
	* @return int Position of the start
	*/
	public function list_articles($lang, $cat_id, &$articles, &$article_count, $limit = 0, $offset = 0);
}
