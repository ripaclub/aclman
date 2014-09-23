<?php
/**
 * Created by visa
 * Date:  8/24/14 11:06 PM
 * Class: PermissionInterface.php
 */

namespace AclMan\Permission;


use Zend\Permissions\Acl\Resource\ResourceInterface;
use Zend\Permissions\Acl\Role\RoleInterface;

interface PermissionInterface extends ResourceInterface, RoleInterface
{
    /**
     * METHOD
     ******************************************************************************************************************/

    public function getAssertion();

    public function setAssertion($assert);

    public function isAllow();

    public function getPrivilege();

    public function setPrivilege($privilege);
} 