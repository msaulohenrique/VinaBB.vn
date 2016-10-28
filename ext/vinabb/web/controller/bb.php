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
	* Index page of phpBB Resource
	* Default mode is 'Statistics'
	*
	* @return \Symfony\Component\HttpFoundation\Response
	*/
	public function index()
	{
		$this->list_bb_cats();

		$this->template->assign_vars(array(
			'S_BB_STATS'	=> true
		));

		return $this->helper->render('bb_body.html', $this->language->lang('BB'));
	}

	/**
	* List categories of each resource types (bb_type)
	*
	* @param $type
	* @return \Symfony\Component\HttpFoundation\Response
	*/
	public function cats($type)
	{
		$this->list_bb_cats();

		switch ($type)
		{
			case constants::BB_TYPE_VARNAME_EXT:
				$this->template->assign_vars(array(
					'S_BB_EXTS'	=> true,
					'BB_TYPE'	=> $this->language->lang('BB_EXTS'),
				));
			break;

			case constants::BB_TYPE_VARNAME_STYLE:
				$this->template->assign_vars(array(
					'S_BB_STYLES'	=> true,
					'BB_TYPE'		=> $this->language->lang('BB_STYLES'),
				));
			break;

			case constants::BB_TYPE_VARNAME_ACP_STYLE:
				$this->template->assign_vars(array(
					'S_BB_ACP_STYLES'	=> true,
					'BB_TYPE'			=> $this->language->lang('BB_ACP_STYLES'),
				));
			break;

			case constants::BB_TYPE_VARNAME_LANG:
				$this->template->assign_vars(array(
					'S_BB_LANGS'	=> true,
					'BB_TYPE'		=> $this->language->lang('BB_LANGS'),
				));
			break;

			case constants::BB_TYPE_VARNAME_TOOL:
				$this->template->assign_vars(array(
					'S_BB_TOOLS'	=> true,
					'BB_TYPE'		=> $this->language->lang('BB_TOOLS'),
				));
			break;
		}

		return $this->helper->render('bb_body.html', $this->language->lang('BB'));
	}

	/**
	* List all categories with every resource types
	*/
	private function list_bb_cats()
	{
		$bb_types = array('ext', 'style', 'acp_style', 'lang', 'tool');

		foreach ($bb_types as $bb_type)
		{
			foreach ($this->cache->get_bb_cats($bb_type) as $cat_id => $cat_data)
			{
				$this->template->assign_block_vars($bb_type . '_cats', array(
					'NAME'		=> ($this->user->lang_name == constants::LANG_VIETNAMESE) ? $cat_data['name_vi'] : $cat_data['name'],
					'VARNAME'	=> $cat_data['varname'],
				));
			}
		}
	}
}
