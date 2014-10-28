<?php
/**
 * ACL Manager
 *
 * @link        https://github.com/ripaclub/aclman
 * @copyright   Copyright (c) 2014, RipaClub
 * @license     http://opensource.org/licenses/BSD-2-Clause Simplified BSD License
 */
namespace AclManTest\Permission;

use AclMan\Permission\GenericPermission;
use AclManTest\AclManTestCase;
use Zend\Permissions\Acl\Resource\GenericResource;
use Zend\Permissions\Acl\Role\GenericRole;

/**
 * Class GenericPermissionTest
 */
class GenericPermissionTest extends AclManTestCase
{
    protected $permission;

    public function testConstruct()
    {
        $role1 = new GenericRole('role1');
        $resource2 = new GenericResource('resource2');

        $options = [
            'resource' => $resource2,
            'role' => $role1,
            'allow' => true,
            'assert' => 'test'
        ];

        $this->permission = new GenericPermission($options);

        $this->assertSame('role1', $this->permission->getRoleId());
        $this->assertSame('resource2', $this->permission->getResourceId());
        $this->assertTrue($this->permission->isAllow());
        $this->assertSame('test', $this->permission->getAssertion());

        $options = [
            'resource' => 'resource2',
            'role' => 'role1',
            'allow' => false,
            'assert' => 'test',
            'privilege' => 'add'
        ];

        $this->permission = new GenericPermission($options);

        $this->assertSame('role1', $this->permission->getRoleId());
        $this->assertSame('resource2', $this->permission->getResourceId());
        $this->assertFalse($this->permission->isAllow());
        $this->assertSame('test', $this->permission->getAssertion());
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

        $this->permission->setPrivilege('edit');
        $this->assertSame('edit', $this->permission->getPrivilege());
    }
}
