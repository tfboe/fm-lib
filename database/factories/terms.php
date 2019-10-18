<?php
declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 5:44 PM
 */

use LaravelDoctrine\ORM\Testing\Factory;
use Tfboe\FmLib\Tests\Entity\Terms;

/** @var Factory $factory */
$factory->define(Terms::class, function (
  /** @noinspection PhpUnusedParameterInspection */
  \Faker\Generator $faker, array $attributes) {
  return [
    'text' => array_key_exists('text', $attributes) ? $attributes['text'] : "Test Terms 1",
    'minorVersion' => array_key_exists('minorVersion', $attributes) ? $attributes['minorVersion'] : 1,
    'majorVersion' => array_key_exists('majorVersion', $attributes) ? $attributes['majorVersion'] : 1
  ];
});