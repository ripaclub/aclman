ACL manager
===========

| Branch  | Build Status | Coverage | Dependencies |
|:-------------:|:-------------:|:-------------:|:-------------:|
|master|[![Build Status](https://travis-ci.org/ripaclub/aclman.svg?branch=master)](https://travis-ci.org/ripaclub/aclman) |[![Coverage Status](https://coveralls.io/repos/ripaclub/aclman/badge.png?branch=master)](https://coveralls.io/r/ripaclub/aclman)|[![Dependency Status](https://www.versioneye.com/user/projects/544efbb39fc4d5226e0000ec/badge.svg)](https://www.versioneye.com/user/projects/544efbb39fc4d5226e0000ec)|
|develop|[![Build Status](https://travis-ci.org/ripaclub/aclman.svg?branch=develop)](https://travis-ci.org/ripaclub/aclman)|[![Coverage Status](https://coveralls.io/repos/ripaclub/aclman/badge.png?branch=develop)](https://coveralls.io/r/ripaclub/aclman?branch=develop)|[![Dependency Status](https://www.versioneye.com/user/projects/544efb509fc4d5e91300017c/badge.svg)](https://www.versioneye.com/user/projects/544efb509fc4d5e91300017c)|

AclMan is a PHP library designed to manage access control list (ACL).

Features
--------

AclMan has various features:

* Assertions

    It provides an `AssertionPluginManager` whose goal is to deliver the assertions (i.e., `AssertionInterface` objects)

* Permissions

    Contains a class, `GenericPermission`, that is a container of permission options (e.g., a role, a resource, a privilege, an assertion)

* Resources and roles

    It provides a set of traits aimed to check the validity of resources and roles and instantiate their relative classes

* Storages

    AclMan allows you to save the ACL configuration in several layers persistence, via `StorageInterface` objects and adapters (e.g., `ArrayAdapter`)

* Services

    A set of classes aimed at the instantiation of ACL objects

Installation
------------

Add `ripaclub/aclman` to your `composer.json`.

```
{
   "require": {
       "ripaclub/aclman": "v0.1.0"
   }
}
```

Configuration
-------------

AclMan library has only two configuration nodes:

1. `aclman_storage`: to configure the persistence layer in which to save your ACL rules

2. `aclman_services`: to configure your services (e.g., a storage and optionally a plugin manager)

Usage (1)
---------

So, here is an example of use. You first need to configure the factories.

Put this PHP array into your configuration file.

```php
'abstract_factories' => [
    'AclMan\Service\ServiceFactory',
    'AclMan\Storage\StorageFactory'
],
'factories' => [
    'AclMan\Assertion\AssertionManager' => 'AclMan\Assertion\AssertionManagerFactory'
]
```

Then we configure our service.

```php
'aclman_services' => [
    'AclService\Ex1' => [
        'storage' => 'AclStorage\Ex1',
        'plugin_manager' => 'AclMan\Assertion\AssertionManager',
    ],
]
```

Finally, our storage configuration.

```php
'aclman_storage' => [
    'AclStorage\Ex1' => [
        'roles' => [
            'Role1' => [
                'resources' => [
                    'Resource1' => [
                        [
                            'assert' => null,
                            'allow' => true,
                            'privilege' => 'add'
                        ]
                    ],
                    'Resource2' => [
                        [
                            'assert' => null,
                            'allow' => true,
                            'privilege' => 'view'
                        ]
                    ]
                ],
            ],
        ],
    ],
]
```

Our first ACL configuration is now complete. Use it:

```php
$aclService1 = $serviceLocator->get('AclService\Ex1');
$aclService1->isAllowed('Role1', 'Resource1', 'view'); // FALSE
$aclService1->isAllowed('Role1', 'Resource1', 'add'); // TRUE
// ...
```

Usage (2)
---------

Now we see how to modify the previous example in order to use the `AssertionManager`.

We can do it in two ways: (1) create an assertion plugin manager or (2) fetch the provided `AssertionPluginManager` and add our assertions.

We suggest you to create your own assertion plugin manager (1). For example:

```php
namespace Ex1;
class OurAssertionPluginManager extends AbstractPluginManager
{
    protected $invokableClasses = [
        'assertFalse' => 'Ex1\Assertion\Assertion1',
        'assertTrue' => 'Ex1\Assertion\Assertion2',
    ];

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
```

Now we need to register it in the service manager to load our assertion plugin manager.

```php
'invokables' => [
    'AclMan\Plugin\Manager' => 'Ex1\OurAssertionPluginManager'
]
```

Finally we can use our new assertions (see `(*)` in the comments) to configure roles:

```php
'aclman_storage' => [
    'AclStorage\Ex1' => [
        'roles' => [
            'Role1' => [
                'resources' => [
                    'Resource1' => [
                        [
                            'assert' => 'assertTrue', // (*)
                            'allow' => true,
                            'privilege' => 'add'
                        ]
                    ],
                    'Resource2' => [
                        [
                            'assert' => 'assertFalse', // (*)
                            'allow' => true,
                            'privilege' => 'view'
                        ]
                    ]
                ],
            ],
        ],
    ],
]
```


```php
$aclService1 = $serviceLocator->get('AclService\Ex1');
$aclService1->isAllowed('Role1', 'Resource1', 'add'); // TRUE
$aclService1->isAllowed('Role1', 'Resource2', 'view'); // FALSE
// ...
```

---

[![Analytics](https://ga-beacon.appspot.com/UA-49655829-1/ripaclub/aclman)](https://github.com/igrigorik/ga-beacon)
