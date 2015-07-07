<?php
/**
 * ACL Manager
 *
 * @link        https://github.com/ripaclub/aclman
 * @copyright   Copyright (c) 2015, RipaClub
 * @license     http://opensource.org/licenses/BSD-2-Clause Simplified BSD License
 */
namespace AclMan\Role;

use Zend\Permissions\Acl\Role\RoleInterface;

/**
 * Trait RoleAwareTrait
 */
trait RoleAwareTrait
{
    /**
     * @var RoleInterface
     */
    protected $role;

    /**
     * @param RoleInterface $role
     */
    public function setRole(RoleInterface $role)
    {
        $this->role = $role;
    }

    /**
     * @return RoleInterface
     */
    public function getRole()
    {
        return $this->role;
    }
}
