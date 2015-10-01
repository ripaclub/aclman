<?php
/**
 * ACL Manager
 *
 * @link        https://github.com/ripaclub/aclman
 * @copyright   Copyright (c) 2015, RipaClub
 * @license     http://opensource.org/licenses/BSD-2-Clause Simplified BSD License
 */
namespace AclMan\Storage\Adapter\ArrayAdapter;

use AclMan\Exception\InvalidParameterException;
use AclMan\Exception\ResourceAlreadyExistException;
use AclMan\Exception\ResourceNotExistException;
use AclMan\Exception\RoleAlreadyExistException;
use AclMan\Exception\RoleNotExistException;
use AclMan\Permission\GenericPermission;
use AclMan\Permission\PermissionCheckTrait;
use AclMan\Permission\PermissionInterface;
use AclMan\Resource\ResourceCheckTrait;
use AclMan\Role\RoleCheckTrait;
use AclMan\Storage\StorageInterface;
use Zend\Permissions\Acl\Resource\GenericResource;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use Zend\Permissions\Acl\Role\GenericRole;

/**
 * Class ArrayAdapter
 */
class ArrayAdapter implements StorageInterface
{
    use RoleCheckTrait;
    use ResourceCheckTrait;
    use PermissionCheckTrait;

    public $roles = [];

    public $resources = [];

    public $permission = [];

