<?php
namespace AclManTest\Integration\Service;

use AclMan\Assertion\AssertionPluginManager;
use AclMan\Service\ServiceImplement;
use AclManTest\AclManTestCase;
use AclMan\Storage\Adapter\ArrayAdapter\ArrayAdapter;
use AclManTest\Integration\Service\TestAsset\Assertion\Assertion1;
use AclManTest\Integration\Service\TestAsset\Assertion\Assertion2;
use Zend\Permissions\Acl\Acl;
use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\ServiceManager;


class ServiceAbstractTest extends AclManTestCase
{
    /**
     * @var $service ServiceManager\ServiceManager
     */
    protected $serviceManager;

    public function setUp()
    {
        $config = [
            'aclman_storage' => [
                'AclStorage' => [
                    'roles' => [
                        'role1' => [
                            'resources' => [
                                'resource1' => [
                                    [
                                        'assert' => null,
                                        'allow' =>  true,
                                        'privilege' => 'view'
                                    ]
                                ],
                                'resource2' => [
                                    [
                                        'assert' => 'assertFalse',
                                        'allow' =>  true,
                                        'privilege' => 'view'
                                    ]
                                ]
                            ]
                        ],
                        'role2' => [
                            'parents' => [
                                'role1'
                            ],
                            'resources' => [
                                'resource1' => [
                                    [
                                        'assert' => null,
                                        'allow' =>  true,
                                        'privilege' => 'add'
                                    ]
                                ],
                                'resource2' => [
                                    [
                                        'assert' => null,
                                        'allow' =>  true,
                                        'privilege' => 'add'
                                    ],
                                    [
                                        'assert' => 'assertTrue',
                                        'allow' =>  true,
                                        'privilege' => 'view'
                                    ]

                                ]
                            ]
                        ],
                    ]
                ]
            ],
            'alcManServices' => [
                'AclService' => [
                    'storage' => 'AclStorage',
                    'pluginManager' => 'assertManager',
                ],
            ]
        ];

        $this->serviceManager = new ServiceManager\ServiceManager(
            new ServiceManagerConfig([
                    'abstract_factories' => [
                        'AclMan\Service\ServiceFactory',
                        'AclMan\Storage\StorageFactory'
                    ],
                    'factories' => [
                        'assertManager' => 'AclMan\Assertion\AssertionManagerFactory'
                    ],
                    'invokables' => [
                        'AclMan\Plugin\Manager' => 'AclManTest\Integration\Service\TestAsset\MockAssertionPluginManager'
                    ]
                ]
            )
        );

        $this->serviceManager->setService('Config', $config);
        $this->serviceManager->setService('PluginManager', new AssertionPluginManager());
    }



    public function testHasService()
    {
       $this->assertTrue($this->serviceManager->has('AclService'));
    }

    /**
     * @depends testHasService
     */
    public function testHasRole()
    {
        /** @var $service ServiceImplement */
        $service = $this->serviceManager->get('AclService');
        $this->assertFalse($service->hasRole('role1'));

        $service->init();
        $this->assertTrue($service->hasRole('role1'));
    }

    public function testIsAllowed()
    {
        $acl = new Acl();
        $acl->addRole('role1');
        $acl->addResource('resource1');
        $acl->allow('role1', 'resource1', 'view');
        $acl->addResource('resource2');
        $acl->allow('role1', 'resource2', 'view', new Assertion1());
        $acl->addRole('role2', ['role1']);
        $acl->allow('role2', 'resource1', 'add');
        $acl->allow('role2', 'resource2', 'add');
        $acl->allow('role2', 'resource2', 'view', new Assertion2());

        /** @var $service ServiceImplement */
        $service = $this->serviceManager->get('AclService');
        $service->init();

        $this->assertSame($acl->isAllowed('role1', 'resource1', 'view'), $service->isAllowed('role1', 'resource1', 'view'));
        $this->assertSame($acl->isAllowed('role1', 'resource1', 'add'), $service->isAllowed('role1', 'resource1', 'add'));

        $this->assertSame($acl->isAllowed('role2', 'resource1', 'view'), $service->isAllowed('role2', 'resource1', 'view'));
        $this->assertSame($acl->isAllowed('role2', 'resource1', 'add'), $service->isAllowed('role2', 'resource1', 'add'));
    }
} 