<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\acp;

/**
* ACP module: acp_menus
*/
class menus_module
{
	/** @var \vinabb\web\controllers\acp\menus_interface $controller */
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

	/** @var int $parent_id */
	private $parent_id;

	/** @var int $menu_id */
	private $menu_id;

	/**
	* Main method of the module
	*
	* @param string	$id		Module basename
	* @param string	$mode	Module mode
	*/
	public function main($id, $mode)
	{
		global $phpbb_container;

		$this->controller = $phpbb_container->get('vinabb.web.acp.menus');
		$this->language = $phpbb_container->get('language');
		$this->request = $phpbb_container->get('request');
		$this->module = $id;
		$this->mode = $mode;

		// ACP template file
		$this->tpl_name = 'acp_menus';
		$this->page_title = $this->language->lang('ACP_MENUS');

		// Language
		$this->language->add_lang('acp_menus', 'vinabb/web');

		// Requests
		$this->action = $this->request->variable('action', 'display');
		$this->parent_id = $this->request->variable('parent_id', 0);
		$this->menu_id = $this->request->variable('id', 0);

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
		$this->controller->display_menus($this->parent_id);
	}

	/**
	* Module action: Add
	*/
	private function action_add()
	{
		$this->tpl_name = 'acp_menus_edit';
		$this->page_title = $this->language->lang('ADD_MENU');
		$this->controller->add_menu($this->parent_id);
	}

	/**
	* Module action: Edit
	*/
	private function action_edit()
	{
		$this->tpl_name = 'acp_menus_edit';
		$this->page_title = $this->language->lang('EDIT_MENU');
		$this->controller->edit_menu($this->menu_id);
	}

	/**
	* Module action: Move Down
	*/
	private function action_move_down()
	{
		$this->controller->move_menu($this->menu_id, 'down');
		$this->action_display();
	}

	/**
	* Module action: Move Up
	*/
	private function action_move_up()
	{
		$this->controller->move_menu($this->menu_id, 'up');
		$this->action_display();
	}

	/**
	* Module action: Delete
	*/
	private function action_delete()
	{
		if (confirm_box(true))
		{
			$this->controller->delete_menu($this->menu_id);
		}
		else
		{
			confirm_box(false, $this->language->lang('CONFIRM_DELETE_MENU'));
		}

		$this->action_display();
	}
}
