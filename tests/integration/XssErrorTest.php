<?php

namespace Tests;

class XssErrorTest extends TestCase
{
    /** @test */
    function error_message_escapes_html_in_queries()
    {
        global $wpdb;
        $wpdb->suppress_errors(true);
        $wpdb->query("SELECT * FROM nonexistent_<script>alert(1)</script>");
        $wpdb->suppress_errors(false);

        $message = $wpdb->dbh->get_error_message();
        $this->assertNotEmpty($message);
        $this->assertStringContainsString('&lt;script&gt;', $message,
            'get_error_message() should HTML-escape <script>');
        $this->assertStringNotContainsString('<script>', $message,
            'get_error_message() should not contain raw <script>');
    }

    /** @test */
    function error_message_escapes_html_in_error_text()
    {
        global $wpdb;
        $wpdb->suppress_errors(true);
        $wpdb->query("CREATE TABLE <script>alert(1)</script> (id int)");
        $wpdb->suppress_errors(false);

        $message = $wpdb->dbh->get_error_message();
        // The error may or may not be set depending on query rewriting
        // But if it is, it should be escaped
        if (!empty($message)) {
            $this->assertStringContainsString('&lt;script&gt;', $message);
            $this->assertStringNotContainsString('<script>', $message);
        } else {
            $this->markTestSkipped('No error message generated for this query');
        }
    }

    /** @test */
    function eZSQL_error_stores_escaped_data()
    {
        global $EZSQL_ERROR;
        $EZSQL_ERROR = [];

        global $wpdb;
        $wpdb->suppress_errors(true);
        $wpdb->show_errors = false;
        // Syntax error with HTML special chars in the query
        $wpdb->query("SELECT '<script>alert(1)</script>' FRM wp_posts");

        $this->assertNotEmpty($EZSQL_ERROR, 'EZSQL_ERROR should have entries');
        foreach ($EZSQL_ERROR as $entry) {
            $this->assertStringNotContainsString('<script>', $entry['query'],
                'Query should not contain raw <script>');
            $this->assertStringNotContainsString('<script>', $entry['error_str'],
                'Error string should not contain raw <script>');
        }
    }
}
