<?php
namespace AclManTest\Assertion;

use AclMan\Assertion\AssertionPluginManager;
use AclManTest\AclManTestCase;

class AssertionPluginManagerTest extends AclManTestCase
{
    protected $manager;

    public function setUp()
    {
        $this->manager = new AssertionPluginManager();
        $this->manager->setInvokableClass('assert1', 'AclManTest\Assertion\TestAsset\Assertion\MockAssertion1');
        $this->manager->setInvokableClass('assert2', 'AclManTest\Assertion\TestAsset\Assertion\MockWrongAssertion');
    }

    public function testAssertionPluginManager()
    {
        $this->assertInstanceOf('AclManTest\Assertion\TestAsset\Assertion\MockAssertion1', $this->manager->get('assert1'));
    }

    /**
     * @expectedException \AclMan\Assertion\Exception\InvalidAssertException
     */
    public function testAssertionPluginManagerException()
    {
        $this->manager->get('assert2');
    }
} 