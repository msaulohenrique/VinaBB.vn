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
	/** @var \vinabb\web\controllers\acp\pages_interface */
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

		// ACP template file
		$this->tpl_name = 'acp_pages';
		$this->page_title = $this->language->lang('ACP_PAGES');

		// Language
		$this->language->add_lang('acp_pages', 'vinabb/web');

		// Requests
		$action = $this->request->variable('action', '');
		$page_id = $this->request->variable('id', 0);

		$this->controller->set_form_action($this->u_action);

		// Do actions via the controller
		switch ($action)
		{
			case 'add':
				$this->tpl_name = 'acp_pages_edit';
				$this->page_title = $this->language->lang('ADD_PAGE');
				$this->controller->add_page();
			// Return to stop execution of this script
			return;

			case 'edit':
				$this->tpl_name = 'acp_pages_edit';
				$this->page_title = $this->language->lang('EDIT_PAGE');
				$this->controller->edit_page($page_id);
			// Return to stop execution of this script
			return;

			case 'delete':
				if (confirm_box(true))
				{
					$this->controller->delete_page($page_id);
				}
				else
				{
					confirm_box(false, $this->language->lang('CONFIRM_DELETE_PAGE'), build_hidden_fields([
						'i'			=> $id,
						'mode'		=> $mode,
						'action'	=> $action,
						'id'		=> $page_id
					]));
				}
			break;
		}

		// Manage pages
		$this->controller->display_pages();
	}
}
