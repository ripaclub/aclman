ACL manager
============

> AclMan is a PHP library designed to manage access control list (ACL).

[![Latest Stable Version](https://img.shields.io/packagist/v/ripaclub/aclman.svg?style=flat-square)](https://packagist.org/packages/ripaclub/aclman) [![Build Status](https://img.shields.io/travis/ripaclub/aclman/master.svg?style=flat-square)](https://travis-ci.org/ripaclub/aclman) [![Coverage Status](https://img.shields.io/coveralls/ripaclub/aclman/master.svg?style=flat-square)](https://coveralls.io/r/ripaclub/aclman)

Requisites
----------

* PHP >= 5.4

* Composer

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

1. `aclman_storage` to configure the persistence layer in which to save your ACL rules

2. `aclman_services` to configure your services (e.g., a storage and optionally a plugin manager)

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
'aclman-assertion-manager' => [
    'invokables' => [
        'assertAlias' => 'assertionClass',
        ...
        ...
    ]
]
```

Finally, our storage configuration.

```php
'aclman_storage' => [
    'AclStorage\Ex1' => [
        'roles' => [
             // Config specific permission for role Role1 to resources Resource1 and Resource2
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
                            'assert' => 'assertAlias',
                            'allow' => true,
                            'privilege' => 'view'
                        ]
                    ]
                ],
            ],
            // Config specific permission for all roles to resource Resource1 (e.x public resource)
            StorageInterface::ALL_ROLES => [
                'resources' => [
                    'Resource3' => [
                        [
                            'allow' => true,
                        ]
                    ],
                ]
            ],
            // Config specific permission for Admin to all resource (e.x access to al resource to the admin)
            'Admin' => [
                'resources' => [
                    StorageInterface::ALL_RESOURCES  => [
                        [
                            'allow' => true,
                        ]
                    ],
                ]
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

Notice the behaviour ...

```php
$aclService1 = $serviceLocator->get('AclService\Ex1');
$aclService1->isAllowed('Role1', 'Resource1', 'add'); // TRUE
$aclService1->isAllowed('Role1', 'Resource2', 'view'); // FALSE
// ...
```

---

[![Analytics](https://ga-beacon.appspot.com/UA-49657176-3/aclman)](https://github.com/igrigorik/ga-beacon)
