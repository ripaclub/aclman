<?php
/**
 * ACL Manager
 *
 * @link        https://github.com/ripaclub/aclman
 * @copyright   Copyright (c) 2014, RipaClub
 * @license     http://opensource.org/licenses/BSD-2-Clause Simplified BSD License
 */
namespace AclMan\Service;

use AclMan\Acl\AclAwareTrait;
use AclMan\Assertion\AssertionAwareTrait;
use AclMan\Permission\GenericPermission;
use AclMan\Resource\ResourceCheckTrait;
use AclMan\Role\RoleCheckTrait;
use AclMan\Storage\StorageAwareTrait;
use Zend\Permissions\Acl\Resource;
use Zend\Permissions\Acl\Role;

/**
 * Class ServiceAbstract
 */
class ServiceAbstract implements ServiceInterface
{
    use StorageAwareTrait;
    use AclAwareTrait;
    use ResourceCheckTrait;
    use RoleCheckTrait;
    use AssertionAwareTrait;

    protected $allowNotFoundResource = false;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        if ($this->getStorage()) {
            // Add Role
            $roles = $this->getStorage()->getRoles();
            foreach ($roles as $role) {
                if (!$this->hasRole($role)) {
                    $roleParents = $this->getStorage()->getParentRoles($role);
                    $this->getAcl()->addRole($role, $roleParents);
                }
            }
        }
        return $this;
    }

    /**
     * Add roles from storage
     *
     * @param string|Role  $role
     * @return self
     */
    public function addRole($role)
    {
        $this->getAcl()->addRole($role);
        return $this;
    }

    /**
     * Check if exist role
     *
     * @param string|Role $role
     * @return bool
     */
    public function hasRole($role)
    {
        return $this->getAcl()->hasRole($role);
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return $this->getAcl()->getRoles();
    }

    /**
     * @param string|Role $role
     * @return Role\RoleInterface
     */
    public function getRole($role)
    {
        return $this->getAcl()->getRole($role);
    }

    /**
     * {@inheritdoc}
     */
    public function loadResource($role = null, $resource = null)
    {
        if ($resource && $this->getStorage()->hasResource($resource) && !$this->getAcl()->hasResource($resource)) {
            $this->getAcl()->addResource($resource);
        }

        $permissions = $this->getStorage()->getPermissions($role, $resource);
        if ($this->getStorage()->hasResource($resource) && count($permissions) > 0) {
            /* @var $permission GenericPermission */
            foreach ($permissions as $permission) {
                $assert = null;
                if ($permission->getAssertion()) {
                    $assert = $this->getPluginManager()->get($permission->getAssertion());
                }

                if ($permission->isAllow()) {
//                    var_dump(sprintf(
//                        'ALLOW: role "%s" resource "%s" privilege "%s"',
//                        $permission->getRoleId(),
//                        $permission->getResourceId(),
//                        $permission->getPrivilege()
//                    ));
                    $this->getAcl()->allow(
                        $permission->getRoleId(),
                        $permission->getResourceId(),
                        $permission->getPrivilege(),
                        $assert
                    );
                } else {
//                    var_dump(sprintf(
//                        'DENY: role "%s" resource "%s" privilege "%s"',
//                        $permission->getRoleId(),
//                        $permission->getResourceId(),
//                        $permission->getPrivilege()
//                    ));
                    $this->getAcl()->deny(
                        $permission->getRoleId(),
                        $permission->getResourceId(),
                        $permission->getPrivilege(),
                        $assert
                    );
                }
            }
            return true;
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function hasResource($resource)
    {
        return $this->getAcl()->hasResource($resource);
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowed($role = null, $resource = null, $privilege = null)
    {
        $this->loadResource($role, $resource);
        return $this->getAcl()->isAllowed($role, $resource, $privilege);
    }

    /**
     * @param boolean $allowNotFoundResource
     */
    public function setAllowNotFoundResource($allowNotFoundResource)
    {
        $this->allowNotFoundResource = (boolean) $allowNotFoundResource;
        if ($this->allowNotFoundResource) {
            $this->getAcl()->allow();
        } else {
            $this->getAcl()->deny();
        }
    }

    /**
     * @return boolean
     */
    public function getAllowNotFoundResource()
    {
        return $this->allowNotFoundResource;
    }
}
