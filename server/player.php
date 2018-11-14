<?php

class Player {

    const STATUS_WAIT = 0;
    const STATUS_IDLE = 1;
    const STATUS_ROOM = 2;
    const STATUS_GAME = 3;
    const STATUS_BLACKLISTED = 4;

    public $mPlayerId;
    public $mStatus;
    public $mNick;
    public $mHero;

    private $mServer;
    private $mGame;

    public function __construct(&$server) {
        $this->mPlayerId = 0;
        $this->mStatus = self::STATUS_WAIT;
        $this->mServer = $server;
        $this->mHero = null;
    }

    public function isReady() {
        logging::d("Player", "check ready. mHero = ");
        logging::d("Player", $this->mHero);
        return ($this->mHero != null);
    }

    public function &game() {
        return $this->mGame;
    }

    public function onCommand($command) {
        $cmd = json_decode($command, true);
        if (!isset($cmd["op"])) {
            return false;
        }

        if ($this->mStatus == self::STATUS_WAIT) {
            return $this->onWaitCommand($cmd);
        } else if ($this->mStatus == self::STATUS_IDLE) {
            return $this->onIdleCommand($cmd);
        } else if ($this->mStatus == self::STATUS_ROOM) {
            return $this->onRoomCommand($cmd);
        } else if ($this->mStatus == self::STATUS_GAME) {
            return $this->onGameCommand($cmd);
        } else {
        }
        return false;
    }

    private function onWaitCommand($command) {
        logging::d("Player.onWaitCommand", $command);
        if ($command["op"] == "login") {
            // TODO: do login
            // $this->mNick = "";
            $this->mPlayerId = rand() % 10000;
            $this->mNick = "TEST NickName";

            $this->mServer->loginSuccess($this);
            $this->mStatus = self::STATUS_IDLE;
        } else {
            return false;
        }
        return true;
    }

    private function onIdleCommand($command) {
        logging::d("Player.onIdleCommand", $command);
        if ($command["op"] == "create") {
            $this->mGame = $this->mServer->createGame($this, $command["title"]);
            if ($this->mGame != null) {
                $this->mStatus = self::STATUS_ROOM;
                $this->mGame->broadcastInfo();
            }
        } else if ($command["op"] == "refresh") {
            $this->mServer->sendGameList($this);
        } else if ($command["op"] == "join") {
            $gameid = $command["game"];
            $this->mGame = $this->mServer->joinGame($this, $gameid);
            if ($this->mGame != null) {
                $this->mStatus = self::STATUS_ROOM;
            }
        } else {
            return false;
        }
        return true;
    }

    private function onRoomCommand($command) {
        $op = $command["op"];
        if ($op == "select") {
            $id = $command["data"];
            $this->mHero = $this->mGame->findHero($id);
            logging::d("Player", $this->mHero);
        } else if ($op == "leave") {
        } else if ($op == "start") {
            if ($this->mGame->isReady()) {
                $this->mServer->startGame($this->mGame);
            } else {
                $this->mServer->startFail($this);
            }
        } else {
            return false;
        }
        return true;
    }
    private function onGameCommand($command) {
        return true;
    }
};




