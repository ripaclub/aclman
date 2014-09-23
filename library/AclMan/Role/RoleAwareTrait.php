<?php
namespace AclMan\Role;

use Zend\Permissions\Acl\Role\RoleInterface;

trait RoleAwareTrait
{
    /**
     * ATTRIBUTE
     ******************************************************************************************************************/

    /**
     * @var RoleInterface
     */
    protected $role;

    /**
     * METHOD
     ******************************************************************************************************************/

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