<?php
/**
 * ACL Manager
 *
 * @link        https://github.com/ripaclub/aclman
 * @copyright   Copyright (c) 2015, RipaClub
 * @license     http://opensource.org/licenses/BSD-2-Clause Simplified BSD License
 */
namespace AclMan\Permission;

use Zend\Permissions\Acl\Resource\ResourceInterface;
use Zend\Permissions\Acl\Role\RoleInterface;

/**
 * Interface PermissionInterface
 */
interface PermissionInterface extends ResourceInterface, RoleInterface
{
    /**
     * Retrieve assertion
     *
     * @return array
     */
    public function getAssertion();

    /**
     * Set assertion
     *
     * @param array $assert
     * @return $this
     */
    public function setAssertion($assert);

    /**
     * Is allowed?
     *
     * @return bool
     */
    public function isAllow();

    /**
     * Retrieve privilege
     *
     * @return string
     */
    public function getPrivilege();

    /**
     * Set privilege
     *
     * @param string $privilege
     * @return $this
     */
    public function setPrivilege($privilege);
}
