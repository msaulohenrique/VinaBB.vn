<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\controller;

use vinabb\web\includes\constants;

class pagination
{
	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\controller\helper */
	protected $helper;

	/**
	* Constructor
	*
	* @param \phpbb\language\language $language
	* @param \phpbb\template\template $template
	* @param \phpbb\user $user
	* @param \phpbb\controller\helper $helper
	*/
	public function __construct(
		\phpbb\language\language $language,
		\phpbb\template\template $template,
		\phpbb\user $user,
		\phpbb\controller\helper $helper
	)
	{
		$this->language = $language;
		$this->template = $template;
		$this->user = $user;
		$this->helper = $helper;
	}

	/**
	* Generate a pagination link based on the url and the page information
	*
	* @param       $route_name
	* @param array $route_params
	* @param       $on_page
	*
	* @return string
	*/
	protected function generate_page_link($route_name, array $route_params = array(), $on_page)
	{
		if ($on_page > 1)
		{
			$route_params['page'] = constants::REWRITE_URL_PAGE . $on_page;
		}

		return $this->helper->route($route_name, $route_params);
	}

	/**
	* Generate template rendered pagination
	* Allows full control of rendering of pagination with the template
	*
	* @param		$route_name
	* @param array	$route_params
	* @param string $block_var_name is the name assigned to the pagination data block within the template (example: <!-- BEGIN pagination -->)
	* @param string $start_name is the name of the parameter containing the first item of the given page (example: start=20)
	*							If you use page numbers inside your controller route, start name should be the string
	*							that should be removed for the first page (example: /page/%d)
	* @param int $num_items the total number of items, posts, etc., used to determine the number of pages to produce
	* @param int $per_page the number of items, posts, etc. to display per page, used to determine the number of pages to produce
	* @param int $start the item which should be considered currently active, used to determine the page we're on
	* @param bool $reverse_count determines whether we weight display of the list towards the start (false) or end (true) of the list
	* @param bool $ignore_on_page decides whether we enable an active (unlinked) item, used primarily for embedded lists
	* @return null
	*/
	public function generate_template_pagination($route_name, array $route_params = array(), $block_var_name, $start_name, $num_items, $per_page, $start = 1, $reverse_count = false, $ignore_on_page = false)
	{
		$total_pages = ceil($num_items / $per_page);
		$on_page = $this->get_on_page($per_page, $start);
		$u_previous_page = $u_next_page = '';

		if ($total_pages > 1)
		{
			if ($reverse_count)
			{
				$start_page = ($total_pages > 5) ? $total_pages - 4 : 1;
				$end_page = $total_pages;
			}
			else
			{
				// What we're doing here is calculating what the "start" and "end" pages should be. We
				// do this by assuming pagination is "centered" around the currently active page with
				// the three previous and three next page links displayed. Anything more than that and
				// we display the ellipsis, likewise anything less.
				//
				// $start_page is the page at which we start creating the list. When we have five or less
				// pages we start at page 1 since there will be no ellipsis displayed. Anymore than that
				// and we calculate the start based on the active page. This is the min/max calculation.
				// First (max) would we end up starting on a page less than 1? Next (min) would we end
				// up starting so close to the end that we'd not display our minimum number of pages.
				//
				// $end_page is the last page in the list to display. Like $start_page we use a min/max to
				// determine this number. Again at most five pages? Then just display them all. More than
				// five and we first (min) determine whether we'd end up listing more pages than exist.
				// We then (max) ensure we're displaying the minimum number of pages.
				$start_page = ($total_pages > 5) ? min(max(1, $on_page - 2), $total_pages - 4) : 1;
				$end_page = ($total_pages > 5) ? max(min($total_pages, $on_page + 2), 5) : $total_pages;
			}

			if ($on_page != 1)
			{
				$u_previous_page = $this->generate_page_link($route_name, $route_params, $on_page - 1);

				$this->template->assign_block_vars($block_var_name, array(
					'PAGE_NUMBER'	=> '',
					'PAGE_URL'		=> $u_previous_page,
					'S_IS_CURRENT'	=> false,
					'S_IS_PREV'		=> true,
					'S_IS_NEXT'		=> false,
					'S_IS_ELLIPSIS'	=> false,
				));
			}

			// This do...while exists purely to negate the need for start and end assign_block_vars, i.e.
			// to display the first and last page in the list plus any ellipsis. We use this loop to jump
			// around a little within the list depending on where we're starting (and ending).
			$at_page = 1;
			
			do
			{
				// We decide whether to display the ellipsis during the loop. The ellipsis is always
				// displayed as either the second or penultimate item in the list. So are we at either
				// of those points and of course do we even need to display it, i.e. is the list starting
				// on at least page 3 and ending three pages before the final item.
				$this->template->assign_block_vars($block_var_name, array(
					'PAGE_NUMBER'	=> $at_page,
					'PAGE_URL'		=> $this->generate_page_link($route_name, $route_params, $at_page),
					'S_IS_CURRENT'	=> (!$ignore_on_page && $at_page == $on_page),
					'S_IS_NEXT'		=> false,
					'S_IS_PREV'		=> false,
					'S_IS_ELLIPSIS'	=> ($at_page == 2 && $start_page > 2) || ($at_page == $total_pages - 1 && $end_page < $total_pages - 1),
				));

				// We may need to jump around in the list depending on whether we have or need to display
				// the ellipsis. Are we on page 2 and are we more than one page away from the start
				// of the list? Yes? Then we jump to the start of the list. Likewise are we at the end of
				// the list and are there more than two pages left in total? Yes? Then jump to the penultimate
				// page (so we can display the ellipsis next pass). Else, increment the counter and keep
				// going
				if ($at_page == 2 && $at_page < $start_page - 1)
				{
					$at_page = $start_page;
				}
				else if ($at_page == $end_page && $end_page < $total_pages - 1)
				{
					$at_page = $total_pages - 1;
				}
				else
				{
					$at_page++;
				}
			}
			while ($at_page <= $total_pages);

			if ($on_page != $total_pages)
			{
				$u_next_page = $this->generate_page_link($route_name, $route_params, $on_page + 1);

				$this->template->assign_block_vars($block_var_name, array(
					'PAGE_NUMBER'	=> '',
					'PAGE_URL'		=> $u_next_page,
					'S_IS_CURRENT'	=> false,
					'S_IS_PREV'		=> false,
					'S_IS_NEXT'		=> true,
					'S_IS_ELLIPSIS'	=> false,
				));
			}
		}

		// If the block_var_name is a nested block, we will use the last (most
		// inner) block as a prefix for the template variables. If the last block
		// name is pagination, the prefix is empty. If the rest of the
		// block_var_name is not empty, we will modify the last row of that block
		// and add our pagination items.
		$tpl_block_name = $tpl_prefix = '';

		if (strrpos($block_var_name, '.') !== false)
		{
			$tpl_block_name = substr($block_var_name, 0, strrpos($block_var_name, '.'));
			$tpl_prefix = strtoupper(substr($block_var_name, strrpos($block_var_name, '.') + 1));
		}
		else
		{
			$tpl_prefix = strtoupper($block_var_name);
		}

		$tpl_prefix = ($tpl_prefix == 'PAGINATION') ? '' : $tpl_prefix . '_';

		$template_array = array(
			$tpl_prefix . 'BASE_URL'				=> (empty($route_name)) ? '' : $this->helper->route($route_name, $route_params),
			$tpl_prefix . 'START_NAME'				=> $start_name,
			$tpl_prefix . 'PER_PAGE'				=> $per_page,
			'U_' . $tpl_prefix . 'PREVIOUS_PAGE'	=> ($on_page != 1) ? $u_previous_page : '',
			'U_' . $tpl_prefix . 'NEXT_PAGE'		=> ($on_page != $total_pages) ? $u_next_page : '',
			$tpl_prefix . 'TOTAL_PAGES'				=> $total_pages,
			$tpl_prefix . 'CURRENT_PAGE'			=> $on_page,
			$tpl_prefix . 'PAGE_NUMBER'				=> $this->on_page($num_items, $per_page, $start),
		);

		if ($tpl_block_name)
		{
			$this->template->alter_block_array($tpl_block_name, $template_array, true, 'change');
		}
		else
		{
			$this->template->assign_vars($template_array);
		}
	}

