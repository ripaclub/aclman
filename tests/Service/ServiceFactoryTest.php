<?php
/**
 * ACL Manager
 *
 * @link        https://github.com/ripaclub/aclman
 * @copyright   Copyright (c) 2014, RipaClub
 * @license     http://opensource.org/licenses/BSD-2-Clause Simplified BSD License
 */
namespace AclManTest\Service;

use AclMan\Service\ServiceFactory;
use AclManTest\AclManTestCase;
use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\ServiceManager;

/**
 * Class ServiceFactoryTest
 */
class ServiceFactoryTest extends AclManTestCase
{
    /**
     * @var \Zend\ServiceManager\ServiceManager
     */
    protected $serviceManager;

    public function setUp()
    {
        $config = [
            'alcManServices' => [
                'AclService' => [
                    'storage' => 'ArrayStorage1',
                    'pluginManager' => 'PluginManager',
                    'allowNotFoundResource' => true
                ],
                'AclService1' => [],
            ],
        ];
        $this->serviceManager = new ServiceManager\ServiceManager(
            new ServiceManagerConfig(
                [
                    'abstract_factories' => [
                        'AclMan\Service\ServiceFactory',
                    ],
                ]
            )
        );

        $this->serviceManager->setService('Config', $config);
        $adapter = $this->getMockBuilder('AclMan\Storage\StorageInterface')->getMock();
        $this->serviceManager->setService('ArrayStorage1', $adapter);
        $pluginManager = $this->getMockBuilder('AclMan\Assertion\AssertionPluginManager')->getMock();
        $this->serviceManager->setService('PluginManager', $pluginManager);
    }

    public function testCreateServiceShouldThrowServiceNotCreatedExceptionWhenStorageIsInvalid()
    {
        $sm = new ServiceManager\ServiceManager(
            new ServiceManagerConfig(
                [
                    'abstract_factories' => [
                        'AclMan\Service\ServiceFactory',
                    ],
                ]
            )
        );
        $sm->setService(
            'Config',
            [
                'alcManServices' => [
                    'AclService' => [
                        'storage' => 'InvalidStorage',
                        'pluginManager' => 'PluginManager',
                        'allowNotFoundResource' => true
                    ],
                    'AclService1' => [],
                ]
            ]
        );
        $sm->setService('PluginManager', $this->getMockBuilder('AclMan\Assertion\AssertionPluginManager')->getMock());
        $sm->setService('InvalidStorage', $this->getMockBuilder('\ArrayObject')->getMock());
        $sf = new ServiceFactory();
        $this->assertTrue($sf->canCreateServiceWithName($sm, null, 'AclService'));
        $this->setExpectedException('\AclMan\Exception\ServiceNotCreatedException');
        $sf->createServiceWithName($sm, null, 'AclService');
    }

    public function testHasService()
    {
        $serviceLocator = $this->serviceManager;
        $this->assertTrue($serviceLocator->has('AclService'));
        $this->assertFalse($serviceLocator->has('AclService1'));
    }

    public function testHasServiceWithoutConfig()
    {
        $this->serviceManager = new ServiceManager\ServiceManager(
            new ServiceManagerConfig(
                [
                    'abstract_factories' => [
                        'AclMan\Service\ServiceFactory',
                    ],
                ]
            )
        );

        $this->assertFalse($this->serviceManager->has('AclService'));

        $this->serviceManager = new ServiceManager\ServiceManager(
            new ServiceManagerConfig(
                [
                    'abstract_factories' => [
                        'AclMan\Service\ServiceFactory',
                    ],
                ]
            )
        );

        $this->serviceManager->setService('Config', []);

        $this->assertFalse($this->serviceManager->has('AclService'));
    }

    /**
     * @depends testHasService
     */
    public function testGetService()
    {
        $serviceLocator = $this->serviceManager;
        $this->assertInstanceOf('AclMan\Service\ServiceInterface', $serviceLocator->get('AclService'));
    }
}
