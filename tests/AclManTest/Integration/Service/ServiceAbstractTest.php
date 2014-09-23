<?php
namespace AclManTest\Integration\Service;

use AclMan\Service\ServiceImplement;
use AclManTest\AclManTestCase;
use AclMan\Storage\Adapter\ArrayAdapter\ArrayAdapter;
use Zend\Permissions\Acl\Acl;

class ServiceAbstractTest extends AclManTestCase
{
    /**
     * @var $service ServiceImplement
     */
    protected $service;

    public function setUp()
    {
        $this->service = new ServiceImplement();

        $this->service->setAcl(new Acl());
    }

    public function setUpArrayObjectStorage()
    {
        $options = [
            'roles' => [
                'admin',
                'moderator',
                'guest'
            ],
            'resources' => [
                'resource1',
                'resource2',
                'resource3',
            ],
            'permission' => [
                [
                    'resource' => 'resource3',
                    'role'     => 'admin',
                    'allow'    => true
                ],
                [
                    'resource'  => 'resource3',
                    'role'      => 'moderator',
                    'privilege' => 'add',
                    'allow'     => true
                ],
                [
                    'resource'  => 'resource3',
                    'role'      => 'guest',
                    'privilege' => 'add',
                    'allow'     => false
                ]
            ]
        ];

       return new ArrayAdapter($options);
    }

    public function testAclArrayStorage()
    {
        $this->service->setStorage($this->setUpArrayObjectStorage());
        $this->service->init();

        //var_dump($this->service->isAllowed('admin', 'resource3'));
        //var_dump($this->service->isAllowed('admin', 'resource3', 'adminEdit'));
        //var_dump($this->service->isAllowed('moderator', 'resource3'));
        //var_dump($this->service->isAllowed('moderator', 'resource3', 'add'));
    }
} 