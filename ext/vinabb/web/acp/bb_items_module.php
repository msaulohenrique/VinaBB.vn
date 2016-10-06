<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\acp;

use vinabb\web\includes\constants;

class bb_items_module
{
	/** @var string */
	public $u_action;

	/** @var string */
	public $bb_type;

	public function main($id, $mode)
	{
		global $phpbb_container;

		$this->auth = $phpbb_container->get('auth');
		$this->cache = $phpbb_container->get('cache');
		$this->db = $phpbb_container->get('dbal.conn');
		$this->language = $phpbb_container->get('language');
		$this->log = $phpbb_container->get('log');
		$this->pagination= $phpbb_container->get('pagination');
		$this->request = $phpbb_container->get('request');
		$this->template = $phpbb_container->get('template');
		$this->user = $phpbb_container->get('user');

		$this->table_prefix = $phpbb_container->getParameter('core.table_prefix');
		$this->bb_categories_table = $this->table_prefix . constants::BB_CATEGORIES_TABLE;
		$this->bb_items_table = $this->table_prefix . constants::BB_ITEMS_TABLE;

		$this->tpl_name = 'acp_bb_items';
		$this->page_title = $this->language->lang('ACP_BB_' . strtoupper($mode) . 'S');
		$this->language->add_lang('acp_bb', 'vinabb/web');

		$action = $this->request->variable('action', '');
		$action = $this->request->is_set_post('add') ? 'add' : ($this->request->is_set_post('save') ? 'save' : $action);

		// Pagination
		$start = $this->request->variable('start', 0);
		$per_page = constants::BB_ITEMS_PER_PAGE;

		add_form_key('vinabb/web');

		$s_hidden_fields = '';
		$errors = array();

		switch ($mode)
		{
			case 'ext':
				$this->bb_type = constants::BB_TYPE_EXT;
			break;

			case 'style':
				$this->bb_type = constants::BB_TYPE_STYLE;
			break;

			case 'acp_style':
				$this->bb_type = constants::BB_TYPE_ACP_STYLE;
			break;

			case 'lang':
				$this->bb_type = constants::BB_TYPE_LANG;
			break;

			case 'tool':
				$this->bb_type = constants::BB_TYPE_TOOL;
			break;
		}

		switch ($action)
		{
			case 'edit':
				$item_id = $this->request->variable('id', 0);

				if (!$item_id)
				{
					trigger_error($this->language->lang('NO_BB_' . strtoupper($mode) .'_ID') . adm_back_link($this->u_action), E_USER_WARNING);
				}

				$sql = 'SELECT *
					FROM ' . $this->bb_items_table . "
					WHERE item_id = $item_id";
				$result = $this->db->sql_query($sql);
				$item_data = $this->db->sql_fetchrow($result);
				$this->db->sql_freeresult($result);

				$s_hidden_fields .= '<input type="hidden" name="id" value="' . $item_id . '" />';
			// No break

			case 'add':
				$this->template->assign_vars(array(
					'ITEM_NAME'		=> isset($item_data['cat_name']) ? $item_data['cat_name'] : '',
					'ITEM_NAME_VI'	=> isset($item_data['cat_name_vi']) ? $item_data['cat_name_vi'] : '',
					'ITEM_VARNAME'	=> isset($item_data['cat_varname']) ? $item_data['cat_varname'] : '',

					'U_ACTION'	=> $this->u_action,
					'U_BACK'	=> $this->u_action,

					'S_EDIT'			=> true,
					'S_HIDDEN_FIELDS'	=> $s_hidden_fields,
				));

				return;
			break;

			case 'save':
				if (!check_form_key('vinabb/web'))
				{
					$errors[] = $this->language->lang('FORM_INVALID');
				}

				$item_id = $this->request->variable('id', 0);
				$item_name = $this->request->variable('cat_name', '', true);
				$item_name_vi = $this->request->variable('cat_name_vi', '', true);
				$item_varname = strtolower($this->request->variable('cat_varname', ''));

				if (empty($cat_name) || empty($cat_name_vi))
				{
					$errors[] = $this->language->lang('ERROR_BB_CAT_NAME_EMPTY');
				}

				if (empty($cat_varname))
				{
					$errors[] = $this->language->lang('ERROR_BB_CAT_VARNAME_EMPTY');
				}
				else
				{
					$sql = 'SELECT *
						FROM ' . $this->bb_categories_table . '
						WHERE bb_type = ' . $this->bb_type . "
							AND cat_varname = '" . $this->db->sql_escape($cat_varname) . "'";
					$result = $this->db->sql_query($sql);
					$rows = $this->db->sql_fetchrowset($result);
					$this->db->sql_freeresult($result);

					if (sizeof($rows))
					{
						$errors[] = $this->language->lang('ERROR_BB_CAT_VARNAME_DUPLICATE', $cat_varname);
					}
				}

				if (sizeof($errors))
				{
					trigger_error(implode('<br>', $errors) . adm_back_link($this->u_action), E_USER_WARNING);
				}

				$sql_ary = array(
					'bb_type'		=> $this->bb_type,
					'cat_name'		=> $cat_name,
					'cat_name_vi'	=> $cat_name_vi,
					'cat_varname'	=> $cat_varname,
				);

				if ($item_id)
				{
					$this->db->sql_query('UPDATE ' . $this->bb_items_table . ' SET ' . $this->db->sql_build_array('UPDATE', $sql_ary) . ' WHERE cat_id = ' . $cat_id);
				}
				else
				{
					$this->db->sql_query('INSERT INTO ' . $this->bb_items_table . ' ' . $this->db->sql_build_array('INSERT', $sql_ary));
				}

				$log_action = ($item_id) ? 'LOG_BB_' . strtoupper($mode) . '_CAT_EDIT' : 'LOG_BB_' . strtoupper($mode) . '_CAT_ADD';
				$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, $log_action, false, array($cat_name));

				$message = ($cat_id) ? $this->language->lang('MESSAGE_BB_CAT_EDIT') : $this->language->lang('MESSAGE_BB_CAT_ADD');
				trigger_error($message . adm_back_link($this->u_action));
			break;

			case 'delete':
				$item_id = $this->request->variable('id', 0);

				if (!$item_id)
				{
					trigger_error($this->language->lang('NO_BB_' . strtoupper($mode) .'_ID') . adm_back_link($this->u_action), E_USER_WARNING);
				}

				if (confirm_box(true))
				{
					$sql = 'SELECT item_name
						FROM ' . $this->bb_items_table . "
						WHERE item_id = $item_id";
					$result = $this->db->sql_query($sql);
					$item_name = $this->db->sql_fetchfield('item_name');
					$this->db->sql_freeresult($result);

					$sql = 'DELETE FROM ' . $this->bb_items_table . "
						WHERE item_name = $item_name";
					$this->db->sql_query($sql);

					$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_BB_' . strtoupper($mode) . '_DELETE', false, array($item_name));

					trigger_error($this->language->lang('MESSAGE_BB_CAT_DELETE') . adm_back_link($this->u_action));
				}
				else
				{
					confirm_box(false, $this->language->lang('CONFIRM_BB_CAT_DELETE'), build_hidden_fields(array(
						'i'			=> $id,
						'mode'		=> $mode,
						'id'		=> $item_id,
						'action'	=> 'delete',
					)));
				}
			break;
		}

