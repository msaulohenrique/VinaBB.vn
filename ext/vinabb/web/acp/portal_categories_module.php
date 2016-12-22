<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\acp;

/**
* ACP module: acp_portal_categories
*/
class portal_categories_module
{
	/** @var \vinabb\web\controllers\acp\portal_comments_interface $controller */
	protected $controller;

	/** @var \phpbb\language\language $language */
	protected $language;

	/** @var \phpbb\request\request $request */
	protected $request;

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

		$this->controller = $phpbb_container->get('vinabb.web.acp.portal_categories');
		$this->language = $phpbb_container->get('language');
		$this->request = $phpbb_container->get('request');

		// ACP template file
		$this->tpl_name = 'acp_portal_categories';
		$this->page_title = $this->language->lang('ACP_PORTAL_CATS');

		// Language
		$this->language->add_lang('acp_portal', 'vinabb/web');

		// Requests
		$action = $this->request->variable('action', '');
		$parent_id = $this->request->variable('parent_id', 0);
		$cat_id = $this->request->variable('id', 0);

		$this->controller->set_form_action($this->u_action);

		// Do actions via the controller
		switch ($action)
		{
			case 'add':
				$this->tpl_name = 'acp_portal_categories_edit';
				$this->page_title = $this->language->lang('ADD_CAT');
				$this->controller->add_cat($parent_id);
			// Return to stop execution of this script
			return;

			case 'edit':
				$this->tpl_name = 'acp_portal_categories_edit';
				$this->page_title = $this->language->lang('EDIT_CAT');
				$this->controller->edit_cat($cat_id);
			// Return to stop execution of this script
			return;

			case 'move_down':
				$this->controller->move_cat($cat_id, 'down');
			break;

			case 'move_up':
				$this->controller->move_cat($cat_id, 'up');
			break;

			case 'delete':
				if (confirm_box(true))
				{
					$this->controller->delete_cat($cat_id);
				}
				else
				{
					confirm_box(false, $this->language->lang('CONFIRM_DELETE_CAT'), build_hidden_fields([
						'i'			=> $id,
						'mode'		=> $mode,
						'action'	=> $action,
						'id'		=> $cat_id
					]));
				}
			break;
		}

		// Manage categories
		$this->controller->display_cats($parent_id);
	}
}
