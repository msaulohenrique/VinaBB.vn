<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\acp;

/**
* ACP module: acp_portal_comments
*/
class portal_comments_module
{
	/** @var \vinabb\web\controllers\acp\portal_comments_interface $controller */
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

		$this->controller = $phpbb_container->get('vinabb.web.acp.portal_comments');
		$this->language = $phpbb_container->get('language');
		$this->request = $phpbb_container->get('request');
		$this->module = $id;
		$this->mode = $mode;

		// ACP template file
		$this->tpl_name = 'acp_portal_comments';
		$this->page_title = $this->language->lang('ACP_PORTAL_COMMENTS');

		// Language
		$this->language->add_lang('acp_portal', 'vinabb/web');

		// Requests
		$action = $this->request->variable('action', '');
		$comment_id = $this->request->variable('id', 0);

		$this->controller->set_form_action($this->u_action);

		// Do actions via the controller
		$this->do_actions($action, $comment_id);
	}

	/**
	* Actions on the module
	*
	* @param string	$action		Action name
	* @param int	$comment_id	Comment ID
	*/
	protected function do_actions($action, $comment_id)
	{
		switch ($action)
		{
			case 'edit':
				$this->tpl_name = 'acp_portal_comments_edit';
				$this->page_title = $this->language->lang('EDIT_COMMENT');
				$this->controller->edit_comment($comment_id);
			// Return to stop execution of this script
			return;

			case 'delete':
				if (confirm_box(true))
				{
					$this->controller->delete_comment($comment_id);
				}
				else
				{
					confirm_box(false, $this->language->lang('CONFIRM_DELETE_COMMENT'));
				}
			break;
		}

		$this->controller->display_comments();
	}
}