	/**
	* Get current page number
	*
	* @param int $per_page the number of items, posts, etc. per page
	* @param int $start the item which should be considered currently active, used to determine the page we're on
	* @return int	Current page number
	*/
	public function get_on_page($per_page, $start)
	{
		return floor($start / $per_page) + 1;
	}

	/**
	* Return current page
	*
	* @param int $num_items the total number of items, posts, topics, etc.
	* @param int $per_page the number of items, posts, etc. per page
	* @param int $start the item which should be considered currently active, used to determine the page we're on
	* @return string Descriptive pagination string (e.g. "page 1 of 10")
	*/
	public function on_page($num_items, $per_page, $start)
	{
		$on_page = $this->get_on_page($per_page, $start);

		return $this->language->lang('PAGE_OF', $on_page, max(ceil($num_items / $per_page), 1));
	}

	/**
	* Get current page number
	*
	* @param int $start the item which should be considered currently active, used to determine the page we're on
	* @param int $per_page the number of items, posts, etc. per page
	* @param int $num_items the total number of items, posts, topics, etc.
	* @return int	Current page number
	*/
	public function validate_start($start, $per_page, $num_items)
	{
		if ($start < 0 || $start >= $num_items)
		{
			return ($start < 0 || $num_items <= 0) ? 0 : floor(($num_items - 1) / $per_page) * $per_page;
		}

		return $start;
	}

	/**
	* Get new start when searching from the end
	*
	* If the user is trying to reach late pages, start searching from the end.
	*
	* @param int $start the item which should be considered currently active, used to determine the page we're on
	* @param int $limit the number of items, posts, etc. to display
	* @param int $num_items the total number of items, posts, topics, etc.
	* @return int	Current page number
	*/
	public function reverse_start($start, $limit, $num_items)
	{
		return max(0, $num_items - $limit - $start);
	}

	/**
	* Get new item limit when searching from the end
	*
	* If the user is trying to reach late pages, start searching from the end.
	* In this case the items to display might be lower then the actual per_page setting.
	*
	* @param int $start the item which should be considered currently active, used to determine the page we're on
	* @param int $per_page the number of items, posts, etc. per page
	* @param int $num_items the total number of items, posts, topics, etc.
	* @return int	Current page number
	*/
	public function reverse_limit($start, $per_page, $num_items)
	{
		if ($start + $per_page > $num_items)
		{
			return min($per_page, max(1, $num_items - $start));
		}

		return $per_page;
	}
}
