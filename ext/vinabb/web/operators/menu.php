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
* Operator for a set of menu items
*/
class menu implements menu_interface
{
	/** @var ContainerInterface */
	protected $container;

	/** @var \vinabb\web\operators\nestedset_menus */
	protected $nestedset;

	/**
	* Constructor
	*
	* @param ContainerInterface						$container	Container object
	* @param \vinabb\web\operators\nestedset_menus	$nestedset	Nestedset object for tree functionality
	*/
	public function __construct(ContainerInterface $container, \vinabb\web\operators\nestedset_menus $nestedset)
	{
		$this->container = $container;
		$this->nestedset = $nestedset;
	}

	/**
	* Get all menus
	*
	* @param int $parent_id Parent ID
	* @return array
	*/
	public function get_menus($parent_id = 0)
	{
		$entities = [];

		// Load all entity data from the database into an array
		$rowset = $this->nestedset->get_menu_data($parent_id);

		foreach ($rowset as $row)
		{
			$entities[] = $this->container->get('vinabb.web.entities.menu')->import($row);
		}

		return $entities;
	}

	/**
	* Add a menu
	*
	* @param \vinabb\web\entities\menu_interface $entity Menu entity
	* @param int $parent_id Parent ID
	* @return \vinabb\web\entities\menu_interface
	*/
	public function add_menu($entity, $parent_id = 0)
	{
		// Insert the entity to the database
		$entity->insert();

		// Get the newly inserted entity ID
		$id = $entity->get_id();

		// Update the tree for the entity in the database
		$this->nestedset->add_to_nestedset($id);

		// If a parent ID was supplied, update the parent ID and tree IDs
		if ($parent_id)
		{
			$this->nestedset->change_parent($id, $parent_id);
		}

		// Reload the data to return a fresh entity
		return $entity->load($id);
	}

	/**
	* Move a menu up/down
	*
	* @param int	$id			Menu ID
	* @param string	$direction	The direction: up|down
	* @param int	$amount		The number of places to move
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function move($id, $direction = 'up', $amount = 1)
	{
		$id = (int) $id;
		$amount = (int) $amount;

		try
		{
			$this->nestedset->move($id, (($direction !== 'up') ? -$amount : $amount));
		}
		catch (\OutOfBoundsException $e)
		{
			throw new \vinabb\web\exceptions\out_of_bounds('menu_id');
		}
	}

	/**
	* Delete a menu
	*
	* @param int $id Menu ID
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function delete_menu($id)
	{
		$id = (int) $id;

		try
		{
			$this->nestedset->delete($id);
		}
		catch (\OutOfBoundsException $e)
		{
			throw new \vinabb\web\exceptions\out_of_bounds('menu_id');
		}
	}

	/**
	* Change the parent
	*
	* @param int	$id				Menu ID
	* @param int	$new_parent_id	New parent ID
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function change_parent($id, $new_parent_id)
	{
		$id = (int) $id;
		$new_parent_id = (int) $new_parent_id;

		try
		{
			$this->nestedset->change_parent($id, $new_parent_id);
		}
		catch (\OutOfBoundsException $e)
		{
			$field = (strpos($e->getMessage(), 'INVALID_ITEM') !== false) ? 'menu_id' : 'new_parent_id';
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
			$entities[] = $this->container->get('vinabb.web.entities.menu')->import($row);
		}

		return $entities;
	}
}
