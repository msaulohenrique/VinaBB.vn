<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\acp;

/**
* ACP module: acp_portal_articles
*/
class portal_articles_module
{
	/** @var \vinabb\web\controllers\acp\portal_articles_interface */
	protected $controller;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var string */
	protected $tpl_name;

	/** @var string */
	protected $page_title;

	/** @var string */
	protected $u_action;

	/**
	* Main method of the module
	*
	* @param string	$id		Module basename
	* @param string	$mode	Module mode
	*/
	public function main($id, $mode)
	{
		global $phpbb_container;

		$this->controller = $phpbb_container->get('vinabb.web.acp.portal_articles');
		$this->language = $phpbb_container->get('language');
		$this->request = $phpbb_container->get('request');

		// ACP template file
		$this->tpl_name = 'acp_portal_articles';
		$this->page_title = $this->language->lang('ACP_PORTAL_ARTICLES');

		// Language
		$this->language->add_lang('acp_portal', 'vinabb/web');

		// Requests
		$action = $this->request->variable('action', '');
		$article_id = $this->request->variable('id', 0);

		$this->controller->set_form_action($this->u_action);

		// Do actions via the controller
		switch ($action)
		{
			case 'add':
				$this->page_title = $this->language->lang('ADD_ARTICLE');
				$this->controller->add_article();

				// Return to stop execution of this script
				return;

			case 'edit':
				$this->page_title = $this->language->lang('EDIT_ARTICLE');
				$this->controller->edit_article($article_id);

				// Return to stop execution of this script
				return;

			case 'delete':
				if (confirm_box(true))
				{
					$this->controller->delete_article($article_id);
				}
				else
				{
					confirm_box(false, $this->language->lang('CONFIRM_DELETE_ARTICLE'), build_hidden_fields([
						'i'			=> $id,
						'mode'		=> $mode,
						'action'	=> $action,
						'id'		=> $article_id
					]));
				}
			break;
		}

		// Manage articles
		$this->controller->display_articles();
	}
}
