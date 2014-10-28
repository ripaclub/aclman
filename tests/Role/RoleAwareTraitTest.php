<?php
/**
 * ACL Manager
 *
 * @link        https://github.com/ripaclub/aclman
 * @copyright   Copyright (c) 2014, RipaClub
 * @license     http://opensource.org/licenses/BSD-2-Clause Simplified BSD License
 */
namespace AclManTest\Role;

use AclManTest\AclManTestCase;

/**
 * Class RoleAwareTraitTest
 */
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