		// Manage items
		$items = array();
		$item_count = 0;
		$start = $this->list_bb_items($this->bb_items_table, $cats, $item_count, $per_page, $start);

		foreach ($items as $row)
		{
			$this->template->assign_block_vars('items', array(
				'NAME'		=> $row['cat_name'],
				'NAME_VI'	=> $row['cat_name_vi'],
				'VARNAME'	=> $row['cat_varname'],

				'U_EDIT'	=> $this->u_action . '&action=edit&id=' . $row['item_id'],
				'U_DELETE'	=> $this->u_action . '&action=delete&id=' . $row['item_id'],
			));
		}

		$this->pagination->generate_template_pagination($this->u_action, 'pagination', 'start', $item_count, $per_page, $start);

		// Output
		$this->template->assign_vars(array(
			'TOTAL_ITEMS'	=> $item_count,

			'U_ACTION'	=> $this->u_action . "&action=$action&start=$start",

			'S_HIDDEN_FIELDS'	=> $s_hidden_fields,
		));
	}

	/**
	* List items with pagination
	*
	* @param     $bb_items_table
	* @param     $items
	* @param     $item_count
	* @param int $limit
	* @param int $offset
	*
	* @return int
	*/
	private function list_bb_items(&$bb_items_table, &$items, &$item_count, $limit = 0, $offset = 0)
	{
		$sql = "SELECT COUNT(item_id) AS item_count
			FROM $bb_items_table
			WHERE bb_type = " . $this->bb_type;
		$result = $this->db->sql_query($sql);
		$item_count = (int) $this->db->sql_fetchfield('item_count');
		$this->db->sql_freeresult($result);

		if ($item_count == 0)
		{
			return 0;
		}

		if ($offset >= $item_count)
		{
			$offset = ($offset - $limit < 0) ? 0 : $offset - $limit;
		}

		$sql = "SELECT *
			FROM $bb_items_table
			WHERE bb_type = " . $this->bb_type . '
			ORDER BY item_name';
		$result = $this->db->sql_query_limit($sql, $limit, $offset);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$items[] = $row;
		}
		$this->db->sql_freeresult($result);

		return $offset;
	}
}
