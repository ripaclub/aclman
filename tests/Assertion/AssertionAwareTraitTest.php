<?php
/**
 * ACL Manager
 *
 * @link        https://github.com/ripaclub/aclman
 * @copyright   Copyright (c) 2014, RipaClub
 * @license     http://opensource.org/licenses/BSD-2-Clause Simplified BSD License
 */
namespace AclManTest\Assertion;

use AclManTest\AclManTestCase;

/**
 * Class AssertionAwareTraitTest
 */
class AssertionAwareTraitTest extends AclManTestCase
{
    /**
     * @var $mockTrait
     */
    protected $mockTrait;

    public function setUp()
    {
        $this->mockTrait = $this->getMockForTrait('AclMan\Assertion\AssertionAwareTrait');
    }

    public function testRoleAwareTraitGetSet()
    {
        $mock = $this->getMock('AclMan\Assertion\AssertionPluginManager');
        $this->mockTrait->setPluginManager($mock);
        $this->assertSame($mock, $this->mockTrait->getPluginManager());
    }
}
