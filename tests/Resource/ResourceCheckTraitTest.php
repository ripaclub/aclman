<?php
/**
 * ACL Manager
 *
 * @link        https://github.com/ripaclub/aclman
 * @copyright   Copyright (c) 2015, RipaClub
 * @license     http://opensource.org/licenses/BSD-2-Clause Simplified BSD License
 */
namespace AclManTest\Role;

use AclManTest\AclManTestCase;

/**
 * Class RoleAwareTraitTest
 */
class ResourceCheckTraitTest extends AclManTestCase
{
    /**
     * @var $mockTrait
     */
    protected $mockTrait;

    public function setUp()
    {
        $this->mockTrait = $this->getMockForTrait('AclMan\Resource\ResourceCheckTrait');
    }

    public function testcheckResource()
    {
        $refl = new \ReflectionClass($this->mockTrait);
        $reflMethod = $refl->getMethod('checkResource');
        $reflMethod->setAccessible(true);
        $this->assertNull($reflMethod->invoke($this->mockTrait, null));

        $this->assertNull($reflMethod->invoke($this->mockTrait));

        $this->assertInstanceOf(
            'Zend\Permissions\Acl\Resource\ResourceInterface',
            $reflMethod->invoke($this->mockTrait, 'role')
        );
    }

    /**
     * @expectedException \AclMan\Exception\InvalidParameterException
     */
    public function testcheckResourceShouldThrowExceptionWhenInvalidInterface()
    {
        $refl = new \ReflectionClass($this->mockTrait);
        $reflMethod = $refl->getMethod('checkResource');
        $reflMethod->setAccessible(true);

        $reflMethod->invoke($this->mockTrait, new \stdClass());
    }
}
