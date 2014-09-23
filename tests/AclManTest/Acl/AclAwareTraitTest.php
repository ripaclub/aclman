<?php
/**
 * Created by visa
 * Date:  8/26/14 3:16 PM
 * Class: AclAwareTraitTest.php
 */

namespace AclManTest\Acl;

use AclManTest\AclManTestCase;

class AclAwareTraitTest extends AclManTestCase
{
    protected $traitObject;

    public function setUp()
    {
        $this->traitObject = $this->getObjectForTrait('AclMan\Acl\AclAwareTrait');
    }

    public function testStorageAwareTrait()
    {
        $this->traitObject->setAcl(
            $this->getMockBuilder('Zend\Permissions\Acl\Acl')
                ->getMock()
        );
        $this->assertInstanceOf('Zend\Permissions\Acl\AclInterface',  $this->traitObject->getAcl());
    }
} 