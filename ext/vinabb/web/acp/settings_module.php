<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\acp;

/**
* ACP module: acp_vinabb_settings
*/
class settings_module
{
	/** @var \vinabb\web\controllers\acp\settings_interface $controller */
	protected $controller;

	/** @var \phpbb\language\language $language */
	protected $language;

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

		$this->controller = $phpbb_container->get('vinabb.web.acp.settings');
		$this->language = $phpbb_container->get('language');
		$this->module = $id;
		$this->mode = $mode;

		// ACP template file
		$this->tpl_name = 'acp_settings';
		$this->page_title = $this->language->lang('ACP_VINABB_' . strtoupper($mode) . '_SETTINGS');

		// Language
		$this->language->add_lang('acp_settings', 'vinabb/web');

		$this->controller->set_form_action($this->u_action);

		// Do actions via the controller
		$this->controller->{'display_' . $mode . '_settings'}();
	}
}
