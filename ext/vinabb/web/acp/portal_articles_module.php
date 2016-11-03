<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\acp;

use vinabb\web\includes\constants;

class portal_articles_module
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
	protected $table_prefix;

	/** @var string */
	protected $portal_categories_table;

	/** @var string */
	protected $portal_articles_table;

	/** @var array */
	protected $cat_data;

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

		$this->tpl_name = 'acp_portal_articles';
		$this->page_title = $this->language->lang('ACP_PORTAL_ARTICLES');
		$this->table_prefix = $phpbb_container->getParameter('core.table_prefix');
		$this->portal_categories_table = $this->table_prefix . constants::PORTAL_CATEGORIES_TABLE;
		$this->portal_articles_table = $this->table_prefix . constants::PORTAL_ARTICLES_TABLE;
		$this->cat_data = $this->cache->get_portal_cats();

		// Build custom BBCodes
		if (!function_exists('display_custom_bbcodes'))
		{
			include "{$phpbb_root_path}includes/functions_display.$phpEx";
			display_custom_bbcodes();
		}

		// Language
		$this->language->add_lang('posting');
		$this->language->add_lang('acp_portal', 'vinabb/web');

		// Common variables
		$action = $this->request->variable('action', '');
		$action = $this->request->is_set_post('add') ? 'add' : ($this->request->is_set_post('save') ? 'save' : $action);
		$article_id = $this->request->variable('id', 0);

		// Pagination
		$start = $this->request->variable('start', 0);
		$per_page = constants::PORTAL_ARTICLES_PER_PAGE;

		add_form_key('vinabb/web');

		$s_hidden_fields = '';
		$errors = array();

		switch ($action)
		{
			case 'edit':
				if (!$article_id)
				{
					trigger_error($this->language->lang('NO_ARTICLE_ID') . adm_back_link($this->u_action), E_USER_WARNING);
				}

				$sql = 'SELECT *
					FROM ' . $this->portal_articles_table . "
					WHERE article_id = $article_id";
				$result = $this->db->sql_query($sql);
				$article_data = $this->db->sql_fetchrow($result);
				$this->db->sql_freeresult($result);

				$s_hidden_fields .= '<input type="hidden" name="id" value="' . $article_id . '" />';
			// No break

			case 'add':
				// Select a category
				$sql = 'SELECT *
					FROM ' . $this->portal_categories_table . '
					ORDER BY cat_name';
				$result = $this->db->sql_query($sql);
				$rows = $this->db->sql_fetchrowset($result);
				$this->db->sql_freeresult($result);

				$article_cat = isset($article_data['cat_id']) ? $article_data['cat_id'] : 0;
				$cat_options = '<option value=""' . (($article_cat == 0) ? ' selected' : '') . '>' . $this->language->lang('SELECT_CATEGORY') . '</option>';

				foreach ($rows as $row)
				{
					$cat_options .= '<option value="' . $row['cat_id'] . '"' . (($article_cat == $row['cat_id']) ? ' selected' : '') . '>' . $row['cat_name'] . ' (' . $row['cat_name_vi'] . ')</option>';
				}

				// Select a language
				$sql = 'SELECT *
					FROM ' . LANG_TABLE . '
					ORDER BY lang_english_name';
				$result = $this->db->sql_query($sql);
				$rows = $this->db->sql_fetchrowset($result);
				$this->db->sql_freeresult($result);

				$article_lang = isset($article_data['article_lang']) ? $article_data['article_lang'] : $this->config['default_lang'];
				$lang_options = '<option value=""' . (($article_lang == '') ? ' selected' : '') . '>' . $this->language->lang('SELECT_LANGUAGE') . '</option>';

				foreach ($rows as $row)
				{
					$lang_options .= '<option value="' . $row['lang_iso'] . '"' . (($article_lang == $row['lang_iso']) ? ' selected' : '') . '>' . $row['lang_english_name'] . ' (' . $row['lang_local_name'] . ')</option>';
				}

				// Prepare a fresh article preview
				if (!isset($article_data['article_text']))
				{
					$article_data['article_text'] = $article_data['article_text_uid'] = $article_data['article_text_bitfield'] = $article_text_preview = '';
					$article_data['article_text_options'] = 7;
				}
				else
				{
					$article_text_preview = generate_text_for_display($article_data['article_text'], $article_data['article_text_uid'], $article_data['article_text_bitfield'], $article_data['article_text_options']);
				}

				// Prepare the article text for editing inside the textbox
				$article_text_edit = generate_text_for_edit($article_data['article_text'], $article_data['article_text_uid'], $article_data['article_text_options']);

				$this->template->assign_vars(array(
					'ARTICLE_NAME'			=> isset($article_data['article_name']) ? $article_data['article_name'] : '',
					'ARTICLE_DESC'			=> isset($article_data['article_desc']) ? $article_data['article_desc'] : '',
					'ARTICLE_TEXT'			=> $article_text_edit['text'],
					'ARTICLE_TEXT_PREVIEW'	=> $article_text_preview,

					'BBCODE_DISABLED'	=> !$article_text_edit['allow_bbcode'],
					'URLS_DISABLED'		=> !$article_text_edit['allow_urls'],
					'SMILIES_DISABLED'	=> !$article_text_edit['allow_smilies'],

					'CAT_OPTIONS'	=> $cat_options,
					'LANG_OPTIONS'	=> $lang_options,

					'MODULE'	=> $id,
					'MODE'		=> $mode,
					'ACTION'	=> $action,

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
				$article_name = $this->request->variable('article_name', '', true);
				$article_lang = $this->request->variable('article_lang', '');
				$article_desc = $this->request->variable('article_desc', '', true);
				$article_text = $this->request->variable('article_text', '', true);
				$revision = $this->request->variable('revision', false);

				if (!$cat_id)
				{
					$errors[] = $this->language->lang('ERROR_ARTICLE_CAT_SELECT');
				}

				if (empty($article_name))
				{
					$errors[] = $this->language->lang('ERROR_ARTICLE_NAME_EMPTY');
				}

				if (empty($article_lang))
				{
					$errors[] = $this->language->lang('ERROR_ARTICLE_LANG_SELECT');
				}

				if (empty($article_desc))
				{
					$errors[] = $this->language->lang('ERROR_ARTICLE_DESC_EMPTY');
				}

				if (empty($article_text))
				{
					$errors[] = $this->language->lang('ERROR_ARTICLE_TEXT_EMPTY');
				}

				if (sizeof($errors))
				{
					trigger_error(implode('<br>', $errors) . adm_back_link($this->u_action), E_USER_WARNING);
				}

				$sql_ary = array(
					'cat_id'				=> $cat_id,
					'article_name'			=> $article_name,
					'article_name_seo'		=> $this->ext_helper->clean_url($article_name),
					'article_lang'			=> $article_lang,
					'article_desc'			=> $article_desc,
					'article_text'			=> $article_text,
					'article_text_uid'		=> '',
					'article_text_bitfield'	=> '',
					'article_text_options'	=> 7,
				);

				if (!$article_id || ($article_id && $revision))
				{
					$sql_ary = array_merge($sql_ary, array(
						'article_time'	=> time()
					));
				}

				// Prepare article text for storage
				if ($sql_ary['article_text'])
				{
					generate_text_for_storage($sql_ary['article_text'], $sql_ary['article_text_uid'], $sql_ary['article_text_bitfield'], $sql_ary['article_text_options'], !$this->request->variable('disable_bbcode', false), !$this->request->variable('disable_urls', false), !$this->request->variable('disable_smilies', false));
				}

				if ($article_id)
				{
					$this->db->sql_query('UPDATE ' . $this->portal_articles_table . ' SET ' . $this->db->sql_build_array('UPDATE', $sql_ary) . ' WHERE article_id = ' . $article_id);
				}
				else
				{
					$this->db->sql_query('INSERT INTO ' . $this->portal_articles_table . ' ' . $this->db->sql_build_array('INSERT', $sql_ary));
					$this->config->increment('vinabb_web_total_articles', 1, true);
				}

				$this->cache->clear_index_articles($article_lang);

				$log_action = ($article_id) ? 'LOG_PORTAL_ARTICLE_EDIT' : 'LOG_PORTAL_ARTICLE_ADD';
				$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, $log_action, false, array($article_name));

				$message = ($article_id) ? $this->language->lang('MESSAGE_ARTICLE_EDIT') : $this->language->lang('MESSAGE_ARTICLE_ADD');
				trigger_error($message . adm_back_link($this->u_action));
			break;

			case 'delete':
				if (!$article_id)
				{
					trigger_error($this->language->lang('NO_ARTICLE_ID') . adm_back_link($this->u_action), E_USER_WARNING);
				}

				if (confirm_box(true))
				{
					$sql = 'SELECT article_name, article_lang
						FROM ' . $this->portal_articles_table . "
						WHERE article_id = $article_id";
					$result = $this->db->sql_query($sql);
					$article_data = $this->db->sql_fetchrow($result);
					$this->db->sql_freeresult($result);

					$sql = 'DELETE FROM ' . $this->portal_articles_table . "
						WHERE article_id = $article_id";
					$this->db->sql_query($sql);

					$this->config->increment('vinabb_web_total_articles', -1, true);

					$this->cache->clear_index_articles($article_data['article_lang']);

					$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_PORTAL_ARTICLE_DELETE', false, array($article_data['article_name']));

					trigger_error($this->language->lang('MESSAGE_ARTICLE_DELETE') . adm_back_link($this->u_action));
				}
				else
				{
					confirm_box(false, $this->language->lang('CONFIRM_ARTICLE_DELETE'), build_hidden_fields(array(
						'i'			=> $id,
						'mode'		=> $mode,
						'id'		=> $article_id,
						'action'	=> 'delete',
					)));
				}
			break;
		}

		// Manage articles
		$articles = array();
		$article_count = 0;
		$start = $this->list_articles($this->portal_articles_table, $articles, $article_count, $per_page, $start);

		foreach ($articles as $row)
		{
			$this->template->assign_block_vars('articles', array(
				'CATEGORY'	=> ($this->user->lang_name == constants::LANG_VIETNAMESE) ? $this->cat_data[$row['cat_id']]['name_vi'] : $this->cat_data[$row['cat_id']]['name'],
				'NAME'		=> $row['article_name'],
				'DESC'		=> $row['article_desc'],
				'TEXT'		=> generate_text_for_display($row['article_text'], $row['article_text_uid'], $row['article_text_bitfield'], $row['article_text_options']),

				'U_EDIT'	=> $this->u_action . '&action=edit&id=' . $row['article_id'],
				'U_DELETE'	=> $this->u_action . '&action=delete&id=' . $row['article_id'],
			));
		}

		$this->pagination->generate_template_pagination($this->u_action, 'pagination', 'start', $article_count, $per_page, $start);

		// Output
		$this->template->assign_vars(array(
			'TOTAL_ARTICLES'	=> $article_count,

			'MODULE'	=> $id,
			'MODE'		=> $mode,

			'U_ACTION'	=> $this->u_action . "&action=$action&start=$start",

			'S_HIDDEN_FIELDS'	=> $s_hidden_fields,
		));
	}

	/**
	* List articles with pagination
	*
	* @param     $portal_articles_table
	* @param     $articles
	* @param     $article_count
	* @param int $limit
	* @param int $offset
	*
	* @return int
	*/
	private function list_articles(&$portal_articles_table, &$articles, &$article_count, $limit = 0, $offset = 0)
	{
		$sql = "SELECT COUNT(article_id) AS article_count
			FROM $portal_articles_table";
		$result = $this->db->sql_query($sql);
		$article_count = (int) $this->db->sql_fetchfield('article_count');
		$this->db->sql_freeresult($result);

		if ($article_count == 0)
		{
			return 0;
		}

		if ($offset >= $article_count)
		{
			$offset = ($offset - $limit < 0) ? 0 : $offset - $limit;
		}

		$sql = "SELECT *
			FROM $portal_articles_table
			ORDER BY article_time";
		$result = $this->db->sql_query_limit($sql, $limit, $offset);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$articles[] = $row;
		}
		$this->db->sql_freeresult($result);

		return $offset;
	}
}
