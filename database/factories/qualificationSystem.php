<?php
declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 5:44 PM
 */

use LaravelDoctrine\ORM\Testing\Factory;
use Tfboe\FmLib\Tests\Entity\QualificationSystem;

/** @var Factory $factory */

$factory->define(QualificationSystem::class, function (
  /** @noinspection PhpUnusedParameterInspection */
  \Faker\Generator $faker,
  /** @noinspection PhpUnusedParameterInspection */
  array $attributes) {
  return [
  ];
});