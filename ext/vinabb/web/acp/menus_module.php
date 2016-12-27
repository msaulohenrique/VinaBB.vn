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
		$action = $this->request->variable('action', '');
		$parent_id = $this->request->variable('parent_id', 0);
		$menu_id = $this->request->variable('id', 0);

		$this->controller->set_form_action($this->u_action);

		// Do actions via the controller
		$this->do_actions($action, $parent_id, $menu_id);
	}

	/**
	* Actions on the module
	*
	* @param string	$action		Action name
	* @param int	$parent_id	Parent ID
	* @param int	$menu_id	Menu ID
	*/
	protected function do_actions($action, $parent_id, $menu_id)
	{
		switch ($action)
		{
			case 'add':
				$this->tpl_name = 'acp_menus_edit';
				$this->page_title = $this->language->lang('ADD_MENU');
				$this->controller->add_menu($parent_id);
			// Return to stop execution of this script
			return;

			case 'edit':
				$this->tpl_name = 'acp_menus_edit';
				$this->page_title = $this->language->lang('EDIT_MENU');
				$this->controller->edit_menu($menu_id);
			// Return to stop execution of this script
			return;

			case 'delete':
				if (confirm_box(true))
				{
					$this->controller->delete_menu($menu_id);
				}
				else
				{
					confirm_box(false, $this->language->lang('CONFIRM_DELETE_MENU'));
				}
			break;
		}

		$this->controller->display_menus($parent_id);
	}
}
