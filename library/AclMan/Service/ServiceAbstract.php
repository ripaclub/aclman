<?php
namespace AclMan\Service;

use AclMan\Acl\AclAwareTrait;
use AclMan\Assertion\AssertionAwareTrait;
use AclMan\Resource\ResourceCheckTrait;
use AclMan\Role\RoleAwareTrait;
use AclMan\Role\RoleCheckTrait;
use AclMan\Storage\StorageAwareTrait;
use Zend\Permissions\Acl\Resource;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use Zend\Permissions\Acl\Role;

class ServiceAbstract implements ServiceInterface
{
    /**
     * TRAIT
     ******************************************************************************************************************/

    use StorageAwareTrait;
    use AclAwareTrait;
    use ResourceCheckTrait;
    use RoleCheckTrait;
    use AssertionAwareTrait;

    /**
     * ATTRIBUTE
     ******************************************************************************************************************/

    protected $allowNotFoundResource = false;

    /**
     * METHOD
     ******************************************************************************************************************/

    /**
     * Load roles from storage
     *
     * @return self
     */
    public function init()
    {
        if($this->getStorage()) {
            // Add Role
            $roles = $this->getStorage()->getRoles();
            foreach ($roles as $role) {
                $roleParents = $this->getStorage()->getParentRoles($role);
                $this->getAcl()->addRole($role, $roleParents);
            }
        }
        return $this;
    }

    /**
     * Add roles from storage
     *
     * @param Role|string $role
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
     * @param Role|string $role
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
     * @param Role|string $role
     * @return Role\RoleInterface
     */
    public function getRole($role)
    {
        return $this->getAcl()->getRole($role);
    }

    /**
     * Load permission
     *
     * @param null $role
     * @param null $resource
     * @return bool
     */
    public function loadResource($role = null, $resource = null)
    {
        if($resource && $this->getStorage()->hasResource($resource) && !$this->getAcl()->hasResource($resource)) {
            $this->getAcl()->addResource($resource);
        }

        $permissions = $this->getStorage()->getPermissions($role, $resource);
        if ($this->getStorage()->hasResource($resource) && count($permissions) > 0) {
        /* @var $permission \AclMan\Permission\GenericPermission */
            foreach($permissions as $permission) {
                $assert = null;

                if ($permission->getAssertion()) {
                    $assert = $this->getPluginManager()->get($permission->getAssertion());
                }

                if($permission->isAllow()) {
                   // var_dump(sprintf('ALLOW: role "%s" resource "%s" privilege "%s"', $permission->getRoleId(), $permission->getResourceId(), $permission->getPrivilege()));
                    $this->getAcl()->allow(
                        $permission->getRoleId(),
                        $permission->getResourceId(),
                        $permission->getPrivilege(),
                        $assert
                    );
                } else {
                 //   var_dump(sprintf('DENY: role "%s" resource "%s" privilege "%s"', $permission->getRoleId(), $permission->getResourceId(), $permission->getPrivilege()));
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
     * Returns true if and only if the Resource exists in the ACL
     *
     * The $resource parameter can either be a Resource or a Resource identifier.
     *
     * @param  Resource\ResourceInterface|string $resource
     * @return bool
     */
    public function hasResource($resource)
    {
        return $this->getAcl()->hasResource($resource);
    }

    /**
     * Returns true if and only if the Role has access to the Resource
     *
     * The $role and $resource parameters may be references to, or the string identifiers for,
     * an existing Resource and Role combination.
     *
     * If either $role or $resource is null, then the query applies to all Roles or all Resources,
     * respectively. Both may be null to query whether the ACL has a "blacklist" rule
     * (allow everything to all). By default, Zend\Permissions\Acl creates a "whitelist" rule (deny
     * everything to all), and this method would return false unless this default has
     * been overridden (i.e., by executing $acl->allow()).
     *
     * If a $privilege is not provided, then this method returns false if and only if the
     * Role is denied access to at least one privilege upon the Resource. In other words, this
     * method returns true if and only if the Role is allowed all privileges on the Resource.
     *
     * This method checks Role inheritance using a depth-first traversal of the Role registry.
     * The highest priority parent (i.e., the parent most recently added) is checked first,
     * and its respective parents are checked similarly before the lower-priority parents of
     * the Role are checked.
     *
     * @param  Role\RoleInterface|string $role
     * @param  Resource\ResourceInterface|string $resource
     * @param  string $privilege
     * @return bool
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

