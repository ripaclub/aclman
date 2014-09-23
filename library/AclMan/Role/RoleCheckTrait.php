<?php
namespace AclMan\Role;

use AclMan\Role\Exception\InvalidParameterException;
use Zend\Permissions\Acl\Role\GenericRole;
use Zend\Permissions\Acl\Role\RoleInterface;

trait RoleCheckTrait
{
    /**
     * @param $role
     * @return GenericRole
     * @throws InvalidParameterException
     */
    private function checkRole($role)
    {
        if(is_string($role)) {
            $role = new GenericRole($role);
        }

        if(!($role instanceof RoleInterface)){
            throw new InvalidParameterException('Invalid type role');
        }

        return $role;
    }
} 