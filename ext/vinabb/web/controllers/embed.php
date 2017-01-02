<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\controllers;

use Symfony\Component\DependencyInjection\ContainerInterface;
use vinabb\web\includes\constants;

class embed
{
	/** @var \phpbb\auth\auth $auth */
	protected $auth;

	/** @var ContainerInterface $container */
	protected $container;

	/** @var \phpbb\language\language $language */
	protected $language;

	/** @var \phpbb\template\template $template */
	protected $template;

	/** @var \phpbb\user $user */
	protected $user;

	/** @var \phpbb\controller\helper $helper */
	protected $helper;

	/**
	* Constructor
	*
	* @param \phpbb\auth\auth $auth
	* @param ContainerInterface									$container		Container object
	* @param \phpbb\language\language $language
	* @param \phpbb\template\template $template
	* @param \phpbb\user $user
	* @param \phpbb\controller\helper $helper
	*/
	public function __construct(
		\phpbb\auth\auth $auth,
		ContainerInterface $container,
		\phpbb\language\language $language,
		\phpbb\template\template $template,
		\phpbb\user $user,
		\phpbb\controller\helper $helper
	)
	{
		$this->auth = $auth;
		$this->container = $container;
		$this->language = $language;
		$this->template = $template;
		$this->user = $user;
		$this->helper = $helper;
	}

	/**
	* Embed page of a forum
	*
	* @param int $forum_id Forum ID
	* @return \Symfony\Component\HttpFoundation\Response
	* @throws \phpbb\exception\http_exception
	*/
	public function forum($forum_id)
	{
		try
		{
			/** @var \vinabb\web\entities\forum_interface $entity */
			$entity = $this->container->get('vinabb.web.entities.forum')->load($forum_id);
		}
		catch (\vinabb\web\exceptions\base $e)
		{
			throw new \phpbb\exception\http_exception(404, 'NO_FORUM');
		}

		// Ok forum exists, but check forum permissions
		if (!$this->auth->acl_gets('f_list', 'f_read', $forum_id) || ($entity->get_type() == FORUM_LINK && $entity->get_link() && !$this->auth->acl_get('f_read', $forum_id)))
		{
			send_status_line(403, 'Forbidden');
			trigger_error('SORRY_AUTH_READ');
		}

		$this->template->assign_vars([
			'FORUM_NAME'	=> $entity->get_name(),
			'FORUM_DESC'	=> truncate_string(strip_tags($entity->get_desc_for_display()), 200, 255, false, $this->language->lang('ELLIPSIS')),
			'FORUM_URL'		=> $this->helper->route('vinabb_web_board_forum_route', ['forum_id' => $forum_id, 'seo' => $entity->get_name_seo() . constants::REWRITE_URL_SEO])
		]);

		return $this->helper->render('embed_forum.html');
	}

	/**
	* Embed page of a post, rediect to the first post
	*
	* @param int $topic_id Topic ID
	* @throws \phpbb\exception\http_exception
	*/
	public function topic($topic_id)
	{
		try
		{
			/** @var \vinabb\web\entities\topic_interface $entity */
			$entity = $this->container->get('vinabb.web.entities.topic')->load($topic_id);
		}
		catch (\vinabb\web\exceptions\base $e)
		{
			throw new \phpbb\exception\http_exception(404, 'NO_TOPIC');
		}

		redirect($this->helper->route('vinabb_web_embed_post_route', ['post_id' => $entity->get_first_post_id()]));
	}

	/**
	* Embed page of a post
	*
	* @param int $post_id Post ID
	* @return \Symfony\Component\HttpFoundation\Response
	* @throws \phpbb\exception\http_exception
	*/
	public function post($post_id)
	{
		try
		{
			/** @var \vinabb\web\entities\post_interface $entity */
			$entity = $this->container->get('vinabb.web.entities.post')->load($post_id);
		}
		catch (\vinabb\web\exceptions\base $e)
		{
			throw new \phpbb\exception\http_exception(404, 'NO_POST');
		}

		if ($entity->get_poster_id())
		{
			try
			{
				/** @var \vinabb\web\entities\user_interface $poster */
				$poster = $this->container->get('vinabb.web.entities.user')->load($entity->get_poster_id());
			}
			catch (\vinabb\web\exceptions\base $e)
			{
				throw new \phpbb\exception\http_exception(404, 'NO_USER');
			}

			$poster_username = $poster->get_username();
			$poster_url = $this->helper->route('vinabb_web_user_profile_route', ['username' => $poster_username]);
		}
		else
		{
			$poster_username = $entity->get_username();
			$poster_url = '';
		}

		$this->template->assign_vars([
			'POST_SUBJECT'	=> truncate_string($entity->get_subject(), 40, 255, false, $this->language->lang('ELLIPSIS')),
			'POST_TEXT'		=> truncate_string(strip_tags($entity->get_text_for_display()), 189, 255, false, $this->language->lang('ELLIPSIS')),
			'POST_URL'		=> $this->helper->route('vinabb_web_board_post_route', ['forum_id' => $entity->get_forum_id(), 'topic_id' => $entity->get_topic_id(), 'post_id' => $post_id, 'seo' => $entity->get_subject_seo() . constants::REWRITE_URL_SEO]),
			'POSTER'		=> $poster_username,
			'POSTER_URL'	=> $poster_url,
			'POST_TIME'		=> $this->user->format_date($entity->get_time(), 'd/m/Y H:i')
		]);

		return $this->helper->render('embed_post.html');
	}
}
