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
	/** @var \vinabb\web\entities\portal_category_interface */
	protected $entity;

	/** @var \vinabb\web\operators\nestedset_portal_categories */
	protected $nestedset;

	/**
	* Constructor
	*
	* @param \vinabb\web\entities\portal_category_interface		$entity		Portal category entity
	* @param \vinabb\web\operators\nestedset_portal_categories	$nestedset	Nestedset object for tree functionality
	*/
	public function __construct(\vinabb\web\entities\portal_category_interface $entity, \vinabb\web\operators\nestedset_portal_categories $nestedset)
	{
		$this->entity = $entity;
		$this->nestedset = $nestedset;
	}

	/**
	* Get all categories
	*
	* @param int $parent_id Parent ID
	* @return array Array of entities
	*/
	public function get_cats($parent_id = 0)
	{
		$entities = [];

		// Load all entity data from the database into an array
		$rowset = $this->nestedset->get_cat_data($parent_id);

		foreach ($rowset as $row)
		{
			$entities[] = $this->entity->import($row);
		}

		return $entities;
	}

	/**
	* Add a category
	*
	* @param int $parent_id Parent ID
	* @return portal_category_interface Added entity
	*/
	public function add_cat($parent_id = 0)
	{
		// Insert the entity to the database
		$this->entity->insert();

		// Get the newly inserted entity ID
		$id = $this->entity->get_id();

		// Update the tree for the entity in the database
		$this->nestedset->add_to_nestedset($id);

		// If a parent ID was supplied, update the parent ID and tree IDs
		if ($parent_id)
		{
			$this->nestedset->change_parent($id, $parent_id);
		}

		// Reload the data to return a fresh entity
		return $this->entity->load($id);
	}

	/**
	* Move a category up/down
	*
	* @param int	$id			Category ID
	* @param string	$direction	The direction: up|down
	* @param int	$amount		The number of places to move the entity
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function move($id, $direction = 'up', $amount = 1)
	{
		$id = (int) $id;
		$amount = (int) $amount;

		// Try to move the entity
		try
		{
			$this->nestedset->move($id, (($direction !== 'up') ? -$amount : $amount));
		}
		catch (\OutOfBoundsException $e)
		{
			throw new \vinabb\web\exceptions\out_of_bounds('cat_id');
		}
	}

	/**
	* Delete a category
	*
	* @param int $id Category ID
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function delete($id)
	{
		$id = (int) $id;

		// Try to delete the entity from the database
		try
		{
			$this->nestedset->delete($id);
		}
		catch (\OutOfBoundsException $e)
		{
			throw new \vinabb\web\exceptions\out_of_bounds('cat_id');
		}
	}

	/**
	* Change the parent
	*
	* @param int	$id				Category ID
	* @param int	$new_parent_id	New parent ID
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function change_parent($id, $new_parent_id)
	{
		$id = (int) $id;
		$new_parent_id = (int) $new_parent_id;

		// Try to change the parent
		try
		{
			$this->nestedset->change_parent($id, $new_parent_id);
		}
		catch (\OutOfBoundsException $e)
		{
			$field = (strpos($e->getMessage(), 'INVALID_ITEM') !== false) ? 'cat_id' : 'new_parent_id';
			throw new \vinabb\web\exceptions\out_of_bounds($field);
		}
	}

	/**
	* Get a category's parent categories (for use in breadcrumbs)
	*
	* @param int $parent_id Parent ID
	* @return array
	*/
	public function get_parents($parent_id)
	{
		$entities = [];

		// Load all parent data from the database into an array
		$rowset = $this->nestedset->get_path_data($parent_id);

		foreach ($rowset as $row)
		{
			$entities[] = $this->entity->import($row);
		}

		return $entities;
	}
}
