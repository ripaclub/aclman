<?php
/**
 * ACL Manager
 *
 * @link        https://github.com/ripaclub/aclman
 * @copyright   Copyright (c) 2015, RipaClub
 * @license     http://opensource.org/licenses/BSD-2-Clause Simplified BSD License
 */
namespace AclManTest\Integration\Service\TestAsset\Assertion;

use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Assertion\AssertionInterface;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use Zend\Permissions\Acl\Role\RoleInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Class Assertion1
 */
class Assertion1 implements AssertionInterface, ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    protected $test;

    public function __construct($option = null)
    {
        if (is_array($option) && isset($option['test'])) {
            $this->test = $option['test'];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function assert(Acl $acl, RoleInterface $role = null, ResourceInterface $resource = null, $privilege = null)
    {
        return false;
    }

    /**
     * @return mixed
     */
    public function getTest()
    {
        return $this->test;
    }

    /**
     * @param mixed $test
     * @return $this
     */
    public function setTest($test)
    {
        $this->test = $test;
        return $this;
    }
}
