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

	/** @var \vinabb\web\controllers\pagination */
	protected $pagination;

	/** @var \phpbb\template\template */
	protected $template;

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
	* @param \vinabb\web\controllers\pagination					$pagination	Pagination object
	* @param \phpbb\template\template							$template	Template object
	* @param \phpbb\user										$user		User object
	* @param \phpbb\controller\helper							$helper		Controller helper
	* @param \vinabb\web\controllers\helper_interface			$ext_helper	Extension helper
	*/
	public function __construct(
		\vinabb\web\controllers\cache\service_interface $cache,
		ContainerInterface $container,
		\phpbb\language\language $language,
		\vinabb\web\controllers\pagination $pagination,
		\phpbb\template\template $template,
		\phpbb\user $user,
		\phpbb\controller\helper $helper,
		\vinabb\web\controllers\helper_interface $ext_helper
	)
	{
		$this->cache = $cache;
		$this->container = $container;
		$this->language = $language;
		$this->pagination = $pagination;
		$this->template = $template;
		$this->user = $user;
		$this->helper = $helper;
		$this->ext_helper = $ext_helper;

		$this->language->add_lang('bb', 'vinabb/web');
	}

	public function main($type, $cat, $page)
	{
		$bb_mode = $this->ext_helper->convert_bb_type_varnames($type);
		$bb_type = $this->ext_helper->get_bb_type_constants($bb_mode);
		$this->cat_data = $this->cache->get_bb_cats($bb_type);

		// Pagination
		$page = max(1, floor(str_replace(constants::REWRITE_URL_PAGE, '', $page)));
		$start = floor(($page - 1) * constants::NUM_ARTICLES_ON_INDEX);

		// Get cat_id from $cat_varname
		$current_cat_id = 0;
		$current_cat_name = '';

		foreach ($this->cat_data as $cat_id => $cat_data)
		{
			if ($cat == $cat_data['varname'])
			{
				$current_cat_id = $cat_id;
				$current_cat_name = $this->cat_data[$cat_id][($this->user->lang_name == constants::LANG_VIETNAMESE) ? 'name_vi' : 'name'];
			}
		}

		if (!$current_cat_id)
		{
			$this->template->assign_var('S_ERROR', true);
			trigger_error('NO_BB_CAT');
		}

		// Breadcrumb
		$this->ext_helper->set_breadcrumb($this->language->lang('BB'), $this->helper->route('vinabb_web_bb_route'));
		$this->ext_helper->set_breadcrumb($this->language->lang('BB_' . strtoupper($bb_mode) . 'S'), $this->helper->route('vinabb_web_bb_type_route', ['type' => $type]));
		$this->ext_helper->set_breadcrumb($current_cat_name);

		// Display items
		$items = [];
		$item_count = 0;
		$start = $this->list_bb_items($bb_type, $current_cat_id, $items, $item_count, constants::NUM_ARTICLES_ON_INDEX, $start);

		foreach ($items as $row)
		{
			$this->template->assign_block_vars('items', [
				'CATEGORY'	=> $this->cat_data[$row['cat_id']][($this->user->lang_name == constants::LANG_VIETNAMESE) ? 'name_vi' : 'name'],
				'CAT_URL'	=> $this->helper->route('vinabb_web_bb_cat_route', ['type' => $type, 'cat' => $this->cat_data[$row['cat_id']]['varname']]),
				'NAME'		=> $row['name'],
				'PRICE'		=> $row['price'],
				'TIME'		=> $this->user->format_date($row['added']),
				'URL'		=> $this->helper->route('vinabb_web_bb_item_route', ['type' => $type, 'cat' => $this->cat_data[$row['cat_id']]['varname'], 'item' => $row['varname']]),
				'DOWNLOADS'	=> 0,

				'S_NEW'	=> ($row['added'] + (constants::FLAG_DAY_NEW_ARTICLE * 24 * 60 * 60)) > time()
			]);
		}

		// Generate pagination
		$this->pagination->generate_template_pagination('vinabb_web_bb_cat_route', ['type' => $type, 'cat' => $cat], 'pagination', $item_count, constants::BB_ITEMS_PER_PAGE, $start);

		// Output
		$this->template->assign_vars([
			'NO_ITEMS_LANG'	=> $this->language->lang('NO_BB_' . strtoupper($bb_mode) . 'S')
		]);

		return $this->helper->render('bb_category.html', $current_cat_name);
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
				'cat_id'	=> $entity->get_cat_id(),
				'id'		=> $entity->get_id(),
				'name'		=> $entity->get_name(),
				'varname'	=> $entity->get_varname(),
				'price'		=> $entity->get_price(),
				'added'		=> $entity->get_added()
			];
		}

		return $offset;
	}
}
