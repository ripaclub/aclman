<?php
/**
 * ACL Manager
 *
 * @link        https://github.com/ripaclub/aclman
 * @copyright   Copyright (c) 2015, RipaClub
 * @license     http://opensource.org/licenses/BSD-2-Clause Simplified BSD License
 */
namespace AclMan\Storage\Adapter\ArrayAdapter;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class ArrayAdapterAbstractServiceFactory
 */
class ArrayAdapterAbstractServiceFactory implements AbstractFactoryInterface
{
    /**
     * @var string
     */
    protected $configKey = 'aclManArrayAdapterStorage'; // FIXME: change config node name (0.3.x)

    /**
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
            isset($config[$requestedName])
            && is_array($config[$requestedName])
            && !empty($config[$requestedName])
            && isset($config[$requestedName]['roles'])
            && is_array($config[$requestedName]['roles'])
        );
    }

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return ArrayAdapter
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $this->getConfig($container)[$requestedName];
        return new ArrayAdapter($config);
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
        if (!isset($config[$this->configKey]) || !is_array($config[$this->configKey])) {
            $this->config = [];
            return $this->config;
        }

        $this->config = $config[$this->configKey];
        return $this->config;
    }
}
