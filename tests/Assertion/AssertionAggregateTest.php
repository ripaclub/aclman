<?php
/**
 * ACL Manager
 *
 * @link        https://github.com/ripaclub/aclman
 * @copyright   Copyright (c) 2015, RipaClub
 * @license     http://opensource.org/licenses/BSD-2-Clause Simplified BSD License
 */
namespace AclManTest\Assertion;

use AclMan\Assertion\AssertionAggregate;
use AclManTest\AclManTestCase;
use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\Permissions\Acl\Assertion\AssertionManager;
use Zend\ServiceManager;

/**
 * Class AssertionAggregateTest
 */
class AssertionAggregateTest extends AclManTestCase
{
    /**
     * @var \Zend\ServiceManager\ServiceManager
     */
    protected $serviceManager;

    public function setUp()
    {
        $config = [
            'factories' => [
                'assertManager' => 'AclMan\Assertion\AssertionManagerFactory',
            ]
        ];

        $sm = $this->serviceManager = new ServiceManager\ServiceManager($config);

        $sm->setService('Config', $config);
    }

    /**
     * @expectedException Zend\Permissions\Acl\Exception\RuntimeException
     */
    public function testNotAssertionException()
    {
        $assertionAggregate = new AssertionAggregate();
        $assertionAggregate->assert($this->getMock('Zend\Permissions\Acl\Acl'), $this->getMock('Zend\Permissions\Acl\Role\RoleInterface'));
    }

    /**
     * @expectedException Zend\Permissions\Acl\Exception\RuntimeException
     */
    public function testNotAssertionManagerWithStringException()
    {
        $assertionAggregate = new AssertionAggregate();
        $assertionAggregate->addAssertions(['test']);
        $assertionAggregate->assert($this->getMock('Zend\Permissions\Acl\Acl'), $this->getMock('Zend\Permissions\Acl\Role\RoleInterface'));
    }

    /**
     * @expectedException Zend\Permissions\Acl\Exception\RuntimeException
     */
    public function testNotAssertionManagerWithArrayException()
    {
        $assertionAggregate = new AssertionAggregate();
        $assertionAggregate->addAssertions(['test' => ['test']]);
        $assertionAggregate->assert($this->getMock('Zend\Permissions\Acl\Acl'), $this->getMock('Zend\Permissions\Acl\Role\RoleInterface'));
    }

    /**
     * @expectedException Zend\Permissions\Acl\Exception\RuntimeException
     */
    public function testNotAssertionManagerWithArrayExceptionNoName()
    {
        $assertionAggregate = new AssertionAggregate();
        $assertionAggregate->addAssertions(['test' => ['test' => 'test']]);
        $assertionAggregate->setAssertionManager($this->getMockBuilder('Zend\Permissions\Acl\Assertion\AssertionManager')->disableOriginalConstructor()->getMock());
        $assertionAggregate->assert($this->getMock('Zend\Permissions\Acl\Acl'), $this->getMock('Zend\Permissions\Acl\Role\RoleInterface'));
    }


    /**
     *
     */
    public function testNameAssert()
    {
        $assertionAggregate = new AssertionAggregate();
        $assertionAggregate->addAssertions(['test' => ['name' => 'nametest', 'test' => 'test']]);

        $mockService = $this->getMock('Zend\Permissions\Acl\Assertion\AssertionInterface');

        $mock =  $this->getMockBuilder('Zend\Permissions\Acl\Assertion\AssertionManager')->disableOriginalConstructor()->getMock();
        $mock->method('get')->willReturn($mockService);


        $assertionAggregate->setAssertionManager($mock);
        $this->assertFalse($assertionAggregate->assert($this->getMock('Zend\Permissions\Acl\Acl'), $this->getMock('Zend\Permissions\Acl\Role\RoleInterface')));
    }

    /**
     * @expectedException Zend\Permissions\Acl\Exception\RuntimeException
     */
    public function testNotAssertionManagerWithNameClassException()
    {
        $assertionAggregate = new AssertionAggregate();
        $assertionAggregate->addAssertions(["stdClass"]);

        $this->assertFalse($assertionAggregate->assert($this->getMock('Zend\Permissions\Acl\Acl'), $this->getMock('Zend\Permissions\Acl\Role\RoleInterface')));
    }

