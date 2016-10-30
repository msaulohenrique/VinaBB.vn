<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\controller;

use vinabb\web\includes\constants;

class bb
{
	/** @var \phpbb\cache\service */
	protected $cache;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\controller\helper */
	protected $helper;

	/**
	* Constructor
	*
	* @param \phpbb\cache\service $cache
	* @param \phpbb\config\config $config
	* @param \phpbb\db\driver\driver_interface $db
	* @param \phpbb\language\language $language
	* @param \phpbb\request\request $request
	* @param \phpbb\template\template $template
	* @param \phpbb\user $user
	* @param \phpbb\controller\helper $helper
	*/
	public function __construct(
		\phpbb\cache\service $cache,
		\phpbb\config\config $config,
		\phpbb\db\driver\driver_interface $db,
		\phpbb\language\language $language,
		\phpbb\request\request $request,
		\phpbb\template\template $template,
		\phpbb\user $user,
		\phpbb\controller\helper $helper
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
	}

	/**
	* List categories of each resource types (bb_type)
	*
	* @param $type
	* @return \Symfony\Component\HttpFoundation\Response
	*/
	public function index($type)
	{
		$type = $this->convert_bb_type_varname($type);

		if (!empty($type))
		{
			$this->template->assign_vars(array(
				'S_BB_' . strtoupper($type) . 'S'	=> true
			));
		}
		// Default mode is 'Statistics'
		else
		{
			$this->template->assign_vars(array(
				'S_BB_STATS'	=> true
			));
		}

		// Breadcrumb
		$this->template->assign_block_vars('breadcrumb', array(
			'NAME'	=> $this->language->lang('BB'),
			'URL'	=> $this->helper->route('vinabb_web_bb_route'),
		));

		$this->template->assign_block_vars('breadcrumb', array(
			'NAME'	=> !empty($type) ? $this->language->lang('BB_' . strtoupper($type) . 'S') : $this->language->lang('STATISTICS')
		));

		// Output
		$this->template->assign_vars(array(
			'S_BB'	=> true
		));

		// Page title
		$page_title = !empty($type) ? $this->language->lang('BB_' . strtoupper($type) . 'S') : $this->language->lang('BB');

		return $this->helper->render('bb_body.html', $page_title);
	}

	/**
	* Convert BB types from URL varnames to standard varnames
	* Example: For ACP styles, URL varname is 'acp-styles' but standard varname is 'acp_style'
	*
	* @param $varname
	* @return string
	*/
	private function convert_bb_type_varname($varname)
	{
		switch ($varname)
		{
			case constants::BB_TYPE_VARNAME_EXT:
				return 'ext';
			break;

			case constants::BB_TYPE_VARNAME_STYLE:
				return 'style';
			break;

			case constants::BB_TYPE_VARNAME_ACP_STYLE:
				return 'acp_style';
			break;

			case constants::BB_TYPE_VARNAME_LANG:
				return 'lang';
			break;

			case constants::BB_TYPE_VARNAME_TOOL:
				return 'tool';
			break;

			default:
				return '';
			break;
		}
	}
}
