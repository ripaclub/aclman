<?php
namespace AclManTest\Assertion;

use AclManTest\AclManTestCase;

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