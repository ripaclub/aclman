<?php
/**
 * Created by visa
 * Date:  8/26/14 11:37 AM
 * Class: ArrayAdapterAbstractServiceFactoryTest.php
 */

namespace AclManTest\Storage\Adapter\ArrayAdapter;

use AclManTest\AclManTestCase;
use Zend\Permissions\Acl\Role\GenericRole;
use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\ServiceManager;

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
                        new GenericRole('role1'),
                        new GenericRole('role2'),
                        new GenericRole('role3')
                    ]
                ],
                'ArrayStorage1' => []
            ],
        ];

        $this->serviceManager = new ServiceManager\ServiceManager(
            new ServiceManagerConfig(array(
                    'abstract_factories' => array(
                        'AclMan\Storage\Adapter\ArrayAdapter\ArrayAdapterAbstractServiceFactory',
                    ),
                )
            )
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
            new ServiceManagerConfig(array(
                    'abstract_factories' => array(
                        'AclMan\Storage\Adapter\ArrayAdapter\ArrayAdapterAbstractServiceFactory',
                    ),
                )
            )
        );

        $this->assertFalse($this->serviceManager->has('ArrayStorage1'));

        $this->serviceManager = new ServiceManager\ServiceManager(
            new ServiceManagerConfig(array(
                    'abstract_factories' => array(
                        'AclMan\Storage\Adapter\ArrayAdapter\ArrayAdapterAbstractServiceFactory',
                    ),
                )
            )
        );

        $this->serviceManager->setService('Config', array());

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