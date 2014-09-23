<?php
namespace AclManTest\Service;

use AclMan\Permission\GenericPermission;
use AclMan\Service\ServiceImplement;
use AclManTest\AclManTestCase;
use Zend\Permissions\Acl\Acl;

class ServiceAbstractTest extends AclManTestCase
{
    /**
     * @var $service ServiceImplement
     */
    protected $service;

    public function setUp()
    {
        $this->service = new ServiceImplement();

        $this->service->setAcl(new Acl());
    }

    public function _testServiceAbstractInit()
    {
        $mockStorage =  $this->getMockBuilder('AclMan\Storage\Adapter\ArrayAdapter\ArrayAdapter')
                ->disableOriginalConstructor()
                ->getMock();

        $mockStorage->expects($this->any())
            ->method('getRoles')
            ->will($this->returnValue([]));

        $this->service->setStorage($mockStorage);
        $this->service->init();

        $this->assertEmpty($this->service->getRoles());

        $mockStorage =  $this->getMockBuilder('AclMan\Storage\Adapter\ArrayAdapter\ArrayAdapter')
            ->disableOriginalConstructor()
            ->getMock();

        $mockStorage->expects($this->any())
            ->method('getRoles')
            ->will($this->returnValue(['test', 'test1']));

        $this->service->setStorage($mockStorage);
        $this->service->init();

        $this->assertNotEmpty($this->service->getRoles());

        $this->assertTrue($this->service->hasRole('test1'));
        $this->assertInstanceOf('Zend\Permissions\Acl\Role\RoleInterface', $this->service->getRole('test1'));

        $this->assertSame($this->service, $this->service->addRole('test2'));
    }

    public function _testServiceAbstractAllowNotFoundResource()
    {
        $this->service->setAllowNotFoundResource(true);
        $this->assertTrue($this->service->getAllowNotFoundResource());

        $this->service->setAllowNotFoundResource(false);
        $this->assertFalse($this->service->getAllowNotFoundResource());
    }

    public function _testLoadResourceNotFound()
    {
        $this->service->addRole('role1');
        $this->service->addRole('role2');

        $mockStorage =  $this->getMockBuilder('AclMan\Storage\Adapter\ArrayAdapter\ArrayAdapter')
            ->disableOriginalConstructor()
            ->setMethods(['hasResource'])
            ->getMock();

        $mockStorage->expects($this->any())
            ->method('hasResource')
            ->will($this->returnValue(false));

        $this->service->setStorage($mockStorage);

        $this->service->setAllowNotFoundResource(false);
        $this->service->loadResource('resource1');

        $this->assertFalse($this->service->isAllowed('role1', 'resource1'));

        $mockStorage =  $this->getMockBuilder('AclMan\Storage\Adapter\ArrayAdapter\ArrayAdapter')
            ->disableOriginalConstructor()
            ->setMethods(['hasResource'])
            ->getMock();

        $mockStorage->expects($this->any())
            ->method('hasResource')
            ->will($this->returnValue(false));

        $this->service->setStorage($mockStorage);

        $this->service->setAllowNotFoundResource(true);
        $resultLoad = $this->service->loadResource('resource2');

        $this->assertTrue($this->service->isAllowed('role1', 'resource2'));
        $this->assertTrue($resultLoad);

        $resultLoad = $this->service->loadResource('resource2');
        $this->assertFalse($resultLoad);
    }

    public function testLoadResourceFound()
    {
        $this->service->addRole('role1');
        $this->service->addRole('role2');

        $mockStorage =  $this->getMockBuilder('AclMan\Storage\Adapter\ArrayAdapter\ArrayAdapter')
            ->disableOriginalConstructor()
            ->setMethods(['hasResource', 'getPermissions'])
            ->getMock();

        $mockStorage->expects($this->any())
            ->method('hasResource')
            ->will($this->returnValue(false));

        $permissions = [
            new GenericPermission([
                    'role' => 'role2',
                    'resource' => 'resource1',
                    'privilege' => 'add'
                ]
            ),
            new GenericPermission([
                    'role' => 'role1',
                    'resource' => 'resource1',
                    'privilege' => 'view'
                ]
            )
        ];

        $mockStorage->expects($this->any())
            ->method('getPermissions')
            ->will($this->returnValue($permissions));

        $this->service->setStorage($mockStorage);

        $resultLoad = $this->service->loadResource('resource2');
    }
} 