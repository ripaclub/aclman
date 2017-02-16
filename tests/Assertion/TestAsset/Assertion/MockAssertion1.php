<?php
/**
 * ACL Manager
 *
 * @link        https://github.com/ripaclub/aclman
 * @copyright   Copyright (c) 2015, RipaClub
 * @license     http://opensource.org/licenses/BSD-2-Clause Simplified BSD License
 */
namespace AclManTest\Assertion\TestAsset\Assertion;

use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Assertion\AssertionInterface;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use Zend\Permissions\Acl\Role\RoleInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Class MockAssertion1
 */
class MockAssertion1 implements AssertionInterface
{
    /**
     * {@inheritdoc}
     */
    public function assert(Acl $acl, RoleInterface $role = null, ResourceInterface $resource = null, $privilege = null)
    {
        // TODO: implement assert() method.
    }
}
