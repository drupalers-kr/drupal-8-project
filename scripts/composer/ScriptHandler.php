<?php

namespace Shomeya\composer;

use Composer\Script\Event;
use Symfony\Component\Filesystem\Filesystem;

class ScriptHandler {

  protected static function getDrupalRoot($project_root) {
    return $project_root .  '/web';
  }

  public static function buildScaffold(Event $event) {
    $fs = new Filesystem();
    if (!$fs->exists(static::getDrupalRoot(getcwd()) . '/autoload.php')) {
      \DrupalComposer\DrupalScaffold\Plugin::scaffold($event);
    }
  }

  public static function createRequiredFiles(Event $event) {
    $fs = new Filesystem();
    $project_root = getcwd();
    $drupal_root = static::getDrupalRoot($project_root);

    $dirs = [
      'modules',
      'profiles',
      'themes',
    ];

    // Required for unit testing
    foreach ($dirs as $dir) {
      if (!$fs->exists($drupal_root . '/'. $dir)) {
        $fs->mkdir($drupal_root . '/'. $dir);
        $fs->touch($drupal_root . '/'. $dir . '/.gitkeep');
      }
    }

    // Prepare the settings file for installation
    if (!$fs->exists($drupal_root . '/sites/default/settings.php')) {
      $fs->copy($drupal_root . '/sites/default/default.settings.php', $drupal_root . '/sites/default/settings.php');
      $fs->chmod($drupal_root . '/sites/default/settings.php', 0666);
      $event->getIO()->write("Create a sites/default/settings.php file with chmod 0666");
    }

    // Prepare the services file for installation
    if (!$fs->exists($drupal_root . '/sites/default/services.yml')) {
      $fs->copy($drupal_root . '/sites/default/default.services.yml', $drupal_root . '/sites/default/services.yml');
      $fs->chmod($drupal_root . '/sites/default/services.yml', 0444);
      $event->getIO()->write("Create a sites/default/services.yml file with chmod 0444");
    }

    // Create the files directory with chmod 0777
    if (!$fs->exists($drupal_root . '/sites/default/files')) {
      $oldmask = umask(0);
      $fs->mkdir($drupal_root . '/sites/default/files', 0777);
      umask($oldmask);
      $event->getIO()->write("Create a sites/default/files directory with chmod 0777");
    }

    // Write custom config to settings.php
    if ($fs->exists($drupal_root . '/sites/default/settings.php')) {
      $contents = file_get_contents($drupal_root . '/sites/default/settings.php');

      if (strpos($contents, static::getSettingsSnippet()) === FALSE) {
        $contents = $contents . static::getSettingsSnippet();
        $fs->dumpFile($drupal_root . '/sites/default/settings.php', $contents, 0666);
        $event->getIO()->write("Added relevant snippets to settings.php");
      }
    }

    // Copy the dbenv settings file
    if (!$fs->exists($drupal_root . '/sites/default/dbenv.settings.php')) {
      $fs->copy($project_root . '/config/settings-files/dbenv.settings.php', $drupal_root . '/sites/default/dbenv.settings.php');
      $fs->chmod($drupal_root . '/sites/default/dbenv.settings.php', 0444);
      $event->getIO()->write("Create a sites/default/dbenv.settings.php file with chmod 0444");
    }

    // Copy the env settings file
    if (!$fs->exists($drupal_root . '/sites/default/env.settings.php')) {
      $fs->copy($project_root . '/config/settings-files/env.settings.php', $drupal_root . '/sites/default/env.settings.php');
      $fs->chmod($drupal_root . '/sites/default/env.settings.php', 0444);
      $event->getIO()->write("Create a sites/default/env.settings.php file with chmod 0444");
    }

    // Copy the flysystem s3 settings file
    if (!$fs->exists($drupal_root . '/sites/default/flysystems3.settings.php')) {
      $fs->copy($project_root . '/config/settings-files/flysystems3.settings.php', $drupal_root . '/sites/default/flysystems3.settings.php');
      $fs->chmod($drupal_root . '/sites/default/flysystems3.settings.php', 0444);
      $event->getIO()->write("Create a sites/default/flysystems3.settings.php file with chmod 0444");
    }

    // Copy the local settings file
    if (!$fs->exists($drupal_root . '/sites/default/local.settings.php')) {
      $fs->copy($project_root . '/config/local.settings.php', $drupal_root . '/sites/default/local.settings.php');
      $fs->chmod($drupal_root . '/sites/default/local.settings.php', 0444);
      $event->getIO()->write("Create a sites/default/local.settings.php file with chmod 0444");
    }

    // Copy the env settings files
    if (!$fs->exists($drupal_root . '/sites/default/env')) {
      $fs->mirror($project_root . '/config/env', $drupal_root . '/sites/default/env', NULL, ['override' => TRUE]);
    }

  }

  public static function getSettingsSnippet() {
    return <<<'EOT'

/**
 ******************* Twelve Factor Drupal configuration *******************
 *
 * The following includes enable Drupal to read it's configuration from
 * local environment variables, and allow Drupal to be installed from
 * composer packages instead of being included in each repository. Any
 * specific changes needed in settings.php should go in
 * config/local.settings.php or in a settings.php file inside a specific
 * enviroment in config/env/ENVIRONMENT/settings.php.
 *
 **************************************************************************
 */

// Load local settings file
include __DIR__ . '/local.settings.php';

// Load database settings from environment variables
include __DIR__ . '/dbenv.settings.php';

// Load settings and config from environment
include __DIR__ . '/env.settings.php';

// Load flysystem config for S3 filesystem
include __DIR__ . '/flysystems3.settings.php';

EOT;
  }
}