<?php
/**
 * ACL Manager
 *
 * @link        https://github.com/ripaclub/aclman
 * @copyright   Copyright (c) 2014, RipaClub
 * @license     http://opensource.org/licenses/BSD-2-Clause Simplified BSD License
 */
namespace AclManTest\Acl;

use AclMan\Acl\AclAwareTrait;
use AclManTest\AclManTestCase;

/**
 * Class AclAwareTraitTest
 */
class AclAwareTraitTest extends AclManTestCase
{
    protected $traitObject;

    public function setUp()
    {
        $this->traitObject = $this->getObjectForTrait('AclMan\Acl\AclAwareTrait');
    }

    public function testStorageAwareTrait()
    {
        $this->traitObject->setAcl($this->getMockBuilder('Zend\Permissions\Acl\Acl')->getMock());
        $this->assertInstanceOf('Zend\Permissions\Acl\AclInterface', $this->traitObject->getAcl());
    }
}
