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
		$this->ext_helper = $phpbb_container->get('vinabb.web.helper');
		$this->language = $phpbb_container->get('language');
		$this->log = $phpbb_container->get('log');
		$this->pagination= $phpbb_container->get('pagination');
		$this->request = $phpbb_container->get('request');
		$this->template = $phpbb_container->get('template');
		$this->user = $phpbb_container->get('user');

		$this->bb_type = $this->ext_helper->get_bb_type_constants($mode);
		$this->lang_data = ($mode == 'lang') ? $this->cache->get_lang_data() : array();
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
				// Select a category
				$sql = 'SELECT *
					FROM ' . $this->bb_categories_table . '
					ORDER BY cat_name';
				$result = $this->db->sql_query($sql);
				$rows = $this->db->sql_fetchrowset($result);
				$this->db->sql_freeresult($result);

				$cat_id = isset($item_data['cat_id']) ? $item_data['cat_id'] : 0;
				$cat_options = '<option value=""' . (($cat_id == 0) ? ' selected' : '' ) . '>' . $this->language->lang('SELECT_CATEGORY') . '</option>';

				foreach ($rows as $row)
				{
					$cat_options .= '<option value="' . $row['cat_id'] . '"' . (($cat_id == $row['cat_id']) ? ' selected' : '' ) . '>' . $row['cat_name'] . ' (' . $row['cat_name_vi'] . ')</option>';
				}

				// Select a language
				if ($mode == 'lang')
				{
					$sql = 'SELECT *
						FROM ' . LANG_TABLE . '
						ORDER BY lang_english_name';
					$result = $this->db->sql_query($sql);
					$rows = $this->db->sql_fetchrowset($result);
					$this->db->sql_freeresult($result);

					$item_lang_iso = isset($item_data['item_lang_iso']) ? $item_data['item_lang_iso'] : constants::LANG_VIETNAMESE;
					$lang_options = '<option value=""' . (($item_lang_iso == '') ? ' selected' : '' ) . '>' . $this->language->lang('SELECT_LANGUAGE') . '</option>';

					foreach ($rows as $row)
					{
						$lang_options .= '<option value="' . $row['lang_iso'] . '"' . (($item_lang_iso == $row['lang_iso']) ? ' selected' : '' ) . '>' . $row['lang_english_name'] . ' (' . $row['lang_local_name'] . ')</option>';
					}
				}

				// Select an OS
				if ($mode == 'tool')
				{
					$os_list = array(
						constants::OS_ALL		=> $this->language->lang(['OS_LIST', 'ALL']),
						constants::OS_WIN		=> $this->language->lang(['OS_LIST', 'WIN']),
						constants::OS_MAC		=> $this->language->lang(['OS_LIST', 'MAC']),
						constants::OS_LINUX		=> $this->language->lang(['OS_LIST', 'LINUX']),
						constants::OS_BSD		=> $this->language->lang(['OS_LIST', 'BSD']),
						constants::OS_ANDROID	=> $this->language->lang(['OS_LIST', 'ANDROID']),
						constants::OS_IOS		=> $this->language->lang(['OS_LIST', 'IOS']),
						constants::OS_WP		=> $this->language->lang(['OS_LIST', 'WP']),
					);

					$item_tool_os = isset($item_data['item_tool_os']) ? $item_data['item_tool_os'] : constants::OS_ALL;
					$os_options = '<option value=""' . (($item_tool_os == '') ? ' selected' : '' ) . '>' . $this->language->lang('SELECT_OS') . '</option>';

					foreach ($os_list as $os_value => $os_name)
					{
						$os_options .= '<option value="' . $os_value . '"' . (($item_tool_os == $os_value) ? ' selected' : '' ) . '>' . $os_name . '</option>';
					}
				}

				$this->template->assign_vars(array(
					'ITEM_NAME'					=> isset($item_data['item_name']) ? $item_data['item_name'] : '',
					'ITEM_NAME_VI'				=> isset($item_data['item_name_vi']) ? $item_data['item_name_vi'] : '',
					'ITEM_VARNAME'				=> isset($item_data['item_varname']) ? $item_data['item_varname'] : '',
					'ITEM_VERSION'				=> isset($item_data['item_version']) ? $item_data['item_version'] : '',
					'ITEM_DESC'					=> isset($item_data['item_desc']) ? $item_data['item_desc'] : '',
					'ITEM_DESC_VI'				=> isset($item_data['item_desc_vi']) ? $item_data['item_desc_vi'] : '',
					'ITEM_EXT_STYLE'			=> isset($item_data['item_ext_style']) && $item_data['item_ext_style'],
					'ITEM_EXT_ACP_STYLE'		=> isset($item_data['item_ext_acp_style']) && $item_data['item_ext_acp_style'],
					'ITEM_EXT_LANG'				=> isset($item_data['item_ext_lang']) && $item_data['item_ext_lang'],
					'ITEM_EXT_DB_SCHEMA'		=> isset($item_data['item_ext_db_schema']) && $item_data['item_ext_db_schema'],
					'ITEM_EXT_DB_DATA'			=> isset($item_data['item_ext_db_data']) && $item_data['item_ext_db_data'],
					'ITEM_STYLE_PRESETS'		=> isset($item_data['item_style_presets']) ? $item_data['item_style_presets'] : 0,
					'ITEM_STYLE_PRESETS_AIO'	=> isset($item_data['item_style_presets_aio']) && $item_data['item_style_presets_aio'],
					'ITEM_STYLE_SOURCE'			=> isset($item_data['item_style_source']) && $item_data['item_style_source'],
					'ITEM_STYLE_RESPONSIVE'		=> isset($item_data['item_style_responsive']) && $item_data['item_style_responsive'],
					'ITEM_STYLE_BOOTSTRAP'		=> isset($item_data['item_style_bootstrap']) && $item_data['item_style_bootstrap'],
					'ITEM_PRICE'				=> isset($item_data['item_price']) ? $item_data['item_price'] : 0,
					'ITEM_URL'					=> isset($item_data['item_url']) ? $item_data['item_url'] : '',
					'ITEM_GITHUB'				=> isset($item_data['item_github']) ? $item_data['item_github'] : '',

					'CAT_OPTIONS'	=> $cat_options,
					'LANG_OPTIONS'	=> ($mode == 'lang') ? $lang_options : '',
					'OS_OPTIONS'	=> ($mode == 'tool') ? $os_options : '',

					'MODE'	=> $mode,

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
				$item_name = $this->request->variable('item_name', '', true);
				$item_name_vi = $this->request->variable('item_name_vi', '', true);
				$item_varname = strtolower($this->request->variable('item_varname', ''));
				$item_version = $this->request->variable('item_version', '');
				$item_desc = $this->request->variable('item_desc', '', true);
				$item_desc_vi = $this->request->variable('item_desc_vi', '', true);
				$item_ext_style = $this->request->variable('item_ext_style', false);
				$item_ext_acp_style = $this->request->variable('item_ext_acp_style', false);
				$item_ext_lang = $this->request->variable('item_ext_lang', false);
				$item_ext_db_schema = $this->request->variable('item_ext_db_schema', false);
				$item_ext_db_data = $this->request->variable('item_ext_db_data', false);
				$item_style_presets = $this->request->variable('item_style_presets', 0);
				$item_style_presets_aio = $this->request->variable('item_style_presets_aio', false);
				$item_style_source = $this->request->variable('item_style_source', false);
				$item_style_responsive = $this->request->variable('item_style_responsive', false);
				$item_style_bootstrap = $this->request->variable('item_style_bootstrap', false);
				$item_lang_iso = $this->request->variable('item_lang_iso', constants::LANG_VIETNAMESE);
				$item_tool_os = $this->request->variable('item_tool_os', constants::OS_ALL);
				$item_price = $this->request->variable('item_price', 0);
				$item_url = $this->request->variable('item_url', '');
				$item_github = $this->request->variable('item_github', '');

				if (empty($item_name) || empty($item_name_vi))
				{
					$errors[] = $this->language->lang('ERROR_BB_ITEM_NAME_EMPTY');
				}

				if (empty($item_varname))
				{
					$errors[] = $this->language->lang('ERROR_BB_ITEM_VARNAME_EMPTY');
				}
				else
				{
					$sql = 'SELECT *
						FROM ' . $this->bb_categories_table . '
						WHERE bb_type = ' . $this->bb_type . "
							AND item_varname = '" . $this->db->sql_escape($item_varname) . "'";
					$result = $this->db->sql_query($sql);
					$rows = $this->db->sql_fetchrowset($result);
					$this->db->sql_freeresult($result);

					if (sizeof($rows))
					{
						$errors[] = $this->language->lang('ERROR_BB_ITEM_VARNAME_DUPLICATE', $item_varname);
					}
				}

				if (sizeof($errors))
				{
					trigger_error(implode('<br>', $errors) . adm_back_link($this->u_action), E_USER_WARNING);
				}

				$sql_ary = array(
					'bb_type'					=> $this->bb_type,
					'item_name'					=> $item_name,
					'item_name_vi'				=> $item_name_vi,
					'item_varname'				=> $item_varname,
					'item_version'				=> $item_version,
					'item_desc'					=> $item_desc,
					'item_desc_vi'				=> $item_desc_vi,
					'item_ext_style'			=> $item_ext_style,
					'item_ext_acp_style'		=> $item_ext_acp_style,
					'item_ext_lang'				=> $item_ext_lang,
					'item_ext_db_schema'		=> $item_ext_db_schema,
					'item_ext_db_data'			=> $item_ext_db_data,
					'item_style_presets'		=> $item_style_presets,
					'item_style_presets_aio'	=> $item_style_presets_aio,
					'item_style_source'			=> $item_style_source,
					'item_style_responsive'		=> $item_style_responsive,
					'item_style_bootstrap'		=> $item_style_bootstrap,
					'item_lang_iso'				=> $item_lang_iso,
					'item_tool_os'				=> $item_tool_os,
					'item_price'				=> $item_price,
					'item_url'					=> $item_url,
					'item_github'				=> $item_github,
				);

				if ($item_id)
				{
					$this->db->sql_query('UPDATE ' . $this->bb_items_table . ' SET ' . $this->db->sql_build_array('UPDATE', $sql_ary) . ' WHERE item_id = ' . $item_id);
				}
				else
				{
					$this->db->sql_query('INSERT INTO ' . $this->bb_items_table . ' ' . $this->db->sql_build_array('INSERT', $sql_ary));
				}

				$log_action = ($item_id) ? 'LOG_BB_' . strtoupper($mode) . '_CAT_EDIT' : 'LOG_BB_' . strtoupper($mode) . '_CAT_ADD';
				$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, $log_action, false, array($item_name));

				$message = ($item_id) ? $this->language->lang('MESSAGE_BB_ITEM_EDIT') : $this->language->lang('MESSAGE_BB_ITEM_ADD');
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
					confirm_box(false, $this->language->lang('CONFIRM_BB_ITEM_DELETE'), build_hidden_fields(array(
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
				'NAME'				=> $row['item_name'],
				'NAME_VI'			=> $row['item_name_vi'],
				'VARNAME'			=> $row['item_varname'],
				'VERSION'			=> $row['item_version'],
				'DESC'				=> $row['item_desc'],
				'DESC_VI'			=> $row['item_desc_vi'],
				'EXT_STYLE'			=> $row['item_ext_style'],
				'EXT_ACP_STYLE'		=> $row['item_ext_acp_style'],
				'EXT_LANG'			=> $row['item_ext_lang'],
				'EXT_DB_SCHEMA'		=> $row['item_ext_db_schema'],
				'EXT_DB_DATA'		=> $row['item_ext_db_data'],
				'STYLE_PRESETS'		=> $row['item_style_presets'],
				'STYLE_PRESETS_AIO'	=> $row['item_style_presets_aio'],
				'STYLE_SOURCE'		=> $row['item_style_source'],
				'STYLE_RESPONSIVE'	=> $row['item_style_responsive'],
				'STYLE_BOOTSTRAP'	=> $row['item_style_bootstrap'],
				'LANG_ISO'			=> ($mode == 'lang' && isset($this->lang_data[$row['item_lang_iso']])) ? $this->lang_data[$row['item_lang_iso']] : '',
				'TOOL_OS'			=> ($mode == 'tool') ? $this->ext_helper->get_os_name($row['item_tool_os']) : '',
				'PRICE'				=> $row['item_price'],
				'URL'				=> $row['item_url'],
				'GITHUB'			=> $row['item_github'],

				'U_EDIT'	=> $this->u_action . '&action=edit&id=' . $row['item_id'],
				'U_DELETE'	=> $this->u_action . '&action=delete&id=' . $row['item_id'],
			));
		}

		$this->pagination->generate_template_pagination($this->u_action, 'pagination', 'start', $item_count, $per_page, $start);

		// Output
		$this->template->assign_vars(array(
			'MODE'					=> $mode,
			'TOTAL_ITEMS'			=> $item_count,
			'PAGE_TITLE_EXPLAIN'	=> $this->language->lang('ACP_BB_' . strtoupper($mode) . 'S_EXPLAIN'),
			'ADD_ITEM_LANG'			=> $this->language->lang('ADD_BB_' . strtoupper($mode)),
			'ITEM_NAME_LANG'		=> $this->language->lang(strtoupper($mode) . '_NAME'),
			'ITEM_VERSION_LANG'		=> $this->language->lang(strtoupper($mode) . '_VERSION'),

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
