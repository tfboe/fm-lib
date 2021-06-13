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


//<editor-fold desc="Public Methods">
  /**
   * @param PlayerInterface $player
   * @param PlayerInterface $toMerge
   * @return bool|string
   */
  public function mergePlayers(PlayerInterface $player, PlayerInterface $toMerge)
  {
    if ($player->getId() == $toMerge->getId()) {
      return "Players are identical!";
    }

   /** @var TournamentInterface[] $ts */
   $ts = $this->em->createQueryBuilder()
     ->select("t")
     ->from(TournamentInterface::class, 't')
     ->innerJoin('t.competitions', 'c')
     ->innerJoin('c.teams', 'te')
     ->innerJoin('te.memberships', 'm')
     ->where('m.player = (:id)')->setParameter('id', $toMerge->getId())
     ->getQuery()->getResult();

   $tMap = [];
   foreach ($ts as $tournament) {
     $tMap[$tournament->getId()] = $tournament;
   }

   $ts = array_values($tMap);
   $this->ls->loadEntities($ts);

   // check if player is attendant in one of the tournaments of toMerge
   foreach ($ts as $tournament) {
     foreach ($tournament->getCompetitions() as $competition) {
       foreach ($competition->getTeams() as $team) {
         foreach ($team->getMemberships() as $membership) {
           if ($membership->getPlayer()->getId() == $player->getId()) {
             return "Player 1 and player 2 both attended the tournament " . $tournament->getName() .
               "(" . $tournament->getStartTime()->format('d.m.Y H:i') . ", id='" . $tournament->getId() . "')";
           }
         }
       }
     }
   }

  //change players
   foreach ($ts as $tournament) {
     foreach ($tournament->getCompetitions() as $competition) {
       $isMember = false;
       foreach ($competition->getTeams() as $team) {
         foreach ($team->getMemberships() as $membership) {
           if ($membership->getPlayer()->getId() == $toMerge->getId()) {
             $membership->setPlayer($player);
             $isMember = true;
           }
         }
       }
       if ($isMember) {
         foreach ($competition->getPhases() as $phase) {
           foreach ($phase->getMatches() as $match) {
             foreach ($match->getGames() as $game) {
               if ($game->getPlayersA()->containsKey($toMerge->getId())) {
                 $game->getPlayersA()->remove($toMerge->getId());
                 $game->getPlayersA()->set($player->getId(), $player);
               }
               if ($game->getPlayersB()->containsKey($toMerge->getId())) {
                 $game->getPlayersB()->remove($toMerge->getId());
                 $game->getPlayersB()->set($player->getId(), $player);
               }
             }
           }
         }
       }
     }
   }

   return true;
  }
//</editor-fold desc="Public Methods">
}