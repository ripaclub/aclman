<?php
/**
 * ACL Manager
 *
 * @link        https://github.com/ripaclub/aclman
 * @copyright   Copyright (c) 2014, RipaClub
 * @license     http://opensource.org/licenses/BSD-2-Clause Simplified BSD License
 */
namespace AclManTest\Integration\Service\TestAsset;

use Zend\Permissions\Acl\Assertion\AssertionInterface;
use Zend\ServiceManager\AbstractPluginManager;

/**
 * Class MockAssertionPluginManager
 */
class MockAssertionPluginManager extends AbstractPluginManager
{
    /**
     * Default set of helpers
     *
     * @var array
     */
    protected $invokableClasses = [
        'assertFalse' => 'AclManTest\Integration\Service\TestAsset\Assertion\Assertion1',
        'assertTrue' => 'AclManTest\Integration\Service\TestAsset\Assertion\Assertion2',
    ];

    /**
     * {@inheritdoc}
     */
    public function validatePlugin($plugin)
    {
        if ($plugin instanceof AssertionInterface) {
            return;
        }

        throw new \Exception(sprintf(
            'Plugin of type "%s" is invalid; must implement Zend\Permissions\Acl\Assertion\AssertionInterface',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin))
        ));
    }
}
