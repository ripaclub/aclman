<?php
/**
 * ACL Manager
 *
 * @link        https://github.com/ripaclub/aclman
 * @copyright   Copyright (c) 2014, RipaClub
 * @license     http://opensource.org/licenses/BSD-2-Clause Simplified BSD License
 */
namespace AclMan\Assertion;

use Zend\ServiceManager\AbstractPluginManager;

/**
 * Trait AssertionAwareTrait
 */
trait AssertionAwareTrait
{
    /**
     * @var AbstractPluginManager|null
     */
    protected $pluginManager;

    /**
     * @param AbstractPluginManager $pluginManager
     */
    public function setPluginManager(AbstractPluginManager $pluginManager)
    {
        $this->pluginManager = $pluginManager;
    }

    /**
     * @return AbstractPluginManager|null
     */
    public function getPluginManager()
    {
        return $this->pluginManager;
    }
}
