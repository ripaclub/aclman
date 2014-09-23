<?php
namespace AclMan\Service;

use Zend\Permissions\Acl\Acl;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ServiceFactory implements AbstractFactoryInterface
{

    /**
     * Config Key
     * @var string
     */
    protected $configKey = 'alcManServices';

    /**
     * Default service class name
     *
     * @var string
     */
    protected $serviceName = 'AclMan\Service\ServiceImplement';

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
            isset($config[$requestedName]['pluginManager']) &&
            is_string($config[$requestedName]['pluginManager']) &&
            $serviceLocator->has($config[$requestedName]['pluginManager'])
        );
    }

    /**
     * Create service with name
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param $name
     * @param $requestedName
     * @return StorageInterface
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $config = $this->getConfig($serviceLocator)[$requestedName];
        $service = new $this->serviceName();

        // Storage
        $storage = $serviceLocator->get($config['storage']);
        // PluginManager
        $pluginManager = $serviceLocator->get($config['pluginManager']);

        // Config Service
        $acl = new Acl();
        /* @var ServiceImplement $service */
        $service->setStorage($storage);
        $service->setAcl($acl);
        $service->setPluginManager($pluginManager);

        if(isset($config['allowNotFoundResource'])) {
            $service->setAllowNotFoundResource($config['allowNotFoundResource']);
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
            $this->config = array();
            return $this->config;
        }

        $config = $serviceLocator->get('Config');
        if (!isset($config[$this->configKey])
            || !is_array($config[$this->configKey])
        ) {
            $this->config = array();
            return $this->config;
        }

        $this->config = $config[$this->configKey];
        return $this->config;
    }
} 