<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\acp;

/**
* Hidden ACP module: acp_bb_item_versions
*/
class bb_item_versions_module
{
	/** @var \vinabb\web\controllers\acp\bb_item_versions_interface $controller */
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

		$this->controller = $phpbb_container->get('vinabb.web.acp.bb_item_versions');
		$this->language = $phpbb_container->get('language');
		$this->request = $phpbb_container->get('request');
		$this->module = $id;
		$this->mode = $mode;

		// ACP template file
		$this->tpl_name = 'acp_bb_item_versions';
		$this->page_title = $this->language->lang('ACP_BB_' . strtoupper($mode) . '_VERSIONS');

		// Language
		$this->language->add_lang('acp_bb', 'vinabb/web');

		// Requests
		$action = $this->request->variable('action', '');
		$item_id = $this->request->variable('id', 0);
		$branch = $this->request->variable('branch', '');

		$this->controller->set_form_data([
			'u_action'	=> $this->u_action,
			'mode'		=> $mode,
			'item_id'	=> $item_id
		]);

		// Do actions via the controller
		$this->do_actions($action, $item_id, $branch);
	}

	/**
	* Actions on the module
	*
	* @param string	$action		Action name
	* @param int	$item_id	Item ID
	* @param string	$branch		phpBB branch
	*/
	protected function do_actions($action, $item_id, $branch)
	{
		switch ($action)
		{
			case 'add':
				$this->tpl_name = 'acp_bb_item_versions_edit';
				$this->page_title = $this->language->lang('ADD_VERSION');
				$this->controller->add_version();
			// Return to stop execution of this script
			return;

			case 'edit':
				$this->tpl_name = 'acp_bb_item_versions_edit';
				$this->page_title = $this->language->lang('EDIT_VERSION');
				$this->controller->edit_version($item_id, $branch);
			// Return to stop execution of this script
			return;

			case 'delete':
				if (confirm_box(true))
				{
					$this->controller->delete_version($item_id, $branch);
				}
				else
				{
					confirm_box(false, $this->language->lang('CONFIRM_DELETE_VERSION'));
				}
			break;
		}

		$this->controller->display_versions();
	}
}
