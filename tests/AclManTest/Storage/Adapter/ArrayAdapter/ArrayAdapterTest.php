<?php
/**
 * Created by visa
 * Date:  8/25/14 10:59 AM
 * Class: ArrayAdapterTest.php
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

    public function setUp()
    {
        $role1 = new GenericRole('role1');
        $role2 = 'role2';
        $role3 = new GenericRole('role3');

        $resource1 = new GenericResource('resource1');
        $resource2 = 'resource2';
        $resource3 = new GenericResource('resource3');

        $options = [
            'roles' => [
                $role1,
                $role2,
                [
                    'role' => $role3,
                    'parents' => [
                        $role2,
                        $role1
                    ],
                ]
            ],
            'resources' => [
                $resource1,
                $resource2,
                $resource3
            ],
            'permission' => [
                new GenericPermission([
                        'resource' => $resource2,
                        'role'     => $role1,
                        'allow'    => true
                    ]
                ),
                [
                    'resource' => 'resource3',
                    'role'     => 'role1',
                    'allow'    => false
                ]
            ]
        ];

        $this->adapter = new ArrayAdapter($options);
    }

    /**
     * @expectedException \AclMan\Role\Exception\InvalidParameterException
     */
    public function testHasRolesException()
    {
        $this->adapter->hasRole(10);
    }

    public function testHasRole()
    {
        $this->assertTrue($this->adapter->hasRole(new GenericRole('role1')));
        $this->assertTrue($this->adapter->hasRole(new GenericRole('role2')));
        $this->assertTrue($this->adapter->hasRole('role3'));
        $this->assertFalse($this->adapter->hasRole(new GenericRole('role4')));
    }

    /**
     * @expectedException \AclMan\Storage\Exception\RoleAlreadyExistException
     */
    public function testAddRoleException()
    {
        $this->adapter->addRole(new GenericRole('role1'));
    }

    /**
     * @expectedException \AclMan\Role\Exception\InvalidParameterException
     */
    public function testAddRolesException()
    {
        $this->adapter->addRoles([10]);
    }

    public function testGetRoles()
    {
        $roles =  $this->adapter->getRoles();
        $this->assertCount(3, $roles);
    }

    public function testGetParentRoles()
    {
        $roles =  $this->adapter->getParentRoles('role3');
        $this->assertCount(2, $roles);
    }

    /**
     * @expectedException \AclMan\Storage\Exception\RoleNotExistException
     */
    public function testGetParentRolesException()
    {
        $roles =  $this->adapter->getParentRoles('role5');
    }

    /**
     * @expectedException \AclMan\Storage\Exception\ResourceAlreadyExistException
     */
    public function testAddResourceException()
    {
        $this->adapter->addResource(new GenericResource('resource1'));
    }

    /**
     * @expectedException \AclMan\Resource\Exception\InvalidParameterException
     */
    public function testAddResourcesException()
    {
        $this->adapter->addResources([10]);
    }

    public function testGetResources()
    {
        $resources = $this->adapter->getResources();
        $this->assertCount(3, $resources);
    }

    public function testGetResource()
    {
        $resource = $this->adapter->getResource('resource1');
        $this->assertInstanceOf('\Zend\Permissions\Acl\Resource\ResourceInterface', $resource);

        $resource = $this->adapter->getResource('resource10');
        $this->assertNull($resource);
    }

    public function testAddPermission()
    {
        $permission = new GenericPermission([
                'role'     => 'role1',
                'resource' => 'resource3',
                'allow'    => 'true'
            ]
        );

        $permission1 = new GenericPermission([
                'role'     => 'role2',
                'resource' => 'resource3',
                'allow'    => 'true'
            ]
        );

        $this->assertInstanceOf('\AclMan\Storage\StorageInterface', $this->adapter->addPermission($permission));
        $this->assertInstanceOf('\AclMan\Storage\StorageInterface', $this->adapter->addPermission($permission1));
    }

    /**
     * @expectedException \AclMan\Storage\Exception\InvalidParameterException
     */
    public function testAddPermissionsException()
    {
        $permission = new GenericPermission([
                'role'     => 'role1',
                'resource' => 'resource3',
                'allow'    => 'true'
            ]
        );

        $list = [$permission, 'test'];

        $this->adapter->addPermissions($list);
    }

    /**
     * @expectedException \AclMan\Storage\Exception\ResourceNotExistException
     */
    public function testAddPermissionResourceException()
    {
        $permission = new GenericPermission([
                'role'     => 'role1',
                'resource' => 'resource30',
                'allow'    => 'true'
            ]
        );

        $this->adapter->addPermission($permission);
    }

    /**
     * @expectedException \AclMan\Storage\Exception\RoleNotExistException
     */
    public function testAddPermissionRoleException()
    {
        $permission = new GenericPermission([
                'role'     => 'role10',
                'resource' => 'resource3',
                'allow'    => 'true'
            ]
        );

        $this->adapter->addPermission($permission);
    }

    public function testGetPermission()
    {
        $resource = new GenericResource('resource2');

        $this->assertCount(1, $this->adapter->getPermissions($resource));
    }

    /**
     * @expectedException \AclMan\Storage\Exception\ResourceNotExistException
     */
    public function testGetPermissionException()
    {
        $resource = new GenericResource('resource22');

        $this->assertCount(1, $this->adapter->getPermissions($resource));
    }
}