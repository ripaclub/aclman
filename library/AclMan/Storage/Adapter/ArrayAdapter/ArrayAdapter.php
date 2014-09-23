<?php
namespace AclMan\Storage\Adapter\ArrayAdapter;

use AclMan\Permission\GenericPermission;
use AclMan\Permission\PermissionInterface;
use AclMan\Resource\ResourceCheckTrait;
use AclMan\Role\RoleCheckTrait;
use AclMan\Storage\Exception\InvalidParameterException;
use AclMan\Storage\Exception\ResourceAlreadyExistException;
use AclMan\Storage\Exception\ResourceNotExistException;
use AclMan\Storage\Exception\RoleAlreadyExistException;
use AclMan\Storage\Exception\RoleNotExistException;
use AclMan\Storage\StorageInterface;
use Zend\Permissions\Acl\Assertion\AssertionInterface;
use Zend\Permissions\Acl\Resource\GenericResource;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use Zend\Permissions\Acl\Role\GenericRole;
use Zend\Permissions\Acl\Role\RoleInterface;

class ArrayAdapter implements StorageInterface
{
    /**
     * TRAIT
     ******************************************************************************************************************/

    use RoleCheckTrait;
    use ResourceCheckTrait;

    /**
     * CONST
     ******************************************************************************************************************/

    const NODE_ROLES       = 'roles';
    const NODE_RESOURCES   = 'resources';
    const NODE_PERMISSION  = 'permission';

    /**
     * ATTRIBUTE
     ******************************************************************************************************************/

    public $roles = [];

    public $resources = [];

    /**
     * METHOD
     ******************************************************************************************************************/

    /**
     * @param array $config
     */
    function __construct(array $config)
    {
        if(isset($config[self::NODE_ROLES])) {
            $this->addRoles($config[self::NODE_ROLES]);
        }

        if(isset($config[self::NODE_RESOURCES])) {
            $this->addResources($config[self::NODE_RESOURCES]);
        }

        if(isset($config[self::NODE_PERMISSION])) {
            $this->addPermissions($config[self::NODE_PERMISSION]);
        }
    }

    /**
     * @param array $roles
     * @return self
     * @throws \AclMan\Storage\Exception\InvalidParameterException
     */
    public function addRoles(array $roles)
    {
        foreach ($roles as $role) {
            $this->addRole($role);
        }
        return $this;
    }

    /**
     * @param RoleInterface $role
     * @return $this|bool
     * @throws \AclMan\Storage\Exception\RoleAlreadyExistException
     * @throws \AclMan\Storage\Exception\InvalidParameterException
     */
    public function addRole($role)
    {
        $role = $this->checkRole($role);

        if($this->hasRole($role)) {
            throw new RoleAlreadyExistException(sprintf('Role %s already stored', $role->getRoleId()));
        }
        $this->roles[$role->getRoleId()] = [];
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
     * @param RoleInterface $role
     * @return bool
     * @throws \AclMan\Storage\Exception\InvalidParameterException
     */
    public function hasRole($role)
    {
        $role = $this->checkRole($role);

        if(array_key_exists($role->getRoleId(), $this->roles)) {
            return true;
        }
        return false;
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
     * @return self
     * @throws \AclMan\Storage\Exception\InvalidParameterException
     */
    public function addResources(array $resources)
    {
        foreach ($resources as $resource) {
            $this->addResource($resource);
        }
        return $this;
    }

    /**
     * @param ResourceInterface $resource
     * @return self
     * @throws \AclMan\Storage\Exception\ResourceAlreadyExistException
     */
    public function addResource($resource)
    {
        $resource = $this->checkResource($resource);

        if($this->hasResource($resource)) {
            throw new ResourceAlreadyExistException(sprintf('Resource %s already stored', $resource->getResourceId()));
        }

        $this->resources[$resource->getResourceId()] = [];
        return $this;
    }

    /**
     * @param string $resource
     * @return null|string|GenericResource|ResourceInterface
     */
    public function getResource($resource)
    {
        $resource = $this->checkResource($resource);

        if($this->hasResource($resource)) {
            return $resource;
        }
        return null;
    }

    /**
     * @param ResourceInterface $resource
     * @return bool
     */
    public function hasResource($resource)
    {
        $resource = $this->checkResource($resource);

        if(array_key_exists($resource->getResourceId(), $this->resources)) {
            return true;
        }
        return false;
    }

    /**
     * @param $permission
     * @return GenericPermission
     * @throws \AclMan\Storage\Exception\InvalidParameterException
     */
    private function checkPermission($permission)
    {
        if(is_array($permission)) {
            $permission = new GenericPermission($permission);
        }

        if(!($permission instanceof PermissionInterface)){
            throw new InvalidParameterException('Invalid type permission');
        }

        return $permission;
    }

    /**
     * @param ResourceInterface $resource
     * @return array
     * @throws \AclMan\Storage\Exception\ResourceNotExistException
     */
    public function getPermissions($resource)
    {
        $permission = $this->checkResource($resource);

        if(!$this->hasResource($resource)) {
            throw new ResourceNotExistException(sprintf('Resource %s not stored', $resource->getResourceId()));
        }

        $result = [];
        if(isset($this->resources[$resource->getResourceId()][self::NODE_PERMISSION])) {
            $listPermission = $this->resources[$resource->getResourceId()][self::NODE_PERMISSION];
            foreach ($listPermission as $permission) {

                $obj = new GenericPermission(array_merge($permission, ['resource' => $resource->getResourceId()]));
                array_push($result, $obj);
            }

        }

        return $result;
    }

    /**
     * @param PermissionInterface $permission
     * @return $this|bool
     * @throws \AclMan\Storage\Exception\InvalidParameterException
     * @throws \AclMan\Storage\Exception\ResourceNotExistException
     * @throws \AclMan\Storage\Exception\RoleNotExistException
     */
    public function addPermission($permission)
    {
        $permission = $this->checkPermission($permission);

        if(!$this->hasResource(new GenericResource($permission->getResourceId()))) {
            throw new ResourceNotExistException(sprintf('Resource %s not stored', $permission->getResourceId()));
        }

        if(!$this->hasRole(new GenericRole($permission->getRoleId()))) {
            throw new RoleNotExistException(sprintf('Role %s not stored', $permission->getRoleId()));
        }

        $assert = $permission->getAssertionClass();

        if($assert instanceof AssertionInterface || is_null($assert)) {

            $permissionSettings =  [
                'role'   => $permission->getRoleId(),
                'assert' => $permission->getAssertionClass(),
                'allow'  => $permission->isAllow(),
                'privilege'  => $permission->getPrivilege()
            ];

            if(isset($this->resources[$permission->getResourceId()][self::NODE_PERMISSION])) {
                array_push($this->resources[$permission->getResourceId()][self::NODE_PERMISSION], $permissionSettings);
            } else {
                $this->resources[$permission->getResourceId()][self::NODE_PERMISSION][] = $permissionSettings;
            }
            return $this;
        } else {
            throw new InvalidParameterException('Invalid type assert');
        }
    }

    /**
     * @param array $permissions
     * @return self
     * @throws \AclMan\Storage\Exception\InvalidParameterException
     */
    public function addPermissions(array $permissions)
    {
        foreach ($permissions as $permission) {
            $this->addPermission($permission);
        }
        return $this;
    }
} 