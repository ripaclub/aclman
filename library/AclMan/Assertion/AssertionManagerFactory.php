<?php
namespace AclMan\Assertion;

use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AssertionManagerFactory implements FactoryInterface
{
    /**
     * CONST
     ******************************************************************************************************************/

    const PLUGIN_MANAGER_CLASS   = 'AclMan\Assertion\AssertionPluginManager';
    const PLUGIN_MANAGER_SERVICE = 'AclMan\Plugin\Manager';

    /**
     * METHOD
     ******************************************************************************************************************/

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return OperationPluginManager
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /* @var $plugins \Zend\ServiceManager\AbstractPluginManager */
        if($serviceLocator->has(self::PLUGIN_MANAGER_SERVICE)) {
            $plugins = $serviceLocator->get(self::PLUGIN_MANAGER_SERVICE);
        } else {
            $pluginManagerClass = static::PLUGIN_MANAGER_CLASS;
            $plugins = new $pluginManagerClass;
        }
        $plugins->setServiceLocator($serviceLocator);
        return $plugins;
    }
} 