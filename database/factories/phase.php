<?php
declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 5:44 PM
 */

use LaravelDoctrine\ORM\Testing\Factory;
use Tfboe\FmLib\Tests\Entity\Phase;

/** @var Factory $factory */
$factory->define(Phase::class, function (/** @noinspection PhpUnusedParameterInspection */
  \Faker\Generator $faker, array $attributes) {
  return [
    'name' => '',
    'phaseNumber' => $attributes['phaseNumber'],
    'startTime' => array_key_exists('startTime', $attributes) ? $attributes['startTime'] : null,
    'endTime' => array_key_exists('endTime', $attributes) ? $attributes['endTime'] : null
  ];
});