<?php
/**
 * This file is part of the VinaBB.vn package.
 *
 * @copyright (c) VinaBB <vinabb.vn>
 * @license GNU General Public License, version 2 (GPL-2.0)
 */

namespace vinabb\web\controller;

class embed
{
	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var string */
	protected $phpbb_root_path;

	/** @var string */
	protected $php_ext;

	/**
	* Constructor
	*
	* @param \phpbb\auth\auth $auth
	* @param \phpbb\db\driver\driver_interface $db
	* @param \phpbb\user $user
	* @param \phpbb\language\language $language
	* @param \phpbb\template\template $template
	* @param \phpbb\request\request $request
	* @param \phpbb\controller\helper $helper
	* @param string $phpbb_root_path
	* @param string $php_ext
	*/
	public function __construct(\phpbb\auth\auth $auth,
								\phpbb\db\driver\driver_interface $db,
								\phpbb\user $user,
								\phpbb\language\language $language,
								\phpbb\template\template $template,
								\phpbb\request\request $request,
								\phpbb\controller\helper $helper,
								$phpbb_root_path,
								$php_ext)
	{
		$this->auth = $auth;
		$this->db = $db;
		$this->user = $user;
		$this->language = $language;
		$this->template = $template;
		$this->request = $request;
		$this->helper = $helper;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->php_ext = $php_ext;
	}

	/**
	* Embed page of a post, rediect to the first post
	* @param $topic_id
	*/
	public function topic($topic_id)
	{
		if (!$topic_id)
		{
			trigger_error('NO_TOPIC');
		}

		$sql = 'SELECT topic_first_post_id
			FROM ' . TOPICS_TABLE . "
			WHERE topic_id = $topic_id";
		$result = $this->db->sql_query($sql);
		$post_id = (int) $this->db->sql_fetchfield('topic_first_post_id');
		$this->db->sql_freeresult($result);

		if ($post_id)
		{
			redirect($this->helper->route('vinabb_web_embed_post_route', array('post_id' => $post_id)));
		}
		else
		{
			trigger_error('NO_TOPIC');
		}
	}

	/**
	* Embed page of a post
	*
	* @param $post_id
	* @return \Symfony\Component\HttpFoundation\Response
	*/
	public function post($post_id)
	{
		if (!$post_id)
		{
			trigger_error('NO_POST');
		}

		$sql = 'SELECT *
			FROM ' . POSTS_TABLE . "
			WHERE post_id = $post_id";
		$result = $this->db->sql_query($sql);

		if ($row = $this->db->sql_fetchrow($result))
		{
			$enable_bbcode = ($row['enable_bbcode']) ? OPTION_FLAG_BBCODE : 0;
			$enable_smilies = ($row['enable_smilies']) ? OPTION_FLAG_SMILIES : 0;
			$enable_magic_url = ($row['enable_magic_url']) ? OPTION_FLAG_LINKS : 0;
			$bbcode_options = $enable_bbcode ^ $enable_smilies ^ $enable_magic_url;

			if ($row['poster_id'])
			{
				$sql2 = 'SELECT username
					FROM ' . USERS_TABLE . '
					WHERE user_id = ' . $row['poster_id'];
				$result2 = $this->db->sql_query($sql2);
				$poster_username = $this->db->sql_fetchfield('username');
				$this->db->sql_freeresult($result2);
			}

			$this->template->assign_vars(array(
				'POST_SUBJECT'	=>truncate_string(generate_text_for_display($row['post_subject'], $row['bbcode_uid'], $row['bbcode_bitfield'], $bbcode_options), 40, 255, false, $this->language->lang('ELLIPSIS')),
				'POST_TEXT'		=> truncate_string(strip_tags(generate_text_for_display($row['post_text'], $row['bbcode_uid'], $row['bbcode_bitfield'], $bbcode_options)), 200, 255, false, $this->language->lang('ELLIPSIS')),
				'POST_URL'		=> append_sid("{$this->phpbb_root_path}viewtopic.{$this->php_ext}", 'f=' . $row['forum_id'] . '&t=' . $row['topic_id'] . '&p=' . $row['post_id'] . '#p' . $row['post_id']),
				'POSTER'		=> $poster_username,
				'POSTER_URL'	=> append_sid("{$this->phpbb_root_path}memberlist.{$this->php_ext}", 'mode=viewprofile&u=' . $row['poster_id']),
				'POST_TIME'		=> $this->user->format_date($row['post_time'], 'd/m/Y H:i')
			));
		}
		$this->db->sql_freeresult($result);

		return $this->helper->render('embed_post.html', '');
	}
}
