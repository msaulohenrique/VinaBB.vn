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
	/** @var \vinabb\web\controllers\acp\settings_interface */
	protected $controller;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var string */
	protected $tpl_name;

	/** @var string */
	protected $page_title;

	/** @var string */
	protected $u_action;

	/**
	* Main method of the module
	*
	* @param $id
	* @param $mode
	*/
	public function main($id, $mode)
	{
		global $phpbb_container;

		$this->controller = $phpbb_container->get('vinabb.web.acp.settings');
		$this->language = $phpbb_container->get('language');

		// Language
		$this->language->add_lang('acp_settings', 'vinabb/web');

		$this->controller->set_form_action($this->u_action);

		// Do actions via the controller
		switch ($mode)
		{
			case 'version':
				$this->tpl_name = 'acp_settings_version';
				$this->page_title = $this->language->lang('ACP_VINABB_SETTINGS_VERSION');
				$this->controller->display_version_settings();
			break;

			case 'setup':
				$this->tpl_name = 'acp_settings_setup';
				$this->page_title = $this->language->lang('ACP_VINABB_SETTINGS_SETUP');
				$this->controller->display_setup_settings();
			break;

			default:
			case 'main':
				$this->tpl_name = 'acp_settings';
				$this->page_title = $this->language->lang('ACP_VINABB_SETTINGS');
				$this->controller->display_main_settings();
			break;
		}
	}
}
