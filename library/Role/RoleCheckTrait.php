<?php
/**
 * ACL Manager
 *
 * @link        https://github.com/ripaclub/aclman
 * @copyright   Copyright (c) 2014, RipaClub
 * @license     http://opensource.org/licenses/BSD-2-Clause Simplified BSD License
 */
namespace AclMan\Role;

use AclMan\Exception\InvalidParameterException;
use Zend\Permissions\Acl\Role\GenericRole;
use Zend\Permissions\Acl\Role\RoleInterface;

/**
 * Trait RoleCheckTrait
 */
trait RoleCheckTrait
{
    /**
     * @param string|RoleInterface $role
     * @return GenericRole
     * @throws InvalidParameterException
     */
    private function checkRole($role)
    {
        if (is_string($role)) {
            $role = new GenericRole($role);
        }

        if (!$role instanceof RoleInterface) {
            throw new InvalidParameterException(sprintf(
                'Invalid role type; received "%s"',
                (is_object($role)) ? get_class($role) : gettype($role)
            ));
        }

        return $role;
    }
}
