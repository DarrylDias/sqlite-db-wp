<?php

namespace Tests;

class PerformanceTest extends TestCase
{
    /** @test */
    function delete_with_limit_does_not_error()
    {
        global $wpdb;

        $this->factory()->post->create();
        $this->factory()->post->create();
        $this->factory()->post->create();

        $result = $wpdb->query("DELETE FROM {$wpdb->posts} WHERE post_type = 'post' LIMIT 1");

        $this->assertNotFalse($result);
    }

    /** @test */
    function update_with_order_by_does_not_error()
    {
        global $wpdb;

        $this->factory()->post->create(['post_title' => 'AAA']);
        $this->factory()->post->create(['post_title' => 'BBB']);

        $result = $wpdb->query("UPDATE {$wpdb->posts} SET post_title = 'Updated' WHERE post_type = 'post' ORDER BY post_title DESC");

        $this->assertNotFalse($result);
    }

    /** @test */
    function pragma_compile_options_is_cached()
    {
        $refl = new \ReflectionClass(\WP_SQLite_DB\PDOSQLiteDriver::class);
        $prop = $refl->getProperty('compile_options');
        $prop->setValue(null);

        $method = $refl->getMethod('has_update_delete_limit');
        $method->setAccessible(true);

        $result1 = $method->invoke(null);
        $result2 = $method->invoke(null);

        $this->assertIsBool($result1);
        $this->assertSame($result1, $result2);
    }

    /** @test */
    function multiple_delete_limits_only_query_pragma_once()
    {
        global $wpdb;

        $refl = new \ReflectionClass(\WP_SQLite_DB\PDOSQLiteDriver::class);
        $prop = $refl->getProperty('compile_options');
        $prop->setValue(null);

        $this->factory()->post->create();
        $this->factory()->post->create();

        $wpdb->query("DELETE FROM {$wpdb->posts} WHERE post_type = 'post' LIMIT 1");
        $wpdb->query("DELETE FROM {$wpdb->posts} WHERE post_type = 'post' LIMIT 1");

        $this->assertNotNull($prop->getValue());
    }
}
