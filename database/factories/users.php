<?php
declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 9/16/17
 * Time: 11:48 AM
 */

use Illuminate\Support\Facades\Hash;
use LaravelDoctrine\ORM\Testing\Factory;
use Tfboe\FmLib\Tests\Entity\User;

/** @var Factory $factory */

$factory->define(User::class, function (\Faker\Generator $faker, array $attributes) {
  if (array_key_exists('originalPassword', $attributes)) {
    $password = $attributes['originalPassword'];
  } else {
    $password = $faker->password(8, 30);
  }
  return [
    'password' => Hash::make($password),
    'email' => $faker->email,
    'jwtVersion' => 1,
    'confirmedTermsMinorVersion' => 0,
    'confirmedTermsMajorVersion' => 0,
  ];
});