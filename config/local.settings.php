<?php

/**
 * @file
 * This file is for local settings overrides that should be present in all
 * environments. It is loaded before DB, ENV and Flysystem settings.
 */

if (!defined('PROJECT_ROOT')) {
  define('PROJECT_ROOT', dirname(DRUPAL_ROOT));
}

$config_directories['sync'] = '../config/sync';
$settings['hash_salt'] = '';

// Load .env file if it exists
if (file_exists(PROJECT_ROOT . '/.env')) {
  // Load environment
  $dotenv = new Dotenv\Dotenv(PROJECT_ROOT);
  $dotenv->load();

  // Require DATABASE_URL to be defined
  $dotenv->required('DRUPAL_HASH_SALT')->notEmpty();
}

if (!empty($hash_salt)) {
  $settings['hash_salt'] = getenv('DRUPAL_HASH_SALT');
}
