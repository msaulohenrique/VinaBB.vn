<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\operators;

/**
* Operator for a set of news categories
*/
class portal_category implements portal_category_interface
{
	/** @var ContainerInterface */
	protected $container;

	/** @var \vinabb\web\operators\nestedset_portal_categories */
	protected $nestedset_portal_categories;

	/**
	* Constructor
	*
	* @param ContainerInterface									$container						Service container interface
	* @param \vinabb\web\operators\nestedset_portal_categories	$nestedset_portal_categories	Nestedset object for tree functionality
	*/
	public function __construct(ContainerInterface $container, \vinabb\web\operators\nestedset_portal_categories $nestedset_portal_categories)
	{
		$this->container = $container;
		$this->nestedset_portal_categories = $nestedset_portal_categories;
	}

	/**
	* Get the entities
	*
	* @param int $parent_id Parent to display sub-entities from
	* @return array Array of entities
	*/
	public function get_cats($parent_id = 0)
	{
		$entities = [];

		// Load all entity data from the database into an array
		$rowset = $this->nestedset_portal_categories->get_cat_data($parent_id);

		// Import entities and store them in an array
		foreach ($rowset as $row)
		{
			$entities[] = $this->container->get('vinabb.web.entity')->import($row);
		}

		return $entities;
	}

	/**
	* Add an entity
	*
	* @param \vinabb\web\entities\portal_category_interface	$entity		Entity with new data to insert
	* @param int											$parent_id	Parent to display sub-entities from
	* @return portal_category_interface Added entity
	*/
	public function add($entity, $parent_id = 0)
	{
		// Insert the entity data to the database
		$entity->insert();

		// Get the newly inserted entity ID
		$cat_id = $entity->get_id();

		// Update the tree for the rule in the database
		$this->nestedset_portal_categories->add_to_nestedset($cat_id);

		// If a parent ID was supplied, update the parent ID and tree IDs
		if ($parent_id)
		{
			$this->nestedset_portal_categories->change_parent($cat_id, $parent_id);
		}

		// Reload the data to return a fresh entity
		return $entity->load($cat_id);
	}

	/**
	* Move the entity up/down
	*
	* @param int	$cat_id		Category ID
	* @param string	$direction	The direction: up|down
	* @param int	$amount		The number of places to move the entity
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function move($cat_id, $direction = 'up', $amount = 1)
	{
		$cat_id = (int) $cat_id;
		$amount = (int) $amount;

		// Try to move the entity
		try
		{
			$this->nestedset_portal_categories->move($cat_id, (($direction !== 'up') ? -$amount : $amount));
		}
		catch (\OutOfBoundsException $e)
		{
			throw new \vinabb\web\exceptions\out_of_bounds('cat_id');
		}
	}

	/**
	* Delete the entity
	*
	* @param int $cat_id Category ID
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function delete($cat_id)
	{
		$cat_id = (int) $cat_id;

		// Try to delete the entity from the database
		try
		{
			$this->nestedset_portal_categories->delete($cat_id);
		}
		catch (\OutOfBoundsException $e)
		{
			throw new \vinabb\web\exceptions\out_of_bounds('cat_id');
		}
	}

	/**
	* Change the parent
	*
	* @param int	$cat_id			Category ID
	* @param int	$new_parent_id	The new parent ID
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function change_parent($cat_id, $new_parent_id)
	{
		$cat_id = (int) $cat_id;
		$new_parent_id = (int) $new_parent_id;

		// Try to change the parent
		try
		{
			$this->nestedset_portal_categories->change_parent($cat_id, $new_parent_id);
		}
		catch (\OutOfBoundsException $e)
		{
			$field = (strpos($e->getMessage(), 'INVALID_ITEM') !== false) ? 'cat_id' : 'new_parent_id';
			throw new \vinabb\web\exceptions\out_of_bounds($field);
		}
	}

	/**
	* Get an entity's parent entities (for use in breadcrumbs)
	*
	* @param int $parent_id Parent to display sub-entities from
	* @return array Array of entity data for an entity's parent entities
	*/
	public function get_parents($parent_id)
	{
		$entities = array();

		// Load all parent data from the database into an array
		$rowset = $this->nestedset_portal_categories->get_path_data($parent_id);

		// Import entities and store them in an array
		foreach ($rowset as $row)
		{
			$entities[] = $this->container->get('vinabb.web.entity')->import($row);
		}

		return $entities;
	}
}
