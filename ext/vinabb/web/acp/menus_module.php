<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\acp;

class menus_module
{
	/** @var \vinabb\web\controller\acp\menus */
	protected $controller;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var string */
	public $tpl_name;

	/** @var string */
	public $page_title;

	/** @var string */
	public $u_action;

	/**
	* Main method of module
	*
	* @param $id
	* @param $mode
	*/
	public function main($id, $mode)
	{
		global $phpbb_container;

		$this->controller = $phpbb_container->get('vinabb.web.acp.menus');
		$this->language = $phpbb_container->get('language');
		$this->request = $phpbb_container->get('request');

		$this->tpl_name = 'acp_menus';
		$this->page_title = $this->language->lang('ACP_MENUS');

		// Language
		$this->language->add_lang('acp_menus', 'vinabb/web');

		// Requests
		$action = $this->request->variable('action', '');
		$menu_id = $this->request->variable('id', 0);

		switch ($action)
		{
			case 'add':
				$this->page_title = $this->language->lang('ACP_MENUS');
				$this->controller->add_menu();

				// Return to stop execution of this script
				return;

			case 'edit':
				$this->page_title = $this->language->lang('ACP_MENUS');
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
					confirm_box(false, $this->language->lang('CONFIRM_DELETE_MENU'), build_hidden_fields([
						'i'			=> $id,
						'mode'		=> $mode,
						'action'	=> $action,
						'page_id'	=> $menu_id
					]));
				}
			break;
		}

		// Display pages
		$this->controller->display_menus();
	}
}