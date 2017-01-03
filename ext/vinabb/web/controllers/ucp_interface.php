<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\controllers;

/**
* Interface for the User Control Panel
*/
interface ucp_interface
{
	/**
	* UCP module
	*
	* @param string	$id		Module basename
	* @param string	$mode	Module mode
	*/
	public function main($id, $mode);

	/**
	* Logout the session
	*/
	public function logout();

	/**
	* Display agreement page
	*
	* @param string	$title		Page title
	* @param string	$message	Page content
	* @return \Symfony\Component\HttpFoundation\Response
	*/
	public function display_agreement($title = 'TERMS_USE', $message = 'TERMS_OF_USE_CONTENT');

	/**
	* Delete Cookies with dynamic names (DO NOT delete poll cookies)
	*/
	public function delete_cookies();

	/**
	* Switch permissions to another user
	*/
	public function switch_perm();

	/**
	* Restore original user permissions
	*/
	public function restore_perm();

	/**
	* Get the list of online friends
	*/
	public function display_online_friends();
}
