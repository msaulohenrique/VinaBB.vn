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
	/** @var \vinabb\web\controllers\acp\bb_categories_interface $controller */
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

	/** @var string $action */
	private $action;

	/** @var int $cat_id */
	private $cat_id;

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
		$this->module = $id;
		$this->mode = $mode;

		// ACP template file
		$this->tpl_name = 'acp_bb_categories';
		$this->page_title = $this->language->lang('ACP_BB_' . strtoupper($mode) . '_CATS');

		// Language
		$this->language->add_lang('acp_bb', 'vinabb/web');

		// Requests
		$this->action = $this->request->variable('action', 'display');
		$this->cat_id = $this->request->variable('id', 0);

		// Form data
		$this->controller->set_form_data([
			'u_action'	=> $this->u_action,
			'mode'		=> $mode,
			'bb_type'	=> $this->ext_helper->get_bb_type_constants($mode)
		]);

		// Do actions via the controller
		$this->{'action_' . $this->action}();
	}

	/**
	* Module action: Display (Default)
	*/
	private function action_display()
	{
		$this->controller->display_cats();
	}

	/**
	* Module action: Add
	*/
	private function action_add()
	{
		$this->tpl_name = 'acp_bb_categories_edit';
		$this->page_title = $this->language->lang('ADD_CAT');
		$this->controller->add_cat();
	}

	/**
	* Module action: Edit
	*/
	private function action_edit()
	{
		$this->tpl_name = 'acp_bb_categories_edit';
		$this->page_title = $this->language->lang('EDIT_CAT');
		$this->controller->edit_cat($this->cat_id);
	}

	/**
	* Module action: Move Down
	*/
	private function action_move_down()
	{
		$this->controller->move_cat($this->cat_id, 'down');
		$this->action_display();
	}

	/**
	* Module action: Move Up
	*/
	private function action_move_up()
	{
		$this->controller->move_cat($this->cat_id, 'up');
		$this->action_display();
	}

	/**
	* Module action: Delete
	*/
	private function action_delete()
	{
		if (confirm_box(true))
		{
			$this->controller->delete_cat($this->cat_id);
		}
		else
		{
			confirm_box(false, $this->language->lang('CONFIRM_DELETE_CAT'));
		}

		$this->action_display();
	}
}
