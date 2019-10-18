<?php
declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 5:44 PM
 */

use LaravelDoctrine\ORM\Testing\Factory;
use Tfboe\FmLib\Tests\Entity\Competition;

/** @var Factory $factory */
$factory->define(Competition::class, function (
  /** @noinspection PhpUnusedParameterInspection */
  \Faker\Generator $faker, array $attributes) {
  return [
    'name' => $attributes['name'],
    'startTime' => array_key_exists('startTime', $attributes) ? $attributes['startTime'] : null,
    'endTime' => array_key_exists('endTime', $attributes) ? $attributes['endTime'] : null
  ];
});