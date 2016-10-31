<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\acp;

use vinabb\web\includes\constants;

class portal_categories_module
{
	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\cache\service */
	protected $cache;

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

	/**
	* Main method of module
	*
	* @param $id
	* @param $mode
	*/
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
		$this->ext_helper = $phpbb_container->get('vinabb.web.helper');

		$this->tpl_name = 'acp_portal_categories';
		$this->page_title = $this->language->lang('ACP_PORTAL_CATS');
		$this->table_prefix = $phpbb_container->getParameter('core.table_prefix');
		$this->portal_categories_table = $this->table_prefix . constants::PORTAL_CATEGORIES_TABLE;
		$this->portal_articles_table = $this->table_prefix . constants::PORTAL_ARTICLES_TABLE;

		// Language
		$this->language->add_lang('acp_portal', 'vinabb/web');

		// Common variables
		$action = $this->request->variable('action', '');
		$action = $this->request->is_set_post('add') ? 'add' : ($this->request->is_set_post('save') ? 'save' : $action);
		$cat_id = $this->request->variable('id', 0);

		// Pagination
		$start = $this->request->variable('start', 0);
		$per_page = constants::PORTAL_CATS_PER_PAGE;

		add_form_key('vinabb/web');

		$s_hidden_fields = '';
		$errors = array();

		switch ($action)
		{
			case 'edit':
				if (!$cat_id)
				{
					trigger_error($this->language->lang('NO_PORTAL_CAT_ID') . adm_back_link($this->u_action), E_USER_WARNING);
				}

				$sql = 'SELECT *
					FROM ' . $this->portal_categories_table . "
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
					'CAT_ICON'		=> isset($cat_data['cat_icon']) ? $cat_data['cat_icon'] : '',

					'MODULE'	=> $id,
					'MODE'		=> $mode,

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

				$cat_name = $this->request->variable('cat_name', '', true);
				$cat_name_vi = $this->request->variable('cat_name_vi', '', true);
				$cat_varname = strtolower($this->request->variable('cat_varname', ''));
				$cat_icon = strtolower($this->request->variable('cat_icon', ''));

				if (empty($cat_name) || empty($cat_name_vi))
				{
					$errors[] = $this->language->lang('ERROR_PORTAL_CAT_NAME_EMPTY');
				}

				if (empty($cat_varname))
				{
					$errors[] = $this->language->lang('ERROR_PORTAL_CAT_VARNAME_EMPTY');
				}
				else if (!preg_match('#^[a-z0-9-]+$#', $cat_varname))
				{
					$errors[] = $this->language->lang('ERROR_PORTAL_CAT_VARNAME_INVALID');
				}
				else
				{
					$sql_and = ($cat_id) ? "AND cat_id <> $cat_id" : '';

					$sql = 'SELECT *
						FROM ' . $this->portal_categories_table . "
						WHERE cat_varname = '" . $this->db->sql_escape($cat_varname) . "'
							$sql_and";
					$result = $this->db->sql_query($sql);
					$rows = $this->db->sql_fetchrowset($result);
					$this->db->sql_freeresult($result);

					if (sizeof($rows))
					{
						$errors[] = $this->language->lang('ERROR_PORTAL_CAT_VARNAME_DUPLICATE', $cat_varname);
					}
				}

				if (sizeof($errors))
				{
					trigger_error(implode('<br>', $errors) . adm_back_link($this->u_action), E_USER_WARNING);
				}

				$sql_ary = array(
					'cat_name'		=> $cat_name,
					'cat_name_vi'	=> $cat_name_vi,
					'cat_varname'	=> $cat_varname,
					'cat_icon'		=> $cat_icon,
				);

				if ($cat_id)
				{
					$this->db->sql_query('UPDATE ' . $this->portal_categories_table . ' SET ' . $this->db->sql_build_array('UPDATE', $sql_ary) . ' WHERE cat_id = ' . $cat_id);
				}
				else
				{
					$sql = 'SELECT COUNT(cat_id) AS cat_count
						FROM ' . $this->portal_categories_table;
					$result = $this->db->sql_query($sql);
					$cat_count = $this->db->sql_fetchfield('cat_count');
					$this->db->sql_freeresult($result);

					$sql_ary = array_merge($sql_ary, array(
						'cat_order'		=> $cat_count + 1,
					));

					$this->db->sql_query('INSERT INTO ' . $this->portal_categories_table . ' ' . $this->db->sql_build_array('INSERT', $sql_ary));
				}

				$this->cache->clear_portal_cats();

				$log_action = ($cat_id) ? 'LOG_PORTAL_CAT_EDIT' : 'LOG_PORTAL_CAT_ADD';
				$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, $log_action, false, array($cat_name));

				$message = ($cat_id) ? $this->language->lang('MESSAGE_PORTAL_CAT_EDIT') : $this->language->lang('MESSAGE_PORTAL_CAT_ADD');
				trigger_error($message . adm_back_link($this->u_action));
			break;

			case 'move_up':
			case 'move_down':
				if (!$cat_id)
				{
					trigger_error($this->language->lang('NO_PORTAL_CAT_ID') . adm_back_link($this->u_action), E_USER_WARNING);
				}

