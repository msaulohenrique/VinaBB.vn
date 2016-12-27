<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\acp;

/**
* ACP module: acp_pages
*/
class pages_module
{
	/** @var \vinabb\web\controllers\acp\portal_comments_interface $controller */
	protected $controller;

	/** @var \phpbb\language\language $language */
	protected $language;

	/** @var \phpbb\request\request $request */
	protected $request;

	/** @var string $module */
	protected $module;

	/** @var string $mode */
	protected $mode;

	/** @var string $tpl_name */
	public $tpl_name;

	/** @var string $page_title */
	public $page_title;

	/** @var string $u_action */
	public $u_action;

	/** @var string $action */
	private $action;

	/** @var int $page_id */
	private $page_id;

	/**
	* Main method of the module
	*
	* @param string	$id		Module basename
	* @param string	$mode	Module mode
	*/
	public function main($id, $mode)
	{
		global $phpbb_container;

		$this->controller = $phpbb_container->get('vinabb.web.acp.pages');
		$this->language = $phpbb_container->get('language');
		$this->request = $phpbb_container->get('request');
		$this->module = $id;
		$this->mode = $mode;

		// ACP template file
		$this->tpl_name = 'acp_pages';
		$this->page_title = $this->language->lang('ACP_PAGES');

		// Language
		$this->language->add_lang('acp_pages', 'vinabb/web');

		// Requests
		$this->action = $this->request->variable('action', 'display');
		$this->page_id = $this->request->variable('id', 0);

		// Form data
		$this->controller->set_form_action($this->u_action);

		// Do actions via the controller
		$this->{'action_' . $this->action}();
	}

	/**
	* Module action: Display (Default)
	*/
	private function action_display()
	{
		$this->controller->display_pages();
	}

	/**
	* Module action: Add
	*/
	private function action_add()
	{
		$this->tpl_name = 'acp_pages_edit';
		$this->page_title = $this->language->lang('ADD_PAGE');
		$this->controller->add_page();
	}

	/**
	* Module action: Edit
	*/
	private function action_edit()
	{
		$this->tpl_name = 'acp_pages_edit';
		$this->page_title = $this->language->lang('EDIT_PAGE');
		$this->controller->edit_page($this->page_id);
	}

	/**
	* Module action: Delete
	*/
	private function action_delete()
	{
		if (confirm_box(true))
		{
			$this->controller->delete_page($this->page_id);
		}
		else
		{
			confirm_box(false, $this->language->lang('CONFIRM_DELETE_PAGE'));
		}

		$this->action_display();
	}
}
