<?php
/**
 * ACL Manager
 *
 * @link        https://github.com/ripaclub/aclman
 * @copyright   Copyright (c) 2015, RipaClub
 * @license     http://opensource.org/licenses/BSD-2-Clause Simplified BSD License
 */
namespace AclMan\Assertion;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Config;
use Zend\ServiceManager\Factory\FactoryInterface;
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
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $configPM = [];
        if ($container->has('Config')) {
            $config = $container->get('Config');
            $configPM = (isset($config['aclman-assertion-manager'])) ? $config['aclman-assertion-manager'] : [];
        }

        $manager = new AssertionManager($container);
        return $manager->configure($configPM);
    }
}
