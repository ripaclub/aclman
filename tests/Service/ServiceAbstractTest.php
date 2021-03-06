<?php
/**
 * ACL Manager
 *
 * @link        https://github.com/ripaclub/aclman
 * @copyright   Copyright (c) 2015, RipaClub
 * @license     http://opensource.org/licenses/BSD-2-Clause Simplified BSD License
 */
namespace AclManTest\Service;

use AclMan\Service\Service;
use AclManTest\AclManTestCase;
use AclManTest\Assertion\TestAsset\Assertion\MockAssertion1;
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Assertion\AssertionManager;

/**
 * Class ServiceAbstractTest
 */
class ServiceAbstractTest extends AclManTestCase
{
    /**
     * @var $service Service
     */
    protected $service;

    public function setUp()
    {
        $this->service = new Service;
        $this->service->setAcl(new Acl);
    }

    public function testHasRole()
    {
        $this->assertFalse($this->service->hasRole('role1'));
    }

    public function testAddRole()
    {
        $this->assertSame($this->service, $this->service->addRole('role1'));
    }

    public function testAddRoleWithParent()
    {
        $this->service->addRole('role1');
        $this->assertSame($this->service, $this->service->addRole('role2', 'role1'));
    }

    /**
     * @depends testAddRole
     */
    public function testGetRole()
    {
        $this->service->addRole('role1');
        $this->assertInstanceOf('Zend\Permissions\Acl\Role\RoleInterface', $this->service->getRole('role1'));
    }

    public function testGetRoles()
    {
        $this->service->addRole('role1');
        $this->assertCount(1, $this->service->getRoles());
    }

    public function testServiceAbstractAllowNotFoundResource()
    {
        $this->service->setAllowNotFoundResource(true);
        $this->assertTrue($this->service->getAllowNotFoundResource());

        $this->service->setAllowNotFoundResource(false);
        $this->assertFalse($this->service->getAllowNotFoundResource());
    }

    public function testLoadResourceNotFound()
    {
        $mockStorage = $this->getMockBuilder('AclMan\Storage\Adapter\ArrayAdapter\ArrayAdapter')
            ->disableOriginalConstructor()
            ->setMethods(['hasResource', 'getPermissions', 'getParentRoles'])
            ->getMock();

        $mockStorage->expects($this->any())
            ->method('hasResource')
            ->will($this->returnValue(true));

        $mockStorage->expects($this->any())
            ->method('getPermissions')
            ->will($this->returnValue([]));

        $this->service->setStorage($mockStorage);

        $this->assertFalse($this->service->loadResource('role1', 'resource1'));
    }

    /**
     * @depends testAddRole
     */
    public function testLoadResourceAllow()
    {
        $this->service->addRole('role1');

        $mockStorage = $this->getMockBuilder('AclMan\Storage\Adapter\ArrayAdapter\ArrayAdapter')
            ->disableOriginalConstructor()
            ->setMethods(['hasResource', 'getPermissions', 'hasRole', 'getParentRoles'])
            ->getMock();

        $mockStorage->expects($this->any())
            ->method('hasResource')
            ->will($this->returnValue(true));

        $permission = $this->getMockBuilder('AclMan\Permission\GenericPermission')
            ->disableOriginalConstructor()
            ->setMethods(['getAssertion', 'isAllow', 'getPrivilege', 'getResourceId', 'getRoleId'])
            ->getMock();

        $permission->expects($this->any())
            ->method('getRoleId')
            ->will($this->returnValue('role1'));

        $permission->expects($this->any())
            ->method('getResourceId')
            ->will($this->returnValue('resource1'));

        $permission->expects($this->any())
            ->method('getPrivilege')
            ->will($this->returnValue('view'));

        $permission->expects($this->any())
            ->method('getAssertion')
            ->will($this->returnValue(null));

        $permission->expects($this->any())
            ->method('isAllow')
            ->will($this->returnValue(true));

        $mockStorage->expects($this->any())
            ->method('getPermissions')
            ->will($this->returnValue([$permission]));

        $this->service->setStorage($mockStorage);

        $this->assertTrue($this->service->loadResource('role1', 'resource1'));
    }

