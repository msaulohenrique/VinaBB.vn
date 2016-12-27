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

		$this->controller = $phpbb_container->get('vinabb.web.acp.headlines');
		$this->language = $phpbb_container->get('language');
		$this->request = $phpbb_container->get('request');
		$this->module = $id;
		$this->mode = $mode;

		// ACP template file
		$this->tpl_name = 'acp_headlines';
		$this->page_title = $this->language->lang('ACP_HEADLINES');

		// Language
		$this->language->add_lang('acp_headlines', 'vinabb/web');

		// Requests
		$action = $this->request->variable('action', '');
		$lang = $this->request->variable('lang', '');
		$headline_id = $this->request->variable('id', 0);

		$this->controller->set_form_data([
			'u_action'		=> $this->u_action,
			'headline_lang'	=> $lang
		]);

		// Do actions via the controller
		$this->do_actions($action, $lang, $headline_id);
	}

	/**
	* Actions on the module
	*
	* @param string	$action			Action name
	* @param string	$lang			2-letter language ISO code
	* @param int	$headline_id	Headline ID
	*/
	protected function do_actions($action, $lang, $headline_id)
	{
		switch ($action)
		{
			case 'add':
				$this->tpl_name = 'acp_headlines_edit';
				$this->page_title = $this->language->lang('ADD_HEADLINE');
				$this->controller->add_headline($lang);
			// Return to stop execution of this script
			return;

			case 'edit':
				$this->tpl_name = 'acp_headlines_edit';
				$this->page_title = $this->language->lang('EDIT_HEADLINE');
				$this->controller->edit_headline($headline_id);
			// Return to stop execution of this script
			return;

			case 'move_down':
				$this->controller->move_headline($lang, $headline_id, 'down');
			break;

			case 'move_up':
				$this->controller->move_headline($lang, $headline_id, 'up');
			break;

			case 'delete':
				if (confirm_box(true))
				{
					$this->controller->delete_headline($headline_id);
				}
				else
				{
					confirm_box(false, $this->language->lang('CONFIRM_DELETE_HEADLINE'));
				}
			break;
		}

		// Select a language before doing something
		if ($lang == '')
		{
			$this->controller->select_lang();
		}
		else
		{
			$this->controller->display_headlines($lang);
		}
	}
}
