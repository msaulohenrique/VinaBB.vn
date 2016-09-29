<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\controller;

class portal
{
	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var string */
	protected $phpbb_root_path;

	/** @var string */
	protected $php_ext;

	/**
	* Constructor
	*
	* @param \phpbb\auth\auth $auth
	* @param \phpbb\db\driver\driver_interface $db
	* @param \phpbb\user $user
	* @param \phpbb\language\language $language
	* @param \phpbb\template\template $template
	* @param \phpbb\request\request $request
	* @param \phpbb\controller\helper $helper
	* @param string $phpbb_root_path
	* @param string $php_ext
	*/
	public function __construct(
		\phpbb\auth\auth $auth,
		\phpbb\db\driver\driver_interface $db,
		\phpbb\user $user,
		\phpbb\language\language $language,
		\phpbb\template\template $template,
		\phpbb\request\request $request,
		\phpbb\controller\helper $helper,
		$phpbb_root_path,
		$php_ext
	)
	{
		$this->auth = $auth;
		$this->db = $db;
		$this->user = $user;
		$this->language = $language;
		$this->template = $template;
		$this->request = $request;
		$this->helper = $helper;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->php_ext = $php_ext;
	}

	public function index()
	{
		$this->template->assign_vars(array(
			'ABC'	=> 'Hello'
		));
	}
}
