<?php
namespace AclMan\Assertion;

use Zend\Permissions\Acl\Assertion\AssertionInterface;
use Zend\ServiceManager\AbstractPluginManager;

class AssertionPluginManager extends AbstractPluginManager
{
    /**
     * METHOD
     ******************************************************************************************************************/

    /**
     * Validate the plugin
     *
     * Checks that the filter loaded is either a valid callback or an instance
     * of FilterInterface.
     *
     * @param mixed $plugin
     * @throws \Zend\ServiceManager\Exception\InvalidAssertException
     */
    public function validatePlugin($plugin)
    {
        if ($plugin instanceof AssertionInterface) {
            return;
        }

        throw new Exception\InvalidAssertException(sprintf(
            'Plugin of type %s is invalid; must implement Zend\Permissions\Acl\Assertion\AssertionInterface',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin))
        ));
    }
} 