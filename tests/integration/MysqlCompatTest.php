<?php

namespace Tests;

class MysqlCompatTest extends TestCase
{
    /** @test */
    function rand_returns_float_between_zero_and_one()
    {
        global $wpdb;
        $result = $wpdb->get_var("SELECT RAND()");
        $value = (float) $result;
        $this->assertGreaterThanOrEqual(0, $value);
        $this->assertLessThanOrEqual(1, $value);
    }

    /** @test */
    function rand_returns_different_values_on_consecutive_calls()
    {
        global $wpdb;
        $first = (float) $wpdb->get_var("SELECT RAND()");
        $second = (float) $wpdb->get_var("SELECT RAND()");
        // Very unlikely to be equal
        $this->assertNotEquals($first, $second);
    }

    /** @test */
    function field_returns_index_of_first_match()
    {
        global $wpdb;
        $result = $wpdb->get_var("SELECT FIELD('c', 'a', 'b', 'c', 'd')");
        $this->assertEquals(3, (int) $result);
    }

    /** @test */
    function field_returns_zero_when_no_match()
    {
        global $wpdb;
        $result = $wpdb->get_var("SELECT FIELD('x', 'a', 'b', 'c')");
        $this->assertEquals(0, (int) $result);
    }

    /** @test */
    function field_is_case_insensitive()
    {
        global $wpdb;
        $result = $wpdb->get_var("SELECT FIELD('HELLO', 'hello')");
        $this->assertEquals(1, (int) $result);
    }

    /** @test */
    function inet_ntoa_converts_integer_to_ip()
    {
        global $wpdb;
        $result = $wpdb->get_var("SELECT INET_NTOA(3232235521)");
        // 3232235521 = 192.168.0.1
        $this->assertEquals('192.168.0.1', $result);
    }

    /** @test */
    function inet_aton_converts_ip_to_integer()
    {
        global $wpdb;
        $result = $wpdb->get_var("SELECT INET_ATON('192.168.0.1')");
        // 192.168.0.1 = 3232235521
        $this->assertEquals(3232235521, (int) $result);
    }

    /** @test */
    function inet_aton_round_trips_with_inet_ntoa()
    {
        global $wpdb;
        $ip = '10.0.0.1';
        $numeric = (int) $wpdb->get_var("SELECT INET_ATON('{$ip}')");
        $back = $wpdb->get_var("SELECT INET_NTOA({$numeric})");
        $this->assertEquals($ip, $back);
    }
}
