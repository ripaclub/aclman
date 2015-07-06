<?php
/**
 * ACL Manager
 *
 * @link        https://github.com/ripaclub/aclman
 * @copyright   Copyright (c) 2014, RipaClub
 * @license     http://opensource.org/licenses/BSD-2-Clause Simplified BSD License
 */
namespace AclMan\Permission;

use AclMan\Exception\InvalidParameterException;

/**
 * Trait PermissionCheckTrait
 */
trait PermissionCheckTrait
{
    /**
     * @param PermissionInterface|array $permission
     * @return GenericPermission
     * @throws InvalidParameterException
     */
    private function checkPermission($permission)
    {
        if (is_array($permission)) {
            $permission = new GenericPermission($permission);
        }

        if (!$permission instanceof PermissionInterface) {
            throw new InvalidParameterException(sprintf(
                'Invalid permission type; received "%s"',
                (is_object($permission)) ? get_class($permission) : gettype($permission)
            ));
        }

        return $permission;
    }
}
