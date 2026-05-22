<?php

namespace Tests;

class SqlInjectionTest extends TestCase
{
    /** @test */
    function show_columns_returns_valid_results()
    {
        global $wpdb;
        $result = $wpdb->get_results(
            "SHOW COLUMNS FROM {$wpdb->posts}"
        );
        $this->assertNotNull($result);
        $this->assertNotEmpty($result);
    }

    /** @test */
    function show_tables_works()
    {
        global $wpdb;
        $tables = $wpdb->get_results("SHOW TABLES");
        $this->assertNotEmpty($tables);
    }

    /** @test */
    function show_index_works()
    {
        global $wpdb;
        $result = $wpdb->get_results("SHOW INDEX FROM {$wpdb->posts}");
        $this->assertNotNull($result);
    }

    /** @test */
    function show_tables_like_preserves_table()
    {
        global $wpdb;
        $wpdb->get_results("SHOW TABLES LIKE '%post%'");
        $this->assertNotNull($wpdb->last_result);
    }

    /** @test */
    function show_columns_with_like_preserves_table()
    {
        global $wpdb;
        $wpdb->get_results(
            "SHOW COLUMNS FROM {$wpdb->posts} LIKE '%tit%'"
        );
        $this->assertNotNull($wpdb->last_result);
    }

    /** @test */
    function show_index_does_not_corrupt_data()
    {
        global $wpdb;
        $before = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts}");
        $wpdb->get_results("SHOW INDEX FROM {$wpdb->posts}");
        $after = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts}");
        $this->assertEquals($before, $after);
    }
}
