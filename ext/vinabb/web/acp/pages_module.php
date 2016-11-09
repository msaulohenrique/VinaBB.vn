<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\acp;

class pages_module
{
	/** @var \vinabb\web\controller\acp\pages */
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

		$this->controller = $phpbb_container->get('vinabb.web.acp.pages');
		$this->language = $phpbb_container->get('language');
		$this->request = $phpbb_container->get('request');

		$this->tpl_name = 'acp_pages';
		$this->page_title = $this->language->lang('ACP_PAGES');

		// Language
		$this->language->add_lang('posting');
		$this->language->add_lang('acp_pages', 'vinabb/web');

		// Requests
		$action = $this->request->variable('action', '');
		$page_id = $this->request->variable('id', 0);

		$this->controller->set_form_action($this->u_action);

		switch ($action)
		{
			case 'add':
				$this->page_title = $this->language->lang('ADD_PAGE');
				$this->controller->add_page();

				// Return to stop execution of this script
				return;

			case 'edit':
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
						'page_id'	=> $page_id
					]));
				}
			break;
		}

		// Display pages
		$this->controller->display_pages();
	}
}
