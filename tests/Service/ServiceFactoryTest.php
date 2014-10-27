<?php
/**
 * ACL Manager
 *
 * @link        https://github.com/ripaclub/aclman
 * @copyright   Copyright (c) 2014, RipaClub
 * @license     http://opensource.org/licenses/BSD-2-Clause Simplified BSD License
 */
namespace AclManTest\Service;

use AclManTest\AclManTestCase;
use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\ServiceManager;

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
            new ServiceManagerConfig(array(
                    'abstract_factories' => array(
                        'AclMan\Service\ServiceFactory',
                    ),
                )
            )
        );

        $this->serviceManager->setService('Config', $config);

        $adapter = $this->getMockBuilder('AclMan\Storage\StorageInterface')->getMock();
        $this->serviceManager->setService('ArrayStorage1', $adapter);

        $pluginManager = $this->getMockBuilder('AclMan\Assertion\AssertionPluginManager')->getMock();
        $this->serviceManager->setService('PluginManager', $pluginManager);
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
            new ServiceManagerConfig(array(
                    'abstract_factories' => array(
                        'AclMan\Service\ServiceFactory',
                    ),
                )
            )
        );

        $this->assertFalse($this->serviceManager->has('AclService'));

        $this->serviceManager = new ServiceManager\ServiceManager(
            new ServiceManagerConfig(array(
                    'abstract_factories' => array(
                        'AclMan\Service\ServiceFactory',
                    ),
                )
            )
        );

        $this->serviceManager->setService('Config', array());

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
