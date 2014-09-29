<?php
namespace AclMan\Permission;

trait PermissionCheckTrait {

    /**
     * @param $permission
     * @return GenericPermission
     * @throws Exception\InvalidParameterException
     */
    private function checkPermission($permission)
    {
        if(is_array($permission)) {
            $permission = new GenericPermission($permission);
        }

        if(!($permission instanceof PermissionInterface)){
            throw new Exception\InvalidParameterException(sprintf('Invalid type permission %s', (is_object($permission)) ? get_class($permission) : gettype($permission)));
        }

        return $permission;
    }
} 