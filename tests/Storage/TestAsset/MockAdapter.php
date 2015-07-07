<?php
/**
 * ACL Manager
 *
 * @link        https://github.com/ripaclub/aclman
 * @copyright   Copyright (c) 2015, RipaClub
 * @license     http://opensource.org/licenses/BSD-2-Clause Simplified BSD License
 */
namespace AclManTest\Storage\TestAsset;

use AclMan\Storage\StorageInterface;

/**
 * Class MockAdapter
 */
class MockAdapter implements StorageInterface
{
    /**
     * {@inheritdoc}
     */
    public function addParentRoles($role, array $parents)
    {
        // TODO: implement addParentRoles() method.
    }

    /**
     * {@inheritdoc}
     */
    public function getParentRoles($role)
    {
        // TODO: implement getParentRoles() method.
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles()
    {
        // TODO: implement getRoles() method.
    }

    /**
     * {@inheritdoc}
     */
    public function addRole($role, array $parents = [])
    {
        // TODO: implement addRole() method.
    }

    /**
     * {@inheritdoc}
     */
    public function hasRole($role)
    {
        // TODO: implement hasRole() method.
    }

    /**
     * {@inheritdoc}
     */
    public function getResources()
    {
        // TODO: implement getResources() method.
    }

    /**
     * {@inheritdoc}
     */
    public function addResource($resource)
    {
        // TODO: implement addResource() method.
    }

    /**
     * {@inheritdoc}
     */
    public function hasResource($resource)
    {
        // TODO: implement hasResource() method.
    }

    /**
     * {@inheritdoc}
     */
    public function getPermissions($role = null, $resource = null)
    {
        // TODO: implement getPermissions() method.
    }

    /**
     * {@inheritdoc}
     */
    public function addPermission($permission)
    {
        // TODO: implement addPermission() method.
    }
}
