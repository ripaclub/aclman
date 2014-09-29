<?php
namespace AclMan\Storage;

use AclMan\Permission\PermissionInterface;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use Zend\Permissions\Acl\Role\RoleInterface;

interface StorageInterface
{
    /**
     * CONST
     ******************************************************************************************************************/

    const NODE_ROLES        = 'roles';
    const NODE_RESOURCES    = 'resources';
    const NODE_PERMISSION   = 'permission';
    const NODE_PARENTS_ROLE = 'parents';

    const ALL_ROLES      = 'all-roles';
    const ALL_RESOURCES  = 'all-resources';
    const ALL_PRIVILEGES = 'all-privileges';

    /**
     * CONST
     ******************************************************************************************************************/

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
     * @param RoleInterface $role
     * @param array $parents
     * @return bool
     */
    public function addRole($role, array $parents = []);

    /**
     * @param RoleInterface $role
     * @return bool
     */
    public function hasRole($role);

    /**
     * @return array
     */
    public function getResources();

    /**
     * @param ResourceInterface $resource
     * @return bool
     */
    public function addResource($resource);

    /**
     * @param ResourceInterface $resource
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