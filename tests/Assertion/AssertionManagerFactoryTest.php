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
use Zend\Mvc\Service\ServiceManagerConfig;
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
        $config = array(
            'factories' => [
                'assertManager' => 'AclMan\Assertion\AssertionManagerFactory',
            ]
        );

        $sm = $this->serviceManager = new ServiceManager\ServiceManager(
            new ServiceManagerConfig($config)
        );

        $sm->setService('Config', $config);
    }

    public function testAssertPluginManager()
    {
        $pluginManager = $this->serviceManager->get('assertManager');
        $this->assertInstanceOf('AclMan\Assertion\AssertionPluginManager', $pluginManager);
    }

    public function testAssetPluginManagerCustom()
    {
        $config = array(
            'factories' => [
                'assertManager' => 'AclMan\Assertion\AssertionManagerFactory',
            ],
            'invokables' => [
                'AclMan\Plugin\Manager' => 'AclManTest\Assertion\TestAsset\MockAssertionPluginManager'
            ]
        );

        $sm = new ServiceManager\ServiceManager(
            new ServiceManagerConfig($config)
        );

        $sm->setService('Config', $config);

        /** @var $pluginManager \AclManTest\Assert\TestAsset\MockAssertionPluginManager */
        $pluginManager = $sm->get('assertManager');
        $this->assertInstanceOf('AclManTest\Assertion\TestAsset\MockAssertionPluginManager', $pluginManager);
    }

}
