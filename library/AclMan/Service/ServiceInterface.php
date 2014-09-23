<?php
namespace AclMan\Service;

use Zend\Permissions\Acl\AclInterface;
use Zend\Permissions\Acl\Resource\ResourceInterface;

interface ServiceInterface extends AclInterface
{
    /**
     * Load roles from storage
     *
     * @return self
     */
    public function init();

    /**
     * Load resource
     *
     * @param ResourceInterface $resource
     * @return bool
     */
    public function loadResource($resource);

} 