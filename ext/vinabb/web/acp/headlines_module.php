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
	/** @var \vinabb\web\controllers\acp\headlines_interface $controller */
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

	/** @var string $action */
	private $action;

	/** @var string $lang */
	private $lang;

	/** @var int $headline_id */
	private $headline_id;

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
		$this->action = $this->request->variable('action', 'display');
		$this->lang = $this->request->variable('lang', '');
		$this->headline_id = $this->request->variable('id', 0);

		// Form data
		$this->controller->set_form_data([
			'u_action'		=> $this->u_action,
			'headline_lang'	=> $this->lang
		]);

		// Do actions via the controller
		$this->{'action_' . $this->action}();
	}

	/**
	* Module action: Display (Default)
	*/
	private function action_display()
	{
		// Select a language before doing something
		if ($this->lang == '')
		{
			$this->controller->select_lang();
		}
		else
		{
			$this->controller->display_headlines($this->lang);
		}
	}

	/**
	* Module action: Add
	*/
	private function action_add()
	{
		$this->tpl_name = 'acp_headlines_edit';
		$this->page_title = $this->language->lang('ADD_HEADLINE');
		$this->controller->add_headline($this->lang);
	}

	/**
	* Module action: Edit
	*/
	private function action_edit()
	{
		$this->tpl_name = 'acp_headlines_edit';
		$this->page_title = $this->language->lang('EDIT_HEADLINE');
		$this->controller->edit_headline($this->headline_id);
	}

	/**
	* Module action: Move Down
	*/
	private function action_move_down()
	{
		$this->controller->move_headline($this->lang, $this->headline_id, 'down');
		$this->action_display();
	}

	/**
	* Module action: Move Up
	*/
	private function action_move_up()
	{
		$this->controller->move_headline($this->lang, $this->headline_id, 'up');
		$this->action_display();
	}

	/**
	* Module action: Delete
	*/
	private function action_delete()
	{
		if (confirm_box(true))
		{
			$this->controller->delete_headline($this->headline_id);
		}
		else
		{
			confirm_box(false, $this->language->lang('CONFIRM_DELETE_HEADLINE'));
		}

		$this->action_display();
	}
}
