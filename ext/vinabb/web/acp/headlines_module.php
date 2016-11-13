<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\acp;

/**
* ACP module: acp_headlines
*/
class headlines_module
{
	/** @var \vinabb\web\controllers\acp\headlines_interface */
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

		$this->controller = $phpbb_container->get('vinabb.web.acp.headlines');
		$this->language = $phpbb_container->get('language');
		$this->request = $phpbb_container->get('request');

		// ACP template file
		$this->tpl_name = 'acp_headlines';
		$this->page_title = $this->language->lang('ACP_HEADLINES');

		// Language
		$this->language->add_lang('acp_menus', 'vinabb/web');

		// Requests
		$action = $this->request->variable('action', '');
		$headline_id = $this->request->variable('id', 0);

		$this->controller->set_form_action($this->u_action);

		// Do actions via the controller
		switch ($action)
		{
			case 'add':
				$this->tpl_name = 'acp_headlines_edit';
				$this->page_title = $this->language->lang('ADD_HEADLINE');
				$this->controller->add_headline();

				// Return to stop execution of this script
				return;

			case 'edit':
				$this->tpl_name = 'acp_headlines_edit';
				$this->page_title = $this->language->lang('EDIT_HEADLINE');
				$this->controller->edit_headline($headline_id);

				// Return to stop execution of this script
				return;

			case 'move_down':
				$this->controller->move_headline($headline_id, 'down');
			break;

			case 'move_up':
				$this->controller->move_headline($headline_id, 'up');
			break;

			case 'delete':
				if (confirm_box(true))
				{
					$this->controller->delete_headline($headline_id);
				}
				else
				{
					confirm_box(false, $this->language->lang('CONFIRM_DELETE_HEADLINE'), build_hidden_fields([
						'i'			=> $id,
						'mode'		=> $mode,
						'action'	=> $action,
						'id'		=> $headline_id
					]));
				}
			break;
		}

		// Manage menus
		$this->controller->display_headlines();
	}
}
