<?php
namespace AclMan\Role;

use Zend\Permissions\Acl\Role\GenericRole;
use Zend\Permissions\Acl\Role\RoleInterface;

trait RoleCheckTrait
{
    /**
     * @param $role
     * @return GenericRole
     * @throws Exception\InvalidParameterException
     */
    private function checkRole($role)
    {
        if(is_string($role)) {
            $role = new GenericRole($role);
        }

        if(!($role instanceof RoleInterface)){
            throw new Exception\InvalidParameterException(sprintf('Invalid type role %s', (is_object($role)) ? get_class($role) : gettype($role)));
        }

        return $role;
    }
} 