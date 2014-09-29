<?php
namespace AclMan\Permission;

use AclMan\Resource\ResourceCheckTrait;
use AclMan\Role\RoleCheckTrait;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use Zend\Permissions\Acl\Role\RoleInterface;

class GenericPermission implements PermissionInterface
{
    /**
     * TRAIT
     ******************************************************************************************************************/

    use RoleCheckTrait;
    use ResourceCheckTrait;

    /**
     * ATTRIBUTE
     ******************************************************************************************************************/

    /**
     * @var string
     */
    protected $roleId;

    /**
     * @var string
     */
    protected $resourceId;

    /**
     * @var string
     */
    protected $privilege;

    /**
     * @var
     */
    protected $assert;

    /**
     * @var bool
     */
    protected $allow = true;

    /**
     * METHOD
     ******************************************************************************************************************/

    /**
     * @param null|array $options
     */
    function __construct($options = null)
    {
        if($options) {
            if(isset($options['role'])) {
                $role = $this->checkRole($options['role']);
                $this->roleId = $role->getRoleId();
            }

            if(isset($options['resource'])) {
                $resource = $this->checkResource($options['resource']);
                $this->resourceId = $resource->getResourceId();
            }

            if(isset($options['privilege'])) {
                $this->setPrivilege($options['privilege']);
            }

            if(isset($options['allow'])) {
                $this->allow = (bool) $options['allow'];
            }

            if(isset($options['assert'])) {
                $this->setAssertion($options['assert']);
            }
        }
    }

    /**
     * @param ResourceInterface $resource
     */
    public function setResourceId(ResourceInterface $resource)
    {
        $this->resourceId = $resource->getResourceId();
    }

    /**
     * @param RoleInterface $role
     */
    public function setRoleId(RoleInterface $role)
    {
        $this->roleId = $role->getRoleId();
    }

    /**
     * @return mixed
     */
    public function getAssertion()
    {
        return $this->assert;
    }

    /**
     * @param $assert
     * @return self
     */
    public function setAssertion($assert)
    {
        $this->assert = $assert;
        return $this;
    }

    /**
     * @return bool
     */
    public function isAllow()
    {
        return (bool) $this->allow;
    }

    /**
     * Returns the string identifier of the Resource
     *
     * @return string
     */
    public function getResourceId()
    {
        return $this->resourceId;
    }

    /**
     * Returns the string identifier of the Role
     *
     * @return string
     */
    public function getRoleId()
    {
        return $this->roleId;
    }

    /**
     * @return string
     */
    public function getPrivilege()
    {
        return $this->privilege;
    }

    /**
     * @param $privilege
     * @return self
     */
    public function setPrivilege($privilege)
    {
        $this->privilege = $privilege;
        return $this;
    }
}