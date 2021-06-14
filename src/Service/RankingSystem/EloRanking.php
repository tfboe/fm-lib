<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 3/7/17
 * Time: 4:31 PM
 */

namespace Tfboe\FmLib\Service\RankingSystem;

use Doctrine\Common\Collections\Collection;
use Tfboe\FmLib\Entity\Categories\ScoreMode;
use Tfboe\FmLib\Entity\GameInterface;
use Tfboe\FmLib\Entity\Helpers\Result;
use Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity;
use Tfboe\FmLib\Entity\PlayerInterface;
use Tfboe\FmLib\Entity\RankingSystemChangeInterface;
use Tfboe\FmLib\Entity\RankingSystemInterface;
use Tfboe\FmLib\Entity\RankingSystemListEntryInterface;
use Tfboe\FmLib\Entity\RankingSystemListInterface;

/**
 * Class EloRanking
 * @package Tfboe\FmLib\Service\TournamentRanking
 */
class EloRanking extends GameRankingSystemService implements EloRankingInterface
{
//<editor-fold desc="Fields">
  const EXP_DIFF = 400;
  const K = 20;
  const MAX_DIFF_TO_OPPONENT_FOR_PROVISORY = 400;
  const NO_NEG = true;
  const NUM_PROVISORY_GAMES = 15;
  const PROVISORY_PARTNER_FACTOR = 0.5;
  const START = 1200.0;
  const TEAM_ADJUSTMENT_FACTOR = 0.1;
//</editor-fold desc="Fields">

//<editor-fold desc="Protected Methods">
  /** @noinspection PhpMissingParentCallCommonInspection */
  /**
   * @inheritDoc
   */
  protected function getAdditionalChangeFields(): array
  {
    return ['teamElo', 'opponentElo'];
  }

  /**
   * Gets additional fields for this ranking type
   * @return string[] list of additional fields
   */
  protected function getAdditionalFields(): array
  {
    return ['playedGames' => 0, 'ratedGames' => 0, 'provisoryRanking' => self::START];
  }

  /**
   * @inheritDoc
   */
  protected function getChanges(TournamentHierarchyEntity $entity, RankingSystemListInterface $list,
                                array $oldChanges, array &$entries): array
  {
    /** @var GameInterface $game */
    $game = $entity;
    $changes = [];

    if (!$game->isPlayed() || $game->getResult() === Result::NOT_YET_FINISHED ||
      $game->getResult() === Result::NULLED) {
      //game gets not elo rated
      $this->addNotRatedChanges($changes, $game->getPlayersA(), $entity, $list->getRankingSystem(), $oldChanges);
      $this->addNotRatedChanges($changes, $game->getPlayersB(), $entity, $list->getRankingSystem(), $oldChanges);
      return $changes;
    }

    $entriesA = $this->getEntriesOfPlayers($game->getPlayersA(), $list, $entries);
    $entriesB = $this->getEntriesOfPlayers($game->getPlayersB(), $list, $entries);

    $isAProvisory = $this->hasProvisoryEntry($entriesA);
    $isBProvisory = $this->hasProvisoryEntry($entriesB);

    $averageA = $this->getEloAverage($entriesA);
    $averageB = $this->getEloAverage($entriesB);

    $expectationA = 1 / (1 + 10 ** (($averageB - $averageA) / self::EXP_DIFF));
    $expectationB = 1 - $expectationA;

    $resultA = 0.0;

    switch ($game->getResult()) {
      case Result::TEAM_A_WINS:
        $resultA = 1.0;
        break;
      case Result::DRAW:
        $resultA = 0.5;
        break;
    }
    $resultB = 1 - $resultA;

    $expectationDiffA = $resultA - $expectationA;
    $expectationDiffB = $resultB - $expectationB;


    $this->computeChanges($changes, $entriesA, $resultA, $expectationDiffA, $game, $averageA, $averageB,
      $isAProvisory, $isBProvisory, $oldChanges);
    $this->computeChanges($changes, $entriesB, $resultB, $expectationDiffB, $game, $averageB, $averageA,
      $isBProvisory, $isAProvisory, $oldChanges);
    return $changes;
  }

  /** @noinspection PhpMissingParentCallCommonInspection */
  /**
   * @inheritDoc
   */
  protected function startPoints(): float
  {
    return 0.0;
  }
//</editor-fold desc="Protected Methods">


//<editor-fold desc="Private Methods">
  /**
   * @param RankingSystemChangeInterface[] $changes
   * @param Collection|PlayerInterface[] $players
   * @param TournamentHierarchyEntity $entity
   * @param RankingSystemInterface $ranking
   * @param RankingSystemChangeInterface[] $oldChanges the dictionary of old changes of this entity indexed by player id
   */
  private function addNotRatedChanges(array &$changes, Collection $players, TournamentHierarchyEntity $entity,
                                      RankingSystemInterface $ranking, array $oldChanges)
  {
    return; //we ignore not rated changes here
    foreach ($players as $player) {
      $change = $this->getOrCreateChange($entity, $ranking, $player, $oldChanges);
      $change->setTeamElo(0.0);
      $change->setOpponentElo(0.0);
      $change->setPointsChange(0.0);
      $change->setPlayedGames(0);
      $change->setRatedGames(0);
      $change->setProvisoryRanking(0.0);
      $changes[] = $change;
    }
  }

