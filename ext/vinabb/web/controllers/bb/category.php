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
* Controller for BB category page
*/
class category
{
	/** @var \vinabb\web\controllers\cache\service_interface $cache */
	protected $cache;

	/** @var \phpbb\language\language $language */
	protected $language;

	/** @var \phpbb\user $user */
	protected $user;

	/** @var \phpbb\controller\helper $helper */
	protected $helper;

	/** @var \vinabb\web\controllers\helper_interface $ext_helper */
	protected $ext_helper;

	/** @var array $cat_data */
	protected $cat_data;

	/**
	* Constructor
	*
	* @param \vinabb\web\controllers\cache\service_interface	$cache		Cache service
	* @param \phpbb\language\language							$language	Language object
	* @param \phpbb\user										$user		User object
	* @param \phpbb\controller\helper							$helper		Controller helper
	* @param \vinabb\web\controllers\helper_interface			$ext_helper	Extension helper
	*/
	public function __construct(
		\vinabb\web\controllers\cache\service_interface $cache,
		\phpbb\language\language $language,
		\phpbb\user $user,
		\phpbb\controller\helper $helper,
		\vinabb\web\controllers\helper_interface $ext_helper
	)
	{
		$this->cache = $cache;
		$this->language = $language;
		$this->user = $user;
		$this->helper = $helper;
		$this->ext_helper = $ext_helper;

		$this->language->add_lang('bb', 'vinabb/web');
	}

	public function main($type, $cat)
	{
		$type = $this->ext_helper->convert_bb_type_varnames($type);
		$this->cat_data = $this->cache->get_bb_cats($this->ext_helper->get_bb_type_constants($type));

		// Category name
		$cat_name = '';

		if (isset($this->cat_data[$cat][($this->user->lang_name == constants::LANG_VIETNAMESE) ? 'name_vi' : 'name']))
		{
			$cat_name = $this->cat_data[$cat][($this->user->lang_name == constants::LANG_VIETNAMESE) ? 'name_vi' : 'name'];
		}
		else
		{
			trigger_error('NO_BB_CAT');
		}

		// Breadcrumb
		$this->ext_helper->set_breadcrumb($this->language->lang('BB'), $this->helper->route('vinabb_web_bb_route'));
		$this->ext_helper->set_breadcrumb($this->language->lang('BB_' . strtoupper($type) . 'S'), $this->helper->route('vinabb_web_bb_type_route', ['type' => $this->ext_helper->get_bb_type_varnames($type)]));
		$this->ext_helper->set_breadcrumb($cat_name);

		trigger_error($this->language->lang('NO_BB_' . strtoupper($type) . 'S'));
	}
}
