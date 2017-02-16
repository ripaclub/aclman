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
use Interop\Container\ContainerInterface;
use Zend\Permissions\Acl\Acl;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;
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
     * @param ContainerInterface $container
     * @param string $requestedName
     * @return bool
     */
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        $config = $this->getConfig($container);
        if (empty($config)) {
            return false;
        }

        return (
            isset($config[$requestedName])  &&
            !empty($config[$requestedName]) &&
            // Check Storage
            isset($config[$requestedName]['storage']) &&
            is_string($config[$requestedName]['storage']) &&
            $container->has($config[$requestedName]['storage']) &&
            // Check Storage
            isset($config[$requestedName]['plugin_manager']) &&
            is_string($config[$requestedName]['plugin_manager']) &&
            $container->has($config[$requestedName]['plugin_manager'])
        );
    }

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return Service
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $this->getConfig($container)[$requestedName];
        $service = new $this->serviceName();

        // Storage
        /** @var $storage StorageInterface */
        $storage = $container->get($config['storage']);
        if (!$storage instanceof StorageInterface) {
            throw new ServiceNotCreatedException(sprintf(
                '"%s" expectes a AclMan\Storage\StorageInterface is set in the config; received "%s"',
                __METHOD__,
                is_object($storage) ? get_class($storage) : gettype($storage)
            ));
        }
        // PluginManager
        $pluginManager = $container->get($config['plugin_manager']);

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
     * @param ContainerInterface $container,
     * @return array
     */
    protected function getConfig(ContainerInterface $container)
    {
        if ($this->config !== null) {
            return $this->config;
        }

        $config = $container->get('Config');
        if (!isset($config[$this->configKey]) || !is_array($config[$this->configKey])) {
            $this->config = [];
            return $this->config;
        }

        $this->config = $config[$this->configKey];
        return $this->config;
    }
}
