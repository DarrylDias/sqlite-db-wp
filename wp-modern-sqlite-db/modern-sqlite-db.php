<?php
/**
 * Plugin Name: Modern SQLite DB for WordPress
 * Plugin URI:  https://github.com/DarrylDias/sqlite-db-wp
 * Description: A secure, PHP 8.x-optimized SQLite drop-in database driver for WordPress.
 * Version:     2.0.0
 * Author:      Darryl Dias
 * Author URI:  https://github.com/DarrylDias
 * License:     GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

defined('ABSPATH') or die();

class ModernSQLiteDB
{
    const DROPIN_FILE = 'db.php';
    const DROPIN_PATH = 'wp-content/db.php';

    public function __construct()
    {
        register_activation_hook(__FILE__, [$this, 'activate']);
        register_deactivation_hook(__FILE__, [$this, 'deactivate']);
        add_action('admin_notices', [$this, 'admin_notices']);
        add_action('admin_menu', [$this, 'admin_menu']);
    }

    public function activate()
    {
        $source = plugin_dir_path(__FILE__) . 'db.php';
        $target = WP_CONTENT_DIR . '/db.php';

        if (! file_exists($source)) {
            set_transient('modern_sqlite_db_error', 'Source file missing: ' . $source, 30);
            return;
        }

        if (file_exists($target)) {
            $existing_hash = md5_file($target);
            $source_hash = md5_file($source);
            if ($existing_hash === $source_hash) {
                set_transient('modern_sqlite_db_notice', 'already_installed', 30);
                return;
            }
        }

        $copied = copy($source, $target);
        if ($copied) {
            set_transient('modern_sqlite_db_notice', 'installed', 30);
        } else {
            set_transient('modern_sqlite_db_error', 'Failed to copy db.php to ' . $target, 30);
        }
    }

    public function deactivate()
    {
        $target = WP_CONTENT_DIR . '/db.php';
        if (file_exists($target) && md5_file($target) === md5_file(plugin_dir_path(__FILE__) . 'db.php')) {
            unlink($target);
        }
    }

    public function admin_notices()
    {
        $notice = get_transient('modern_sqlite_db_notice');
        $error = get_transient('modern_sqlite_db_error');

        if ($error) {
            echo '<div class="notice notice-error"><p><strong>Modern SQLite DB:</strong> ' . esc_html($error) . '</p></div>';
            delete_transient('modern_sqlite_db_error');
        }

        if ($notice === 'installed') {
            $db_path = WP_CONTENT_DIR . '/db.php';
            $active = file_exists($db_path) ? 'active' : 'inactive';
            echo '<div class="notice notice-success"><p><strong>Modern SQLite DB:</strong> Drop-in installed. '
                . 'Status: <strong>' . esc_html($active) . '</strong>. '
                . '<a href="' . admin_url('options-general.php?page=modern-sqlite-db') . '">View status</a></p></div>';
            delete_transient('modern_sqlite_db_notice');
        }

        if ($notice === 'already_installed') {
            echo '<div class="notice notice-info"><p><strong>Modern SQLite DB:</strong> Drop-in already installed (up to date).</p></div>';
            delete_transient('modern_sqlite_db_notice');
        }
    }

    public function admin_menu()
    {
        add_options_page(
            'Modern SQLite DB',
            'SQLite DB',
            'manage_options',
            'modern-sqlite-db',
            [$this, 'admin_page']
        );
    }

    public function admin_page()
    {
        $db_path = WP_CONTENT_DIR . '/db.php';
        $dropin_active = file_exists($db_path);
        $db_file = defined('FQDB') ? FQDB : 'Not initialized';
        ?>
        <div class="wrap">
            <h1>Modern SQLite DB for WordPress</h1>
            <table class="widefat striped" style="max-width:600px">
                <tr><th>Drop-in Status</th><td><?php echo $dropin_active ? '<span style="color:green;font-weight:bold">Active</span>' : '<span style="color:red">Inactive</span>'; ?></td></tr>
                <tr><th>Drop-in Location</th><td><code><?php echo esc_html($db_path); ?></code></td></tr>
                <tr><th>Database File</th><td><code><?php echo esc_html($db_file); ?></code></td></tr>
                <tr><th>Plugin Version</th><td>2.0.0</td></tr>
                <tr><th>PHP Version</th><td><?php echo PHP_VERSION; ?></td></tr>
                <tr><th>PDO SQLite</th><td><?php echo extension_loaded('pdo_sqlite') ? '✓ Loaded' : '✗ Missing'; ?></td></tr>
                <tr><th>Journal Mode</th><td><?php echo $this->get_pragma('journal_mode'); ?></td></tr>
            </table>
            <p><a href="https://github.com/DarrylDias/sqlite-db-wp" target="_blank">GitHub</a></p>
        </div>
        <?php
    }

    private function get_pragma($name)
    {
        global $wpdb;
        if (! $wpdb || ! $wpdb->dbh) {
            return 'N/A';
        }
        try {
            return $wpdb->get_var("PRAGMA {$name}");
        } catch (Exception $e) {
            return 'Error';
        }
    }
}

new ModernSQLiteDB();
