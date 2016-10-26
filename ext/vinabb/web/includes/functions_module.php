<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\includes;

global $phpbb_root_path, $phpEx;
require($phpbb_root_path . 'includes/functions_module.' . $phpEx);

class p_master extends \p_master
{
	/**
	* Build navigation structure
	*
	* Copied from phpBB 3.2.0-RC1 with one change:
	*	$u_title = append_sid($u_title);
	*
	* REMEMBER TO UPDATE CODE CHANGES FOR LATER PHPBB VERSIONS IF NEEDED
	*
	* @param $module_url
	*/
	public function assign_tpl_vars($module_url)
	{
		global $template;

		$current_id = $right_id = false;

		// Make sure the module_url has a question mark set, effectively determining the delimiter to use
		$delim = (strpos($module_url, '?') === false) ? '?' : '&amp;';

		$current_depth = 0;
		$linear_offset 	= 'l_block1';
		$tabular_offset = 't_block2';

		// Generate the list of modules, we'll do this in two ways ...
		// 1) In a linear fashion
		// 2) In a combined tabbed + linear fashion ... tabs for the categories
		//    and a linear list for subcategories/items
		foreach ($this->module_ary as $row_id => $item_ary)
		{
			// Skip hidden modules
			if (!$item_ary['display'])
			{
				continue;
			}

			// Skip branch
			if ($right_id !== false)
			{
				if ($item_ary['left'] < $right_id)
				{
					continue;
				}

				$right_id = false;
			}

			// Category with no members on their way down (we have to check every level)
			if (!$item_ary['name'])
			{
				$empty_category = true;

				// We go through the branch and look for an activated module
				foreach (array_slice($this->module_ary, $row_id + 1) as $temp_row)
				{
					if ($temp_row['left'] > $item_ary['left'] && $temp_row['left'] < $item_ary['right'])
					{
						// Module there and displayed?
						if ($temp_row['name'] && $temp_row['display'])
						{
							$empty_category = false;
							break;
						}
						continue;
					}
					break;
				}

				// Skip the branch
				if ($empty_category)
				{
					$right_id = $item_ary['right'];
					continue;
				}
			}

			// Select first id we can get
			if (!$current_id && (isset($this->module_cache['parents'][$item_ary['id']]) || $item_ary['id'] == $this->p_id))
			{
				$current_id = $item_ary['id'];
			}

			$depth = $item_ary['depth'];

			if ($depth > $current_depth)
			{
				$linear_offset = $linear_offset . '.l_block' . ($depth + 1);
				$tabular_offset = ($depth + 1 > 2) ? $tabular_offset . '.t_block' . ($depth + 1) : $tabular_offset;
			}
			else if ($depth < $current_depth)
			{
				for ($i = $current_depth - $depth; $i > 0; $i--)
				{
					$linear_offset = substr($linear_offset, 0, strrpos($linear_offset, '.'));
					$tabular_offset = ($i + $depth > 1) ? substr($tabular_offset, 0, strrpos($tabular_offset, '.')) : $tabular_offset;
				}
			}

			$u_title = $module_url . $delim . 'i=';

			// if the item has a name use it, else use its id
			if (empty($item_ary['name']))
			{
				$u_title .= $item_ary['id'];
			}
			else
			{
				// if the category has a name, then use it.
				$u_title .= $this->get_module_identifier($item_ary['name']);
			}

			// If the item is not a category append the mode
			if (!$item_ary['cat'])
			{
				if ($item_ary['is_duplicate'])
				{
					$u_title .= '&amp;icat=' . $current_id;
				}

				$u_title .= '&amp;mode=' . $item_ary['mode'];
			}

			// Was not allowed in categories before - /*!$item_ary['cat'] && */
			$u_title .= (isset($item_ary['url_extra'])) ? $item_ary['url_extra'] : '';

			// Use append_sid() here to modify URL via the event core.append_sid
			$u_title = append_sid($u_title);

			// Only output a categories items if it's currently selected
			if (!$depth || ($depth && (in_array($item_ary['parent'], array_values($this->module_cache['parents'])) || $item_ary['parent'] == $this->p_parent)))
			{
				$use_tabular_offset = (!$depth) ? 't_block1' : $tabular_offset;

				$tpl_ary = array(
					'L_TITLE'		=> $item_ary['lang'],
					'S_SELECTED'	=> (isset($this->module_cache['parents'][$item_ary['id']]) || $item_ary['id'] == $this->p_id) ? true : false,
					'U_TITLE'		=> $u_title
				);

				$template->assign_block_vars($use_tabular_offset, array_merge($tpl_ary, array_change_key_case($item_ary, CASE_UPPER)));
			}

			$tpl_ary = array(
				'L_TITLE'		=> $item_ary['lang'],
				'S_SELECTED'	=> (isset($this->module_cache['parents'][$item_ary['id']]) || $item_ary['id'] == $this->p_id) ? true : false,
				'U_TITLE'		=> $u_title
			);

			$template->assign_block_vars($linear_offset, array_merge($tpl_ary, array_change_key_case($item_ary, CASE_UPPER)));

			$current_depth = $depth;
		}
	}
}
