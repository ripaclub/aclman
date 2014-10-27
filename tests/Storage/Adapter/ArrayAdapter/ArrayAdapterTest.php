<?php
/**
 * ACL Manager
 *
 * @link        https://github.com/ripaclub/aclman
 * @copyright   Copyright (c) 2014, RipaClub
 * @license     http://opensource.org/licenses/BSD-2-Clause Simplified BSD License
 */
namespace AclManTest\Storage\Adapter\ArrayAdapter;

use AclMan\Permission\GenericPermission;
use AclMan\Storage\Adapter\ArrayAdapter\ArrayAdapter;
use AclManTest\AclManTestCase;
use Zend\Permissions\Acl\Resource\GenericResource;
use Zend\Permissions\Acl\Role\GenericRole;

class ArrayAdapterTest extends AclManTestCase
{
    /**
     * @var $adapter \AclMan\Storage\Adapter\ArrayAdapter\ArrayAdapter
     */
    protected $adapter;

    protected $config = [
      'roles' => [
          'role1' => [
              'resources' => [
                  'resource1' => [
                      [
                          'assert' => 'test',
                          'allow' =>  true,
                          'privilege' => 'view'
                      ],
                      [
                          'assert' => 'test',
                          'allow' =>  true,
                          'privilege' => 'add'
                      ]
                  ]
              ]
          ],
          'role2' => [
              'parents' => [
                  'role1'
              ],
              'resources' => [
                  'resource2' => [
                      [
                          'assert' => 'test',
                          'allow' =>  true,
                          'privilege' => 'view'
                      ],
                      [
                          'assert' => 'test',
                          'allow' =>  true,
                          'privilege' => 'add'
                      ]
                  ]
              ]
          ],
      ]
    ];

    public function setUp()
    {
        $this->adapter = new ArrayAdapter();
    }

    /**
     * @expectedException \AclMan\Role\Exception\InvalidParameterException
     */
    public function testHasRolesException()
    {
        $this->adapter->hasRole(10);
    }

    public function testHasRoles()
    {
        $this->assertFalse($this->adapter->hasRole('role1'));
    }

    /**
     * @depends testHasRoles
     */
    public function testAddRole()
    {
        $this->assertSame($this->adapter, $this->adapter->addRole('role1'));
        $this->assertTrue($this->adapter->hasRole('role1'));
    }

    /**
     * @depends testAddRole
     */
    public function testAddRoleParents()
    {
        $this->adapter->addRole('role1');
        $this->adapter->addRole('role2', ['role1']);
        $this->assertTrue($this->adapter->hasRole('role1'));
        $this->assertTrue($this->adapter->hasRole('role2'));
    }

    /**
     * @depends testHasRoles
     * @expectedException \AclMan\Storage\Exception\RoleAlreadyExistException
     */
    public function testAddRoleException()
    {
        $this->adapter->addRole('role1');
        $this->adapter->addRole('role1');
    }

    /**
     * @expectedException \AclMan\Role\Exception\InvalidParameterException
     */
    public function testAddRoleException2()
    {
        $this->adapter->hasRole(11);
    }


    /**
     * @depends testAddRole
     */
    public function testAddRoles()
    {
        $this->adapter->addRoles(['role1']);
        $this->assertTrue($this->adapter->hasRole('role1'));


        $this->adapter->addRoles([['role' => 'role2', 'parents' => ['role1']]]);
        $this->assertTrue($this->adapter->hasRole('role2'));
    }

    /**
     * @depends testAddRole
     */
    public function testGetRoles()
    {
        $this->assertCount(0,  $this->adapter->getRoles());
        $this->adapter->addRoles(['role1']);
        $this->assertCount(1,  $this->adapter->getRoles());
    }

    /**
     * @depends testAddRole
     */
    public function testGetParentRoles()
    {
        $this->adapter->addRoles(['role1']);
        $this->assertCount(0,  $this->adapter->getParentRoles('role1'));

        $this->adapter->addRoles([['role' => 'role2', 'parents' => ['role1']]]);
        $this->assertCount(1,  $this->adapter->getParentRoles('role2'));
    }

    /**
     * @expectedException \AclMan\Storage\Exception\RoleNotExistException
     */
    public function testGetParentRolesException()
    {
        $this->adapter->getParentRoles('role1');
    }

