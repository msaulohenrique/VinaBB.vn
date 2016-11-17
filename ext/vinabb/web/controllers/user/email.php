<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\controllers\user;

/**
* Sending an email with/without topic link to an user
*/
class email implements email_interface
{
	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\message\topic_form */
	protected $message_form_topic;

	/** @var \phpbb\message\user_form */
	protected $message_form_user;

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
	* @param \phpbb\message\topic_form $message_form_topic
	* @param \phpbb\message\user_form $message_form_user
	* @param \phpbb\request\request $request
	* @param \phpbb\template\template $template
	* @param \phpbb\controller\helper $helper
	* @param string $root_path
	* @param string $php_ext
	*/
	public function __construct(
		\phpbb\language\language $language,
		\phpbb\message\topic_form $message_form_topic,
		\phpbb\message\user_form $message_form_user,
		\phpbb\request\request $request,
		\phpbb\template\template $template,
		\phpbb\controller\helper $helper,
		$root_path,
		$php_ext
	)
	{
		$this->language = $language;
		$this->message_form_topic = $message_form_topic;
		$this->message_form_user = $message_form_user;
		$this->request = $request;
		$this->template = $template;
		$this->helper = $helper;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;
	}

	/**
	* Main method
	*
	* @param string	$type	Object type (user|topic)
	* @param int	$id		User or topic ID
	* @return \Symfony\Component\HttpFoundation\Response
	*/
	public function main($type, $id)
	{
		if (!class_exists('messenger'))
		{
			include "{$this->root_path}includes/functions_messenger.{$this->php_ext}";
		}

		// Language
		$this->language->add_lang('memberlist');

		$form = ($type == 'topic') ? $this->message_form_topic : $this->message_form_user;
		$form->bind_with_id($this->request, $id);
		$error = $form->check_allow();
		
		if ($error)
		{
			trigger_error($error);
		}

		if ($this->request->is_set_post('submit'))
		{
			$messenger = new \messenger(false);
			$form->submit($messenger);
		}
		
		$form->render($this->template);

		return $this->helper->render($form->get_template_file(), $form->get_page_title());
	}
}