  /** @noinspection PhpTooManyParametersInspection */ //TODO refactor this method
  /**
   * @param array $changes
   * @param RankingSystemListEntryInterface[] $entries
   * @param float $result
   * @param float $expectationDiff
   * @param GameInterface $game
   * @param float $teamAverage
   * @param float $opponentAverage
   * @param bool $teamHasProvisory
   * @param bool $opponentHasProvisory
   * @param RankingSystemChangeInterface[] $oldChanges the dictionary of old changes of this entity indexed by player id
   */
  private function computeChanges(array &$changes, array $entries, float $result, float $expectationDiff,
                                  GameInterface $game, float $teamAverage, float $opponentAverage,
                                  bool $teamHasProvisory, bool $opponentHasProvisory, array $oldChanges)
  {
    foreach ($entries as $entry) {
      $change = $this->getOrCreateChange($game, $entry->getRankingSystemList()->getRankingSystem(),
        $entry->getPlayer(), $oldChanges);
      $change->setPlayedGames(1);
      $change->setTeamElo($teamHasProvisory ? 0.0 : $teamAverage);
      $change->setOpponentElo($opponentHasProvisory ? 0.0 : $opponentAverage);
      $factor = 2 * $result - 1;
      if ($entry->getPlayedGames() < self::NUM_PROVISORY_GAMES) {
        //provisory entry => recalculate
        if (count($entries) > 1) {
          $teamMatesAverage = ($teamAverage * count($entries) - $entry->getProvisoryRanking()) /
            (count($entries) - 1);
          if ($teamMatesAverage > $opponentAverage + self::MAX_DIFF_TO_OPPONENT_FOR_PROVISORY) {
            $teamMatesAverage = $opponentAverage + self::MAX_DIFF_TO_OPPONENT_FOR_PROVISORY;
          }
          if ($teamMatesAverage < $opponentAverage - self::MAX_DIFF_TO_OPPONENT_FOR_PROVISORY) {
            $teamMatesAverage = $opponentAverage - self::MAX_DIFF_TO_OPPONENT_FOR_PROVISORY;
          }
          $performance = $opponentAverage * (1 + self::PROVISORY_PARTNER_FACTOR) -
            $teamMatesAverage * self::PROVISORY_PARTNER_FACTOR;
        } else {
          $performance = $opponentAverage;
        }
        if ($performance < self::START) {
          $performance = self::START;
        }
        $performance += self::EXP_DIFF * $factor;
        //old average performance = $entry->getProvisoryRating()
        //=> new average performance = ($entry->getProvisoryRating() * $entry->getRatedGames() + $performance) /
        //                             ($entry->getRatedGames() + 1)
        //=> performance change = ($entry->getProvisoryRating() * $entry->getRatedGames() + $performance) /
        //                        ($entry->getRatedGames() + 1) - $entry->getProvisoryRating()
        //                      = ($performance - $entry->getProvisoryRating()) / ($entry->getRatedGames() + 1)
        $change->setProvisoryRanking(($performance - $entry->getProvisoryRanking()) / ($entry->getRatedGames() + 1));
        $change->setPointsChange(0.0);
        $change->setRatedGames(1);
        if ($entry->getPlayedGames() == self::NUM_PROVISORY_GAMES - 1) {
          $change->setPointsChange(max(self::START, $entry->getProvisoryRanking() + $change->getProvisoryRanking())
            - $entry->getPoints());
        }
      } else if (!$teamHasProvisory && !$opponentHasProvisory) {
        //real elo ranking
        $change->setProvisoryRanking(0.0);
        $gameFactor = 1;
        $scoreMode = $game->getInherited("getScoreMode");
        if ($scoreMode == ScoreMode::BEST_OF_THREE) {
          $gameFactor = 2;
        } else if ($scoreMode == ScoreMode::BEST_OF_FIVE) {
          $gameFactor = 3;
        }

        $eloChange = self::K * $gameFactor * $expectationDiff;

        if (count($entries) > 1) {
          //let team mates come closer to each other with 10% of the points (5% on each side)
          $teamDiff = $teamAverage - $entry->getPoints();
          $adjustment = min(abs($teamDiff), abs($eloChange * self::TEAM_ADJUSTMENT_FACTOR / 2));
          $eloChange += ($teamDiff < 0 ? (-1) : 1) * $adjustment;
        }

        $change->setPointsChange(max($eloChange, self::START - $entry->getPoints()));
        $change->setRatedGames(1);

      } else {
        //does not get rated
        $change->setProvisoryRanking(0.0);
        $change->setPointsChange(0.0);
        $change->setRatedGames(0);
      }
      $changes[] = $change;
    }
  }

  /**
   * Computes the average rating of the given entries
   * @param RankingSystemListEntryInterface[] $entries must be nonempty
   * @return float
   */
  private function getEloAverage(array $entries): float
  {
    $sum = 0;
    foreach ($entries as $entry) {
      $sum += $entry->getRatedGames() < self::NUM_PROVISORY_GAMES ? $entry->getProvisoryRanking() : $entry->getPoints();
    }
    return $sum / count($entries);
  }

  /**
   * Checks if the given list of entries has at least one provisory entry
   * @param RankingSystemListEntryInterface[] $entries
   * @return bool
   */
  private function hasProvisoryEntry(array $entries): bool
  {
    foreach ($entries as $entry) {
      if ($entry->getPlayedGames() < self::NUM_PROVISORY_GAMES) {
        return true;
      }
    }
    return false;
  }
//</editor-fold desc="Private Methods">
}