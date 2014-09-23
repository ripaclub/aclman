<?php
namespace AclMan\Assertion;

use Zend\ServiceManager\AbstractPluginManager;

trait AssertionAwareTrait
{
    /**
     * ATTRIBUTE
     ******************************************************************************************************************/

    /**
     * @var AbstractPluginManager
     */
    protected $pluginManager;


    /**
     * METHOD
     ******************************************************************************************************************/

    /**
     * @param mixed $pluginManager
     */
    public function setPluginManager(AbstractPluginManager $pluginManager)
    {
        $this->pluginManager = $pluginManager;
    }

    /**
     * @return mixed
     */
    public function getPluginManager()
    {
        return $this->pluginManager;
    }
}