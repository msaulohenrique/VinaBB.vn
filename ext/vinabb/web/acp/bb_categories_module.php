<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\acp;

use vinabb\web\includes\constants;

class bb_categories_module
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

		$this->tpl_name = 'acp_bb_categories';
		$this->page_title = $this->language->lang('ACP_BB_' . strtoupper($mode) . '_CATS');
		$this->language->add_lang('acp_bb', 'vinabb/web');

		$action = $this->request->variable('action', '');
		$action = $this->request->is_set_post('add') ? 'add' : ($this->request->is_set_post('save') ? 'save' : $action);

		// Pagination
		$start = $this->request->variable('start', 0);
		$per_page = constants::BB_CATS_PER_PAGE;

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
				$cat_id = $this->request->variable('id', 0);

				if (!$cat_id)
				{
					trigger_error($this->language->lang('NO_BB_CAT_ID') . adm_back_link($this->u_action), E_USER_WARNING);
				}

				$sql = 'SELECT *
					FROM ' . $this->bb_categories_table . "
					WHERE cat_id = $cat_id";
				$result = $this->db->sql_query($sql);
				$cat_data = $this->db->sql_fetchrow($result);
				$this->db->sql_freeresult($result);

				$s_hidden_fields .= '<input type="hidden" name="id" value="' . $cat_id . '" />';
			// No break

			case 'add':
				$this->template->assign_vars(array(
					'CAT_NAME'		=> isset($cat_data['cat_name']) ? $cat_data['cat_name'] : '',
					'CAT_NAME_VI'	=> isset($cat_data['cat_name_vi']) ? $cat_data['cat_name_vi'] : '',
					'CAT_VARNAME'	=> isset($cat_data['cat_varname']) ? $cat_data['cat_varname'] : '',

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

				$cat_id = $this->request->variable('id', 0);
				$cat_name = $this->request->variable('cat_name', '', true);
				$cat_name_vi = $this->request->variable('cat_name_vi', '', true);
				$cat_varname = strtolower($this->request->variable('cat_varname', ''));

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

				if ($cat_id)
				{
					$this->db->sql_query('UPDATE ' . $this->bb_categories_table . ' SET ' . $this->db->sql_build_array('UPDATE', $sql_ary) . ' WHERE cat_id = ' . $cat_id);
				}
				else
				{
					$this->db->sql_query('INSERT INTO ' . $this->bb_categories_table . ' ' . $this->db->sql_build_array('INSERT', $sql_ary));
				}

				$this->cache->clear_bb_cat_data($mode);

				$log_action = ($cat_id) ? 'LOG_BB_' . strtoupper($mode) . '_CAT_EDIT' : 'LOG_BB_' . strtoupper($mode) . '_CAT_ADD';
				$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, $log_action, false, array($cat_name));

				$message = ($cat_id) ? $this->language->lang('MESSAGE_BB_CAT_EDIT') : $this->language->lang('MESSAGE_BB_CAT_ADD');
				trigger_error($message . adm_back_link($this->u_action));
			break;

			case 'delete':
				$cat_id = $this->request->variable('id', 0);

				if (!$cat_id)
				{
					trigger_error($this->language->lang('NO_BB_CAT_ID') . adm_back_link($this->u_action), E_USER_WARNING);
				}

				if (confirm_box(true))
				{
					$sql = 'SELECT cat_name
						FROM ' . $this->bb_categories_table . "
						WHERE cat_id = $cat_id";
					$result = $this->db->sql_query($sql);
					$cat_name = $this->db->sql_fetchfield('cat_name');
					$this->db->sql_freeresult($result);

					$sql = 'DELETE FROM ' . $this->bb_categories_table . "
						WHERE cat_id = $cat_id";
					$this->db->sql_query($sql);

					$this->cache->clear_bb_cat_data($mode);

					$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_BB_' . strtoupper($mode) . '_CAT_DELETE', false, array($cat_name));

					trigger_error($this->language->lang('MESSAGE_BB_CAT_DELETE') . adm_back_link($this->u_action));
				}
				else
				{
					confirm_box(false, $this->language->lang('CONFIRM_BB_CAT_DELETE'), build_hidden_fields(array(
						'i'			=> $id,
						'mode'		=> $mode,
						'id'		=> $cat_id,
						'action'	=> 'delete',
					)));
				}
			break;
		}

		// Manage categories
		$cats = array();
		$cat_count = 0;
		$start = $this->list_bb_cats($this->bb_categories_table, $cats, $cat_count, $per_page, $start);

		foreach ($cats as $row)
		{
			$this->template->assign_block_vars('cats', array(
				'NAME'		=> $row['cat_name'],
				'NAME_VI'	=> $row['cat_name_vi'],
				'VARNAME'	=> $row['cat_varname'],

				'U_EDIT'	=> $this->u_action . '&action=edit&id=' . $row['cat_id'],
				'U_DELETE'	=> $this->u_action . '&action=delete&id=' . $row['cat_id'],
			));
		}

		$this->pagination->generate_template_pagination($this->u_action, 'pagination', 'start', $cat_count, $per_page, $start);

		// Output
		$this->template->assign_vars(array(
			'TOTAL_CATS'	=> $cat_count,

			'U_ACTION'	=> $this->u_action . "&action=$action&start=$start",

			'S_HIDDEN_FIELDS'	=> $s_hidden_fields,
		));
	}

	/**
	* List categories with pagination
	*
	* @param     $bb_categories_table
	* @param     $mode
	* @param     $cats
	* @param     $cat_count
	* @param int $limit
	* @param int $offset
	*
	* @return int
	*/
	private function list_bb_cats(&$bb_categories_table, &$cats, &$cat_count, $limit = 0, $offset = 0)
	{
		$sql = "SELECT COUNT(cat_id) AS cat_count
			FROM $bb_categories_table
			WHERE bb_type = " . $this->bb_type;
		$result = $this->db->sql_query($sql);
		$cat_count = (int) $this->db->sql_fetchfield('cat_count');
		$this->db->sql_freeresult($result);

		if ($cat_count == 0)
		{
			return 0;
		}

		if ($offset >= $cat_count)
		{
			$offset = ($offset - $limit < 0) ? 0 : $offset - $limit;
		}

		$sql = "SELECT *
			FROM $bb_categories_table
			WHERE bb_type = " . $this->bb_type . '
			ORDER BY cat_name';
		$result = $this->db->sql_query_limit($sql, $limit, $offset);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$cats[] = $row;
		}
		$this->db->sql_freeresult($result);

		return $offset;
	}
}
