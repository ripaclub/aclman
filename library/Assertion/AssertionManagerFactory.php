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
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class AssertionManagerFactory
 */
class AssertionManagerFactory implements FactoryInterface
{
    const PLUGIN_MANAGER_CLASS   = 'AclMan\Assertion\AssertionPluginManager';
    const PLUGIN_MANAGER_SERVICE = 'AclMan\Plugin\Manager';

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return AssertionManager
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /* @var $plugins AbstractPluginManager */
        if ($serviceLocator->has(self::PLUGIN_MANAGER_SERVICE)) {
            $plugins = $serviceLocator->get(self::PLUGIN_MANAGER_SERVICE);
        } else {
            $pluginManagerClass = static::PLUGIN_MANAGER_CLASS;
            $plugins = new $pluginManagerClass;
        }
        $plugins->setServiceLocator($serviceLocator);

        return $plugins;
    }
}
