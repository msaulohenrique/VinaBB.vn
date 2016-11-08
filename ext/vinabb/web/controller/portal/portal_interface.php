<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\controller\portal;

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
}
