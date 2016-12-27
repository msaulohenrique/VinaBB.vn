<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\acp;

/**
* ACP module: acp_bb_items
*/
class bb_items_module
{
	/** @var \vinabb\web\controllers\acp\bb_items_interface $controller */
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

	/**
	* Main method of the module
	*
	* @param string	$id		Module basename
	* @param string	$mode	Module mode
	*/
	public function main($id, $mode)
	{
		global $phpbb_container;

		$this->controller = $phpbb_container->get('vinabb.web.acp.bb_items');
		$this->language = $phpbb_container->get('language');
		$this->request = $phpbb_container->get('request');
		$this->ext_helper = $phpbb_container->get('vinabb.web.helper');
		$this->module = $id;
		$this->mode = $mode;

		// ACP template file
		$this->tpl_name = 'acp_bb_items';
		$this->page_title = $this->language->lang('ACP_BB_' . strtoupper($mode) . 'S');

		// Language
		$this->language->add_lang('acp_bb', 'vinabb/web');

		// Requests
		$action = $this->request->variable('action', '');
		$item_id = $this->request->variable('id', 0);

		$this->controller->set_form_data([
			'u_action'	=> $this->u_action,
			'mode'		=> $mode,
			'bb_type'	=> $this->ext_helper->get_bb_type_constants($mode)
		]);

		// Do actions via the controller
		$this->do_actions($action, $item_id);
	}

	/**
	* Actions on the module
	*
	* @param string	$action		Action name
	* @param int	$item_id	Item ID
	*/
	protected function do_actions($action, $item_id)
	{
		switch ($action)
		{
			case 'add':
				$this->tpl_name = 'acp_bb_items_edit';
				$this->page_title = $this->language->lang('ADD_BB_' . strtoupper($this->mode));
				$this->controller->add_item();
			// Return to stop execution of this script
			return;

			case 'edit':
				$this->tpl_name = 'acp_bb_items_edit';
				$this->page_title = $this->language->lang('EDIT_BB_' . strtoupper($this->mode));
				$this->controller->edit_item($item_id);
			// Return to stop execution of this script
			return;

			case 'delete':
				if (confirm_box(true))
				{
					$this->controller->delete_item($item_id);
				}
				else
				{
					confirm_box(false, $this->language->lang('CONFIRM_DELETE_BB_' . strtoupper($this->mode)));
				}
			break;
		}

		$this->controller->display_items();
	}
}
