<?php
namespace AclMan\Assertion;

use Zend\Permissions\Acl\Assertion\AssertionManager as BaseAssertionManager;

/**
 * Class AssertionManager
 */
class AssertionManager extends BaseAssertionManager
{
    /**
     * zend-servicemanager v3 compatibility
     * @var bool
     */
    protected $shareByDefault = false;

    /**
     * zend-servicemanager v2 compatibility
     * @var bool
     */
    protected $sharedByDefault = false;
}