    /**
     * @depends testAddRole
     */
    public function testLoadResourceDeny()
    {
        $this->service->addRole('role1');

        $mockStorage = $this->getMockBuilder('AclMan\Storage\Adapter\ArrayAdapter\ArrayAdapter')
            ->disableOriginalConstructor()
            ->setMethods(['hasResource', 'getPermissions', 'hasRole', 'getParentRoles'])
            ->getMock();

        $mockStorage->expects($this->any())
            ->method('hasResource')
            ->will($this->returnValue(true));

        $permission = $this->getMockBuilder('AclMan\Permission\GenericPermission')
            ->disableOriginalConstructor()
            ->setMethods(['getAssertion', 'isAllow', 'getPrivilege', 'getResourceId', 'getRoleId'])
            ->getMock();

        $permission->expects($this->any())
            ->method('getRoleId')
            ->will($this->returnValue('role1'));

        $permission->expects($this->any())
            ->method('getResourceId')
            ->will($this->returnValue('resource1'));

        $permission->expects($this->any())
            ->method('getPrivilege')
            ->will($this->returnValue('view'));

        $permission->expects($this->any())
            ->method('getAssertion')
            ->will($this->returnValue(null));

        $permission->expects($this->any())
            ->method('isAllow')
            ->will($this->returnValue(false));

        $mockStorage->expects($this->any())
            ->method('getPermissions')
            ->will($this->returnValue([$permission]));

        $this->service->setStorage($mockStorage);

        $this->assertTrue($this->service->loadResource('role1', 'resource1'));
    }

    /**
     * @depends testAddRole
     */
    public function testLoadResourceAssert()
    {
        $this->service->addRole('role1');

        $pluginManager = new AssertionManager();

        $pluginManager->setService('testAssert', new MockAssertion1());

        $mockStorage = $this->getMockBuilder('AclMan\Storage\Adapter\ArrayAdapter\ArrayAdapter')
            ->disableOriginalConstructor()
            ->setMethods(['hasResource', 'getPermissions', 'hasRole', 'getParentRoles'])
            ->getMock();

        $mockStorage->expects($this->any())
            ->method('hasResource')
            ->will($this->returnValue(true));

        $permission = $this->getMockBuilder('AclMan\Permission\GenericPermission')
            ->disableOriginalConstructor()
            ->setMethods(['getAssertion', 'isAllow', 'getPrivilege', 'getResourceId', 'getRoleId'])
            ->getMock();

        $permission->expects($this->any())
            ->method('getRoleId')
            ->will($this->returnValue('role1'));

        $permission->expects($this->any())
            ->method('getResourceId')
            ->will($this->returnValue('resource1'));

        $permission->expects($this->any())
            ->method('getPrivilege')
            ->will($this->returnValue('view'));

        $permission->expects($this->any())
            ->method('getAssertion')
            ->will($this->returnValue('testAssert'));

        $permission->expects($this->any())
            ->method('isAllow')
            ->will($this->returnValue(false));

        $mockStorage->expects($this->any())
            ->method('getPermissions')
            ->will(
                $this->returnValue(
                    [
                        $permission
                    ]
                )
            );

        $this->service->setStorage($mockStorage);
        $this->service->setPluginManager($pluginManager);

        $this->assertTrue($this->service->loadResource('role1', 'resource1'));
    }

    public function testHasResource()
    {
        $this->assertFalse($this->service->hasResource('resource1'));
    }

    /**
     * @depends testAddRole
     */
    public function testIsAllowed()
    {
        $this->service->addRole('role1');
        $this->service->addRole('role2', 'role1');

        $mockStorage = $this->getMockBuilder('AclMan\Storage\Adapter\ArrayAdapter\ArrayAdapter')
            ->disableOriginalConstructor()
            ->setMethods(['hasResource', 'getPermissions', 'hasRole', 'getParentRoles'])
            ->getMock();

        $mockStorage->expects($this->any())
            ->method('hasResource')
            ->will($this->returnValue(true));

        $mockStorage->expects($this->any())
            ->method('getParentRoles')
            ->will($this->returnValue(['role1']));

        $permission = $this->getMockBuilder('AclMan\Permission\GenericPermission')
            ->disableOriginalConstructor()
            ->setMethods(['getAssertion', 'isAllow', 'getPrivilege', 'getResourceId', 'getRoleId'])
            ->getMock();

        $permission->expects($this->any())
            ->method('getRoleId')
            ->will($this->returnValue('role1'));

        $permission->expects($this->any())
            ->method('getResourceId')
            ->will($this->returnValue('resource1'));

        $permission->expects($this->any())
            ->method('getPrivilege')
            ->will($this->returnValue('view'));

        $permission->expects($this->any())
            ->method('getAssertion')
            ->will($this->returnValue(null));

        $permission->expects($this->any())
            ->method('isAllow')
            ->will($this->returnValue(true));

        $mockStorage->expects($this->any())
            ->method('getPermissions')
            ->will(
                $this->returnValue(
                    [
                        $permission
                    ]
                )
            );

        $this->service->setStorage($mockStorage);

        $this->assertTrue($this->service->isAllowed('role2', 'resource1', 'view'));
        $this->assertTrue($this->service->isAllowed('role1', 'resource1', 'view'));
    }
}
