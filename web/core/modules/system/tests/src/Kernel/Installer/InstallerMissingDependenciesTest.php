<?php

namespace Drupal\Tests\system\Kernel\Installer;

use Drupal\KernelTests\KernelTestBase;

/**
 * Tests that we handle the absence of a module dependency during install.
 *
 * @group Installer
 */
class InstallerMissingDependenciesTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = ['system'];

  /**
   * Verifies that the exception message in the profile step is correct.
   *
   * @expectedException \Drupal\Core\Installer\Exception\MissingDependencyException
   * @expectedExceptionMessage The install profile depends on the missing module missing_module.
   */
  public function testSetUpWithMissingDependencies() {
    global $install_state;
    $install_state['parameters']['profile'] = 'testing_missing_dependencies';

    // Rebuild system.module.files state data.
    // @todo Remove as part of https://www.drupal.org/node/2186491
    drupal_static_reset('system_rebuild_module_data');
    system_rebuild_module_data();
    // We must set interactive to false to ensure that the exceptions are thrown.
    $install_state['interactive'] = FALSE;
    drupal_check_profile('testing_missing_dependencies');
  }

}
