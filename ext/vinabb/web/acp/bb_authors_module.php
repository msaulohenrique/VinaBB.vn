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
	/** @var \vinabb\web\controllers\acp\bb_authors_interface */
	protected $controller;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \vinabb\web\controllers\helper_interface */
	protected $ext_helper;

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

		$this->controller = $phpbb_container->get('vinabb.web.acp.bb_authors');
		$this->language = $phpbb_container->get('language');
		$this->request = $phpbb_container->get('request');
		$this->ext_helper = $phpbb_container->get('vinabb.web.helper');

		// ACP template file
		$this->tpl_name = 'acp_bb_authors';
		$this->page_title = $this->language->lang('ACP_BB_AUTHORS');

		// Language
		$this->language->add_lang('acp_bb', 'vinabb/web');

		// Requests
		$action = $this->request->variable('action', '');
		$author_id = $this->request->variable('id', 0);

		$this->controller->set_form_action($this->u_action);

		// Do actions via the controller
		switch ($action)
		{
			case 'add':
				$this->tpl_name = 'acp_bb_authors_edit';
				$this->page_title = $this->language->lang('ADD_AUTHOR');
				$this->controller->add_author();

				// Return to stop execution of this script
				return;

			case 'edit':
				$this->tpl_name = 'acp_bb_authors_edit';
				$this->page_title = $this->language->lang('EDIT_AUTHOR');
				$this->controller->edit_author($author_id);

				// Return to stop execution of this script
				return;

			case 'delete':
				if (confirm_box(true))
				{
					$this->controller->delete_author($author_id);
				}
				else
				{
					confirm_box(false, $this->language->lang('CONFIRM_DELETE_AUTHOR'), build_hidden_fields([
						'i'			=> $id,
						'mode'		=> $mode,
						'action'	=> $action,
						'id'		=> $author_id
					]));
				}
			break;
		}

		// Manage authors
		$this->controller->display_authors();
	}
}
