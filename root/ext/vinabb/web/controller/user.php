<?php
/**
 * This file is part of the VinaBB.vn package.
 *
 * @copyright (c) VinaBB <vinabb.vn>
 * @license GNU General Public License, version 2 (GPL-2.0)
 */

namespace vinabb\web\controller;

class user
{
	/** @var string */
	protected $phpbb_root_path;

	/** @var string */
	protected $php_ext;

	/**
	* Constructor
	*
	* @param string $phpbb_root_path
	* @param string $php_ext
	*/
	public function __construct($phpbb_root_path, $php_ext)
	{
		$this->phpbb_root_path = $phpbb_root_path;
		$this->php_ext = $php_ext;
	}

	/**
	* Controller for route /user/{username}
	*
	* @param string $username
	*/
	public function info($username)
	{
		redirect(append_sid("{$this->phpbb_root_path}memberlist.{$this->php_ext}", "mode=viewprofile&amp;un=$username"));
	}
}
