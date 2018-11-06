<?php
include_once(dirname(__FILE__) . "/hero.php");

class Game {
    private $mTitle;
    private $mPlayers = array();
    private $mIndex = 0;
    private $mServer;
    private $mHeroes;

    public function __construct(&$server, $title, $id) {
        $this->mServer = $server;
        $this->mTitle = $title;
        $this->mIndex = $id;
        $this->mHeroes = Hero::createHeroes();
    }

    public function getSummary() {
        return array("title" => $this->mTitle, "id" => $this->mIndex,  "players" => count($this->mPlayers));
    }

    public function getInfo() {
        return array("title" => $this->mTitle, "id" => $this->mIndex,  "players" => $this->mPlayers);
    }

    public function getTitle() {
        return $this->mTitle;
    }

    public function getId() {
        return $this->mIndex;
    }

    public function attachPlayer(&$player) {
        $this->mPlayers []= $player;
    }

    public function broadcast($data) {
        foreach ($this->mPlayers as &$player) {
            $this->mServer->sendToPlayer($player, $data);
        }
    }

    public function broadcastInfo() {
        foreach ($this->mPlayers as &$player) {
            $this->mServer->sendGameInfo($this, $player);
        }
    }

    public function findHero($id) {
        logging::d("Game", "find hero: $id");
        foreach ($this->mHeroes as &$hero) {
            logging::d("Game", "check hero:");
            logging::d("Game", $hero);
            if ($hero->identify == $id) {
                return $hero;
            }
        }
        return null;
    }

    public function isReady() {
        foreach ($this->mPlayers as &$player) {
            if (!$player->isReady()) {
                return false;
            }
        }
        return true;
    }

};

