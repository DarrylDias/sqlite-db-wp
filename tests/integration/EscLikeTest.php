<?php

namespace Tests;

class EscLikeTest extends TestCase
{
    /** @test */
    function it_escapes_percent_sign()
    {
        global $wpdb;
        $result = $wpdb->esc_like('100%');

        $this->assertStringContainsString('\\%', $result, 'esc_like should escape % with backslash');
    }

    /** @test */
    function it_escapes_underscore()
    {
        global $wpdb;
        $result = $wpdb->esc_like('test_');

        $this->assertStringContainsString('\\_', $result, 'esc_like should escape _ with backslash');
    }

    /** @test */
    function it_escapes_backslash()
    {
        global $wpdb;
        $result = $wpdb->esc_like('foo\\bar');

        $this->assertStringContainsString('\\\\', $result, 'esc_like should escape \\ with backslash');
    }

    /** @test */
    function it_returns_plain_text_unchanged()
    {
        global $wpdb;
        $result = $wpdb->esc_like('hello world');

        $this->assertSame('hello world', $result);
    }

    /** @test */
    function like_query_with_escaped_percent_does_not_match_wildcard()
    {
        global $wpdb;

        $this->factory()->post->create(['post_title' => 'Test 100% Complete']);
        $this->factory()->post->create(['post_title' => 'Test 100 Done']);
        $this->factory()->post->create(['post_title' => 'Test 1000 Items']);

        $escaped = $wpdb->esc_like('100%');
        $results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT ID FROM {$wpdb->posts} WHERE post_title LIKE %s AND post_type = 'post'",
                '%' . $escaped . '%'
            )
        );

        $this->assertCount(1, $results, 'LIKE with escaped %% should only match literal 100%');
        $this->assertSame('Test 100% Complete', get_the_title($results[0]->ID));
    }

    /** @test */
    function like_query_with_escaped_underscore_does_not_match_wildcard()
    {
        global $wpdb;

        $this->factory()->post->create(['post_title' => 'test_1_result']);
        $this->factory()->post->create(['post_title' => 'test1result']);

        $escaped = $wpdb->esc_like('test_');
        $results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT ID FROM {$wpdb->posts} WHERE post_title LIKE %s AND post_type = 'post'",
                $escaped . '%'
            )
        );

        $this->assertCount(1, $results, 'LIKE with escaped \\_ should only match literal underscore');
        $this->assertSame('test_1_result', get_the_title($results[0]->ID));
    }
}