    /**
     * @expectedException Zend\Permissions\Acl\Exception\RuntimeException
     */
    public function testNotAssertionManagerWithClassException()
    {
        $assertionAggregate = new AssertionAggregate();
        $assertionAggregate->addAssertions([new \stdClass()]);

        $assertionAggregate->assert($this->getMock('Zend\Permissions\Acl\Acl'), $this->getMock('Zend\Permissions\Acl\Role\RoleInterface'));
    }

    /**
     * @expectedException Zend\Permissions\Acl\Exception\RuntimeException
     */
    public function testNotAssertionManagerNotServiceFound()
    {
        $assertionAggregate = new AssertionAggregate();
        $assertionAggregate->setMode(AssertionAggregate::MODE_AT_LEAST_ONE);
        $assertionAggregate->addAssertions(['test']);

        $mock =  $this->getMockBuilder('Zend\Permissions\Acl\Assertion\AssertionManager')->disableOriginalConstructor()->getMock();
        $mock->method('has')->willReturn(false);

        $assertionAggregate->setAssertionManager($mock);
        $assertionAggregate->assert($this->getMock('Zend\Permissions\Acl\Acl'), $this->getMock('Zend\Permissions\Acl\Role\RoleInterface'));
    }

    /**
     *
     */
    public function testNameClassAssert()
    {
        $assertionAggregate = new AssertionAggregate();
        $assertionAggregate->addAssertions(['AclManTest\Integration\Service\TestAsset\Assertion\Assertion1']);

        $this->assertFalse($assertionAggregate->assert($this->getMock('Zend\Permissions\Acl\Acl'), $this->getMock('Zend\Permissions\Acl\Role\RoleInterface')));
    }

    /**
     *
     */
    public function testNameAssertFalse()
    {
        $assertionAggregate = new AssertionAggregate();
        $assertionAggregate->addAssertions(['test']);

        $mockService = $this->getMock('Zend\Permissions\Acl\Assertion\AssertionInterface');

        $mock =  $this->getMockBuilder('Zend\Permissions\Acl\Assertion\AssertionManager')->disableOriginalConstructor()->getMock();
        $mock->method('get')->willReturn($mockService);
        $mock->method('has')->willReturn(true);

        $assertionAggregate->setAssertionManager($mock);
        $this->assertFalse(
            $assertionAggregate->assert(
                $this->getMock('Zend\Permissions\Acl\Acl'),
                $this->getMock('Zend\Permissions\Acl\Role\RoleInterface')
            )
        );
    }

    /**
     *
     */
    public function testNameAssertTrue()
    {
        $assertionAggregate = new AssertionAggregate();
        $assertionAggregate->addAssertions(['test']);

        $mockService = $this->getMock('Zend\Permissions\Acl\Assertion\AssertionInterface');
        $mockService->method('assert')->willReturn(true);

        $mock =  $this->getMockBuilder('Zend\Permissions\Acl\Assertion\AssertionManager')->disableOriginalConstructor()->getMock();
        $mock->method('get')->willReturn($mockService);
        $mock->method('has')->willReturn(true);

        $assertionAggregate->setAssertionManager($mock);
        $this->assertTrue(
            $assertionAggregate->assert(
                $this->getMock('Zend\Permissions\Acl\Acl'),
                $this->getMock('Zend\Permissions\Acl\Role\RoleInterface')
            )
        );
    }

    public function testNameAssertFalseSpecial()
    {
        $assertionAggregate = new AssertionAggregate();
        $assertionAggregate->setMode(AssertionAggregate::MODE_AT_LEAST_ONE);
        $assertionAggregate->addAssertions(['test']);

        $mockService = $this->getMock('Zend\Permissions\Acl\Assertion\AssertionInterface');
        $mockService->method('assert')->willReturn(false);

        $mock =  $this->getMockBuilder('Zend\Permissions\Acl\Assertion\AssertionManager')->disableOriginalConstructor()->getMock();
        $mock->method('get')->willReturn($mockService);
        $mock->method('has')->willReturn(true);

        $assertionAggregate->setAssertionManager($mock);
        $this->assertFalse(
            $assertionAggregate->assert(
                $this->getMock('Zend\Permissions\Acl\Acl'),
                $this->getMock('Zend\Permissions\Acl\Role\RoleInterface')
            )
        );
    }
}
