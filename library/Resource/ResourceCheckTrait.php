<?php
/**
 * ACL Manager
 *
 * @link        https://github.com/ripaclub/aclman
 * @copyright   Copyright (c) 2015, RipaClub
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
    private function checkResource($resource = null)
    {
        if (is_null($resource)) {
            return $resource;
        }

        if (is_string($resource)) {
            $resource = new GenericResource($resource);
        }

        if (!$resource instanceof ResourceInterface) {
            throw new InvalidParameterException(sprintf(
                'Invalid type resource "%s"',
                (is_object($resource) ? get_class($resource) : gettype($resource))
            ));
        }

        return $resource;
    }
}
