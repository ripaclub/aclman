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
use Zend\Permissions\Acl\Assertion\AssertionManager;

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
        $assertionManager = new AssertionManager();
        $this->mockTrait->setPluginManager($assertionManager);
        $this->assertSame($assertionManager, $this->mockTrait->getPluginManager());
    }
}
