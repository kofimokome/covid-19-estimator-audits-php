<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../test-utils.php';
require_once __DIR__ . '/../../src/estimator.php';

class challengeOneTest extends TestCase
{
  public function estimatorDataProvider()
  {
    return [
      "days - challenge 1" => ["days", "ch-1"],
      "weeks - challenge 1" => ["weeks", "ch-1"],
      "months - challenge 1" => ["months", "ch-1"]
    ];
  }

  /**
   * @test
   * @testdox
   * @dataProvider estimatorDataProvider
   */
  public function estimate_current_and_projected_infections($duration, $challenge)
  {
    $response_object = mockEstimatorFor($duration);
    $this->assertIsObject($response_object, "Something went wrong with testing data");

    $estimation = convertEstimatesToArray($response_object);
    $data = $estimation["data"];
    $estimate = $estimation["estimate"];

    $result = covid19ImpactEstimator($data);

    $this->assertIsArray($result);
    $this->assertNotEmpty($result);
    $this->assertArrayHasKey('impact', $result);
    $this->assertArrayHasKey('severeImpact', $result);
    $this->assertIsArray($result['impact']);
    $this->assertIsArray($result['severeImpact']);

    foreach ($GLOBALS['fields'][$challenge] as $key) {
      $this->assertArrayHasKey($key, $result['impact']);
      $this->assertArrayHasKey($key, $result['severeImpact']);
    }

    $values = valueOnFields($result, $estimate, $challenge);
    foreach ($values as $estimation) {
      $field = $estimation[0];
      $produced = $estimation[1];
      $expected = $estimation[2];
      $this->assertEquals($produced, $expected, 'The expected ' . $field . ' ' . $expected . ' is not equal to your output ' . $produced);
    }
  }
}
