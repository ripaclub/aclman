<?php
/**
 * ACL Manager
 *
 * @link        https://github.com/ripaclub/aclman
 * @copyright   Copyright (c) 2014, RipaClub
 * @license     http://opensource.org/licenses/BSD-2-Clause Simplified BSD License
 */
namespace AclMan\Service;

use Zend\Permissions\Acl\AclInterface;
use Zend\Permissions\Acl\Resource\ResourceInterface;

/**
 * Interface ServiceInterface
 */
interface ServiceInterface extends AclInterface
{
    /**
     * Load roles from storage
     *
     * @return self
     */
    public function init();

    /**
     * @param null $role
     * @param ResourceInterface|null $resource
     * @return bool
     */
    public function loadResource($role = null, $resource = null);
}
