<?php
/**
 * ACL Manager
 *
 * @link        https://github.com/ripaclub/aclman
 * @copyright   Copyright (c) 2014, RipaClub
 * @license     http://opensource.org/licenses/BSD-2-Clause Simplified BSD License
 */
namespace AclMan\Storage;

use AclMan\Permission\PermissionInterface;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use Zend\Permissions\Acl\Role\RoleInterface;

/**
 * Interface StorageInterface
 */
interface StorageInterface
{
    const NODE_ROLES        = 'roles';
    const NODE_RESOURCES    = 'resources';
    const NODE_PERMISSION   = 'permission';
    const NODE_PARENTS_ROLE = 'parents';

    const ALL_ROLES         = 'all-roles';
    const ALL_RESOURCES     = 'all-resources';
    const ALL_PRIVILEGES    = 'all-privileges';

    /**
     * @param $role
     * @param array $parents
     * @return bool
     */
    public function addParentRoles($role, array $parents);

    /**
     * @param $role
     * @return array
     */
    public function getParentRoles($role);

    /**
     * @return array
     */
    public function getRoles();

    /**
     * @param RoleInterface|string $role
     * @param array $parents
     * @return $this
     */
    public function addRole($role, array $parents = []);

    /**
     * @param RoleInterface|string $role
     * @return bool
     */
    public function hasRole($role);

    /**
     * @return array
     */
    public function getResources();

    /**
     * @param ResourceInterface|string $resource
     * @return bool
     */
    public function addResource($resource);

    /**
     * @param ResourceInterface|string $resource
     * @return bool
     */
    public function hasResource($resource);

    /**
     * @param null $role
     * @param null $resource
     * @return array
     */
    public function getPermissions($role = null, $resource = null);

    /**
     * @param PermissionInterface $permission
     * @return bool
     */
    public function addPermission($permission);
}
