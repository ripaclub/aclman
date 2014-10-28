<?php
/**
 * ACL Manager
 *
 * @link        https://github.com/ripaclub/aclman
 * @copyright   Copyright (c) 2014, RipaClub
 * @license     http://opensource.org/licenses/BSD-2-Clause Simplified BSD License
 */
namespace AclManTest\Storage\Adapter\ArrayAdapter;

use AclManTest\AclManTestCase;
use Zend\Permissions\Acl\Role\GenericRole;
use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\ServiceManager;

/**
 * Class ArrayAdapterAbstractServiceFactoryTest
 */
class ArrayAdapterAbstractServiceFactoryTest extends AclManTestCase
{
    /**
     * @var \Zend\ServiceManager\ServiceManager
     */
    protected $serviceManager;

    public function setUp()
    {

        $config = [
            'aclManArrayAdapterStorage' => [
                'ArrayStorage' => [
                    'roles' => [

                    ]
                ],
                'ArrayStorage1' => []
            ],
        ];

        $this->serviceManager = new ServiceManager\ServiceManager(
            new ServiceManagerConfig([
                'abstract_factories' => [
                    'AclMan\Storage\Adapter\ArrayAdapter\ArrayAdapterAbstractServiceFactory',
                ],
            ])
        );

        $this->serviceManager->setService('Config', $config);
    }

    public function testHasService()
    {
        $serviceLocator = $this->serviceManager;

        $this->assertTrue($serviceLocator->has('ArrayStorage'));
        $this->assertFalse($serviceLocator->has('ArrayStorage1'));
    }

    public function testHasServiceWithoutConfig()
    {
        $this->serviceManager = new ServiceManager\ServiceManager(
            new ServiceManagerConfig([
                'abstract_factories' => [
                    'AclMan\Storage\Adapter\ArrayAdapter\ArrayAdapterAbstractServiceFactory',
                ],
            ])
        );
        $this->assertFalse($this->serviceManager->has('ArrayStorage1'));

        $this->serviceManager = new ServiceManager\ServiceManager(
            new ServiceManagerConfig([
                'abstract_factories' => [
                    'AclMan\Storage\Adapter\ArrayAdapter\ArrayAdapterAbstractServiceFactory',
                ],
            ])
        );
        $this->serviceManager->setService('Config', []);
        $this->assertFalse($this->serviceManager->has('ArrayStorage1'));
    }

    /**
     * @depends testHasService
     */
    public function testGetService()
    {
        $serviceLocator = $this->serviceManager;
        $this->assertInstanceOf('AclMan\Storage\StorageInterface', $serviceLocator->get('ArrayStorage'));
    }
}
