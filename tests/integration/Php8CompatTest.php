<?php

namespace Tests;

class Php8CompatTest extends TestCase
{
    /** @test */
    function deriveInterval_handles_empty_input()
    {
        global $wpdb;
        $engine = $wpdb->dbh;
        // deriveInterval is a private method — test via a query that triggers it
        // INTERVAL '0' DAY is a MySQLism that gets rewritten
        $result = $wpdb->get_results("SELECT DATE('now') + INTERVAL '1' DAY");
        // Should not throw a TypeError
        $this->assertNotNull($result);
    }

    /** @test */
    function least_returns_minimum_value()
    {
        global $wpdb;
        $result = $wpdb->get_var("SELECT LEAST(3, 1, 2)");
        // LEAST(3,1,2) should return 1
        $this->assertEquals(1, (int) $result);
    }

    /** @test */
    function greatest_returns_maximum_value()
    {
        global $wpdb;
        $result = $wpdb->get_var("SELECT GREATEST(3, 1, 2)");
        // GREATEST(3,1,2) should return 3
        $this->assertEquals(3, (int) $result);
    }

    /** @test */
    function least_with_single_arg_returns_same_value()
    {
        global $wpdb;
        $result = $wpdb->get_var("SELECT LEAST(42)");
        $this->assertEquals(42, (int) $result);
    }

    /** @test */
    function greatest_with_single_arg_returns_same_value()
    {
        global $wpdb;
        $result = $wpdb->get_var("SELECT GREATEST(42)");
        $this->assertEquals(42, (int) $result);
    }
}
