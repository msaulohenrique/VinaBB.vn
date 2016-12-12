<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\controllers\bb;

use Symfony\Component\DependencyInjection\ContainerInterface;
use vinabb\web\includes\constants;

/**
* Controller for BB category page
*/
class category
{
	/** @var \vinabb\web\controllers\cache\service_interface $cache */
	protected $cache;

	/** @var ContainerInterface $container */
	protected $container;

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
	* @param ContainerInterface									$container	Service container
	* @param \phpbb\language\language							$language	Language object
	* @param \phpbb\user										$user		User object
	* @param \phpbb\controller\helper							$helper		Controller helper
	* @param \vinabb\web\controllers\helper_interface			$ext_helper	Extension helper
	*/
	public function __construct(
		\vinabb\web\controllers\cache\service_interface $cache,
		ContainerInterface $container,
		\phpbb\language\language $language,
		\phpbb\user $user,
		\phpbb\controller\helper $helper,
		\vinabb\web\controllers\helper_interface $ext_helper
	)
	{
		$this->cache = $cache;
		$this->container = $container;
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

	/**
	* List phpBB resource items with pagination
	*
	* @param int	$bb_type	phpBB resource type in constant value
	* @param int	$cat_id		Category ID
	* @param array	$items		Array of items
	* @param int	$item_count	Number of items
	* @param int	$limit		Items per page
	* @param int	$offset		Position of the start
	*
	* @return int Position of the start
	*/
	public function list_bb_items($bb_type, $cat_id, &$items, &$item_count, $limit = 0, $offset = 0)
	{
		$operators = $this->container->get('vinabb.web.operators.bb_item');
		$item_count = $operators->count_items($bb_type, $cat_id);

		if ($item_count == 0)
		{
			return 0;
		}

		if ($offset >= $item_count)
		{
			$offset = ($offset - $limit < 0) ? 0 : $offset - $limit;
		}

		/** @var \vinabb\web\entities\bb_item_interface $entity */
		foreach ($operators->list_items($bb_type, $cat_id, 'item_updated DESC', $limit, $offset) as $entity)
		{
			$items[] = [
				'id'		=> $entity->get_id(),
				'name'		=> $entity->get_name(),
				'varname'	=> $entity->get_varname(),
				'price'		=> $entity->get_price(),
				'added'		=> $entity->get_added(),
				'updated'	=> $entity->get_updated()
			];
		}

		return $offset;
	}
}