    /**
     * @depends testAddRole
     */
    public function testAddParentRoles()
    {
        $this->adapter->addRoles(['role1']);
        $this->adapter->addRoles(['role2']);
        $this->adapter->addRoles(['role3']);


        $this->assertTrue($this->adapter->addParentRoles('role3', ['role1', 'role2']));
        $this->assertCount(2,  $this->adapter->getParentRoles('role3'));
    }

    /**
     * @expectedException \AclMan\Storage\Exception\RoleNotExistException
     */
    public function testAddParentRolesException1()
    {
        $this->adapter->addParentRoles('role3', ['role1', 'role2']);
    }

    /**
     * @depends testAddRole
     * @expectedException \AclMan\Storage\Exception\RoleNotExistException
     */
    public function testAddParentRolesException2()
    {
        $this->adapter->addRole('role1');
        $this->adapter->addParentRoles('role1', ['role3', 'role2']);
    }

    public function testHasResource()
    {
        $this->assertFalse($this->adapter->hasResource('resource1'));
    }

    /**
     * @depends testHasResource
     */
    public function testAddResource()
    {
        $this->assertSame($this->adapter, $this->adapter->addResource('resource1'));
        $this->assertTrue($this->adapter->hasResource('resource1'));
    }

    /**
     * @depends testAddResource
     * @expectedException \AclMan\Storage\Exception\ResourceAlreadyExistException
     */
    public function testAddResourceException()
    {
        $this->adapter->addResource('resource1');
        $this->adapter->addResource('resource1');
    }

    /**
     * @depends testAddResource
     * @expectedException \AclMan\Resource\Exception\InvalidParameterException
     */
    public function testAddResourceException2()
    {
        $this->adapter->addResource(1111);
    }

    /**
     * @depends testAddResource
     */
    public function testAddResources()
    {
        $this->assertSame($this->adapter, $this->adapter->addResources(['resource1', 'resource2']));
        $this->assertTrue($this->adapter->hasResource('resource1'));
        $this->assertTrue($this->adapter->hasResource('resource2'));
    }

    /**
     * @depends testAddResource
     */
    public function testGetResources()
    {
        $this->adapter->addResource('resource1');
        $this->adapter->addResource('resource2');
        $this->assertCount(2, $this->adapter->getResources());
    }

    /**
     * @depends testAddRole
     */
    public function testAddPermission()
    {
        $this->adapter->addRoles(['role1']);
        $this->adapter->addResource('resource1');

        $permission =  $this->getMockBuilder('AclMan\Permission\GenericPermission')
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
            ->will($this->returnValue('test'));

        $permission->expects($this->any())
            ->method('getAssertion')
            ->will($this->returnValue(null));

        $permission->expects($this->any())
            ->method('isAllow')
            ->will($this->returnValue(true));


        $this->assertSame($this->adapter, $this->adapter->addPermission($permission));
    }

    /**
     * @depends testAddRole
     */
    public function testAddPermissionNoRoleNoResource()
    {
        $this->adapter->addRoles(['role1']);
        $this->adapter->addResource('resource1');

        $permission =  $this->getMockBuilder('AclMan\Permission\GenericPermission')
            ->disableOriginalConstructor()
            ->setMethods(['getAssertion', 'isAllow', 'getPrivilege', 'getResourceId', 'getRoleId'])
            ->getMock();

        $permission->expects($this->any())
            ->method('getRoleId')
            ->will($this->returnValue(null));

        $permission->expects($this->any())
            ->method('getResourceId')
            ->will($this->returnValue(null));

        $permission->expects($this->any())
            ->method('getPrivilege')
            ->will($this->returnValue('test'));

        $permission->expects($this->any())
            ->method('getAssertion')
            ->will($this->returnValue(null));

        $permission->expects($this->any())
            ->method('isAllow')
            ->will($this->returnValue(true));

        $this->assertSame($this->adapter, $this->adapter->addPermission($permission));
    }

    /**
     * @depends testAddPermission
     * @expectedException \AclMan\Storage\Exception\ResourceNotExistException
     */
    public function testAddPermissionException1()
    {
        $this->adapter->addRoles(['role1']);

        $permission =  $this->getMockBuilder('AclMan\Permission\GenericPermission')
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
            ->will($this->returnValue('test'));

        $permission->expects($this->any())
            ->method('getAssertion')
            ->will($this->returnValue(null));

        $permission->expects($this->any())
            ->method('isAllow')
            ->will($this->returnValue(true));


        $this->adapter->addPermission($permission);
    }

