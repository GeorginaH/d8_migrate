<?php

namespace Drupal\custom_migrate\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate_tools\MigrateExecutable;
use Drupal\migrate\MigrateMessage;

/**
 * Class DefaultController.
 *
 * @package Drupal\custom_migrate\Controller
 */
class DefaultController extends ControllerBase {


  /**
   * Hello.
   *
   * @return string
   *   Return Hello string.
   */
  public function migrate() {
    /**
     * @var $migration \Drupal\migrate\Plugin\Migration
     */
    $migration = \Drupal::service('plugin.manager.config_entity_migration')->createInstance('profile');
    $migration->setStatus(MigrationInterface::STATUS_IDLE);
    $migration->getIdMap()->prepareUpdate();
    $exe = new MigrateExecutable($migration, new MigrateMessage());
    $exe->import();
    return [
      '#type' => 'markup',
      '#markup' => $this->t('Implement method: migrate')
    ];
  }
}
