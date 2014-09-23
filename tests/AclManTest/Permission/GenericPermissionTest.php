<?php
/**
 * Created by visa
 * Date:  8/26/14 3:38 PM
 * Class: GenericPermissionTest.php
 */

namespace AclManTest\Permission;

use AclMan\Permission\GenericPermission;
use AclManTest\AclManTestCase;
use Zend\Permissions\Acl\Resource\GenericResource;
use Zend\Permissions\Acl\Role\GenericRole;

class GenericPermissionTest extends AclManTestCase
{
    protected $permission;

    public function testConstruct()
    {
        $role1 = new GenericRole('role1');
        $resource2 = new GenericResource('resource2');

        $options = [
            'resource' => $resource2,
            'role'     => $role1,
            'allow'    => true,
            'assert'   => 'test'
        ];

        $this->permission = new GenericPermission($options);

        $this->assertSame('role1', $this->permission->getRoleId());
        $this->assertSame('resource2', $this->permission->getResourceId());
        $this->assertTrue($this->permission->isAllow());
        $this->assertSame('test', $this->permission->getAssertionClass());

        $options = [
            'resource'  => 'resource2',
            'role'      => 'role1',
            'allow'     => false,
            'assert'    => 'test',
            'privilege' => 'add'
        ];

        $this->permission = new GenericPermission($options);

        $this->assertSame('role1', $this->permission->getRoleId());
        $this->assertSame('resource2', $this->permission->getResourceId());
        $this->assertFalse($this->permission->isAllow());
        $this->assertSame('test', $this->permission->getAssertionClass());
        $this->assertSame('add', $this->permission->getPrivilege());

    }

    public function testSetterRoleResource()
    {
        $role1 = new GenericRole('role1');
        $resource2 = new GenericResource('resource2');

        $this->permission = new GenericPermission();

        $this->permission->setResourceId($resource2);
        $this->permission->setRoleId($role1);

        $this->assertSame('role1', $this->permission->getRoleId());
        $this->assertSame('resource2', $this->permission->getResourceId());
    }
    public function testSetterPrivilege()
    {
        $this->permission = new GenericPermission();

        $this->permission ->setPrivilege('edit');
        $this->assertSame('edit',  $this->permission ->getPrivilege());
    }
} 