<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/29/17
 * Time: 11:10 PM
 */

namespace Tfboe\FmLib\Service;


use Doctrine\ORM\EntityManagerInterface;
use Tfboe\FmLib\Entity\PlayerInterface;
use Tfboe\FmLib\Entity\TournamentInterface;

/**
 * Class PlayerService
 * @package App\Service
 */
class PlayerService implements PlayerServiceInterface
{
//<editor-fold desc="Fields">
  /** @var EntityManagerInterface */
  private $em;

  /** @var LoadingServiceInterface */
  private $ls;

  /** @var RankingSystemServiceInterface */
  private $rankingSystemService;
//</editor-fold desc="Fields">

//<editor-fold desc="Constructor">

  /**
   * PlayerService constructor.
   * @param EntityManagerInterface $em
   * @param LoadingServiceInterface $ls
   * @param RankingSystemServiceInterface $rankingSystemService
   */
  public function __construct(
    EntityManagerInterface $em,
    LoadingServiceInterface $ls,
    RankingSystemServiceInterface $rankingSystemService
  )
  {
    $this->em = $em;
    $this->ls = $ls;
    $this->rankingSystemService = $rankingSystemService;
  }
//</editor-fold desc="Constructor">

private function tournamentDoesNotContainPlayer(int $playerId, string $tournamentId) {
  $tournament = $this->em->find(TournamentInterface::class, $tournamentId);
  if ($tournament === null) {
    return "Tournament with id $tournamentId does not exist";
  }
  $this->ls->loadEntities([$tournament], [
    TournamentInterface::class => [["competitions"]],
    CompetitionInterface::class => [["teams"]],
    TeamInterface::class => [["memberships"]],
    TeamMembershipInterface::class => [["player"]],
  ]);
  foreach ($tournament->getCompetitions() as $competition) {
    foreach ($competition->getTeams() as $team) {
      foreach ($team->getMemberships() as $membership) {
        if ($membership->getPlayer()->getId() == $playerId) {
          return "Player 1 and player 2 both attended the tournament " . $tournament->getName() .
            "(" . $tournament->getStartTime()->format('d.m.Y H:i') . ", id='" . $tournament->getId() . "')";
        }
      }
    }
  }
  $this->em->clear();
  return true;
}

private function changePlayerInTournament(int $fromPlayerId, int $toPlayerId, string $tournamentId) {
  $tournament = $this->em->find(TournamentInterface::class, $tournamentId);
  $player = $this->em->find(PlayerInterface::class, $toPlayerId);
  if ($tournament === null) {
    return "Tournament with id $tournamentId does not exist";
  }
  if ($player === null) {
    return "Player with id $toPlayerId does not exist";
  }
  $this->ls->loadEntities([$tournament], [
    TournamentInterface::class => [["competitions"]],
    CompetitionInterface::class => [["teams"], ["phases"]],
    TeamInterface::class => [["memberships"]],
    PhaseInterface::class => [["matches"]],
    MatchInterface::class => [["games"]],
    GameInterface::class => [["playersA", "playersB"]],
  ]);
  foreach ($tournament->getCompetitions() as $competition) {
    $isMember = false;
    foreach ($competition->getTeams() as $team) {
      foreach ($team->getMemberships() as $membership) {
        if ($membership->getPlayer()->getId() == $fromPlayerId) {
          $membership->setPlayer($player);
          $isMember = true;
        }
      }
    }
    if ($isMember) {
      foreach ($competition->getPhases() as $phase) {
        foreach ($phase->getMatches() as $match) {
          foreach ($match->getGames() as $game) {
            if ($game->getPlayersA()->containsKey($fromPlayerId)) {
              $game->getPlayersA()->remove($fromPlayerId);
              $game->getPlayersA()->set($player->getId(), $player);
            }
            if ($game->getPlayersB()->containsKey($fromPlayerId)) {
              $game->getPlayersB()->remove($fromPlayerId);
              $game->getPlayersB()->set($player->getId(), $player);
            }
          }
        }
      }
    }
  }
  $this->em->flush();
  $this->em->clear();
}

//<editor-fold desc="Public Methods">
  /**
   * @param PlayerInterface $player
   * @param PlayerInterface $toMerge
   * @return bool|string
   */
  public function mergePlayers(PlayerInterface $player, PlayerInterface $toMerge)
  {
    $playerId = $player->getId();
    $toMergeId = $toMerge->getId();
    if ($playerId == $toMergeId) {
      return "Players are identical!";
    }

   /** @var TournamentInterface[] $ts */
   $ts = $this->em->createQueryBuilder()
     ->select("t.id")
     ->from(TournamentInterface::class, 't')
     ->innerJoin('t.competitions', 'c')
     ->innerJoin('c.teams', 'te')
     ->innerJoin('te.memberships', 'm')
     ->where('m.player = (:id)')->setParameter('id', $toMergeId)
     ->getQuery()->getResult();

    $tIds = [];
    foreach ($ts as $tournament) {
      $tIds[] = $tournament["id"];
    }

    foreach($tIds as $id) {
      $check = $this->tournamentDoesNotContainPlayer($playerId, $id);
      if (!$check) {
        return $check;
      }
    }

    foreach($tIds as $id) {
      $this->changePlayerInTournament($toMergeId, $playerId, $id);
    }

    return true;
  }
//</editor-fold desc="Public Methods">
}