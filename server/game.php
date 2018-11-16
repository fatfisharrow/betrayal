<?php
include_once(dirname(__FILE__) . "/hero.php");

class Game {
    private $mTitle;
    private $mPlayers = array();
    private $mIndex = 0;
    private $mServer;
    private $mHeroes;
    private $mOwner = null;

    private static $nullptr = null;

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
        if ($this->mOwner == null) {
            $this->mOwner = $player;
        }
        $player->mHero = $this->findEmptyHero();
    }

    public function detachPlayer(&$player) {
        logging::d("Game", "about to detach player: " . $player->mPlayerId . ":" . $player->mNick);
        foreach ($this->mPlayers as $k => &$v) {
            if ($v == $player) {
                logging::d("Game", "detached.");
                unset($this->mPlayers[$k]);
                if ($this->mOwner == $player) {
                    reset($this->mPlayers);
                    $this->mOwner = current($this->mPlayers);
                }
                return true;
            }
        }
        return false;
    }

    public function isEmpty() {
        return (count($this->mPlayers) == 0);
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

    public function &findHero($id) {
        logging::d("Game", "find hero: $id");
        foreach ($this->mHeroes as &$hero) {
            logging::d("Game", "check hero:");
            logging::d("Game", $hero);
            if ($hero->identify == $id) {
                return $hero;
            }
        }
        return self::$nullptr;
    }

    private function isHeroSelected(&$hero) {
        foreach ($this->mPlayers as &$player) {
            if ($player->mHero == $hero) {
                return true;
            }
        }
        return false;
    }

    public function &findEmptyHero() {
        foreach ($this->mHeroes as &$hero) {
            logging::d("Game", $hero);
            if (!$this->isHeroSelected($hero)) {
                logging::d("Game", "empyt hero: " . $hero->identify);
                return $hero;
            }
        }
        return self::$nullptr;
    }

    public function &findNextHero(&$currenthero) {
        if ($currenthero == null) {
            return $this->findEmptyHero();
        }

        $reach = false;
        foreach ($this->mHeroes as &$hero) {
            if ($hero == $currenthero) {
                $reach = true;
            }
            if (!$reach) {
                continue;
            }
            if (!$this->isHeroSelected($hero)) {
                return $hero;
            }
        }

        foreach ($this->mHeroes as &$hero) {
            if ($hero == $currenthero) {
                return $currenthero;
            }
            if (!$this->isHeroSelected($hero)) {
                return $hero;
            }
        }
        return $currenthero;
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

