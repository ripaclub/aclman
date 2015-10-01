<?php
/**
 * ACL Manager
 *
 * @link        https://github.com/ripaclub/aclman
 * @copyright   Copyright (c) 2015, RipaClub
 * @license     http://opensource.org/licenses/BSD-2-Clause Simplified BSD License
 */
namespace AclManTest\Integration\Service;

use AclMan\Service\Service;
use AclMan\Storage\StorageInterface;
use AclManTest\AclManTestCase;
use AclManTest\Integration\Service\TestAsset\Assertion\Assertion1;
use AclManTest\Integration\Service\TestAsset\Assertion\Assertion2;
use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Assertion\AssertionManager;
use Zend\ServiceManager;

/**
 * Class ServiceAbstractTest
 *
 * @group integration
 */
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
                                        'allow' => true,
                                        'privileges' => [
                                            'view'
                                        ]
                                    ]
                                ],
                                'resource2' => [
                                    [
                                        'assert' => 'assertFalse',
                                        'allow' => true,
                                        'privileges' => [
                                            'view'
                                        ]
                                    ]
                                ],
                                'resource3' => [
                                    [
                                        'allow' => true,
                                        'privileges' => ['PUT']
                                    ]
                                ],
                            ],
                        ],
                        'role2' => [
                            'parents' => [
                                'role1'
                            ],
                            'resources' => [
                                'resource1' => [
                                    [
                                        'assert' => null,
                                        'allow' => true,
                                        'privileges' => [
                                            'add',
                                            'view' => [
                                                'allow' => false,
                                            ]
                                        ]
                                    ]
                                ],
                                'resource2' => [
                                    [
                                        'assert' => null,
                                        'allow' => true,
                                        'privileges' => [
                                            'add',
                                            'view' => [
                                                'assert' => 'assertTrue',
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ],
                    ]
                ],
                'AclStorage2' => [
                    'roles' => [
                        StorageInterface::ALL_ROLES => [
                            'resources' => [
                                'resource1' => [
                                    [
                                        'allow' => true,
                                    ]
                                ],
                                'resource2' => [
                                    [
                                        'privileges' => [
                                            'view'
                                        ]
                                    ]
                                ]
                            ]
                        ],
                    ]
                ],
                'AclStorage3' => [
                    'roles' => [
                        'role1' => [
                            'resources' => [
                                StorageInterface::ALL_RESOURCES  => [
                                    [
                                        'allow' => true,
                                    ]
                                ],
                            ]
                        ],
                    ]
                ],
                'AclStorage4' => [
                    'roles' => [
                        StorageInterface::ALL_ROLES => [
                            'resources' => [
                                'resource1' => [],
                                'resource2' => [
                                    [
                                        'privileges' => [
                                            'view'
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        'role1' => [
                            'resource1' => [
                                [
                                    'allow' => false,
                                ]
                            ],
                        ]
                    ]
                ],
                'AclStorageAllRolesAllResources' => [
                    'roles' => [
                        StorageInterface::ALL_ROLES => [
                            'resources' => [
                                StorageInterface::ALL_RESOURCES => [
                                    [
                                        'allow' => true,
                                    ]
                                ]
                            ]
                        ],
                    ]
                ],

                'AclStorageAllRolesAllResourcesWithPrivilege' => [
                    'roles' => [
                        StorageInterface::ALL_ROLES => [
                            'resources' => [
                                StorageInterface::ALL_RESOURCES => [
                                    [
                                        'allow' => true,
                                        'privileges' => [
                                            'view'
                                        ]
                                    ]
                                ]
                            ]
                        ],
                    ]
                ],
            ],
            'aclman_services' => [
                'AclService' => [
                    'storage' => 'AclStorage',
                    'plugin_manager' => 'assertManager',
                ],
                'AclService2' => [
                    'storage' => 'AclStorage2',
                    'plugin_manager' => 'assertManager',
                ],
                'AclService3' => [
                    'storage' => 'AclStorage3',
                    'plugin_manager' => 'assertManager',
                ],
                'AclService4' => [
                    'storage' => 'AclStorage4',
                    'plugin_manager' => 'assertManager',
                ],
                'AclServiceAllRolesAllResources' => [
                    'storage' => 'AclStorageAllRolesAllResources',
                    'plugin_manager' => 'assertManager',
                ],
                'AclServiceAllRolesAllResourcesWithPrivilege' => [
                    'storage' => 'AclStorageAllRolesAllResourcesWithPrivilege',
                    'plugin_manager' => 'assertManager',
                ],
            ],
            'aclman-assertion-manager' => [
                'invokables' => [
                    'assertTrue'  => 'AclManTest\Integration\Service\TestAsset\Assertion\Assertion2',
                    'assertFalse' => 'AclManTest\Integration\Service\TestAsset\Assertion\Assertion1',
                ],
            ],
        ];

        $this->serviceManager = new ServiceManager\ServiceManager(
            new ServiceManagerConfig(
                [
                    'abstract_factories' => [
                        'AclMan\Service\ServiceFactory',
                        'AclMan\Storage\StorageFactory'
                    ],
                    'factories' => [
                        'assertManager' => 'AclMan\Assertion\AssertionManagerFactory'
                    ],
                    'invokables' => [
                        'AclMan\Plugin\Manager' => 'AclManTest\Integration\Service\TestAsset\MockAssertionPluginManager'
                    ],
                ]
            )
        );

        $this->serviceManager->setService('Config', $config);
        $this->serviceManager->setService('PluginManager', new AssertionManager());
    }


    public function testHasService()
    {
        $this->assertTrue($this->serviceManager->has('AclService'));
    }

    public function testIsAllowed()
    {
        $acl = new Acl;
        $acl->addRole('role1');
        $acl->addResource('resource1');
        $acl->allow('role1', 'resource1', 'view');
        $acl->addResource('resource2');
        $acl->addResource('resource3');
        $acl->allow('role1', 'resource2', 'view', new Assertion1);
        $acl->allow('role1', 'resource3', 'PUT');
        $acl->addRole('role2', ['role1']);
        $acl->allow('role2', 'resource1', 'add');
        $acl->deny('role2', 'resource1', 'view');
        $acl->allow('role2', 'resource2', 'add');
        $acl->allow('role2', 'resource2', 'view', new Assertion2);

        /** @var $service Service */
        $service = $this->serviceManager->get('AclService');

        $this->assertSame(
            $acl->isAllowed('role1', 'resource1', 'view'),
            $service->isAllowed('role1', 'resource1', 'view')
        );
        $this->assertSame(
            $acl->isAllowed('role1', 'resource1', 'add'),
            $service->isAllowed('role1', 'resource1', 'add')
        );

        $this->assertSame(
            $acl->isAllowed('role2', 'resource1', 'view'),
            $service->isAllowed('role2', 'resource1', 'view')
        );
        $this->assertSame(
            $acl->isAllowed('role2', 'resource1', 'add'),
            $service->isAllowed('role2', 'resource1', 'add')
        );
        $this->assertSame(
            $acl->isAllowed('role2', 'resource3', 'PUT'),
            $service->isAllowed('role2', 'resource3', 'PUT')
        );
    }

    public function testAllRolesAreAllowed()
    {
        $acl = new Acl;
        $acl->addRole('role1');
        $acl->addRole('role2');
        $acl->addRole('role3');
        $acl->addResource('resource1');


        $acl->allow(null, 'resource1', null);

        $service = $this->serviceManager->get('AclService2');
        $service->loadResource(null, null);

        $this->assertSame(
            $acl->isAllowed('role1', 'resource1'),
            $service->isAllowed('role1', 'resource1')
        );

        $this->assertSame(
            $acl->isAllowed('role2', 'resource1'),
            $service->isAllowed('role2', 'resource1')
        );

        $this->assertSame(
            $acl->isAllowed('role3', 'resource1'),
            $service->isAllowed('role3', 'resource1')
        );

        $this->assertSame(
            $acl->isAllowed('role1', 'resource1', 'add'),
            $service->isAllowed('role1', 'resource1', 'add')
        );


        $this->assertSame(
            $acl->isAllowed('role2', 'resource1', 'add'),
            $service->isAllowed('role2', 'resource1', 'add')
        );

        $this->assertSame(
            $acl->isAllowed('role3', 'resource1', 'add'),
            $service->isAllowed('role3', 'resource1', 'add')
        );
    }

    public function testAllResourceIsAllowed()
    {
        $acl = new Acl();
        $acl->addRole('role1');
        $acl->addResource('resource1');
        $acl->addResource('resource2');
        $acl->addResource('resource3');

        $acl->allow('role1', null, null);

        $service = $this->serviceManager->get('AclService3');

        $this->assertSame(
            $acl->isAllowed('role1', 'resource1'),
            $service->isAllowed('role1', 'resource1')
        );
        $this->assertSame(
            $acl->isAllowed('role1', 'resource2'),
            $service->isAllowed('role1', 'resource2')
        );
        $this->assertSame(
            $acl->isAllowed('role1', 'resource2'),
            $service->isAllowed('role1', 'resource2')
        );

        $this->assertSame(
            $acl->isAllowed('role1', 'resource1', 'add'),
            $service->isAllowed('role1', 'resource1', 'add')
        );
        $this->assertSame(
            $acl->isAllowed('role1', 'resource2', 'add'),
            $service->isAllowed('role1', 'resource2', 'add')
        );
        $this->assertSame(
            $acl->isAllowed('role1', 'resource2', 'add'),
            $service->isAllowed('role1', 'resource2', 'add')
        );
    }

    public function testOverrideIsAllowed()
    {
        $acl = new Acl();
        $acl->addRole('role1');
        $acl->addResource('resource1');
        $acl->addResource('resource2');

        $acl->allow(null, 'resource1', null);
        $acl->allow(null, 'resource2', 'view');
        $acl->deny('role1', 'resource1', null);

        $service = $this->serviceManager->get('AclService4');
        $service->loadResource(null, null);

        $this->assertSame(
            $acl->isAllowed('role1', 'resource1', 'add'),
            $service->isAllowed('role1', 'resource1', 'add')
        );

        $this->assertSame(
            $acl->isAllowed('role1', 'resource2', 'add'),
            $service->isAllowed('role1', 'resource2', 'add')
        );

        $this->assertSame(
            $acl->isAllowed('role1', 'resource2', 'view'),
            $service->isAllowed('role1', 'resource2', 'view')
        );
    }

    public function testAllRolesAllResources()
    {
        $service = $this->serviceManager->get('AclServiceAllRolesAllResources');


        $this->assertTrue($service->isAllowed('role1', 'resource1', 'add'));
        $this->assertTrue($service->isAllowed(null, 'resource1', 'add'));
        $this->assertTrue($service->isAllowed('role1', null, 'add'));
        $this->assertTrue($service->isAllowed('role1', 'resource1', null));
        $this->assertTrue($service->isAllowed(null, null, 'add'));
        $this->assertTrue($service->isAllowed('role1', null, null));
        $this->assertTrue($service->isAllowed(null, null, null));



        $service = $this->serviceManager->get('AclServiceAllRolesAllResourcesWithPrivilege');

        $this->assertTrue($service->isAllowed('role1', 'resource1', 'view'));
        $this->assertTrue($service->isAllowed(null, 'resource1', 'view'));
        $this->assertTrue($service->isAllowed('role1', null, 'view'));
        $this->assertFalse($service->isAllowed('role1', 'resource1', null));
        $this->assertTrue($service->isAllowed(null, null, 'view'));
        $this->assertFalse($service->isAllowed('role1', null, null));
        $this->assertFalse($service->isAllowed(null, null, null));
    }
}
