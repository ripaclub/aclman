<?php
/**
 * ACL Manager
 *
 * @link        https://github.com/ripaclub/aclman
 * @copyright   Copyright (c) 2014, RipaClub
 * @license     http://opensource.org/licenses/BSD-2-Clause Simplified BSD License
 */
namespace AclMan\Assertion;

use Zend\Permissions\Acl\Assertion\AssertionManager;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\Config;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class AssertionManagerFactory
 */
class AssertionManagerFactory implements FactoryInterface
{
    /**
     * Config Key
     *
     * @var string
     */
    protected $configKey = 'aclman-assertion-manager';

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return AssertionManager
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');
        $configManager = (isset($config['aclman-assertion-manager'])) ? new Config($config['aclman-assertion-manager']) : null;
        return new AssertionManager($configManager);
    }
}