				$sql = 'SELECT cat_order
					FROM ' . $this->portal_categories_table . "
					WHERE cat_id = $cat_id";
				$result = $this->db->sql_query($sql);
				$order = $this->db->sql_fetchfield('cat_order');
				$this->db->sql_freeresult($result);

				if ($order === false || ($order == 0 && $action == 'move_up'))
				{
					break;
				}

				$order = (int) $order;
				$order_total = $order * 2 + (($action == 'move_up') ? -1 : 1);

				$sql = 'UPDATE ' . $this->portal_categories_table . '
					SET cat_order = ' . $order_total . ' - cat_order
					WHERE ' . $this->db->sql_in_set('cat_order', array($order, ($action == 'move_up') ? $order - 1 : $order + 1));
				$this->db->sql_query($sql);

				if ($this->request->is_ajax())
				{
					$json_response = new \phpbb\json_response;
					$json_response->send(array(
						'success'	=> (bool) $this->db->sql_affectedrows(),
					));
				}
			break;

			case 'delete':
				$cat_id = $this->request->variable('id', 0);

				if (!$cat_id)
				{
					trigger_error($this->language->lang('NO_PORTAL_CAT_ID') . adm_back_link($this->u_action), E_USER_WARNING);
				}

				if (confirm_box(true))
				{
					$sql = 'SELECT COUNT(article_id) AS article_count
						FROM ' . $this->portal_articles_table . "
						WHERE cat_id = $cat_id";
					$result = $this->db->sql_query($sql);
					$article_count = $this->db->sql_fetchfield('article_count');
					$this->db->sql_freeresult($result);

					if ($article_count)
					{
						trigger_error($this->language->lang('ERROR_PORTAL_CAT_DELETE') . adm_back_link($this->u_action), E_USER_WARNING);
					}

					$sql = 'SELECT cat_name
						FROM ' . $this->portal_categories_table . "
						WHERE cat_id = $cat_id";
					$result = $this->db->sql_query($sql);
					$cat_name = $this->db->sql_fetchfield('cat_name');
					$this->db->sql_freeresult($result);

					$sql = 'DELETE FROM ' . $this->portal_categories_table . "
						WHERE cat_id = $cat_id";
					$this->db->sql_query($sql);

					$this->cache->clear_portal_cats();

					$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_PORTAL_CAT_DELETE', false, array($cat_name));

					trigger_error($this->language->lang('MESSAGE_PORTAL_CAT_DELETE') . adm_back_link($this->u_action));
				}
				else
				{
					confirm_box(false, $this->language->lang('CONFIRM_PORTAL_CAT_DELETE'), build_hidden_fields(array(
						'i'			=> $id,
						'mode'		=> $mode,
						'id'		=> $cat_id,
						'action'	=> 'delete',
					)));
				}
			break;
		}

		// Item counter
		$sql = 'SELECT cat_id, COUNT(article_id) AS article_count
			FROM ' . $this->portal_articles_table . '
			GROUP BY cat_id';
		$result = $this->db->sql_query($sql);

		$article_count = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$article_count[$row['cat_id']] = $row['article_count'];
		}
		$this->db->sql_freeresult($result);

		// Manage categories
		$cats = array();
		$cat_count = 0;
		$start = $this->list_portal_cats($this->portal_categories_table, $cats, $cat_count, $per_page, $start);

		foreach ($cats as $row)
		{
			$this->template->assign_block_vars('cats', array(
				'NAME'		=> $row['cat_name'],
				'NAME_VI'	=> $row['cat_name_vi'],
				'VARNAME'	=> $row['cat_varname'],
				'ICON'		=> $row['cat_icon'],
				'ARTICLES'	=> isset($article_count[$row['cat_id']]) ? $article_count[$row['cat_id']] : 0,

				'U_EDIT'		=> $this->u_action . '&action=edit&id=' . $row['cat_id'],
				'U_MOVE_UP'		=> $this->u_action . '&action=move_up&id=' . $row['cat_id'],
				'U_MOVE_DOWN'	=> $this->u_action . '&action=move_down&id=' . $row['cat_id'],
				'U_DELETE'		=> $this->u_action . '&action=delete&id=' . $row['cat_id'],
			));
		}

		$this->pagination->generate_template_pagination($this->u_action, 'pagination', 'start', $cat_count, $per_page, $start);

		// Output
		$this->template->assign_vars(array(
			'TOTAL_CATS'	=> $cat_count,

			'MODULE'	=> $id,
			'MODE'		=> $mode,

			'U_ACTION'	=> $this->u_action . "&action=$action&start=$start",

			'S_HIDDEN_FIELDS'	=> $s_hidden_fields,
		));
	}

	/**
	* List categories with pagination
	*
	* @param     $portal_categories_table
	* @param     $cats
	* @param     $cat_count
	* @param int $limit
	* @param int $offset
	*
	* @return int
	*/
	private function list_portal_cats(&$portal_categories_table, &$cats, &$cat_count, $limit = 0, $offset = 0)
	{
		$sql = "SELECT COUNT(cat_id) AS cat_count
			FROM $portal_categories_table";
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
			FROM $portal_categories_table
			ORDER BY cat_order";
		$result = $this->db->sql_query_limit($sql, $limit, $offset);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$cats[] = $row;
		}
		$this->db->sql_freeresult($result);

		return $offset;
	}
}
