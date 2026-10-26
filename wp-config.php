<?php
/**
 * Every value comes from the environment: .env locally, the runtime in production.
 * Nothing secret lives in this file, so it is safe to commit.
 */

function poychamp_env(string $name): string
{
    $value = getenv($name);
    if ($value === false || $value === '') {
        throw new RuntimeException("Missing environment variable {$name}");
    }
    return $value;
}

define('DB_NAME', poychamp_env('DB_NAME'));
define('DB_USER', poychamp_env('DB_USER'));
define('DB_PASSWORD', poychamp_env('DB_PASSWORD'));
define('DB_HOST', getenv('DB_HOST') ?: 'db');
define('DB_CHARSET', 'utf8mb4');
define('DB_COLLATE', '');

foreach (['AUTH_KEY', 'SECURE_AUTH_KEY', 'LOGGED_IN_KEY', 'NONCE_KEY',
          'AUTH_SALT', 'SECURE_AUTH_SALT', 'LOGGED_IN_SALT', 'NONCE_SALT'] as $key) {
    define($key, poychamp_env('WP_' . $key));
}

$table_prefix = getenv('WP_TABLE_PREFIX') ?: 'wp_';

define('WP_ENVIRONMENT_TYPE', getenv('APP_ENV') ?: 'local');
define('WP_HOME', poychamp_env('WP_HOME'));
define('WP_SITEURL', WP_HOME);

define('WP_DEBUG', WP_ENVIRONMENT_TYPE === 'local');
define('WP_DEBUG_DISPLAY', WP_ENVIRONMENT_TYPE === 'local');
// Locally, re-read theme.json and theme pattern files on every request instead of caching them.
define('WP_DEVELOPMENT_MODE', WP_ENVIRONMENT_TYPE === 'local' ? 'theme' : '');

// Core is baked into the image. Update it by bumping WORDPRESS_VERSION, never from wp-admin.
define('WP_AUTO_UPDATE_CORE', false);
define('DISALLOW_FILE_EDIT', true);
// The production image is read-only, so plugin and theme installs belong in the repo.
define('DISALLOW_FILE_MODS', WP_ENVIRONMENT_TYPE === 'production');

if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/');
}

require_once ABSPATH . 'wp-settings.php';
