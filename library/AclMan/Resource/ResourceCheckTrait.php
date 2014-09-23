<?php
namespace AclMan\Resource;


use AclMan\Resource\Exception\InvalidParameterException;
use Zend\Permissions\Acl\Resource\GenericResource;
use Zend\Permissions\Acl\Resource\ResourceInterface;

trait ResourceCheckTrait
{
    /**
     * @param $resource
     * @return GenericResource
     * @throws Exception\InvalidParameterException
     */
    private function checkResource($resource)
    {
        if(is_string($resource)) {
            $resource = new GenericResource($resource);
        }

        if(!($resource instanceof ResourceInterface)){
            throw new InvalidParameterException('Invalid type resource');
        }

        return $resource;
    }
} 