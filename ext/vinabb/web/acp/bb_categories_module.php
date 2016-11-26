<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\acp;

/**
* ACP module: acp_bb_categories
*/
class bb_categories_module
{
	/** @var \vinabb\web\controllers\acp\bb_categories_interface */
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

		$this->controller = $phpbb_container->get('vinabb.web.acp.bb_categories');
		$this->language = $phpbb_container->get('language');
		$this->request = $phpbb_container->get('request');
		$this->ext_helper = $phpbb_container->get('vinabb.web.helper');

		// phpBB resource types
		$bb_type = $this->ext_helper->get_bb_type_constants($mode);
		$lang_key = strtoupper($mode);

		// ACP template file
		$this->tpl_name = 'acp_bb_categories';
		$this->page_title = $this->language->lang('ACP_BB_' . $lang_key . '_CATS');

		// Language
		$this->language->add_lang('acp_bb', 'vinabb/web');

		// Requests
		$action = $this->request->variable('action', '');
		$cat_id = $this->request->variable('id', 0);

		$this->controller->set_form_action($this->u_action);
		$this->controller->set_bb_type($bb_type, $mode);

		// Do actions via the controller
		switch ($action)
		{
			case 'add':
				$this->tpl_name = 'acp_bb_categories_edit';
				$this->page_title = $this->language->lang('ADD_CAT');
				$this->controller->add_cat();
			// Return to stop execution of this script
			return;

			case 'edit':
				$this->tpl_name = 'acp_bb_categories_edit';
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
		$this->controller->display_cats();
	}
}
