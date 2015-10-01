<?php
/**
 * ACL Manager
 *
 * @link        https://github.com/ripaclub/aclman
 * @copyright   Copyright (c) 2015, RipaClub
 * @license     http://opensource.org/licenses/BSD-2-Clause Simplified BSD License
 */
namespace AclMan\Exception;

use Zend\ServiceManager\Exception\ServiceNotCreatedException as ZendServiceNotCreatedException;

/**
 * Class ServiceNotCreatedException
 */
class ServiceNotCreatedException extends ZendServiceNotCreatedException implements ExceptionInterface
{
}