    /**
     * @param array $config
     */
    public function __construct($config = null)
    {
        if (is_array($config) && isset($config[self::NODE_ROLES])) {
            foreach ($config[self::NODE_ROLES] as $role => $resources) {
                $parents = (isset($resources['parents'])) ? $resources['parents'] : [];
                $this->addRole($role, $parents);
                if (is_array($resources) && isset($resources[self::NODE_RESOURCES])) {
                    foreach ($resources[self::NODE_RESOURCES] as $resource => $permissions) {
                        if ($resource && !$this->hasResource($resource)) {
                            $this->addResource($resource);
                        }
                        if (is_array($permissions)) {
                            foreach ($permissions as $permission) {
                                if (is_array($permission)
                                    && isset($permission['privileges'])
                                    && is_array($permission['privileges'])
                                ) {
                                    foreach ($permission['privileges'] as $key => $nestedPermission) {
                                        $perm = $permission;
                                        $perm['role'] = $role;
                                        $perm['resource'] = $resource;

                                        if (is_array($nestedPermission)) {
                                            $perm['privilege'] = $key;
                                            if (isset($nestedPermission['allow'])) {
                                                $perm['allow'] = $nestedPermission['allow'];
                                            }
                                            if (isset($nestedPermission['assert'])) {
                                                $perm['assert'] = $nestedPermission['assert'];
                                            }
                                        } else {
                                            $perm['privilege'] = $nestedPermission;
                                        }
                                        $this->addPermission($perm);
                                    }
                                } else {
                                    if (!is_array($permission)) {
                                        $permission = [];
                                    }

                                    $permission['role'] = $role;
                                    $permission['resource'] = $resource;
                                    $this->addPermission($permission);
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * @param array $roles
     * @return $this
     */
    public function addRoles(array $roles)
    {
        foreach ($roles as $role) {
            $parents = [];
            if (is_array($role) && isset($role['parents'])) {
                $parents = $role['parents'];
            }

            if (is_array($role) && isset($role['role'])) {
                $role = $role['role'];
            } else {
                $role = $this->checkRole($role);
            }
            $this->addRole($role, $parents);
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addRole($role, array $parents = [])
    {
        $role = $this->checkRole($role);

        if ($this->hasRole($role)) {
            throw new RoleAlreadyExistException(
                sprintf(
                    'Role %s already stored',
                    $role->getRoleId()
                )
            );
        }

        $this->roles[$role->getRoleId()][self::NODE_PARENTS_ROLE] = $this->extractRoleParents($parents);
        $this->permission[$role->getRoleId()][self::NODE_RESOURCES] = [];

        return $this;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        $roles = [];

        foreach ($this->roles as $role => $option) {
            array_push($roles, new GenericRole($role));
        }
        return $roles;
    }

    /**
     * {@inheritdoc}
     */
    public function hasRole($role)
    {
        $role = $this->checkRole($role);
        if (array_key_exists($role->getRoleId(), $this->roles)) {
            return true;
        }
        return false;
    }

    /**
     * FIXME: Review, it is useful? Upserts parents?
     *
     * @param $role
     * @param array $parents
     * @return bool
     * @throws RoleNotExistException
     */
    public function addParentRoles($role, array $parents)
    {
        $role = $this->checkRole($role);
        $roleId = $role->getRoleId();
        if ($this->hasRole($roleId)) {
            foreach ($parents as $parent) {
                $parent = $this->checkRole($parent);
                $parentId = $parent->getRoleId();
                if ($this->hasRole($parentId)) {
                    array_push($this->roles[$roleId][self::NODE_PARENTS_ROLE], $parentId);
                } else {
                    throw new RoleNotExistException(
                        sprintf(
                            'Role parent %s not stored',
                            $roleId
                        )
                    );
                }
            }
            return true;
        } else {
            throw new RoleNotExistException(
                sprintf(
                    'Role %s not stored',
                    $roleId
                )
            );
        }
    }

    /**
     * @param $role
     * @return array
     */
    public function getParentRoles($role)
    {
        $role = $this->checkRole($role);
        $roleId = $role->getRoleId();
        if (!array_key_exists($roleId, $this->roles)) {
            return [];
        }
        return $this->roles[$roleId][self::NODE_PARENTS_ROLE];
    }

    /**
     * @param array $parents
     * @return array
     */
    protected function extractRoleParents(array $parents)
    {
        $roleParents = [];
        foreach ($parents as $parent) {
            $role = $this->checkRole($parent);
            array_push($roleParents, $role->getRoleId());
        }
        return $roleParents;
    }

    /**
     * @return array
     */
    public function getResources()
    {
        $resources = [];

        foreach ($this->resources as $resource => $option) {
            array_push($resources, new GenericResource($resource));
        }
        return $resources;
    }


    /**
     * @param array $resources
     * @return $this
     * @throws ResourceAlreadyExistException
     */
    public function addResources(array $resources)
    {
        foreach ($resources as $resource) {
            $this->addResource($resource);
        }

        return $this;
    }

    /**
     * @param ResourceInterface|string $resource
     * @return $this
     * @throws ResourceAlreadyExistException
     */
    public function addResource($resource)
    {
        $resource = $this->checkResource($resource);
        if ($resource && $this->hasResource($resource)) {
            throw new ResourceAlreadyExistException(
                sprintf(
                    'Resource %s already stored',
                    $resource->getResourceId()
                )
            );
        }
        $this->resources[$resource->getResourceId()] = [];

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasResource($resource)
    {
        $resource = $this->checkResource($resource);
        if (array_key_exists($resource->getResourceId(), $this->resources)) {
            return true;
        }

        return false;
    }

    /**
     * @param null $role
     * @param null $resource
     * @return array
     */
    public function getPermissions($role = null, $resource = null)
    {
        $role = $this->getRolePermission(new GenericPermission(['role' => $role]));
        $result = [];
        if ($resource) {
            $resource = $this->checkResource($resource);
            if (isset($this->permission[$role->getRoleId()][self::NODE_RESOURCES][$resource->getResourceId()][self::NODE_PERMISSION])) {
                $listPermission = $this->permission[$role->getRoleId()][self::NODE_RESOURCES][$resource->getResourceId()][self::NODE_PERMISSION];
                foreach ($listPermission as $permission) {
                    $permission['role'] = ($role->getRoleId() == StorageInterface::ALL_ROLES) ?
                        null :
                        $role->getRoleId();
                    $permission['resource'] = ($resource->getResourceId() == StorageInterface::ALL_RESOURCES) ?
                        null :
                        $resource->getResourceId();

                    $obj = new GenericPermission($permission);
                    array_push($result, $obj);
                }
            }
        } else {
            if (isset($this->permission[$role->getRoleId()][self::NODE_RESOURCES])) {
                $listResource = $this->permission[$role->getRoleId()][self::NODE_RESOURCES];
                foreach ($listResource as $keyResource => $listPermission) {
                    foreach ($listPermission[self::NODE_PERMISSION] as $permission) {
                        $permission['role'] = ($role->getRoleId() == StorageInterface::ALL_ROLES) ?
                            null :
                            $role->getRoleId();
                        $permission['resource'] = ($keyResource == StorageInterface::ALL_RESOURCES) ?
                            null : $keyResource;

                        $obj = new GenericPermission($permission);
                        array_push($result, $obj);
                    }
                }
            }
        }
        return $result;
    }

    /**
     * @param PermissionInterface|array $permission
     * @return $this|bool
     */
    public function addPermission($permission)
    {
        $permission = $this->checkPermission($permission);

        $role = $this->getRolePermission($permission);
        $resource = $this->getResourcePermission($permission);

        $this->checkRoleResource($role, $resource);

        $newPermission = [
            'assert' => $permission->getAssertion(),
            'allow' => $permission->isAllow(),
            'privilege' => $permission->getPrivilege()
        ];

        $roleId = $role->getRoleId();
        $resrcId = $resource->getResourceId();
        array_push($this->permission[$roleId][self::NODE_RESOURCES][$resrcId][self::NODE_PERMISSION], $newPermission);

        return $this;
    }

    /**
     * @param array $permissions
     * @return $this
     */
    public function addPermissions(array $permissions)
    {
        foreach ($permissions as $permission) {
            $this->addPermission($permission);
        }
        return $this;
    }

    /**
     * @param GenericPermission $permission
     * @return GenericRole
     * @throws RoleNotExistException
     */
    protected function getRolePermission(GenericPermission $permission)
    {
        if ($permission->getRoleId()) {
            if ($permission->getRoleId() && !$this->hasRole(new GenericRole($permission->getRoleId()))) {
                $this->addRole($permission->getRoleId());
            }
            $role = new GenericRole($permission->getRoleId());
        } else {
            $role = new GenericRole(self::ALL_ROLES);
        }
        return $role;
    }

    /**
     * @param GenericPermission $permission
     * @return GenericResource
     * @throws ResourceNotExistException
     */
    protected function getResourcePermission(GenericPermission $permission)
    {
        if ($permission->getResourceId()) {
            // Check if resource is already stored
            if (
                $permission->getResourceId() && !$this->hasResource(
                    new GenericResource($permission->getResourceId())
                )
            ) {
                $this->addResource($permission->getResourceId());
            }
            $resource = new GenericResource($permission->getResourceId());
        } else {
            $resource = new GenericResource(self::ALL_RESOURCES);
        }
        return $resource;
    }

    /**
     * Check if already exists a resource permission node config, if not exist add it
     *
     * @param $role
     * @param $resource
     * @return $this
     */
    protected function checkRoleResource($role, $resource)
    {
        $resource = $this->checkResource($resource);
        $role = $this->checkRole($role);
        $roleId = ($role->getRoleId() == null) ? StorageInterface::ALL_ROLES : $role->getRoleId();
        $resrcId = ($resource->getResourceId() == null) ? StorageInterface::ALL_RESOURCES : $resource->getResourceId();
        if (!isset($this->permission[$roleId][self::NODE_RESOURCES][$resrcId][self::NODE_PERMISSION])) {
            $this->permission[$roleId][self::NODE_RESOURCES][$resrcId][self::NODE_PERMISSION] = [];
        }

        return $this;
    }
}
