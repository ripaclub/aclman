<?php
namespace AclMan\Assertion;

use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Assertion\AssertionAggregate as BaseAssertionAggregate;
use Zend\Permissions\Acl\Assertion\AssertionInterface;
use Zend\Permissions\Acl\Exception\RuntimeException;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use Zend\Permissions\Acl\Role\RoleInterface;

/**
 * Class AssertionAggregate
 */
class AssertionAggregate extends BaseAssertionAggregate
{
    public function assert(Acl $acl, RoleInterface $role = null, ResourceInterface $resource = null, $privilege = null)
    {
        // check if assertions are set
        if (!$this->assertions) {
            throw new RuntimeException('No assertion have been aggregated to this AssertionAggregate');
        }

        foreach ($this->assertions as $key => $assertions) {
            switch (true) {
                case is_array($assertions):
                    if (!$this->getAssertionManager()) {
                        throw new RuntimeException('No assertion manager is set - cannot look up for assertions');
                    }

                    $name = $assertions;
                    $option = [];

                    if (!isset($assertions['name'])) {
                        throw new RuntimeException('Name not set in the assertion');
                    }

                    $name = $assertions['name'];
                    unset($assertions['name']);
                    $option = $assertions;

                    $assertion = $this->getAssertionManager()->get($name, $option);
                    break;
                case is_string($assertions) && class_exists($assertions):
                    $assertion = new $assertions();
                    if (!($assertion instanceof AssertionInterface)) {
                        throw new RuntimeException(sprintf('Instace of %s is not an instance of Zend\Permissions\Acl\Assertion\AssertionInterface', get_class($assertion)));
                    }
                    break;
                case is_string($assertions):
                    if (!$this->getAssertionManager()) {
                        throw new RuntimeException('No assertion manager is set - cannot look up for assertions');
                    }
                    if ($this->getAssertionManager()->has($assertions)) {
                        $assertion = $this->getAssertionManager()->get($assertions);
                    } else {
                        throw new RuntimeException(sprintf(' Noi instace of %s found', $assertions));
                    }
                    break;
                default:
                    throw new RuntimeException('Invalid params in aggregate Assertion');
            }

            $result = (bool)$assertion->assert($acl, $role, $resource, $privilege);
            if ($this->getMode() == self::MODE_ALL && !$result) {
                // on false is enough
                return false;
            }

            if ($this->getMode() == self::MODE_AT_LEAST_ONE && $result) {
                // one true is enough
                return true;
            }
        }

        if ($this->getMode() == self::MODE_ALL) {
            // none of the assertions returned false
            return true;
        } else {
            return false;
        }
    }
}