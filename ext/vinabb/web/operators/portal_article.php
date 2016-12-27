<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\operators;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
* Operator for a set of articles
*/
class portal_article implements portal_article_interface
{
	/** @var ContainerInterface $container */
	protected $container;

	/** @var \phpbb\db\driver\driver_interface $db */
	protected $db;

	/** @var string $table_name */
	protected $table_name;

	/**
	* Constructor
	*
	* @param ContainerInterface					$container	Container object
	* @param \phpbb\db\driver\driver_interface	$db			Database object
	* @param string								$table_name	Table name
	*/
	public function __construct(ContainerInterface $container, \phpbb\db\driver\driver_interface $db, $table_name)
	{
		$this->container = $container;
		$this->db = $db;
		$this->table_name = $table_name;
	}

	/**
	* Get number of articles
	*
	* @param string	$lang	2-letter language ISO code
	* @param int	$cat_id	Category ID
	* @return int
	*/
	public function count_articles($lang, $cat_id = 0)
	{
		$sql_where = ($lang != '') ? "WHERE article_lang = '" . $this->db->sql_escape($lang) . "'" : "WHERE article_lang <> ''";
		$sql_where .= $cat_id ? ' AND cat_id = ' . (int) $cat_id : '';

		$sql = 'SELECT COUNT(article_id) AS counter
			FROM ' . $this->table_name . "
			$sql_where";
		$result = $this->db->sql_query($sql);
		$counter = (int) $this->db->sql_fetchfield('counter');
		$this->db->sql_freeresult($result);

		return $counter;
	}

	/**
	* Get all articles
	*
	* @return array
	*/
	public function get_articles()
	{
		$entities = [];

		$sql = 'SELECT *
			FROM ' . $this->table_name . '
			ORDER BY article_time DESC';
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$entities[] = $this->container->get('vinabb.web.entities.portal_article')->import($row);
		}
		$this->db->sql_freeresult($result);

		return $entities;
	}

	/**
	* Get articles in range for pagination
	*
	* @param string	$lang			2-letter language ISO code
	* @param int	$cat_id			Category ID
	* @param string	$order_field	Sort by this field
	* @param int	$limit			Number of items
	* @param int	$offset			Position of the start
	* @return array
	*/
	public function list_articles($lang, $cat_id = 0, $order_field = 'article_time DESC', $limit = 0, $offset = 0)
	{
		$entities = [];
		$sql_where = ($lang != '') ? "WHERE article_lang = '" . $this->db->sql_escape($lang) . "'" : "WHERE article_lang <> ''";
		$sql_where .= $cat_id ? ' AND cat_id = ' . (int) $cat_id : '';

		$sql = 'SELECT *
			FROM ' . $this->table_name . "
			$sql_where
			ORDER BY $order_field";
		$result = $this->db->sql_query_limit($sql, $limit, $offset);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$entities[] = $this->container->get('vinabb.web.entities.portal_article')->import($row);
		}
		$this->db->sql_freeresult($result);

		return $entities;
	}

	/**
	* Get latest articles
	*
	* @param string	$lang	2-letter language ISO code
	* @param int	$limit	Number of items
	* @return array
	*/
	public function get_latest_articles($lang, $limit = 10)
	{
		return $this->list_articles($lang, 0, 'article_time DESC', $limit);
	}

	/**
	* Add an article
	*
	* @param \vinabb\web\entities\portal_article_interface $entity Article entity
	* @return \vinabb\web\entities\portal_article_interface
	*/
	public function add_article(\vinabb\web\entities\portal_article_interface $entity)
	{
		// Insert the entity to the database
		$entity->insert();

		// Get the newly inserted entity ID
		$id = $entity->get_id();

		// Reload the data to return a fresh entity
		return $entity->load($id);
	}

	/**
	* Delete an article
	*
	* @param int $id Article ID
	* @return bool True if row was deleted, false otherwise
	*/
	public function delete_article($id)
	{
		$sql = 'DELETE FROM ' . $this->table_name . '
			WHERE article_id = ' . (int) $id;
		$this->db->sql_query($sql);

		// Return true/false if the entity was deleted
		return (bool) $this->db->sql_affectedrows();
	}
}
