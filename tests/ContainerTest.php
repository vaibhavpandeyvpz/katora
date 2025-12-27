<?php

/*
 * This file is part of vaibhavpandeyvpz/katora package.
 *
 * (c) Vaibhav Pandey <contact@vaibhavpandey.com>
 *
 * This source file is subject to the MIT license that is bundled with this source code in the LICENSE file.
 */

namespace Katora;

use Katora\Mock\ContainerKeeper;
use Katora\Mock\ThirteenServiceProvider;
use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase
{
    public function test_array_access(): void
    {
        $container = new Container;
        $container['thirteen'] = 13;
        $container['object'] = fn () => new \stdClass;
        $this->assertArrayHasKey('thirteen', $container);
        $this->assertArrayHasKey('object', $container);
        $this->assertSame(13, $container['thirteen']);
        $this->assertInstanceOf(\stdClass::class, $container['object']);
        unset($container['thirteen'], $container['object']);
        $this->assertArrayHasKey('thirteen', $container);
        $this->assertArrayHasKey('object', $container);
    }

    public function test_get(): void
    {
        $container = new Container;
        $container->set('thirteen', 13);
        $container->set('object', fn () => new \stdClass);
        $this->assertSame(13, $container->get('thirteen'));
        $this->assertInstanceOf(\stdClass::class, $container->get('object'));
    }

    public function test_get_invalid_id(): void
    {
        $container = new Container;
        // In PHP 8.2+, int is converted to string, so it checks for key "13"
        // Since it doesn't exist, it throws NotFoundException
        $this->expectException(NotFoundException::class);
        // @phpstan-ignore-next-line - intentionally testing invalid type
        $container->get(13);
    }

    public function test_get_missing_id(): void
    {
        $container = new Container;
        $this->expectException(NotFoundException::class);
        $container->get('thirteen');
    }

    public function test_has(): void
    {
        $container = new Container;
        $container->set('thirteen', 13);
        $this->assertTrue($container->has('thirteen'));
        $this->assertFalse($container->has('fourteen'));
    }

    public function test_has_invalid_id(): void
    {
        $container = new Container;
        // In PHP 8.2+, int is converted to string for array key lookup
        // So has(13) checks for key "13" and returns false (no exception)
        $this->assertFalse($container->has(13));
    }

    public function test_install(): void
    {
        $container = new Container;
        $container->install(new ThirteenServiceProvider);
        $this->assertTrue($container->has('thirteen'));
        $this->assertSame(13, $container->get('thirteen'));
    }

    public function test_property_access(): void
    {
        $container = new Container;
        $container->thirteen = 13;
        $container->object = fn () => new \stdClass;
        $this->assertTrue(isset($container->thirteen));
        $this->assertTrue(isset($container->object));
        $this->assertSame(13, $container->thirteen);
        $this->assertInstanceOf(\stdClass::class, $container->object);
        unset($container->thirteen, $container->object);
        $this->assertTrue(isset($container->thirteen));
        $this->assertTrue(isset($container->object));
    }

    public function test_raw(): void
    {
        $container = new Container;
        $container->set('object', $container->raw(fn () => new \stdClass));
        $this->assertInstanceOf(\Closure::class, $container->get('object'));
        $this->assertInstanceOf(\stdClass::class, ($container->get('object'))());
    }

    public function test_set(): void
    {
        $container = new Container;
        $container->set('thirteen', 13);
        $this->assertSame(13, $container->get('thirteen'));
    }

    public function test_set_invalid_id(): void
    {
        $container = new Container;
        // In PHP 8.2+, int is converted to string for array key
        // So set(13, 'thirteen') sets key "13" successfully
        $container->set(13, 'thirteen');
        $this->assertTrue($container->has('13'));
        $this->assertSame('thirteen', $container->get('13'));
    }

    public function test_extras(): void
    {
        $keeper = new ContainerKeeper;
        $this->assertNull($keeper->getContainer());
        $keeper->setContainer($container = new Container);
        $this->assertNotNull($keeper->getContainer());
        $this->assertSame($container, $keeper->getContainer());
    }

    public function test_share(): void
    {
        $container = new Container;
        $container->set('object', $container->share(fn () => new \stdClass));
        $this->assertSame($container->get('object'), $container->get('object'));
    }

    // Additional comprehensive test cases

    public function test_constructor_with_initial_entries(): void
    {
        $container = new Container(['foo' => 'bar', 'baz' => fn () => 'qux']);
        $this->assertTrue($container->has('foo'));
        $this->assertTrue($container->has('baz'));
        $this->assertSame('bar', $container->get('foo'));
        $this->assertSame('qux', $container->get('baz'));
    }

    public function test_constructor_with_empty_array(): void
    {
        $container = new Container([]);
        $this->assertFalse($container->has('nonexistent'));
    }

    public function test_set_with_null_value(): void
    {
        $container = new Container;
        $container->set('null_value', null);
        $this->assertTrue($container->has('null_value'));
        $this->assertNull($container->get('null_value'));
    }

    public function test_set_with_false_value(): void
    {
        $container = new Container;
        $container->set('false_value', false);
        $this->assertTrue($container->has('false_value'));
        $this->assertFalse($container->get('false_value'));
    }

    public function test_set_with_true_value(): void
    {
        $container = new Container;
        $container->set('true_value', true);
        $this->assertTrue($container->has('true_value'));
        $this->assertTrue($container->get('true_value'));
    }

    public function test_set_with_array_value(): void
    {
        $container = new Container;
        $array = ['a' => 1, 'b' => 2];
        $container->set('array_value', $array);
        $this->assertSame($array, $container->get('array_value'));
    }

    public function test_set_with_object_value(): void
    {
        $container = new Container;
        $object = new \stdClass;
        $object->prop = 'value';
        $container->set('object_value', $object);
        $retrieved = $container->get('object_value');
        $this->assertSame($object, $retrieved);
        $this->assertSame('value', $retrieved->prop);
    }

    public function test_set_with_empty_string_id(): void
    {
        $container = new Container;
        $container->set('', 'empty_id');
        $this->assertTrue($container->has(''));
        $this->assertSame('empty_id', $container->get(''));
    }

    public function test_set_chaining(): void
    {
        $container = new Container;
        $result = $container->set('a', 1)->set('b', 2)->set('c', 3);
        $this->assertSame($container, $result);
        $this->assertSame(1, $container->get('a'));
        $this->assertSame(2, $container->get('b'));
        $this->assertSame(3, $container->get('c'));
    }

    public function test_overwriting_entry(): void
    {
        $container = new Container;
        $container->set('value', 'original');
        $this->assertSame('original', $container->get('value'));
        $container->set('value', 'updated');
        $this->assertSame('updated', $container->get('value'));
    }

    public function test_install_chaining(): void
    {
        $container = new Container;
        $provider1 = new ThirteenServiceProvider;
        $provider2 = new class implements ServiceProviderInterface
        {
            public function provide(Container $container): void
            {
                $container->set('fourteen', fn () => 14);
            }
        };

        $result = $container->install($provider1)->install($provider2);
        $this->assertSame($container, $result);
        $this->assertTrue($container->has('thirteen'));
        $this->assertTrue($container->has('fourteen'));
        $this->assertSame(13, $container->get('thirteen'));
        $this->assertSame(14, $container->get('fourteen'));
    }

    public function test_share_multiple_calls_return_same_instance(): void
    {
        $container = new Container;
        $container->set('shared', $container->share(fn () => new \stdClass));

        $instance1 = $container->get('shared');
        $instance2 = $container->get('shared');
        $instance3 = $container->get('shared');

        $this->assertSame($instance1, $instance2);
        $this->assertSame($instance2, $instance3);
    }

    public function test_share_different_closures_are_independent(): void
    {
        $container = new Container;

        $shared1 = $container->share(fn () => new \stdClass);
        $shared2 = $container->share(fn () => new \stdClass);

        $container->set('service1', $shared1);
        $container->set('service2', $shared2);

        $instance1a = $container->get('service1');
        $instance1b = $container->get('service1');
        $instance2a = $container->get('service2');
        $instance2b = $container->get('service2');

        $this->assertSame($instance1a, $instance1b);
        $this->assertSame($instance2a, $instance2b);
        $this->assertNotSame($instance1a, $instance2a);
    }

    public function test_share_with_callable_that_uses_container(): void
    {
        $container = new Container;
        $container->set('dependency', 'dep_value');
        $container->set('service', $container->share(function ($c) {
            return $c->get('dependency').'_resolved';
        }));

        $this->assertSame('dep_value_resolved', $container->get('service'));
        $this->assertSame('dep_value_resolved', $container->get('service')); // Should be cached
    }

    public function test_share_with_null_return(): void
    {
        $container = new Container;
        $container->set('null_service', $container->share(fn () => null));

        $this->assertNull($container->get('null_service'));
        $this->assertNull($container->get('null_service')); // Should still work
    }

    public function test_raw_with_different_callables(): void
    {
        $container = new Container;

        $callable1 = fn () => 'result1';
        $callable2 = fn () => 'result2';

        $container->set('raw1', $container->raw($callable1));
        $container->set('raw2', $container->raw($callable2));

        // raw() returns a callable that when called returns the original callable
        // get() will call it, so we get the original callable back
        $retrieved1 = $container->get('raw1');
        $retrieved2 = $container->get('raw2');

        // The retrieved values should be the original callables
        $this->assertSame($callable1, $retrieved1);
        $this->assertSame($callable2, $retrieved2);

        // And calling them should return their results
        $this->assertSame('result1', $retrieved1());
        $this->assertSame('result2', $retrieved2());
    }

    public function test_raw_nested(): void
    {
        $container = new Container;
        $inner = fn () => 'inner';
        $outer = $container->raw($inner);
        $container->set('nested', $container->raw($outer));

        // get() calls the raw wrapper, which returns the outer wrapper
        $result = $container->get('nested');
        $this->assertInstanceOf(\Closure::class, $result);

        // Calling the outer wrapper returns the inner callable
        $middle = $result();
        $this->assertInstanceOf(\Closure::class, $middle);

        // Calling the inner callable returns its result
        $final = $middle();
        $this->assertSame('inner', $final);
    }

    public function test_callable_that_returns_null(): void
    {
        $container = new Container;
        $container->set('null_callable', fn () => null);
        $this->assertNull($container->get('null_callable'));
    }

    public function test_callable_that_throws_exception(): void
    {
        $container = new Container;
        $container->set('throws', fn () => throw new \RuntimeException('Test exception'));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Test exception');
        $container->get('throws');
    }

    public function test_callable_with_container_parameter(): void
    {
        $container = new Container;
        $container->set('dep', 'dependency');
        $container->set('service', function ($c) {
            return 'Service with '.$c->get('dep');
        });

        $this->assertSame('Service with dependency', $container->get('service'));
    }

    public function test_array_access_offset_get_with_missing_key(): void
    {
        $container = new Container;
        $this->expectException(NotFoundException::class);
        $container['missing'];
    }

    public function test_array_access_offset_get_with_invalid_offset(): void
    {
        $container = new Container;
        $this->expectException(ContainerException::class);
        // @phpstan-ignore-next-line - intentionally testing invalid type
        $container[123];
    }

    public function test_array_access_offset_set_with_null_offset(): void
    {
        $container = new Container;
        // PHP passes null to offsetSet, which should throw ContainerException
        $this->expectException(ContainerException::class);
        // @phpstan-ignore-next-line - intentionally testing null offset
        $container[null] = 'value_with_null_offset';
    }

    public function test_array_access_offset_set_with_invalid_offset(): void
    {
        $container = new Container;
        $this->expectException(ContainerException::class);
        // @phpstan-ignore-next-line - intentionally testing invalid type
        $container[123] = 'value';
    }

    public function test_array_access_offset_exists_with_invalid_offset(): void
    {
        $container = new Container;
        $this->assertFalse(isset($container[123]));
        $this->assertFalse(isset($container[null]));
        $this->assertFalse(isset($container[new \stdClass]));
    }

    public function test_array_access_offset_unset_does_nothing(): void
    {
        $container = new Container;
        $container['test'] = 'value';
        $this->assertTrue(isset($container['test']));
        unset($container['test']);
        $this->assertTrue(isset($container['test'])); // Should still exist
    }

    public function test_property_access_with_missing_property(): void
    {
        $container = new Container;
        $this->expectException(NotFoundException::class);
        $value = $container->nonexistent;
    }

    public function test_property_access_unset_does_nothing(): void
    {
        $container = new Container;
        $container->test = 'value';
        $this->assertTrue(isset($container->test));
        unset($container->test);
        $this->assertTrue(isset($container->test)); // Should still exist
    }

    public function test_property_access_with_null_value(): void
    {
        $container = new Container;
        $container->null_prop = null;
        // isset() returns false for null values in PHP, but has() returns true
        $this->assertTrue($container->has('null_prop'));
        $this->assertNull($container->null_prop);
    }

    public function test_has_with_empty_string(): void
    {
        $container = new Container;
        $container->set('', 'empty');
        $this->assertTrue($container->has(''));
        $this->assertFalse($container->has(' ')); // Space is different
    }

    public function test_get_with_empty_string(): void
    {
        $container = new Container;
        $container->set('', 'empty_string_id');
        $this->assertSame('empty_string_id', $container->get(''));
    }

    public function test_service_provider_overwrites_existing(): void
    {
        $container = new Container;
        $container->set('thirteen', 999);
        $this->assertSame(999, $container->get('thirteen'));

        $container->install(new ThirteenServiceProvider);
        $this->assertSame(13, $container->get('thirteen')); // Should be overwritten
    }

    public function test_multiple_service_providers(): void
    {
        $container = new Container;

        $provider1 = new class implements ServiceProviderInterface
        {
            public function provide(Container $container): void
            {
                $container->set('service1', fn () => 'value1');
            }
        };

        $provider2 = new class implements ServiceProviderInterface
        {
            public function provide(Container $container): void
            {
                $container->set('service2', fn () => 'value2');
            }
        };

        $container->install($provider1)->install($provider2);

        $this->assertSame('value1', $container->get('service1'));
        $this->assertSame('value2', $container->get('service2'));
    }

    public function test_service_provider_that_registers_multiple_services(): void
    {
        $container = new Container;

        $provider = new class implements ServiceProviderInterface
        {
            public function provide(Container $container): void
            {
                $container->set('service1', 'value1');
                $container->set('service2', 'value2');
                $container->set('service3', fn () => 'value3');
            }
        };

        $container->install($provider);

        $this->assertSame('value1', $container->get('service1'));
        $this->assertSame('value2', $container->get('service2'));
        $this->assertSame('value3', $container->get('service3'));
    }

    public function test_long_string_id(): void
    {
        $container = new Container;
        $longId = str_repeat('a', 1000);
        $container->set($longId, 'long_id_value');
        $this->assertTrue($container->has($longId));
        $this->assertSame('long_id_value', $container->get($longId));
    }

    public function test_special_characters_in_id(): void
    {
        $container = new Container;
        $specialId = 'service.with-dots_and_underscores-123';
        $container->set($specialId, 'special_value');
        $this->assertTrue($container->has($specialId));
        $this->assertSame('special_value', $container->get($specialId));
    }

    public function test_unicode_characters_in_id(): void
    {
        $container = new Container;
        $unicodeId = '服务_サービス_서비스';
        $container->set($unicodeId, 'unicode_value');
        $this->assertTrue($container->has($unicodeId));
        $this->assertSame('unicode_value', $container->get($unicodeId));
    }

    public function test_callable_with_different_return_types(): void
    {
        $container = new Container;

        $container->set('string', fn () => 'string');
        $container->set('int', fn () => 42);
        $container->set('float', fn () => 3.14);
        $container->set('bool', fn () => true);
        $container->set('array', fn () => [1, 2, 3]);
        $container->set('object', fn () => new \stdClass);

        $this->assertIsString($container->get('string'));
        $this->assertIsInt($container->get('int'));
        $this->assertIsFloat($container->get('float'));
        $this->assertIsBool($container->get('bool'));
        $this->assertIsArray($container->get('array'));
        $this->assertIsObject($container->get('object'));
    }

    public function test_nested_container_access(): void
    {
        $outer = new Container;
        $inner = new Container;

        $inner->set('inner_value', 'inner');
        $outer->set('inner_container', $inner);
        $outer->set('service', function ($c) {
            $inner = $c->get('inner_container');

            return $inner->get('inner_value');
        });

        $this->assertSame('inner', $outer->get('service'));
    }

    public function test_share_with_callable_that_returns_different_types(): void
    {
        $container = new Container;

        $counter = 0;
        $container->set('counter', $container->share(function () use (&$counter) {
            return ++$counter;
        }));

        // First call should increment
        $this->assertSame(1, $container->get('counter'));
        // Subsequent calls should return cached value
        $this->assertSame(1, $container->get('counter'));
        $this->assertSame(1, $container->get('counter'));
    }

    public function test_exception_message_contains_id(): void
    {
        $container = new Container;

        try {
            $container->get('missing_service');
            $this->fail('Expected NotFoundException');
        } catch (NotFoundException $e) {
            $this->assertStringContainsString('missing_service', $e->getMessage());
        }
    }

    public function test_container_exception_message_contains_type(): void
    {
        $container = new Container;

        try {
            // @phpstan-ignore-next-line - intentionally testing invalid type
            $container->offsetGet(123);
            $this->fail('Expected ContainerException');
        } catch (ContainerException $e) {
            $this->assertStringContainsString('string', $e->getMessage());
            $this->assertStringContainsString('int', $e->getMessage());
        }
    }

    public function test_has_container_trait_with_null_container(): void
    {
        $keeper = new ContainerKeeper;
        $this->assertNull($keeper->getContainer());
    }

    public function test_has_container_trait_with_container(): void
    {
        $keeper = new ContainerKeeper;
        $container1 = new Container;
        $container2 = new Container;

        $keeper->setContainer($container1);
        $this->assertSame($container1, $keeper->getContainer());

        $keeper->setContainer($container2);
        $this->assertSame($container2, $keeper->getContainer());
    }
}
