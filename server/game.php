<?php

class Game {
    private $mTitle;
    private $mPlayers = array();
    private $mIndex = 0;
    private $mServer;

    public function __construct(&$server, $title, $id) {
        $this->mServer = $server;
        $this->mTitle = $title;
        $this->mIndex = $id;
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

    public function broadcastInfo() {
        foreach ($this->mPlayers as &$player) {
            $this->mServer->sendGameInfo($this, $player);
        }
    }
};

