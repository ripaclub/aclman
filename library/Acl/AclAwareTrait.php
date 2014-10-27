<?php
/**
 * ACL Manager
 *
 * @link        https://github.com/ripaclub/aclman
 * @copyright   Copyright (c) 2014, RipaClub
 * @license     http://opensource.org/licenses/BSD-2-Clause Simplified BSD License
 */
namespace AclMan\Acl;

use Zend\Permissions\Acl\Acl;

/**
 * Trait AclAwareTrait
 */
trait AclAwareTrait
{
    protected $acl;

    /**
     * @param Acl $acl
     */
    public function setAcl(Acl $acl)
    {
        $this->acl = $acl;
    }

    /**
     * @return Acl
     */
    public function getAcl()
    {
        return $this->acl;
    }
}
