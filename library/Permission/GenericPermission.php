<?php
/**
 * ACL Manager
 *
 * @link        https://github.com/ripaclub/aclman
 * @copyright   Copyright (c) 2014, RipaClub
 * @license     http://opensource.org/licenses/BSD-2-Clause Simplified BSD License
 */
namespace AclMan\Permission;

use AclMan\Resource\ResourceCheckTrait;
use AclMan\Role\RoleCheckTrait;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use Zend\Permissions\Acl\Role\RoleInterface;

/**
 * Class GenericPermission
 */
class GenericPermission implements PermissionInterface
{
    use RoleCheckTrait;
    use ResourceCheckTrait;

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
     * @var string
     */
    protected $assert;

    /**
     * @var bool
     */
    protected $allow = true;

    /**
     * @param null|array $options
     */
    public function __construct($options = null)
    {
        switch(true) {
            case isset($options['role']):
                $role = $this->checkRole($options['role']);
                $this->roleId = $role->getRoleId();
                break;
            case isset($options['resource']):
                $resource = $this->checkResource($options['resource']);
                $this->resourceId = $resource->getResourceId();
                break;
            case isset($options['privilege']):
                $this->setPrivilege($options['privilege']);
                break;
            case isset($options['allow']):
                $this->allow = (bool) $options['allow'];
                break;
            case isset($options['assert']):
                $this->setAssertion($options['assert']);
                break;
            default:
                break;
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
     * {@inheritdoc}
     */
    public function getAssertion()
    {
        return $this->assert;
    }

    /**
     * {@inheritdoc}
     */
    public function setAssertion($assert)
    {
        $this->assert = $assert;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isAllow()
    {
        return (bool) $this->allow;
    }

    /**
     * {@inheritdoc}
     */
    public function getResourceId()
    {
        return $this->resourceId;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoleId()
    {
        return $this->roleId;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrivilege()
    {
        return $this->privilege;
    }

    /**
     * {@inheritdoc}
     */
    public function setPrivilege($privilege)
    {
        $this->privilege = $privilege;
        return $this;
    }
}
