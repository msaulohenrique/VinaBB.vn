<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\controllers\bb;

use vinabb\web\includes\constants;

/**
* Controller for phpBB resource categories
*/
class bb implements bb_interface
{
	/** @var \vinabb\web\controllers\cache\service_interface $cache */
	protected $cache;

	/** @var \phpbb\config\config $config */
	protected $config;

	/** @var \phpbb\db\driver\driver_interface $db */
	protected $db;

	/** @var \phpbb\language\language $language */
	protected $language;

	/** @var \phpbb\request\request $request */
	protected $request;

	/** @var \phpbb\template\template $template */
	protected $template;

	/** @var \phpbb\user $user */
	protected $user;

	/** @var \phpbb\controller\helper $helper */
	protected $helper;

	/** @var \vinabb\web\controllers\helper_interface $ext_helper */
	protected $ext_helper;

	/**
	* Constructor
	*
	* @param \vinabb\web\controllers\cache\service_interface	$cache		Cache service
	* @param \phpbb\config\config								$config		Config object
	* @param \phpbb\db\driver\driver_interface					$db			Database object
	* @param \phpbb\language\language							$language	Language object
	* @param \phpbb\request\request								$request	Request object
	* @param \phpbb\template\template							$template	Template object
	* @param \phpbb\user										$user		User object
	* @param \phpbb\controller\helper							$helper		Controller helper
	* @param \vinabb\web\controllers\helper_interface			$ext_helper	Extension helper
	*/
	public function __construct(
		\vinabb\web\controllers\cache\service_interface $cache,
		\phpbb\config\config $config,
		\phpbb\db\driver\driver_interface $db,
		\phpbb\language\language $language,
		\phpbb\request\request $request,
		\phpbb\template\template $template,
		\phpbb\user $user,
		\phpbb\controller\helper $helper,
		\vinabb\web\controllers\helper_interface $ext_helper
	)
	{
		$this->cache = $cache;
		$this->config = $config;
		$this->db = $db;
		$this->language = $language;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->helper = $helper;
		$this->ext_helper = $ext_helper;
	}

	/**
	* List categories of each resource types (bb_type)
	*
	* @param $type phpBB resource type URL varname
	* @return \Symfony\Component\HttpFoundation\Response
	*/
	public function index($type)
	{
		$type = $this->convert_bb_type_varname($type);

		if (!empty($type))
		{
			$this->template->assign_vars([
				'S_BB_' . strtoupper($type) . 'S'	=> true
			]);
		}
		// Default mode is 'Statistics'
		else
		{
			$this->template->assign_vars([
				'S_BB_STATS'	=> true
			]);
		}

		// Breadcrumb
		$this->ext_helper->set_breadcrumb($this->language->lang('BB'), $this->helper->route('vinabb_web_bb_route'));
		$this->ext_helper->set_breadcrumb(!empty($type) ? $this->language->lang('BB_' . strtoupper($type) . 'S') : $this->language->lang('STATISTICS'));

		// Output
		$this->template->assign_vars([
			'S_BB'	=> true
		]);

		// Page title
		$page_title = !empty($type) ? $this->language->lang('BB_' . strtoupper($type) . 'S') : $this->language->lang('BB');

		return $this->helper->render('bb.html', $page_title);
	}

	/**
	* Convert BB types from URL varnames to standard varnames
	* Example: For ACP styles, URL varname is 'acp-styles' but standard varname is 'acp_style'
	*
	* @param string $varname phpBB resource type URL varname
	* @return string
	*/
	protected function convert_bb_type_varname($varname)
	{
		switch ($varname)
		{
			case constants::BB_TYPE_VARNAME_EXT:
			return 'ext';

			case constants::BB_TYPE_VARNAME_STYLE:
			return 'style';

			case constants::BB_TYPE_VARNAME_ACP_STYLE:
			return 'acp_style';

			case constants::BB_TYPE_VARNAME_LANG:
			return 'lang';

			case constants::BB_TYPE_VARNAME_TOOL:
			return 'tool';

			default:
			return '';
		}
	}
}
