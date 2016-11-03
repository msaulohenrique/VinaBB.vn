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
	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\cache\service */
	protected $cache;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\log\log */
	protected $log;

	/** @var \phpbb\pagination */
	protected $pagination;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var \vinabb\web\controller\helper */
	protected $ext_helper;

	/** @var string */
	public $tpl_name;

	/** @var string */
	public $page_title;

	/** @var string */
	public $u_action;

	/** @var string */
	protected $bb_type;

	/** @var string */
	protected $table_prefix;

	/** @var string */
	protected $bb_categories_table;

	/** @var string */
	protected $bb_items_table;

	/** @var array */
	protected $cat_data;

	/** @var array */
	protected $lang_data;

	/**
	* Main method of module
	*
	* @param $id
	* @param $mode
	*/
	public function main($id, $mode)
	{
		global $phpbb_root_path, $phpbb_container, $phpEx;

		$this->auth = $phpbb_container->get('auth');
		$this->cache = $phpbb_container->get('cache');
		$this->config = $phpbb_container->get('config');
		$this->db = $phpbb_container->get('dbal.conn');
		$this->language = $phpbb_container->get('language');
		$this->log = $phpbb_container->get('log');
		$this->pagination = $phpbb_container->get('pagination');
		$this->request = $phpbb_container->get('request');
		$this->template = $phpbb_container->get('template');
		$this->user = $phpbb_container->get('user');
		$this->ext_helper = $phpbb_container->get('vinabb.web.helper');

		$this->tpl_name = 'acp_bb_items';
		$this->page_title = $this->language->lang('ACP_BB_' . strtoupper($mode) . 'S');
		$this->bb_type = $this->ext_helper->get_bb_type_constants($mode);
		$this->table_prefix = $phpbb_container->getParameter('core.table_prefix');
		$this->bb_categories_table = $this->table_prefix . constants::BB_CATEGORIES_TABLE;
		$this->bb_items_table = $this->table_prefix . constants::BB_ITEMS_TABLE;
		$this->cat_data = $this->cache->get_bb_cats($this->bb_type);
		$this->lang_data = ($mode == 'lang') ? $this->cache->get_lang_data() : array();

		// Build custom BBCodes
		if (!function_exists('display_custom_bbcodes'))
		{
			include "{$phpbb_root_path}includes/functions_display.$phpEx";
			display_custom_bbcodes();
		}

		// Language
		$this->language->add_lang('posting');
		$this->language->add_lang('acp_bb', 'vinabb/web');

		// Common variables
		$action = $this->request->variable('action', '');
		$action = $this->request->is_set_post('add') ? 'add' : ($this->request->is_set_post('save') ? 'save' : $action);
		$item_id = $this->request->variable('id', 0);

		// Pagination
		$start = $this->request->variable('start', 0);
		$per_page = constants::BB_ITEMS_PER_PAGE;

		add_form_key('vinabb/web');

		$s_hidden_fields = '';
		$errors = array();

		switch ($action)
		{
			case 'edit':
				if (!$item_id)
				{
					trigger_error($this->language->lang('NO_BB_' . strtoupper($mode) . '_ID') . adm_back_link($this->u_action), E_USER_WARNING);
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
					WHERE bb_type = ' . $this->bb_type . '
					ORDER BY cat_name';
				$result = $this->db->sql_query($sql);
				$rows = $this->db->sql_fetchrowset($result);
				$this->db->sql_freeresult($result);

				$cat_id = isset($item_data['cat_id']) ? $item_data['cat_id'] : 0;
				$cat_options = '<option value=""' . (($cat_id == 0) ? ' selected' : '') . '>' . $this->language->lang('SELECT_CATEGORY') . '</option>';

				foreach ($rows as $row)
				{
					$cat_options .= '<option value="' . $row['cat_id'] . '"' . (($cat_id == $row['cat_id']) ? ' selected' : '') . '>' . $row['cat_name'] . ' (' . $row['cat_name_vi'] . ')</option>';
				}

				// Select a phpBB version
				$phpbb_versions = $this->ext_helper->get_phpbb_versions();
				$item_phpbb_version = isset($item_data['item_phpbb_version']) ? $item_data['item_phpbb_version'] : '';
				$phpbb_version_options = '<option value=""' . (($item_phpbb_version == '') ? ' selected' : '') . '>' . $this->language->lang('SELECT_PHPBB_VERSION') . '</option>';

				foreach ($phpbb_versions as $branch => $branch_data)
				{
					$phpbb_version_options .= '<optgroup label="' . $this->language->lang('PHPBB_VERSION_X', $branch) . '">"';

					foreach ($branch_data as $phpbb_version => $phpbb_version_data)
					{
						$phpbb_version_options .= '<option value="' . $phpbb_version . '"' . (($item_phpbb_version == $phpbb_version) ? ' selected' : '') . '>' . $phpbb_version_data['name'] . '</option>';
					}

					$phpbb_version_options .= '</optgroup>';
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

					$item_lang_iso = isset($item_data['item_lang_iso']) ? $item_data['item_lang_iso'] : $this->config['default_lang'];
					$lang_options = '<option value=""' . (($item_lang_iso == '') ? ' selected' : '') . '>' . $this->language->lang('SELECT_LANGUAGE') . '</option>';

					foreach ($rows as $row)
					{
						$lang_options .= '<option value="' . $row['lang_iso'] . '"' . (($item_lang_iso == $row['lang_iso']) ? ' selected' : '') . '>' . $row['lang_english_name'] . ' (' . $row['lang_local_name'] . ')</option>';
					}
				}

				// Select an OS
				if ($mode == 'tool')
				{
					$os_list = array(
						constants::OS_ALL,
						constants::OS_WIN,
						constants::OS_MAC,
						constants::OS_LINUX,
						constants::OS_BSD,
						constants::OS_ANDROID,
						constants::OS_IOS,
						constants::OS_WP,
					);

					$item_tool_os = isset($item_data['item_tool_os']) ? $item_data['item_tool_os'] : constants::OS_ALL;
					$os_options = '<option value=""' . (($item_tool_os == '') ? ' selected' : '') . '>' . $this->language->lang('SELECT_OS') . '</option>';

					foreach ($os_list as $os_value)
					{
						$os_options .= '<option value="' . $os_value . '"' . (($item_tool_os == $os_value) ? ' selected' : '') . '>' . (($os_value == constants::OS_ALL) ? $this->language->lang('OS_ALL') : $this->ext_helper->get_os_name($os_value)) . '</option>';
					}
				}

				// Prepare the article text for editing inside the textbox
				if (!isset($item_data['item_desc']))
				{
					$item_data['item_desc'] = $item_data['item_desc_uid'] = $item_data['item_desc_bitfield'] = '';
					$item_data['item_desc_options'] = 7;
				}

				$item_desc_edit = generate_text_for_edit($item_data['item_desc'], $item_data['item_desc_uid'], $item_data['item_desc_options']);

				if (!isset($item_data['item_desc_vi']))
				{
					$item_data['item_desc_vi'] = $item_data['item_desc_vi_uid'] = $item_data['item_desc_vi_bitfield'] = '';
					$item_data['item_desc_vi_options'] = 7;
				}

				$item_desc_vi_edit = generate_text_for_edit($item_data['item_desc_vi'], $item_data['item_desc_vi_uid'], $item_data['item_desc_vi_options']);

				$this->template->assign_vars(array(
					'ITEM_NAME'					=> isset($item_data['item_name']) ? $item_data['item_name'] : '',
					'ITEM_VARNAME'				=> isset($item_data['item_varname']) ? $item_data['item_varname'] : '',
					'ITEM_VERSION'				=> isset($item_data['item_version']) ? $item_data['item_version'] : '',
					'ITEM_DESC'					=> $item_desc_edit['text'],
					'ITEM_DESC_VI'				=> $item_desc_vi_edit['text'],
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

					'BBCODE_DISABLED'		=> !$item_desc_edit['allow_bbcode'],
					'URLS_DISABLED'			=> !$item_desc_edit['allow_urls'],
					'SMILIES_DISABLED'		=> !$item_desc_edit['allow_smilies'],
					'BBCODE_VI_DISABLED'	=> !$item_desc_vi_edit['allow_bbcode'],
					'URLS_VI_DISABLED'		=> !$item_desc_vi_edit['allow_urls'],
					'SMILIES_VI_DISABLED'	=> !$item_desc_vi_edit['allow_smilies'],

					'CAT_OPTIONS'			=> $cat_options,
					'PHPBB_VERSION_OPTIONS'	=> $phpbb_version_options,
					'LANG_OPTIONS'			=> ($mode == 'lang') ? $lang_options : '',
					'OS_OPTIONS'			=> ($mode == 'tool') ? $os_options : '',

					'MODE'	=> $mode,

					'ITEM_DETAILS_LANG'	=> $this->language->lang(strtoupper($mode) . '_DETAILS'),
					'ITEM_NAME_LANG'	=> $this->language->lang(strtoupper($mode) . '_NAME'),
					'ITEM_VARNAME_LANG'	=> $this->language->lang(strtoupper($mode) . '_VARNAME'),
					'ITEM_VERSION_LANG'	=> $this->language->lang(strtoupper($mode) . '_VERSION'),

					'MODULE'	=> $id,
					'MODE'		=> $mode,

					'U_ACTION'	=> $this->u_action,
					'U_BACK'	=> $this->u_action,

					'S_EDIT'			=> true,
					'S_HIDDEN_FIELDS'	=> $s_hidden_fields,
				));
			return;

			case 'save':
				if (!check_form_key('vinabb/web'))
				{
					$errors[] = $this->language->lang('FORM_INVALID');
				}

				$cat_id = $this->request->variable('cat_id', 0);
				$item_name = $this->request->variable('item_name', '', true);
				$item_varname = strtolower($this->request->variable('item_varname', ''));
				$item_version = strtoupper($this->request->variable('item_version', ''));
				$item_phpbb_version = $this->request->variable('item_phpbb_version', '');
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
				$item_lang_iso = $this->request->variable('item_lang_iso', '');
				$item_tool_os = $this->request->variable('item_tool_os', 0);
				$item_price = $this->request->variable('item_price', 0);
				$item_url = $this->request->variable('item_url', '');
				$item_github = $this->request->variable('item_github', '');

				if (!$cat_id)
				{
					$errors[] = $this->language->lang('ERROR_BB_ITEM_CAT_SELECT');
				}

				if (empty($item_name))
				{
					$errors[] = $this->language->lang('ERROR_BB_' . strtoupper($mode) . '_NAME_EMPTY');
				}

				if (empty($item_varname))
				{
					$errors[] = $this->language->lang('ERROR_BB_' . strtoupper($mode) . '_VARNAME_EMPTY');
				}
				else if (($mode == 'ext' && !preg_match('#^([a-z0-9-]+)\.([a-z0-9-]+)$#', $item_varname)) || ($mode != 'ext' && !preg_match('#^[a-z0-9-]+$#', $item_varname)))
				{
					$errors[] = $this->language->lang('ERROR_BB_' . strtoupper($mode) . '_VARNAME_INVALID');
				}
				else
				{
					$sql_and = ($item_id) ? "AND item_id <> $item_id" : '';

					$sql = 'SELECT *
						FROM ' . $this->bb_items_table . '
						WHERE bb_type = ' . $this->bb_type . "
							AND item_varname = '" . $this->db->sql_escape($item_varname) . "'
							$sql_and";
					$result = $this->db->sql_query($sql);
					$rows = $this->db->sql_fetchrowset($result);
					$this->db->sql_freeresult($result);

					if (sizeof($rows))
					{
						$errors[] = $this->language->lang('ERROR_BB_' . strtoupper($mode) . '_VARNAME_DUPLICATE', $item_varname);
					}
				}

				if (empty($item_version))
				{
					$errors[] = $this->language->lang('ERROR_BB_' . strtoupper($mode) . '_VERSION_EMPTY');
				}
				else if (!preg_match('#^\d+(\.\d){1,3}(\-(((?:A|B|RC|PL)\d+)|DEV))?$#', $item_version))
				{
					$errors[] = $this->language->lang('ERROR_BB_' . strtoupper($mode) . '_VERSION_INVALID');
				}

				if (empty($item_phpbb_version))
				{
					$errors[] = $this->language->lang('ERROR_BB_ITEM_PHPBB_VERSION_SELECT');
				}

				if (empty($item_desc) || empty($item_desc_vi))
				{
					$errors[] = $this->language->lang('ERROR_BB_ITEM_DESC_EMPTY');
				}

				if (sizeof($errors))
				{
					trigger_error(implode('<br>', $errors) . adm_back_link($this->u_action), E_USER_WARNING);
				}

				$sql_ary = array(
					'bb_type'				=> $this->bb_type,
					'cat_id'				=> $cat_id,
					'item_name'				=> $item_name,
					'item_varname'			=> $item_varname,
					'item_version'			=> $item_version,
					'item_phpbb_version'	=> $item_phpbb_version,
					'item_desc'				=> $item_desc,
					'item_desc_uid'			=> '',
					'item_desc_bitfield'	=> '',
					'item_desc_options'		=> 7,
					'item_desc_vi'			=> $item_desc_vi,
					'item_desc_vi_uid'		=> '',
					'item_desc_vi_bitfield'	=> '',
					'item_desc_vi_options'	=> 7,
					'item_price'			=> $item_price,
					'item_url'				=> $item_url,
					'item_github'			=> $item_github,
				);

				// Prepare description for storage
				if ($sql_ary['item_desc'])
				{
					generate_text_for_storage($sql_ary['item_desc'], $sql_ary['item_desc_uid'], $sql_ary['item_desc_bitfield'], $sql_ary['item_desc_options'], !$this->request->variable('disable_bbcode', false), !$this->request->variable('disable_urls', false), !$this->request->variable('disable_smilies', false));
				}

				if ($sql_ary['item_desc_vi'])
				{
					generate_text_for_storage($sql_ary['item_desc_vi'], $sql_ary['item_desc_vi_uid'], $sql_ary['item_desc_vi_bitfield'], $sql_ary['item_desc_vi_options'], !$this->request->variable('disable_bbcode_vi', false), !$this->request->variable('disable_urls_vi', false), !$this->request->variable('disable_smilies_vi', false));
				}

				// Properties
				switch ($mode)
				{
					case 'ext':
						$sql_ary = array_merge($sql_ary, array(
							'item_ext_style'		=> $item_ext_style,
							'item_ext_acp_style'	=> $item_ext_acp_style,
							'item_ext_lang'			=> $item_ext_lang,
							'item_ext_db_schema'	=> $item_ext_db_schema,
							'item_ext_db_data'		=> $item_ext_db_data,
						));
					break;

					case 'style':
					case 'acp_style':
						$sql_ary = array_merge($sql_ary, array(
							'item_style_presets'		=> $item_style_presets,
							'item_style_presets_aio'	=> $item_style_presets_aio,
							'item_style_source'			=> $item_style_source,
							'item_style_responsive'		=> $item_style_responsive,
							'item_style_bootstrap'		=> $item_style_bootstrap,
						));
					break;

					case 'lang':
						$sql_ary = array_merge($sql_ary, array(
							'item_lang_iso'	=> $item_lang_iso,
						));
					break;

					case 'tool':
						$sql_ary = array_merge($sql_ary, array(
							'item_tool_os'	=> $item_tool_os,
						));
					break;
				}

				if ($item_id)
				{
					// Is there a new version?
					$sql = 'SELECT item_version
						FROM ' . $this->bb_items_table . "
						WHERE item_id = $item_id";
					$result = $this->db->sql_query($sql);
					$item_old_version = $this->db->sql_fetchfield('item_version');
					$this->db->sql_freeresult($result);

					if (version_compare($item_version, $item_old_version, '>'))
					{
						$sql_ary = array_merge($sql_ary, array(
							'item_updated'	=> time()
						));
					}

					$this->db->sql_query('UPDATE ' . $this->bb_items_table . ' SET ' . $this->db->sql_build_array('UPDATE', $sql_ary) . ' WHERE item_id = ' . $item_id);
				}
				else
				{
					$sql_ary = array_merge($sql_ary, array(
						'item_added'	=> time()
					));

					$this->db->sql_query('INSERT INTO ' . $this->bb_items_table . ' ' . $this->db->sql_build_array('INSERT', $sql_ary));
					$this->config->increment('vinabb_web_total_bb_' . strtolower($mode) . 's', 1, true);
				}

				$this->cache->clear_new_bb_items($mode);

				$log_action = ($item_id) ? 'LOG_BB_' . strtoupper($mode) . '_EDIT' : 'LOG_BB_' . strtoupper($mode) . '_ADD';
				$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, $log_action, false, array($item_name));

				$message = ($item_id) ? $this->language->lang('MESSAGE_BB_' . strtoupper($mode) . '_EDIT') : $this->language->lang('MESSAGE_BB_' . strtoupper($mode) . '_ADD');
				trigger_error($message . adm_back_link($this->u_action));
			break;

			case 'delete':
				if (!$item_id)
				{
					trigger_error($this->language->lang('NO_BB_' . strtoupper($mode) . '_ID') . adm_back_link($this->u_action), E_USER_WARNING);
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

					$this->config->increment('vinabb_web_total_bb_' . strtolower($mode) . 's', -1, true);

					$this->cache->clear_new_bb_items($mode);

					$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_BB_' . strtoupper($mode) . '_DELETE', false, array($item_name));

					trigger_error($this->language->lang('MESSAGE_BB_' . strtoupper($mode) . '_DELETE') . adm_back_link($this->u_action));
				}
				else
				{
					confirm_box(false, $this->language->lang('CONFIRM_BB_' . strtoupper($mode) . '_DELETE'), build_hidden_fields(array(
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
				'CATEGORY'			=> ($this->user->lang_name == constants::LANG_VIETNAMESE) ? $this->cat_data[$row['cat_id']]['name_vi'] : $this->cat_data[$row['cat_id']]['name'],
				'NAME'				=> $row['item_name'],
				'VARNAME'			=> $row['item_varname'],
				'VERSION'			=> $row['item_version'],
				'PHPBB_VERSION'		=> $row['item_phpbb_version'],
				'DESC'				=> generate_text_for_display($row['item_desc'], $row['item_desc_uid'], $row['item_desc_bitfield'], $row['item_desc_options']),
				'DESC_VI'			=> generate_text_for_display($row['item_desc_vi'], $row['item_desc_vi_uid'], $row['item_desc_vi_bitfield'], $row['item_desc_vi_options']),
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
				'TOOL_OS'			=> ($mode == 'tool') ? (($row['item_tool_os']) ? $this->ext_helper->get_os_name($row['item_tool_os']) : $this->language->lang('OS_ALL')) : '',
				'PRICE'				=> $row['item_price'],
				'URL'				=> htmlspecialchars_decode($row['item_url']),
				'GITHUB'			=> htmlspecialchars_decode($row['item_github']),

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
			'ITEM_VARNAME_LANG'		=> $this->language->lang(strtoupper($mode) . '_VARNAME'),
			'ITEM_VERSION_LANG'		=> $this->language->lang(strtoupper($mode) . '_VERSION'),
			'COUNTER_ITEM_LANG_KEY'	=> 'COUNTER_' . strtoupper($mode) . 'S',

			'MODULE'	=> $id,
			'MODE'		=> $mode,

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
