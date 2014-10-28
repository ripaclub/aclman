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
use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\ServiceManager;

/**
 * Class StorageFactoryTest
 */
class StorageFactoryTest extends AclManTestCase
{
    /**
     * @var \Zend\ServiceManager\ServiceManager
     */
    protected $serviceManager;

    public function setUp()
    {
        $config = [
            'aclman_storage' => [
                'AclStorage' => [
                    'roles' => [
                        'role1' => [

                        ],
                        'role2' => [
                            'parents' => [
                                'role1'
                            ]
                        ]
                    ]
                ],
                'AclStorage1' => [
                    'type' => 'AclManTest\Storage\TestAsset\MockAdapter',
                    'roles' => [
                        'role1' => [

                        ],
                        'role2' => [
                            'parents' => [
                                'role1'
                            ]
                        ]
                    ]
                ]
            ],

        ];

        $this->serviceManager = new ServiceManager\ServiceManager(
            new ServiceManagerConfig([
                'abstract_factories' => [
                    'AclMan\Storage\StorageFactory',
                ],
            ])
        );
        $this->serviceManager->setService('Config', $config);
    }

    public function testHasService()
    {
        $serviceLocator = $this->serviceManager;
        $this->assertTrue($serviceLocator->has('AclStorage'));
        $this->assertTrue($serviceLocator->has('AclStorage1'));
    }

    public function testGetService()
    {
        $serviceLocator = $this->serviceManager;
        $this->assertInstanceOf('AclMan\Storage\StorageInterface', $serviceLocator->get('AclStorage'));
        $this->assertInstanceOf('AclMan\Storage\StorageInterface', $serviceLocator->get('AclStorage1'));
    }

    public function testHasServiceWithoutConfig()
    {
        $this->serviceManager = new ServiceManager\ServiceManager(
            new ServiceManagerConfig([
                'abstract_factories' => [
                    'AclMan\Storage\StorageFactory',
                ],
            ])
        );

        $this->assertFalse($this->serviceManager->has('AclStorage'));

        $this->serviceManager = new ServiceManager\ServiceManager(
            new ServiceManagerConfig([
                'abstract_factories' => [
                    'AclMan\Storage\StorageFactory',
                ],
            ])
        );

        $this->serviceManager->setService('Config', []);

        $this->assertFalse($this->serviceManager->has('AclStorage'));
    }
}