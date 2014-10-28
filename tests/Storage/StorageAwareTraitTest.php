<?php
/**
 * ACL Manager
 *
 * @link        https://github.com/ripaclub/aclman
 * @copyright   Copyright (c) 2014, RipaClub
 * @license     http://opensource.org/licenses/BSD-2-Clause Simplified BSD License
 */
namespace AclManTest\Storage;

use AclManTest\AclManTestCase;

/**
 * Class StorageAwareTraitTest
 */
class StorageAwareTraitTest extends AclManTestCase
{
    protected $traitObject;

    public function setUp()
    {
        $this->traitObject = $this->getObjectForTrait('AclMan\Storage\StorageAwareTrait');
    }

    public function testStorageAwareTrait()
    {
        $this->traitObject->setStorage(
            $this->getMockBuilder('AclMan\Storage\StorageInterface')
                ->getMock()
        );
        $this->assertInstanceOf('AclMan\Storage\StorageInterface', $this->traitObject->getStorage());
    }
}
