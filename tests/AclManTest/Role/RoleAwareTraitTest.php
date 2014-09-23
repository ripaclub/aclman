<?php
namespace AclManTest\Role;

use AclManTest\AclManTestCase;

class RoleAwareTraitTest extends AclManTestCase
{
    /**
     * @var $mockTrait
     */
    protected $mockTrait;

    public function setUp()
    {
        $this->mockTrait = $this->getMockForTrait('AclMan\Role\RoleAwareTrait');
    }

    public function testRoleAwareTraitGetSet()
    {
        $mock = $this->getMock('Zend\Permissions\Acl\Role\RoleInterface');
        $this->mockTrait->setRole($mock);
        $this->assertSame($mock, $this->mockTrait->getRole());
    }
} 