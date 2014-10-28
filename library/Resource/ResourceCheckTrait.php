<?php
/**
 * ACL Manager
 *
 * @link        https://github.com/ripaclub/aclman
 * @copyright   Copyright (c) 2014, RipaClub
 * @license     http://opensource.org/licenses/BSD-2-Clause Simplified BSD License
 */
namespace AclMan\Resource;

use AclMan\Exception\InvalidParameterException;
use Zend\Permissions\Acl\Resource\GenericResource;
use Zend\Permissions\Acl\Resource\ResourceInterface;

/**
 * Trait ResourceCheckTrait
 */
trait ResourceCheckTrait
{
    /**
     * @param string|ResourceInterface $resource
     * @return GenericResource
     * @throws InvalidParameterException
     */
    private function checkResource($resource)
    {
        if (is_string($resource)) {
            $resource = new GenericResource($resource);
        }

        if (!$resource instanceof ResourceInterface) {
            throw new InvalidParameterException('Invalid type resource');
        }

        return $resource;
    }
}
