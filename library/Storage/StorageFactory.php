<?php
/**
 * ACL Manager
 *
 * @link        https://github.com/ripaclub/aclman
 * @copyright   Copyright (c) 2015, RipaClub
 * @license     http://opensource.org/licenses/BSD-2-Clause Simplified BSD License
 */
namespace AclMan\Storage;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;

/**
 * Class StorageFactory
 */
class StorageFactory implements AbstractFactoryInterface
{
    /**
     * Config Key
     * @var string
     */
    protected $configKey = 'aclman_storage';

    /**
     * Config
     * @var array
     */
    protected $config;

    /**
     * Default service class name
     *
     * @var string
     */
    protected $serviceName = 'AclMan\Storage\Adapter\ArrayAdapter\ArrayAdapter';

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
            isset($config[$requestedName])
        );
    }

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return mixed
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $this->getConfig($container);

        $configStorage = null;
        if (isset($config[$requestedName]['roles'])) {
            $configStorage['roles'] = $config[$requestedName]['roles'];
        }

        if (isset($config[$requestedName]['type'])) {
            return new $config[$requestedName]['type']($configStorage);
        } else {
            return new $this->serviceName($configStorage);
        }
    }

    /**
     * @param ContainerInterface $container
     * @return array
     */
    protected function getConfig(ContainerInterface $container)
    {
        if ($this->config !== null) {
            return $this->config;
        }

        $config = $container->get('Config');
        if (!isset($config[$this->configKey])
            || !is_array($config[$this->configKey])
        ) {
            $this->config = [];
            return $this->config;
        }

        $this->config = $config[$this->configKey];
        return $this->config;
    }
}
