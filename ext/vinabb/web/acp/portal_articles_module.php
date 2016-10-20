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
	/** @var string */
	public $u_action;

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

		$this->table_prefix = $phpbb_container->getParameter('core.table_prefix');
		$this->portal_categories_table = $this->table_prefix . constants::PORTAL_CATEGORIES_TABLE;
		$this->portal_articles_table = $this->table_prefix . constants::PORTAL_ARTICLES_TABLE;

		$this->tpl_name = 'acp_portal_articles';
		$this->page_title = $this->language->lang('ACP_PORTAL_ARTICLES');
		$this->language->add_lang('acp_portal', 'vinabb/web');

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
				$this->template->assign_vars(array(
					'ARTICLE_NAME'	=> isset($cat_data['cat_name']) ? $cat_data['cat_name'] : '',
					'ARTICLE_DESC'	=> isset($cat_data['cat_desc']) ? $cat_data['cat_desc'] : '',

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

				$article_name = $this->request->variable('article_name', '', true);
				$article_lang = $this->request->variable('article_lang', constants::LANG_VIETNAMESE);

				if (empty($cat_name))
				{
					$errors[] = $this->language->lang('ERROR_ARTICLE_NAME_EMPTY');
				}

				if (sizeof($errors))
				{
					trigger_error(implode('<br>', $errors) . adm_back_link($this->u_action), E_USER_WARNING);
				}

				$sql_ary = array(
					'article_name'		=> $article_name,
					'article_name_seo'	=> $this->ext_helper->clean_url($article_name),
					'article_lang'		=> $article_lang,
				);

				if ($article_id)
				{
					$this->db->sql_query('UPDATE ' . $this->portal_articles_table . ' SET ' . $this->db->sql_build_array('UPDATE', $sql_ary) . ' WHERE cat_id = ' . $article_id);
				}
				else
				{
					$this->db->sql_query('INSERT INTO ' . $this->portal_articles_table . ' ' . $this->db->sql_build_array('INSERT', $sql_ary));
					$this->config->increment('vinabb_web_total_articles', 1, true);
				}

				$this->cache->clear_portal_cat_data($mode);

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
					$sql = 'SELECT article_name
						FROM ' . $this->portal_articles_table . "
						WHERE article_id = $article_id";
					$result = $this->db->sql_query($sql);
					$article_name = $this->db->sql_fetchfield('article_name');
					$this->db->sql_freeresult($result);

					$sql = 'DELETE FROM ' . $this->portal_articles_table . "
						WHERE article_id = $article_id";
					$this->db->sql_query($sql);

					$this->config->increment('vinabb_web_total_articles', -1, true);

					$this->cache->clear_index_articles();

					$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_PORTAL_ARTICLE_DELETE', false, array($article_name));

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

		// Manage categories
		$articles = array();
		$article_count = 0;
		$start = $this->list_articles($this->portal_articles_table, $articles, $article_count, $per_page, $start);

		foreach ($articles as $row)
		{
			$this->template->assign_block_vars('articles', array(
				'NAME'		=> $row['article_name'],

				'U_EDIT'	=> $this->u_action . '&action=edit&id=' . $row['article_id'],
				'U_DELETE'	=> $this->u_action . '&action=delete&id=' . $row['article_id'],
			));
		}

		$this->pagination->generate_template_pagination($this->u_action, 'pagination', 'start', $article_count, $per_page, $start);

		// Output
		$this->template->assign_vars(array(
			'TOTAL_ARTICLES'	=> $article_count,

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
