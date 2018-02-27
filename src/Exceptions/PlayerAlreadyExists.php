<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 11:31 PM
 */

namespace Tfboe\FmLib\Exceptions;


use Tfboe\FmLib\Entity\PlayerInterface;

/**
 * Class PlayerAlreadyExists
 * @package Tfboe\FmLib\Exceptions
 */
class PlayerAlreadyExists extends AbstractException
{
//<editor-fold desc="Fields">
  /** @var  PlayerInterface[] */
  private $players;
//</editor-fold desc="Fields">
//<editor-fold desc="Constructor">
  /**
   * PlayerAlreadyExists constructor.
   * @param PlayerInterface[] $players list of already existing players
   */
  public function __construct(array $players)
  {
    $this->players = $players;
    parent::__construct("Some players do already exist!", 409);
  }
//</editor-fold desc="Constructor">

//<editor-fold desc="Public Methods">
  /**
   * Gets a json representation of the exception
   * @return array
   */
  public function getJsonMessage()
  {
    return [
      'message' => "Some players do already exist",
      'players' => array_map(function (PlayerInterface $player) {
        return [
          "firstName" => $player->getFirstName(),
          "lastName" => $player->getLastName(),
          "id" => $player->getId(),
          "birthday" => $player->getBirthday()->format("Y-m-d")];
      }, $this->players)
    ];
  }
//</editor-fold desc="Public Methods">
}