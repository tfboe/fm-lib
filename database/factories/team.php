<?php
declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 5:44 PM
 */

use LaravelDoctrine\ORM\Testing\Factory;
use Tfboe\FmLib\Tests\Entity\Team;

/** @var Factory $factory */

$factory->define(Team::class, function (/** @noinspection PhpUnusedParameterInspection */
  \Faker\Generator $faker, array $attributes) {
  return [
    'name' => '',
    'startNumber' => $attributes['startNumber'],
    'rank' => $attributes['rank']
  ];
});