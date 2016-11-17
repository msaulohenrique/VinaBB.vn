<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\controllers\user;

/**
* Contact us by sending an email to the board contact email
*/
class contact implements contact_interface
{
	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\message\admin_form */
	protected $message_form_admin;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var string */
	protected $root_path;

	/** @var string */
	protected $php_ext;

	/**
	* Constructor
	*
	* @param \phpbb\language\language $language
	* @param \phpbb\message\admin_form $message_form_admin
	* @param \phpbb\request\request $request
	* @param \phpbb\template\template $template
	* @param \phpbb\controller\helper $helper
	* @param string $root_path
	* @param string $php_ext
	*/
	public function __construct(
		\phpbb\language\language $language,
		\phpbb\message\admin_form $message_form_admin,
		\phpbb\request\request $request,
		\phpbb\template\template $template,
		\phpbb\controller\helper $helper,
		$root_path,
		$php_ext
	)
	{
		$this->language = $language;
		$this->message_form_admin = $message_form_admin;
		$this->request = $request;
		$this->template = $template;
		$this->helper = $helper;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;
	}

	/**
	* Main method
	*
	* @return \Symfony\Component\HttpFoundation\Response
	*/
	public function main()
	{
		define('SKIP_CHECK_BAN', true);
		define('SKIP_CHECK_DISABLED', true);

		if (!class_exists('messenger'))
		{
			include "{$this->root_path}includes/functions_messenger.{$this->php_ext}";
		}

		// Language
		$this->language->add_lang('memberlist');

		$this->message_form_admin->bind($this->request);
		$error = $this->message_form_admin->check_allow();

		if ($error)
		{
			trigger_error($error);
		}

		if ($this->request->is_set_post('submit'))
		{
			$messenger = new \messenger(false);
			$this->message_form_admin->submit($messenger);
		}

		$this->message_form_admin->render($this->template);

		return $this->helper->render($this->message_form_admin->get_template_file(), $this->message_form_admin->get_page_title());
	}
}
