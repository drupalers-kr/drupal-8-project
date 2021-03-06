<?php

/**
 * @file
 * Integrates DB settings as parsed from environment variables.
 */

// Load .env file if it exists
if (file_exists(PROJECT_ROOT . '/.env')) {
  // Load environment
  $dotenv = new Dotenv\Dotenv(PROJECT_ROOT);
  $dotenv->load();

  // Require DATABASE_URL to be defined
  $dotenv->required('DATABASE_URL')->notEmpty();
}

$dsn_info = parse_url(getenv('DATABASE_URL'));

if ($dsn_info['scheme'] == 'sqlite') {
  $databases['default']['default'] = array (
    'driver' => $dsn_info['scheme'],
    'database' => $dsn_info['path'],
  );
} else {
  $databases['default']['default'] = array (
    'database' => str_replace('/', '', $dsn_info['path']),
    'username' => $dsn_info['user'],
    'password' => $dsn_info['pass'],
    'host' => $dsn_info['host'],
    'port' => $dsn_info['port'],
    'driver' => $dsn_info['scheme'],
    'prefix' => '',
    'collation' => 'utf8mb4_general_ci',
  );
}
