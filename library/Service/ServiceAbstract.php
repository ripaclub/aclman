<?php
/**
 * ACL Manager
 *
 * @link        https://github.com/ripaclub/aclman
 * @copyright   Copyright (c) 2015, RipaClub
 * @license     http://opensource.org/licenses/BSD-2-Clause Simplified BSD License
 */
namespace AclMan\Service;

use AclMan\Acl\AclAwareTrait;
use AclMan\Assertion\AssertionAggregate;
use AclMan\Assertion\AssertionAwareTrait;
use AclMan\Permission\GenericPermission;
use AclMan\Resource\ResourceCheckTrait;
use AclMan\Role\RoleCheckTrait;
use AclMan\Storage\StorageAwareTrait;
use Zend\Permissions\Acl\Assertion\AssertionInterface;
use Zend\Permissions\Acl\Resource;
use Zend\Permissions\Acl\Role\RoleInterface;
use Zend\Stdlib\ArrayUtils;

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

    /**
     * @var bool
     */
    protected $allowNotFoundResource = false;

    /**
     * @var bool
     */
    protected $loaded = [];

    /**
     * Add roles from storage
     *
     * @param string|RoleInterface $role
     * @param string|array|RoleInterface $parents
     * @return $this
     */
    public function addRole($role, $parents = null)
    {
        $this->getAcl()->addRole($role, $parents);
        return $this;
    }

    /**
     * Check if exist role
     *
     * @param string|RoleInterface $role
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
     * @param string|RoleInterface $role
     * @return RoleInterface
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
        $role = ($role instanceof RoleInterface) ? $role->getRoleId() : $role;
        $resource = ($resource instanceof Resource\ResourceInterface) ? $resource->getResourceId() : $resource;
        // start recursion
        if (isset($this->loaded[(string)$role]) && isset($this->loaded[(string)$role][(string)$resource])) {
            return true;
        }

        if (!isset($this->loaded[(string)$role])) {
            $this->loaded[(string)$role] = [];
        }
        $this->loaded[(string)$role][(string)$resource] = true;

        $parentRoles = [];
        if ($role && ($parentRoles = $this->getStorage()->getParentRoles($role))) {
            foreach ($parentRoles as $parentRole) {
                $this->loadResource($parentRole, $resource);
            }
        }

        if ($role && $resource) {
            $this->loadResource(); // ensures loading for ALL_ROLES and ALL_RESOURCES
            $this->loadResource(null, $resource);
            $this->loadResource($role, null);
        }
        // end recursion

        if ($role && !$this->getAcl()->hasRole($role)) {
            $this->getAcl()->addRole($role, $parentRoles);
        }

        if ($resource && !$this->getAcl()->hasResource($resource)) {
            $this->getAcl()->addResource($resource);
        }

        $permissions = $this->getStorage()->getPermissions($role, $resource);
        if (count($permissions) > 0) {
            /* @var $permission GenericPermission */
            foreach ($permissions as $permission) {
                $assert = null;
                if ($permission->getAssertion()) {
                    $assertConfig = $permission->getAssertion();
                    /** @var $assert AssertionInterface */
                    if (is_array($assertConfig)) {
                        if (is_array(current($assertConfig)) || count($assertConfig) > 1) {

                            /** @var $assert AssertionAggregate */
                            $assert = new AssertionAggregate();
                            $assert->setAssertionManager($this->getPluginManager());
                            foreach ($assertConfig as $item) {
                                $assert->addAssertion($item);
                            }
                        } elseif (count($assertConfig) == 1) {
                            $assert = $this->getPluginManager()->get(current($assertConfig));
                        }
                    } else {
                        $assert = $this->getPluginManager()->get($permission->getAssertion());
                    }
                }
                // When load multiple resource
                if ($permission->getResourceId() && !$this->getAcl()->hasResource($permission->getResourceId())) {
                    $this->getAcl()->addResource($permission->getResourceId());
                }

                $method = $permission->isAllow() ? 'allow' : 'deny';
                $this->getAcl()->{$method}(
                    $permission->getRoleId(),
                    $permission->getResourceId(),
                    $permission->getPrivilege(),
                    $assert
                );
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
        $this->allowNotFoundResource = (boolean)$allowNotFoundResource;
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