    /**
     * @depends testAddPermission
     * @expectedException \AclMan\Storage\Exception\RoleNotExistException
     */
    public function testAddPermissionException2()
    {
        $permission =  $this->getMockBuilder('AclMan\Permission\GenericPermission')
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
            ->will($this->returnValue('test'));

        $permission->expects($this->any())
            ->method('getAssertion')
            ->will($this->returnValue(null));

        $permission->expects($this->any())
            ->method('isAllow')
            ->will($this->returnValue(true));


        $this->adapter->addPermission($permission);
    }

    /**
     * @depends testAddPermission
     * @expectedException \AclMan\Permission\Exception\InvalidParameterException
     */
    public function testAddPermissionException3()
    {
        $this->adapter->addPermission(1111);
    }

    /**
     * @depends testAddPermission
     */
    public function testAddPermissions()
    {
        $this->adapter->addRoles(['role1']);
        $this->assertCount(0, $this->adapter->getPermissions('role1'));

        $this->adapter->addResource('resource1');
        $this->adapter->addResource('resource2');

        $permissions = [];

        $permission =  $this->getMockBuilder('AclMan\Permission\GenericPermission')
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
            ->will($this->returnValue('test'));

        $permission->expects($this->any())
            ->method('getAssertion')
            ->will($this->returnValue(null));

        $permission->expects($this->any())
            ->method('isAllow')
            ->will($this->returnValue(true));

        array_push($permissions, $permission);

        $permission =  $this->getMockBuilder('AclMan\Permission\GenericPermission')
            ->disableOriginalConstructor()
            ->setMethods(['getAssertion', 'isAllow', 'getPrivilege', 'getResourceId', 'getRoleId'])
            ->getMock();

        $permission->expects($this->any())
            ->method('getRoleId')
            ->will($this->returnValue('role1'));

        $permission->expects($this->any())
            ->method('getResourceId')
            ->will($this->returnValue('resource2'));

        $permission->expects($this->any())
            ->method('getPrivilege')
            ->will($this->returnValue('test'));

        $permission->expects($this->any())
            ->method('getAssertion')
            ->will($this->returnValue(null));

        $permission->expects($this->any())
            ->method('isAllow')
            ->will($this->returnValue(true));

        array_push($permissions, $permission);
        $this->assertSame($this->adapter, $this->adapter->addPermissions($permissions));
    }

    /**
     * @depends testAddPermission
     */
    public function testGetPermission()
    {
        $this->adapter->addRoles(['role1']);
        $this->assertCount(0, $this->adapter->getPermissions('role1'));

        $this->adapter->addResource('resource1');
        $this->adapter->addResource('resource2');

        $permission =  $this->getMockBuilder('AclMan\Permission\GenericPermission')
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
            ->will($this->returnValue('test'));

        $permission->expects($this->any())
            ->method('getAssertion')
            ->will($this->returnValue(null));

        $permission->expects($this->any())
            ->method('isAllow')
            ->will($this->returnValue(true));

        $this->adapter->addPermission($permission);
        $this->assertCount(1, $this->adapter->getPermissions('role1', 'resource1'));

        $this->assertCount(1, $this->adapter->getPermissions('role1', 'resource1'));

        $permission =  $this->getMockBuilder('AclMan\Permission\GenericPermission')
            ->disableOriginalConstructor()
            ->setMethods(['getAssertion', 'isAllow', 'getPrivilege', 'getResourceId', 'getRoleId'])
            ->getMock();

        $permission->expects($this->any())
            ->method('getRoleId')
            ->will($this->returnValue('role1'));

        $permission->expects($this->any())
            ->method('getResourceId')
            ->will($this->returnValue('resource2'));

        $permission->expects($this->any())
            ->method('getPrivilege')
            ->will($this->returnValue('test'));

        $permission->expects($this->any())
            ->method('getAssertion')
            ->will($this->returnValue(null));

        $permission->expects($this->any())
            ->method('isAllow')
            ->will($this->returnValue(true));

        $this->adapter->addPermission($permission);

        $this->assertCount(2, $this->adapter->getPermissions('role1'));
    }

    public function testConstruct()
    {
        $adapter = new ArrayAdapter($this->config);

        $this->assertTrue($adapter->hasRole('role1'));
        $this->assertTrue($adapter->hasRole('role2'));

        $this->assertCount(1, $adapter->getParentRoles('role2'));

        $this->assertTrue($adapter->hasResource('resource1'));
        $this->assertTrue($adapter->hasResource('resource2'));

        $this->assertCount(2, $adapter->getPermissions('role1'));
        $this->assertCount(2, $adapter->getPermissions('role2'));
    }
}
