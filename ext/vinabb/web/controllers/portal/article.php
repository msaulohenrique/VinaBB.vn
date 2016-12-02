<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\controllers\portal;

use Symfony\Component\DependencyInjection\ContainerInterface;
use vinabb\web\includes\constants;

/**
* Controller for the article
*/
class article implements article_interface
{
	/** @var \vinabb\web\controllers\cache\service_interface */
	protected $cache;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var ContainerInterface */
	protected $container;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \vinabb\web\controllers\pagination */
	protected $pagination;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var \vinabb\web\controllers\helper_interface */
	protected $ext_helper;

	/** @var string */
	protected $root_path;

	/** @var string */
	protected $php_ext;

	/** @var array */
	protected $portal_cats;

	/**
	* Constructor
	*
	* @param \vinabb\web\controllers\cache\service_interface	$cache			Cache service
	* @param \phpbb\config\config								$config			Config object
	* @param ContainerInterface									$container		Container object
	* @param \phpbb\db\driver\driver_interface					$db				Database object
	* @param \phpbb\language\language							$language		Language object
	* @param \vinabb\web\controllers\pagination					$pagination		Pagination object
	* @param \phpbb\request\request								$request		Request object
	* @param \phpbb\template\template							$template		Template object
	* @param \phpbb\user										$user			User  object
	* @param \phpbb\controller\helper							$helper			Controller helper
	* @param \vinabb\web\controllers\helper_interface			$ext_helper		Extension helper
	* @param string												$root_path		phpBB root path
	* @param string												$php_ext		PHP file extension
	*/
	public function __construct(
		\vinabb\web\controllers\cache\service_interface $cache,
		\phpbb\config\config $config,
		ContainerInterface $container,
		\phpbb\db\driver\driver_interface $db,
		\phpbb\language\language $language,
		\vinabb\web\controllers\pagination $pagination,
		\phpbb\request\request $request,
		\phpbb\template\template $template,
		\phpbb\user $user,
		\phpbb\controller\helper $helper,
		\vinabb\web\controllers\helper_interface $ext_helper,
		$root_path,
		$php_ext
	)
	{
		$this->cache = $cache;
		$this->config = $config;
		$this->container = $container;
		$this->db = $db;
		$this->language = $language;
		$this->pagination = $pagination;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->helper = $helper;
		$this->ext_helper = $ext_helper;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;

		$this->portal_cats = $this->cache->get_portal_cats();
	}

	/**
	* View details an article
	*
	* @param $article_id Article ID
	* @return \Symfony\Component\HttpFoundation\Response
	*/
	public function article($article_id)
	{
		$page_title = $this->language->lang('VINABB');

		if (!$article_id)
		{
			trigger_error('NO_PORTAL_ARTICLE_ID');
		}
		else
		{
			// Initiate and load the entity
			/** @var \vinabb\web\entities\portal_article_interface $entity */
			$entity = $this->container->get('vinabb.web.entities.portal_article')->load($article_id);

			if (!$entity->get_id())
			{
				trigger_error('NO_PORTAL_ARTICLE');
			}
			else
			{
				$page_title = $entity->get_name();
				$category_name = $this->portal_cats[$entity->get_cat_id()][($this->user->lang_name == constants::LANG_VIETNAMESE) ? 'name_vi' : 'name'];
				$cat_varname = $this->portal_cats[$entity->get_cat_id()]['varname'];

				// Breadcrumb
				$this->ext_helper->set_breadcrumb($this->language->lang('NEWS'), $this->helper->route('vinabb_web_portal_route'));
				$this->ext_helper->set_breadcrumb($category_name, $this->helper->route('vinabb_web_portal_cat_route', ['varname' => $cat_varname]));
				$this->ext_helper->set_breadcrumb($this->language->lang('PORTAL_ARTICLE'));

				$this->template->assign_vars([
					'ARTICLE_NAME'	=> $entity->get_name(),
					'ARTICLE_IMG'	=> $entity->get_img(),
					'ARTICLE_DESC'	=> $entity->get_desc(),
					'ARTICLE_TEXT'	=> $entity->get_text_for_display(),
					'ARTICLE_TIME'	=> $this->user->format_date($entity->get_time())
				]);
			}
		}

		return $this->helper->render('portal_article.html', $page_title);
	}
}
