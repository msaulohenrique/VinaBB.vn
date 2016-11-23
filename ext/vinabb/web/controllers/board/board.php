<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\controllers\board;

class board implements board_interface
{
	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var \vinabb\web\controllers\helper_interface */
	protected $ext_helper;

	/** @var string */
	protected $root_path;

	/** @var string */
	protected $php_ext;

	/**
	* Constructor
	*
	* @param \phpbb\auth\auth $auth
	* @param \phpbb\config\config $config
	* @param \phpbb\db\driver\driver_interface $db
	* @param \phpbb\language\language $language
	* @param \phpbb\template\template $template
	* @param \phpbb\user $user
	* @param \phpbb\controller\helper $helper
	* @param \vinabb\web\controllers\helper_interface $ext_helper
	* @param string $root_path
	* @param string $php_ext
	*/
	public function __construct(
		\phpbb\auth\auth $auth,
		\phpbb\config\config $config,
		\phpbb\db\driver\driver_interface $db,
		\phpbb\language\language $language,
		\phpbb\template\template $template,
		\phpbb\user $user,
		\phpbb\controller\helper $helper,
		\vinabb\web\controllers\helper_interface $ext_helper,
		$root_path,
		$php_ext
	)
	{
		$this->auth = $auth;
		$this->config = $config;
		$this->db = $db;
		$this->language = $language;
		$this->template = $template;
		$this->user = $user;
		$this->helper = $helper;
		$this->ext_helper = $ext_helper;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;
	}

	/**
	* Board index page
	*
	* @return \Symfony\Component\HttpFoundation\Response
	*/
	public function main()
	{
		// Common functions
		include "{$this->root_path}includes/functions_display.{$this->php_ext}";

		// Language
		$this->language->add_lang('viewforum');

		// Display available forums
		display_forums('', $this->config['load_moderators']);

		// Breadcrumb
		$this->ext_helper->set_breadcrumb($this->language->lang('BOARD'));

		// Assign index specific vars
		$this->template->assign_vars([
			'S_BOARD'	=> true,

			'U_MARK_FORUMS'	=> ($this->user->data['is_registered'] || $this->config['load_anon_lastread']) ? $this->helper->route('vinabb_web_board_route', ['hash' => generate_link_hash('global'), 'mark' => 'forums', 'mark_time' => time()]) : ''
		]);

		return $this->helper->render('index_body.html', $this->language->lang('BOARD'));
	}
}
