<?php
/**
 * ACL Manager
 *
 * @link        https://github.com/ripaclub/aclman
 * @copyright   Copyright (c) 2014, RipaClub
 * @license     http://opensource.org/licenses/BSD-2-Clause Simplified BSD License
 */
namespace AclManTest\Assertion;

use AclManTest\AclManTestCase;
use AclManTest\Assertion\TestAsset\MockAssertionPluginManager;
use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\Permissions\Acl\Assertion\AssertionManager;
use Zend\ServiceManager;

/**
 * Class AssertionManagerFactoryTest
 */
class AssertionManagerFactoryTest extends AclManTestCase
{
    /**
     * @var \Zend\ServiceManager\ServiceManager
     */
    protected $serviceManager;

    public function setUp()
    {
        $config = [
            'factories' => [
                'assertManager' => 'AclMan\Assertion\AssertionManagerFactory',
            ]
        ];

        $sm = $this->serviceManager = new ServiceManager\ServiceManager(
            new ServiceManagerConfig($config)
        );

        $sm->setService('Config', $config);
    }

    public function testAssertPluginManager()
    {
        $pluginManager = $this->serviceManager->get('assertManager');
        $this->assertInstanceOf('Zend\Permissions\Acl\Assertion\AssertionManager', $pluginManager);
    }

    public function testAssetPluginManagerConfig()
    {
        $config = [
            'factories' => [
                'assertManager' => 'AclMan\Assertion\AssertionManagerFactory',
            ],
            'aclman-assertion-manager' => [
                'AclManTest\Assertion\TestAsset\Assertion\MockAssertion1' => 'AclManTest\Assertion\TestAsset\Assertion\MockAssertion1',
                'invokables' => [
                    'assert' => 'AclManTest\Assertion\TestAsset\Assertion\MockAssertion1',
                ]
            ]
        ];

        $sm = new ServiceManager\ServiceManager(
            new ServiceManagerConfig($config)
        );

        $sm->setService('Config', $config);

        /** @var $pluginManager AssertionManager */
        $pluginManager = $sm->get('assertManager');
        $this->assertInstanceOf('Zend\Permissions\Acl\Assertion\AssertionManager', $pluginManager);
        $this->assertInstanceOf(
            'Zend\Permissions\Acl\Assertion\AssertionInterface',
            $pluginManager->get('AclManTest\Assertion\TestAsset\Assertion\MockAssertion1')
        );
        $this->assertInstanceOf(
            'Zend\Permissions\Acl\Assertion\AssertionInterface',
            $pluginManager->get('assert')
        );
    }
}
