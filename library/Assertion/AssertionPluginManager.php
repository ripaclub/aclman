<?php
/**
 * ACL Manager
 *
 * @link        https://github.com/ripaclub/aclman
 * @copyright   Copyright (c) 2014, RipaClub
 * @license     http://opensource.org/licenses/BSD-2-Clause Simplified BSD License
 */
namespace AclMan\Assertion;

use AlcMan\Exception\InvalidAssertException;
use Zend\Permissions\Acl\Assertion\AssertionInterface;
use Zend\ServiceManager\AbstractPluginManager;

/**
 * Class AssertionPluginManager
 */
class AssertionPluginManager extends AbstractPluginManager
{
    /**
     * Validate the plugin
     *
     * Checks that the filter loaded is either a valid callback or an instance
     * of FilterInterface.
     *
     * @param mixed $plugin
     * @throws InvalidAssertException
     */
    public function validatePlugin($plugin)
    {
        if ($plugin instanceof AssertionInterface) {
            return;
        }

        throw new InvalidAssertException(sprintf(
            'Plugin of type %s is invalid; must implement Zend\Permissions\Acl\Assertion\AssertionInterface',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin))
        ));
    }
}
