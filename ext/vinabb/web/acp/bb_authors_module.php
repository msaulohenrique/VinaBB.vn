<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\acp;

/**
* ACP module: acp_bb_authors
*/
class bb_authors_module
{
	/** @var \vinabb\web\controllers\acp\bb_authors_interface $controller */
	protected $controller;

	/** @var \phpbb\language\language $language */
	protected $language;

	/** @var \phpbb\request\request $request */
	protected $request;

	/** @var \vinabb\web\controllers\helper_interface $ext_helper */
	protected $ext_helper;

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

	/** @var string $author_id */
	private $author_id;

	/**
	* Main method of the module
	*
	* @param string	$id		Module basename
	* @param string	$mode	Module mode
	*/
	public function main($id, $mode)
	{
		global $phpbb_container;

		$this->controller = $phpbb_container->get('vinabb.web.acp.bb_authors');
		$this->language = $phpbb_container->get('language');
		$this->request = $phpbb_container->get('request');
		$this->ext_helper = $phpbb_container->get('vinabb.web.helper');
		$this->module = $id;
		$this->mode = $mode;

		// ACP template file
		$this->tpl_name = 'acp_bb_authors';
		$this->page_title = $this->language->lang('ACP_BB_AUTHORS');

		// Language
		$this->language->add_lang('acp_bb', 'vinabb/web');

		// Requests
		$this->action = $this->request->variable('action', 'display');
		$this->author_id = $this->request->variable('id', 0);

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
		$this->controller->display_authors();
	}

	/**
	* Module action: Add
	*/
	private function action_add()
	{
		$this->tpl_name = 'acp_bb_authors_edit';
		$this->page_title = $this->language->lang('ADD_AUTHOR');
		$this->controller->add_author();
	}

	/**
	* Module action: Edit
	*/
	private function action_edit()
	{
		$this->tpl_name = 'acp_bb_authors_edit';
		$this->page_title = $this->language->lang('EDIT_AUTHOR');
		$this->controller->edit_author($this->author_id);
	}

	/**
	* Module action: Delete
	*/
	private function action_delete()
	{
		if (confirm_box(true))
		{
			$this->controller->delete_author($this->author_id);
		}
		else
		{
			confirm_box(false, $this->language->lang('CONFIRM_DELETE_AUTHOR'));
		}

		$this->action_display();
	}
}
