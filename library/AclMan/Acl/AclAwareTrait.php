<?php
/**
 * Created by visa
 * Date:  8/13/14 1:32 PM
 * Class: AclAwareTrait.php
 */

namespace AclMan\Acl;

use Zend\Permissions\Acl\Acl;

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