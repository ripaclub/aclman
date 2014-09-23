<?php
namespace AclMan\Storage;

use AclMan\Permission\PermissionInterface;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use Zend\Permissions\Acl\Role\RoleInterface;

interface StorageInterface
{
    /**
     * @return array
     */
    public function getRoles();

    /**
     * @param $role
     * @return array
     */
    public function getParentRoles($role);

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
     * @param string $resourceId
     * @return null|ResourceInterface
     */
    public function getResource($resourceId);

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
     * @param ResourceInterface $resource
     * @return array
     */
    public function getPermissions($resource);

    /**
     * @param PermissionInterface $permission
     * @return bool
     */
    public function addPermission($permission);
} 