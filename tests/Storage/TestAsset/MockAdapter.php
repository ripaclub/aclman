<?php
/**
 * ACL Manager
 *
 * @link        https://github.com/ripaclub/aclman
 * @copyright   Copyright (c) 2014, RipaClub
 * @license     http://opensource.org/licenses/BSD-2-Clause Simplified BSD License
 */
namespace AclManTest\Storage\TestAsset;

use AclMan\Permission\PermissionInterface;
use AclMan\Storage\StorageInterface;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use Zend\Permissions\Acl\Role\RoleInterface;

class MockAdapter implements StorageInterface
{
    /**
     * @param $role
     * @param array $parents
     * @return bool
     */
    public function addParentRoles($role, array $parents)
    {
        // TODO: Implement addParentRoles() method.
    }

    /**
     * @param $role
     * @return array
     */
    public function getParentRoles($role)
    {
        // TODO: Implement getParentRoles() method.
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        // TODO: Implement getRoles() method.
    }

    /**
     * @param RoleInterface $role
     * @param array $parents
     * @return bool
     */
    public function addRole($role, array $parents = [])
    {
        // TODO: Implement addRole() method.
    }

    /**
     * @param RoleInterface $role
     * @return bool
     */
    public function hasRole($role)
    {
        // TODO: Implement hasRole() method.
    }

    /**
     * @return array
     */
    public function getResources()
    {
        // TODO: Implement getResources() method.
    }

    /**
     * @param ResourceInterface $resource
     * @return bool
     */
    public function addResource($resource)
    {
        // TODO: Implement addResource() method.
    }

    /**
     * @param ResourceInterface $resource
     * @return bool
     */
    public function hasResource($resource)
    {
        // TODO: Implement hasResource() method.
    }

    /**
     * @param null $role
     * @param null $resource
     * @return array
     */
    public function getPermissions($role = null, $resource = null)
    {
        // TODO: Implement getPermissions() method.
    }

    /**
     * @param PermissionInterface $permission
     * @return bool
     */
    public function addPermission($permission)
    {
        // TODO: Implement addPermission() method.
    }

}
