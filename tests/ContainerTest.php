<?php

/*
 * This file is part of vaibhavpandeyvpz/katora package.
 *
 * (c) Vaibhav Pandey <contact@vaibhavpandey.com>
 *
 * This source file is subject to the MIT license that is bundled with this source code in the LICENSE file.
 */

namespace Katora;

use Katora\Mock\ThirteenServiceProvider;

/**
 * Class ContainerTest
 * @package Katora
 */
class ContainerTest extends \PHPUnit_Framework_TestCase
{
    public function testArrayAccess()
    {
        $container = new Container();
        $container['thirteen'] = 13;
        $container['object'] = function () {
            return new \stdClass();
        };
        $this->assertArrayHasKey('thirteen', $container);
        $this->assertArrayHasKey('object', $container);
        $this->assertEquals(13, $container['thirteen']);
        $this->assertInstanceOf('stdClass', $container['object']);
        unset($container['thirteen'], $container['object']);
        $this->assertArrayHasKey('thirteen', $container);
        $this->assertArrayHasKey('object', $container);
    }

    public function testGet()
    {
        $container = new Container();
        $container->set('thirteen', 13);
        $container->set('object', function () {
            return new \stdClass();
        });
        $this->assertEquals(13, $container->get('thirteen'));
        $this->assertInstanceOf('stdClass', $container->get('object'));
    }

    public function testGetInvalidID()
    {
        $container = new Container();
        $this->setExpectedException('Psr\Container\ContainerExceptionInterface');
        $container->get(13);
    }

    public function testGetMissingID()
    {
        $container = new Container();
        $this->setExpectedException('Psr\Container\NotFoundExceptionInterface');
        $container->get('thirteen');
    }

    public function testHas()
    {
        $container = new Container();
        $container->set('thirteen', 13);
        $this->assertTrue($container->has('thirteen'));
        $this->assertFalse($container->has('fourteen'));
    }

    public function testHasInvalidID()
    {
        $container = new Container();
        $this->setExpectedException('Psr\Container\ContainerExceptionInterface');
        $container->has(13);
    }

    public function testInstall()
    {
        $container = new Container();
        $container->install(new ThirteenServiceProvider());
        $this->assertTrue($container->has('thirteen'));
        $this->assertEquals(13, $container->get('thirteen'));
    }

    public function testPropertyAccess()
    {
        $container = new Container();
        $container->thirteen = 13;
        $container->object = function () {
            return new \stdClass();
        };
        $this->assertTrue(isset($container->thirteen));
        $this->assertTrue(isset($container->object));
        $this->assertEquals(13, $container->thirteen);
        $this->assertInstanceOf('stdClass', $container->object);
        unset($container->thirteen, $container->object);
        $this->assertTrue(isset($container->thirteen));
        $this->assertTrue(isset($container->object));
    }

    public function testRaw()
    {
        $container = new Container();
        $container->set('object', $container->raw(function () {
            return new \stdClass();
        }));
        $this->assertInstanceOf('Closure', $container->get('object'));
        $this->assertInstanceOf('stdClass', call_user_func($container->get('object')));
    }

    public function testSet()
    {
        $container = new Container();
        $container->set('thirteen', 13);
        $this->assertEquals(13, $container->get('thirteen'));
    }

    public function testSetInvalidID()
    {
        $container = new Container();
        $this->setExpectedException('Psr\Container\ContainerExceptionInterface');
        $container->set(13, 'thirteen');
    }

    public function testShare()
    {
        $container = new Container();
        $container->set('object', $container->share(function () {
            return new \stdClass();
        }));
        $this->assertSame($container->get('object'), $container->get('object'));
    }
}
