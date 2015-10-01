<?php
/**
 * ACL Manager
 *
 * @link        https://github.com/ripaclub/aclman
 * @copyright   Copyright (c) 2015, RipaClub
 * @license     http://opensource.org/licenses/BSD-2-Clause Simplified BSD License
 */
namespace AclMan\Service;

use AclMan\Exception\ServiceNotCreatedException;
use AclMan\Storage\StorageInterface;
use Zend\Permissions\Acl\Acl;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class ServiceFactory
 */
class ServiceFactory implements AbstractFactoryInterface
{
    /**
     * Config Key
     * @var string
     */
    protected $configKey = 'aclman_services';

    /**
     * Default service class name
     *
     * @var string
     */
    protected $serviceName = 'AclMan\Service\Service';

    /**
     * Config
     * @var array
     */
    protected $config;

    /**
     * Determine if we can create a service with name
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param $name
     * @param $requestedName
     * @return bool
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $config = $this->getConfig($serviceLocator);
        if (empty($config)) {
            return false;
        }

        return (
            isset($config[$requestedName])  &&
            !empty($config[$requestedName]) &&
            // Check Storage
            isset($config[$requestedName]['storage']) &&
            is_string($config[$requestedName]['storage']) &&
            $serviceLocator->has($config[$requestedName]['storage']) &&
            // Check Storage
            isset($config[$requestedName]['plugin_manager']) &&
            is_string($config[$requestedName]['plugin_manager']) &&
            $serviceLocator->has($config[$requestedName]['plugin_manager'])
        );
    }

    /**
     * Create service with name
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param $name
     * @param $requestedName
     * @return StorageInterface
     * @throws ServiceNotCreatedException
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $config = $this->getConfig($serviceLocator)[$requestedName];
        $service = new $this->serviceName();

        // Storage
        /** @var $storage StorageInterface */
        $storage = $serviceLocator->get($config['storage']);
        if (!$storage instanceof StorageInterface) {
            throw new ServiceNotCreatedException(sprintf(
                '"%s" expectes a AclMan\Storage\StorageInterface is set in the config; received "%s"',
                __METHOD__,
                is_object($storage) ? get_class($storage) : gettype($storage)
            ));
        }
        // PluginManager
        $pluginManager = $serviceLocator->get($config['plugin_manager']);

        // Config Service
        $acl = new Acl();
        /* @var Service $service */
        $service->setStorage($storage);
        $service->setAcl($acl);
        $service->setPluginManager($pluginManager);

        if (isset($config['allow_not_found_resource'])) {
            $service->setAllowNotFoundResource($config['allow_not_found_resource']);
        }

        return $service;
    }

    /**
     * Get model configuration, if any
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return array
     */
    protected function getConfig(ServiceLocatorInterface $serviceLocator)
    {
        if ($this->config !== null) {
            return $this->config;
        }

        if (!$serviceLocator->has('Config')) {
            $this->config = [];
            return $this->config;
        }

        $config = $serviceLocator->get('Config');
        if (!isset($config[$this->configKey]) || !is_array($config[$this->configKey])) {
            $this->config = [];
            return $this->config;
        }

        $this->config = $config[$this->configKey];
        return $this->config;
    }
}
