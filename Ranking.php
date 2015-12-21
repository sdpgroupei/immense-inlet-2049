<?php
	
require_once('Court.php');
require_once('CourtCom.php');
require_once('CourtEditHist.php');
require_once('CourtModifHist.php');
require_once('Group.php');
require_once('Match.php');
require_once('Pair.php');
require_once('Tournament.php');
require_once('User.php');
require_once('UserCom.php');
require_once('UserHist.php');

class Ranking {
	private $db;

	public function Ranking($db){
		this->$db = $db;
	}

	public function set_ranking($group, $tournament) {
		$st = this->$db->prepare('SELECT count(match.winner_pair_fk), match.winner_pair_fk FROM match, groups, tournaments, pairs WHERE tournaments.idtournaments = :e AND pairs.tournament_fk = tournaments.idtournaments AND match.winner_pair_fk = pairs.idpairs GROUP BY match.winner_pair_fk ORDER BY count(match.winner_pair_fk) DESC');
		$st->execute(array('e' => $tournament));

		$i = 0;
		while (($row = $st->fetch(PDO::FETCH_ASSOC)) && ($i < 3)) {
			if ($i == 0) {
				$st1 = this->$db->prepare('UPDATE groups SET groups.first = :a WHERE groups.idgroups = :b');
				$st->execute(array('a' => $row['idpairs'],'b' => $group);
			}
			if ($i == 1) {
				$st1 = this->$db->prepare('UPDATE groups SET groups.second = :a WHERE groups.idgroups = :b');
				$st->execute(array('a' => $row['idpairs'],'b' => $group);
			}
			if ($i == 2) {
				$st1 = this->$db->prepare('UPDATE groups SET groups.third = :a WHERE groups.idgroups = :b');
				$st->execute(array('a' => $row['idpairs'],'b' => $group);
			}
			$i++;
		}
	}
}
