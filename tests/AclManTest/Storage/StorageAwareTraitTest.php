<?php
/**
 * Created by visa
 * Date:  8/26/14 12:48 PM
 * Class: StorageAwareTraitTest.php
 */

namespace AclManTest\Storage;

use AclManTest\AclManTestCase;

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
        $this->assertInstanceOf('AclMan\Storage\StorageInterface',  $this->traitObject->getStorage());
    }
} 