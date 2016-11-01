<?php

namespace Drupal\Tests\migrate\Unit\process;

use Drupal\migrate\Plugin\migrate\process\Route;
use Drupal\Core\Url;
use Drupal\Core\Path\PathValidatorInterface;

/**
 * Tests the route process plugin.
 *
 * @coversDefaultClass \Drupal\migrate\Plugin\migrate\process\Route
 *
 * @group migrate
 */
class RouteTest extends MigrateProcessTestCase {

  /**
   * The path validator service.
   *
   * @var \Drupal\Core\Path\PathValidatorInterface
   */
  protected $pathValidator;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    $this->pathValidator = $this->prophesize(PathValidatorInterface::CLASS);
    parent::setUp();
  }

  /**
   * Tests Route plugin based on providerTestRoute() values.
   *
   * @param mixed $value
   *   Input value for the Route process plugin.
   * @param array $expected
   *   The expected results from the Route transform process.
   *
   * @dataProvider providerTestRoute
   */
  public function testRoute($value, $expected) {
    $link_path = is_string($value) ? $value : $value[0];
    $url = parse_url($link_path, PHP_URL_SCHEME) ? Url::fromUri($link_path) : Url::fromRoute($link_path);

    $this->pathValidator->getUrlIfValidWithoutAccessCheck($link_path)->willReturn($url);
    $plugin = new Route([], 'route', [], $this->pathValidator->reveal());
    $actual = $plugin->transform($value, $this->migrateExecutable, $this->row, 'destinationproperty');
    $this->assertSame($expected, $actual);
  }

  /**
   * Data provider for testRoute().
   *
   * @return array
   *  An array of arrays, where the first element is the input to the Route
   *  process plugin, and the second is the expected results.
   */
  public function providerTestRoute() {
    // Valid link path and valid options.
    $values[0] = [
      "user/login",
      [
        'attributes' => [
          'title' => 'Test menu link 1',
        ],
      ],
    ];
    $expected[0] = [
      'route_name' => "user/login",
      'route_parameters' => [],
      'options' => [
        'attributes' => [
          'title' => "Test menu link 1"
        ],
      ],
      'url' => NULL,
    ];

    // Valid link path and valid empty options.
    $values[1] = [
      "user/login",
      [],
    ];
    $expected[1] = [
      'route_name' => "user/login",
      'route_parameters' => [],
      'options' => [],
      'url' => NULL,
    ];

    // Valid link path and no options.
    $values[2] = 'user/login';
    $expected[2] = [
      'route_name' => "user/login",
      'route_parameters' => [],
      'options' => [],
      'url' => NULL,
    ];

    // Valid external link path and valid options.
    $values[3] = [
      'https://www.drupal.org',
      [
        'attributes' => [
          'title' => '',
        ],
      ],
    ];
    $expected[3] = [
      'route_name' => NULL,
      'route_parameters' => [],
      'options' => [
        'attributes' => [
          'title' => '',
        ],
      ],
      'url' => 'https://www.drupal.org',
    ];

    return [
      // Test with valid link path and valid options.
      [$values[0], $expected[0]],
      // Test with valid link path and valid empty options.
      [$values[1], $expected[1]],
      // Test with valid link path and no options,
      [$values[2], $expected[2]],
      // Test with external link path and valid options.
      [$values[3], $expected[3]],
    ];
  }

}
